<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Faq;
use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class UserController extends Controller
{
    public function index(Request $request, $search = null)
    {
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'name',
                'email',
                '',
                'email_verified_at',
                'status'
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $users = User::where('id', 'like', '%' . $q . '%')
                    ->OrWhere('firstname', 'like', '%' . $q . '%')
                    ->OrWhere('lastname', 'like', '%' . $q . '%')
                    ->OrWhere('email', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $users = User::orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = User::count();
            foreach ($users as $row) {

                if($row->isSubscribed()){
                    if ($row->subscription->isCancelled())
                        $subscribe_badge = '<span class="badge bg-warning">'.admin_lang('Canceled').'</span>';
                    elseif ($row->subscription->isExpired())
                        $subscribe_badge = '<span class="badge bg-danger">'.admin_lang('Expired').'</span>';
                    else
                        $subscribe_badge = '<span class="badge bg-success">'.admin_lang('Subscribed').'</span>';
                }else{
                    $subscribe_badge = '<span class="badge bg-secondary">'.admin_lang('Unsubscribed').'</span>';
                }

                if ($row->email_verified_at)
                    $email_badge = '<span class="badge bg-success">'.admin_lang('Verified').'</span>';
                else
                    $email_badge = '<span class="badge bg-warning">'.admin_lang('Unverified').'</span>';

                if ($row->status)
                    $status_badge = '<span class="badge bg-success">'.admin_lang('Active').'</span>';
                else
                    $status_badge = '<span class="badge bg-danger">'.admin_lang('Banned').'</span>';


                if ($row->isSubscribed())
                    $action_btn = '<a href="#" data-url="'.route('admin.subscriptions.edit', $row->subscription->id).'" data-toggle="slidePanel"  title="'.admin_lang('Subscription').'" class="btn btn-icon btn-primary" data-tippy-placement="top"><i class="icon-feather-award"></i></a>';
                else
                    $action_btn = "";

                $rows = array();
                $rows[] = '<td>'.$row->id.'</td>';
                $rows[] = '<td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <a href="'.route('admin.users.edit', $row->id).'">
                                            <img class="rounded-circle" alt="'.$row->username.'" src="'.asset('storage/avatars/users/'.$row->avatar).'" />
                                        </a>
                                    </div>
                                    <div>
                                        <a class="text-body fw-semibold text-truncate"
                                            href="'.route('admin.users.edit', $row->id).'">'.$row->name.'</a>
                                        <p class="text-muted mb-0">@'.$row->username.'</p>
                                    </div>
                                </div>
                            </td>';
                $rows[] = '<td><span class="text-truncate">'.$row->email.'</span></td>';
                $rows[] = '<td>'.$subscribe_badge.'</td>';
                $rows[] = '<td>'.$email_badge.'</td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>'.date_formating($row->created_at).'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="'.route('admin.users.edit', $row->id).'" title="'.admin_lang('Edit').'" class="btn btn-icon btn-default me-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                    '.$action_btn.'
                                </div>
                            </td>';
                $rows[] = '<td>
                                <div class="checkbox">
                                <input type="checkbox" id="check_'.$row->id.'" value="'.$row->id.'" class="quick-check">
                                <label for="check_'.$row->id.'"><span class="checkbox-icon"></span></label>
                            </div>
                           </td>';

                $rows['DT_RowId'] = $row->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw"            => intval( $params['draw'] ),
                "recordsTotal"    => intval( $totalRecords ),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        $unviewedUsers = User::where('is_viewed', 0)->get();
        if ($unviewedUsers->count() > 0) {
            foreach ($unviewedUsers as $unviewedUser) {
                $unviewedUser->is_viewed = 1;
                $unviewedUser->save();
            }
        }
        $activeUsersCount = User::where('status', 1)->get()->count();
        $bannedUserscount = User::where('status', 0)->get()->count();

        return view('admin.users.index', [
            'activeUsersCount' => $activeUsersCount,
            'bannedUserscount' => $bannedUserscount
        ]);
    }

    public function create()
    {
        $password = Str::random(16);
        return view('admin.users.create', ['password' => $password]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'email', 'string', 'max:100', 'unique:users'],
            'country' => ['required', 'integer', 'exists:countries,id'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $country = Country::find($request->country);

        if (!empty($request->get('avatar'))) {
            $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150');
        } else {
            $avatar = "default.png";
        }

        $address = ['address' => '', 'city' => '', 'state' => '', 'zip' => '', 'country' => $country->name];
        $user = User::create([
            'name' => $request->firstname . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'address' => $address,
            'avatar' => $avatar,
            'password' => Hash::make($request->password),
        ]);
        if ($user) {
            $user->forceFill(['email_verified_at' => Carbon::now()])->save();

            $result = array('success' => true, 'message' => admin_lang('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    public function show(User $user)
    {
        return abort(404);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'email' => ['required', 'email', 'string', 'max:100', 'unique:users,email,' . $user->id],
            'address' => ['nullable', 'max:255'],
            'city' => ['nullable', 'max:150'],
            'state' => ['nullable', 'max:150'],
            'zip' => ['nullable', 'max:100'],
            'country' => ['required', 'integer', 'exists:countries,id'],
            'user_type' => ['required', 'string', 'max:5'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }
        $country = Country::find($request->country);
        $status = $request->status;
        $address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $country->name,
        ];

        $avatar = $user->avatar;
        if ($request->has('avatar')) {
            if ($user->avatar == 'default.png') {
                $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150');
            } else {
                $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150', null, $user->avatar);
            }
        }

        $update = $user->update([
            'avatar' => $avatar,
            'name' => $request->firstname . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'address' => $address,
            'status' => $status,
        ]);
        if ($update) {
            $emailValue = ($request->has('email_status') && $request->email_status == 1) ? Carbon::now() : null;
            $user->forceFill([
                'email_verified_at' => $emailValue,
            ])->save();

            quick_alert_success(admin_lang('Updated Successfully'));
            return back();
        }
    }

    public function destroy(User $user)
    {
        if ($user->avatar != "default.png") {
            remove_file('storage/avatars/users/'.$user->avatar);
        }
        delete_admin_notification(route('admin.users.edit', $user->id));
        $user->delete();
        quick_alert_success(admin_lang('Deleted Successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $users = User::whereIn('id',$ids)->get();
        foreach($users as $user){
            if ($user->avatar != "default.png") {
                remove_file('storage/avatars/users/'.$user->avatar);
            }
            delete_admin_notification(route('admin.users.edit', $user->id));
        }
        User::whereIn('id',$ids)->delete();
        $result = array('success' => true, 'message' => admin_lang('Deleted Successfully'));
        return response()->json($result, 200);
    }

    public function deleteAvatar(User $user)
    {
        $avatar = "default.png";
        if ($user->avatar != "default.png") {
            remove_file('storage/avatars/users/'.$user->avatar);
        } else {
            quick_alert_error(admin_lang('Default avatar cannot be deleted'));
            return back();
        }
        $update = $user->update([
            'avatar' => $avatar,
        ]);
        if ($update) {
            quick_alert_success(admin_lang('Removed Successfully'));
            return back();
        }
    }

    public function password(User $user)
    {
        return view('admin.users.password', ['user' => $user]);
    }

    public function updatePassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }

        $update = $user->update([
            'password' => bcrypt($request->get('password')),
        ]);
        if ($update) {
            quick_alert_success(lang('Account password has been changed successfully', 'account'));
            return back();
        }
    }

    public function sendMail(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string'],
            'reply_to' => ['required', 'email'],
            'message' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }
        if (!settings('smtp')->status) {
            quick_alert_error(admin_lang('SMTP is not enabled'));
            return back()->withInput();
        }
        try {
            $email = $user->email;
            $subject = $request->subject;
            $replyTo = $request->reply_to;
            $msg = $request->message;
            \Mail::send([], [], function ($message) use ($msg, $email, $subject, $replyTo) {
                $message->to($email)
                    ->replyTo($replyTo)
                    ->subject($subject)
                    ->html($msg);
            });
            quick_alert_success(admin_lang('Sent successfully'));
            return back();
        } catch (\Exception $e) {
            quick_alert_error(admin_lang('Sent error'));
            return back();
        }
    }

    public function logs(User $user)
    {
        if (request()->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = request();

            //define index of column
            $columns = array(
                'ip',
                'browser',
                'os',
                'location',
                'timezone',
                'latitude',
                'longitude'
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $logs = UserLog::where('user_id', $user->id)
                    ->where(function ($query) use ($q) {
                        return $query->where('ip', 'like', '%' . $q . '%')
                            ->orWhere('browser', 'like', '%' . $q . '%')
                            ->orWhere('os', 'like', '%' . $q . '%')
                            ->orWhere('location', 'like', '%' . $q . '%')
                            ->orWhere('timezone', 'like', '%' . $q . '%')
                            ->orWhere('latitude', 'like', '%' . $q . '%')
                            ->orWhere('longitude', 'like', '%' . $q . '%');
                    })
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $logs = UserLog::where('user_id', $user->id)
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = UserLog::where('user_id', $user->id)->count();
            foreach ($logs as $log) {
                $rows = array();
                $rows[] = '<td><a href="'.route('admin.users.logsbyip', $log->ip).'">'.$log->ip.'</a></td>';
                $rows[] = '<td>'.$log->browser.'</td>';
                $rows[] = '<td>'.$log->os.'</td>';
                $rows[] = '<td>'.$log->location.'</td>';
                $rows[] = '<td>'.$log->timezone.'</td>';
                $rows[] = '<td>'.$log->latitude.'</td>';
                $rows[] = '<td>'.$log->longitude.'</td>';
                $rows['DT_RowId'] = $log->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw"            => intval( $params['draw'] ),
                "recordsTotal"    => intval( $totalRecords ),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.users.userlogs', ['user' => $user]);
    }

    public function logsByIp($ip)
    {
        if (request()->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = request();

            //define index of column
            $columns = array(
                'ip',
                'user',
                'browser',
                'os',
                'location',
                'timezone',
                'latitude',
                'longitude'
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $logs = UserLog::with('user')
                    ->where('ip', $ip)
                    ->where(function ($query) use ($q) {
                        return $query->orWhere('browser', 'like', '%' . $q . '%')
                            ->orWhere('os', 'like', '%' . $q . '%')
                            ->orWhere('location', 'like', '%' . $q . '%')
                            ->orWhere('timezone', 'like', '%' . $q . '%')
                            ->orWhere('latitude', 'like', '%' . $q . '%')
                            ->orWhere('longitude', 'like', '%' . $q . '%');
                    })
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $logs = UserLog::with('user')
                    ->where('ip', $ip)
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = UserLog::where('ip', $ip)->count();
            foreach ($logs as $log) {
                $rows = array();
                $rows[] = '<td>'.$log->ip.'</td>';
                $rows[] = '<td><a href="'.route('admin.users.edit', $log->user->id).'">'.$log->user->username.'</a></td>';
                $rows[] = '<td>'.$log->browser.'</td>';
                $rows[] = '<td>'.$log->os.'</td>';
                $rows[] = '<td>'.$log->location.'</td>';
                $rows[] = '<td>'.$log->timezone.'</td>';
                $rows[] = '<td>'.$log->latitude.'</td>';
                $rows[] = '<td>'.$log->longitude.'</td>';
                $rows['DT_RowId'] = $log->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw"            => intval( $params['draw'] ),
                "recordsTotal"    => intval( $totalRecords ),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.users.logs', ['ip' => $ip]);
    }
}
