<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = AdminNotification::orderbyDesc('id')->get();
        $unreadNotificationsCount = AdminNotification::where('status', 0)->get()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadNotificationsCount'));
    }


    /**
     * Redirect to the notification link
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view($id)
    {
        $notification = AdminNotification::where('id', unhashid($id))->firstOrFail();
        $updateStatus = $notification->update(['status' => 1]);
        if ($updateStatus) {
            return redirect($notification->link);
        }
    }

    /**
     * Mark all notifications as read
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead()
    {
        AdminNotification::where('status', 0)->update(['status' => 1]);
        quick_alert_success(lang('All notifications marked as read'));
        return back();
    }

    /**
     * Delete all read notifications
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAllRead()
    {
        AdminNotification::where('status', 1)->delete();
        quick_alert_success(lang('Deleted Successfully'));
        return back();
    }
}
