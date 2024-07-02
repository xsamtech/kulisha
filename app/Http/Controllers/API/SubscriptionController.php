<?php

namespace App\Http\Controllers\API;

use App\Models\Group;
use App\Models\History;
use App\Models\Subscription;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\Subscription as ResourcesSubscription;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class SubscriptionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriptions = Subscription::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesSubscription::collection($subscriptions), __('notifications.find_all_subscriptions_success'));
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
            'email' => $request->email,
            'phone' => $request->phone,
            'user_id' => $request->user_id,
            'subscriber_id' => $request->subscriber_id,
            'status_id' => $request->status_id
        ];

        $subscription = Subscription::create($inputs);

        return $this->handleResponse(new ResourcesSubscription($subscription), __('notifications.create_subscription_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subscription = Subscription::find($id);

        if (is_null($subscription)) {
            return $this->handleError(__('notifications.find_subscription_404'));
        }

        return $this->handleResponse(new ResourcesSubscription($subscription), __('notifications.find_subscription_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        // Get inputs
        $inputs = [
            'notify_post' => $request->notify_post,
            'notify_message' => $request->notify_message,
            'is_following' => $request->is_following,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_id' => $request->user_id,
            'subscriber_id' => $request->subscriber_id,
            'status_id' => $request->status_id
        ];

        if ($inputs['notify_post'] != null) {
            $subscription->update([
                'notify_post' => $inputs['notify_post'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['notify_message'] != null) {
            $subscription->update([
                'notify_message' => $inputs['notify_message'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['is_following'] != null) {
            $subscription->update([
                'is_following' => $inputs['is_following'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['email'] != null) {
            $subscription->update([
                'email' => $inputs['email'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['phone'] != null) {
            $subscription->update([
                'phone' => $inputs['phone'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $subscription->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['subscriber_id'] != null) {
            $subscription->update([
                'subscriber_id' => $inputs['subscriber_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['status_id'] != null) {
            $subscription->update([
                'status_id' => $inputs['status_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesSubscription($subscription), __('notifications.update_subscription_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        $subscriptions = Subscription::all();

        return $this->handleResponse(ResourcesSubscription::collection($subscriptions), __('notifications.delete_subscription_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Invite a contact by mail or phone
     *
     * @param  string $data
     * @param  int $visitor_id
     * @return \Illuminate\Http\Response
     */
    public function inviteContact($data, $visitor_id = null)
    {
        // Groups
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Types
        $invitation_history_type = Type::where([['type_name->fr', 'Historique des invitations'], ['group_id', $history_type_group->id]])->first();
        // Visitor
        $visitor = User::find($visitor_id);

        if (is_null($visitor)) {
            return $this->handleError(__('notifications.find_visitor_404'));
        }

        if (is_numeric($data)) {
            $subscription = Subscription::create([
                'phone' => $data,
                'user_id' => $visitor->id
            ]);

        } else {
            $subscription = Subscription::create([
                'email' => $data,
                'user_id' => $visitor->id
            ]);
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        History::create([
            'search_content' => $data,
            'type_id' => $invitation_history_type->id,
            'from_user_id' => $visitor->id
        ]);

        return $this->handleResponse(new ResourcesSubscription($subscription), __('notifications.create_subscription_success'));
    }
}
