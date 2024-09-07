<?php

namespace App\Http\Controllers\API;

use App\Models\Group;
use App\Models\Notification;
use App\Models\Reaction;
use App\Models\SentReaction;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Notification as ResourcesNotification;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class NotificationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = Notification::orderByDesc('updated_at')->get();

        return $this->handleResponse(ResourcesNotification::collection($notifications), __('notifications.find_all_notifications_success'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get inputs
        $inputs = [
            'days_before_blocking' => $request->days_before_blocking,
            'type_id' => $request->type_id,
            'status_id' => $request->status_id,
            'from_user_id' => $request->from_user_id,
            'to_user_id' => $request->to_user_id,
            'post_id' => $request->post_id,
            'event_id' => $request->event_id,
            'community_id' => $request->community_id,
            'message_id' => $request->message_id,
            'team_id' => $request->team_id,
            'reaction_id' => $request->reaction_id,
            'cart_id' => $request->cart_id
        ];

        $validator = Validator::make($inputs, [
            'type_id' => ['required'],
            'status_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());       
        }

        $notification = Notification::create($inputs);

        return $this->handleResponse(new ResourcesNotification($notification), __('notifications.create_notification_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = Notification::find($id);

        if (is_null($notification)) {
            return $this->handleError(__('notifications.find_notification_404'));
        }

        return $this->handleResponse(new ResourcesNotification($notification), __('notifications.find_notification_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'days_before_blocking' => $request->days_before_blocking,
            'type_id' => $request->type_id,
            'status_id' => $request->status_id,
            'from_user_id' => $request->from_user_id,
            'to_user_id' => $request->to_user_id,
            'post_id' => $request->post_id,
            'event_id' => $request->event_id,
            'community_id' => $request->community_id,
            'message_id' => $request->message_id,
            'team_id' => $request->team_id,
            'reaction_id' => $request->reaction_id,
            'cart_id' => $request->cart_id,
            'updated_at' => now()
        ];

        $validator = Validator::make($inputs, [
            'type_id' => ['required'],
            'status_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());       
        }

        $notification->update($inputs);

        return $this->handleResponse(new ResourcesNotification($notification), __('notifications.update_notification_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        $notifications = Notification::all();

        return $this->handleResponse(ResourcesNotification::collection($notifications), __('notifications.delete_notification_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Select all user notifications.
     *
     * @param  int $user_id
     * @param  string $status_alias
     * @return \Illuminate\Http\Response
     */
    public function selectByUser($user_id, $status_alias)
    {
        // Group
        $reaction_on_post_type_group = Group::where('group_name->fr', 'Réaction sur type de notification')->first();
        // Reaction
        $don_t_see_this_anymore_reaction = Reaction::where([['reaction_name->fr', 'Ne plus voir ça'], ['group_id', $reaction_on_post_type_group->id]])->first();
        // Requests
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        // Get the IDs of the notifications that are hidden by the current user
        $with_sent_reactions_notification_type_ids = SentReaction::where([['reaction_id', $don_t_see_this_anymore_reaction->id], ['user_id', $user->id]])
                                                                    ->pluck('to_notification_type_id')->toArray();
        $with_sent_reactions_notification_type_ids = $with_sent_reactions_notification_type_ids != null ? $with_sent_reactions_notification_type_ids : [0];

        if ($status_alias != '0') {
            $status = Status::where('alias', $status_alias)->first();

            if (is_null($status)) {
                return $this->handleError(__('notifications.find_status_404'));
            }

            $notifications = Notification::whereNotIn('type_id', $with_sent_reactions_notification_type_ids)
                                            ->where([['status_id', $status->id], ['to_user_id', $user->id]])
                                            ->orderByDesc('created_at')->orderByDesc('created_at')->paginate(20);

            return $this->handleResponse(ResourcesNotification::collection($notifications), __('notifications.find_all_notifications_success'), $notifications->lastPage());

        } else {
            $notifications = Notification::whereNotIn('type_id', $with_sent_reactions_notification_type_ids)
                                            ->where('to_user_id', $user->id)->orderByDesc('created_at')->paginate(20);

            return $this->handleResponse(ResourcesNotification::collection($notifications), __('notifications.find_all_notifications_success'), $notifications->lastPage());
        }
    }

    /**
     * Change notification status.
     *
     * @param  int $id
     * @param  string $status_id
     * @return \Illuminate\Http\Response
     */
    public function switchStatus($id, $status_alias)
    {
        $notification = Notification::find($id);

        if (is_null($notification)) {
            return $this->handleError(__('notifications.find_notification_404'));
        }

        $status = Status::where('alias', $status_alias)->first();

        if (is_null($status)) {
            return $this->handleError(__('notifications.find_status_404'));
        }

        // update "status_id" column
        $notification->update([
            'status_id' => $status->id,
            'updated_at' => now()
        ]);

        return $this->handleResponse(new ResourcesNotification($notification), __('notifications.update_notification_success'));
    }

    /**
     * Mark all notifications status as read.
     *
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function markAllRead($user_id)
    {
        $status_read = Status::where('alias', 'notification_read')->first();
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $notifications = Notification::where('to_user_id', $user->id)->get();

        // update "status_id" column for all user notifications
        foreach ($notifications as $notification):
            $notification->update([
                'status_id' => $status_read->id,
                'updated_at' => now()
            ]);
        endforeach;

        return $this->handleResponse(ResourcesNotification::collection($notifications), __('notifications.find_all_notifications_success'));
    }
}
