<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'name',
                'email',
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

                if ($row->email_verified_at)
                    $email_badge = '<span class="badge bg-success">'.lang('Verified').'</span>';
                else
                    $email_badge = '<span class="badge bg-warning">'.lang('Unverified').'</span>';

                if ($row->status)
                    $status_badge = '<span class="badge bg-success">'.lang('Active').'</span>';
                else
                    $status_badge = '<span class="badge bg-danger">'.lang('Banned').'</span>';


                if ($row->isSubscribed())
                    $action_btn = '<a href="#" data-url="'.route('admin.subscriptions.edit', $row->subscription->id).'" data-toggle="slidePanel"  title="'.lang('Subscription').'" class="btn btn-icon btn-primary" data-tippy-placement="top"><i class="icon-feather-award"></i></a>';
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
                $rows[] = '<td>'.$email_badge.'</td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>'.date_formating($row->created_at).'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="'.route('admin.users.edit', $row->id).'" title="'.lang('Edit').'" class="btn btn-icon btn-default me-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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

        /* Mark users as viewed */
        $unviewed = User::where('is_viewed', 0)->get();
        foreach ($unviewed as $user) {
            $user->is_viewed = 1;
            $user->save();
        }

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'email', 'string', 'max:100', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'country' => ['required', 'integer', 'exists:countries,id'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        if (!empty($request->get('avatar'))) {
            $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150');
        } else {
            $avatar = "default.png";
        }

        $country = Country::find($request->country);

        $user = User::create([
            'user_type' => 'user',
            'name' => $request->firstname . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $avatar,
            'address' => ['address' => '', 'city' => '', 'state' => '', 'zip' => '', 'country' => $country->name],
        ]);
        if ($user) {
            $user->forceFill(['email_verified_at' => Carbon::now()])->save();

            $result = array('success' => true, 'message' => lang('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     */
    public function show(User $user)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
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

        $avatar = $user->avatar;
        if (!empty($request->get('avatar'))) {
            if ($user->avatar == 'default.png') {
                $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150');
            } else {
                $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150', null, $user->avatar);
            }
        }

        $country = Country::find($request->country);

        $update = $user->update([
            'user_type' => $request->user_type,
            'avatar' => $avatar,
            'name' => $request->firstname . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'status' => $request->status,
            'address' => [
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'country' => $country->name,
            ],
        ]);
        if ($update) {
            $emailValue = ($request->email_status) ? Carbon::now() : null;
            $user->forceFill([
                'email_verified_at' => $emailValue,
            ])->save();

            quick_alert_success(lang('Updated Successfully'));
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     */
    public function destroy(User $user)
    {
        abort(404);
    }

    /**
     * Remove the multiple resources from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
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

        $result = array('success' => true, 'message' => lang('Deleted Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Remove user's avatar
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function deleteAvatar(User $user)
    {
        $avatar = "default.png";
        if ($user->avatar != "default.png") {
            remove_file('storage/avatars/users/'.$user->avatar);
        }

        $update = $user->update([
            'avatar' => $avatar,
        ]);
        if ($update) {
            quick_alert_success(lang('Removed Successfully'));
            return back();
        }
    }

    /**
     * Display password form
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function password(User $user)
    {
        return view('admin.users.password', compact('user'));
    }

    /**
     * Update password
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
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
            'password' => Hash::make($request->password),
        ]);
        if ($update) {
            quick_alert_success(lang('Password changed successfully'));
            return back();
        }
    }

    /**
     * Send user email
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function sendMail(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string'],
            'message' => ['required', 'string'],
            'reply_to' => ['required', 'email'],
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
            quick_alert_error(lang('SMTP is disabled, setup the SMTP details first.'));
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
            quick_alert_success(lang('Sent successfully'));
            return back();
        } catch (\Exception $e) {
            quick_alert_error(lang('Error in sending email') . ' ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Display user logs
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
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

        return view('admin.users.userlogs', compact('user'));
    }

    /**
     * Display logs by ip
     *
     * @param $ip
     * @return \Illuminate\Http\Response
     */
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

        return view('admin.users.logs', compact('ip'));
    }
}
