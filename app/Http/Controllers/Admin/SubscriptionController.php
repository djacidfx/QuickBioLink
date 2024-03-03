<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class SubscriptionController extends Controller
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
                'user_id',
                'plan_id',
                'created_at',
                'expiry_at',
                'status'
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $subscription = Subscription::with(['user', 'plan'])
                    ->where('id', 'like', '%' . $q . '%')
                    ->OrWhere('user_id', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $subscription = Subscription::with(['user', 'plan'])
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();

            }

            $totalRecords = Subscription::count();
            foreach ($subscription as $row) {
                if ($row->isExpired()) {
                    $status_badge = '<span class="badge bg-danger">' . lang('Expired') . '</span>';
                } elseif ($row->isCancelled()) {
                    $status_badge = '<span class="badge bg-warning">' . lang('Canceled') . '</span>';
                } else {
                    $status_badge = '<span class="badge bg-success">' . lang('Active') . '</span>';
                }
                $rows = array();
                $rows[] = '<td>' . $row->id . '</td>';
                $rows[] = '<td>
                                <div class="quick-user-box">
                                <div class="quick-user-box">
                                    <a class="quick-user-avatar"
                                        href="' . route('admin.users.edit', $row->user->id) . '">
                                        <img src="' . asset('storage/avatars/users/' . $row->user->avatar) . '" alt="User" />
                                    </a>
                                    <div>
                                        <a class="text-body fw-bold"
                                            href="' . route('admin.users.edit', $row->user->id) . '">' . $row->user->name . '</a>
                                        <p class="text-muted mb-0">' . $row->user->email . '</p>
                                    </div>
                                </div>
                            </td>';
                $rows[] = '<td>' . $row->plan->name . '</td>';
                $rows[] = '<td>' . date_formating($row->created_at) . '</td>';
                $rows[] = '<td>' . date_formating($row->expiry_at) . '</td>';
                $rows[] = '<td>' . $status_badge . '</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="#" data-url="' . route('admin.subscriptions.edit', $row->id) . '" data-toggle="slidePanel" title="' . lang('Edit') . '" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                </div>
                            </td>';
                $rows[] = '<td>
                                <div class="checkbox">
                                <input type="checkbox" id="check_' . $row->id . '" value="' . $row->id . '" class="quick-check">
                                <label for="check_' . $row->id . '"><span class="checkbox-icon"></span></label>
                            </div>
                           </td>';
                $rows['DT_RowId'] = $row->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw" => intval($params['draw']),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        /* Mark subscriptions as viewed */
        $unviewed = Subscription::where('is_viewed', 0)->get();
        foreach ($unviewed as $subscription) {
            $subscription->is_viewed = true;
            $subscription->save();
        }

        return view('admin.subscriptions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('status', 1)->get();
        $plans = Plan::all();
        return view('admin.subscriptions.create', compact('users', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => ['required', 'integer'],
            'plan' => ['required', 'integer'],
        ]);

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $user = User::where('id', $request->user)->with('subscription')->firstOrFail();
        if ($user->isSubscribed()) {
            $result = array('success' => false, 'message' => lang('User is already subscribed.'));
            return response()->json($result, 200);
        }

        $plan = Plan::where('id', $request->plan)->firstOrFail();

        if ($plan->interval == 1) {
            $expiry_at = Carbon::now()->addMonth();
        } else {
            $expiry_at = Carbon::now()->addYear();
        }

        $create = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'plan_settings' => $plan->settings,
            'expiry_at' => $expiry_at,
            'is_viewed' => 1,
        ]);
        if ($create) {
            $result = array('success' => true, 'message' => lang('Added Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Subscription $subscription
     */
    public function show(Subscription $subscription)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        $plans = Plan::all();
        return view('admin.subscriptions.edit', compact('subscription', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'boolean'],
            'plan' => ['required', 'integer']
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $plan = Plan::where('id', $request->plan)->firstOrFail();
        $expiry_at = Carbon::parse($request->expiry_at);

        $update = $subscription->update([
            'plan_id' => $plan->id,
            'status' => $request->status,
            'plan_settings' => $plan->settings,
            'expiry_at' => $expiry_at,
        ]);
        if ($update) {
            $result = array('success' => true, 'message' => lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Subscription $subscription
     */
    public function destroy(Subscription $subscription)
    {
        abort(404);
    }

    /**
     * Remove the multiple resources from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $sql = Subscription::whereIn('id', $ids)->delete();
        if ($sql) {
            $result = array('success' => true, 'message' => lang('Deleted Successfully'));
            return response()->json($result, 200);
        }
    }
}
