<?php

namespace App\Http\Controllers\API;

use App\Models\BlockedUser;
use App\Models\Group;
use App\Models\Notification;
use App\Models\ReactionReason;
use App\Models\Status;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BlockedUser as ResourcesBlockedUser;
use Carbon\Carbon;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class BlockedUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blocked_users = BlockedUser::orderByDesc('created_at')->paginate(30);
        $count_blocked_users = BlockedUser::count();

        return $this->handleResponse(ResourcesBlockedUser::collection($blocked_users), __('notifications.find_all_blocked_users_success'), $blocked_users->lastPage(), $count_blocked_users);
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
            'user_id' => $request->user_id,
            'reaction_reason_id' => $request->reaction_reason_id,
            'status_id' => $request->status_id
        ];

        $validator = Validator::make($inputs, [
            'user_id' => ['required'],
            'reaction_reason_id' => ['required'],
            'status_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());       
        }

        $blocked_user = BlockedUser::create($inputs);

        return $this->handleResponse(new ResourcesBlockedUser($blocked_user), __('notifications.create_blocked_user_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $blocked_user = BlockedUser::find($id);

        if (is_null($blocked_user)) {
            return $this->handleError(__('notifications.find_blocked_user_404'));
        }

        return $this->handleResponse(new ResourcesBlockedUser($blocked_user), __('notifications.find_blocked_user_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlockedUser  $blocked_user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlockedUser $blocked_user)
    {
        // Get inputs
        $inputs = [
            'user_id' => $request->user_id,
            'reaction_reason_id' => $request->reaction_reason_id,
            'status_id' => $request->status_id
        ];

        if ($inputs['user_id'] != null) {
            $blocked_user->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['reaction_reason_id'] != null) {
            $blocked_user->update([
                'reaction_reason_id' => $inputs['reaction_reason_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['status_id'] != null) {
            $blocked_user->update([
                'status_id' => $inputs['status_id'],
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(new ResourcesBlockedUser($blocked_user), __('notifications.update_blocked_user_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlockedUser  $blocked_user
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlockedUser $blocked_user)
    {
        $blocked_user->delete();

        $blocked_users = BlockedUser::orderByDesc('created_at')->paginate(30);
        $count_blocked_users = BlockedUser::count();

        return $this->handleResponse(ResourcesBlockedUser::collection($blocked_users), __('notifications.delete_blocked_user_success'), $blocked_users->lastPage(), $count_blocked_users);
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Unlock a user.
     *
     * @param  int $user_id
     */
    public function unlockUser($user_id)
    {
        // Groups
        $member_status_group = Group::where('group_name->fr', 'Etat du membre')->first();
        $blocked_member_status_group = Group::where('group_name->fr', 'Etat du membre bloqué')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Statuses
        $activated_member_status = Status::where([['status_name->fr', 'Activé'], ['group_id', $member_status_group->id]])->first();
        $in_progress_blocking_status = Status::where([['status_name->fr', 'Blocage en cours'], ['group_id', $blocked_member_status_group->id]])->first();
        $finished_blocking_status = Status::where([['status_name->fr', 'Blocage terminé'], ['group_id', $blocked_member_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        // Types
        $unlocked_account_type = Type::where([['type_name->fr', 'Compte débloqué'], ['group_id', $notification_type_group->id]])->first();
        // Requests
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $blocked_user = BlockedUser::where([['user_id', $user->id], ['status_id', $in_progress_blocking_status->id]])->first();

        if (is_null($blocked_user)) {
            return $this->handleError(__('notifications.find_blocked_user_404'));
        }

        $reaction_reason = ReactionReason::find($blocked_user->reaction_reason_id);

        if (is_null($reaction_reason)) {
            return $this->handleError(__('notifications.find_reaction_reason_404'));
        }

        // Create two date instances
        $current_date = date('Y-m-d');
        $blocking_date = $blocked_user->created_at->format('Y-m-d');
        $current_date_instance = Carbon::parse($current_date);
        $blocking_date_instance = Carbon::parse($blocking_date);
        // Determine the difference between dates
        $diffInDays = $current_date_instance->diffInDays($blocking_date_instance);

        if ($diffInDays < $reaction_reason->number_of_days) {
            return $this->handleError(new ResourcesBlockedUser($blocked_user), __('notifications.sanction_period_not_exhausted'), 401);

        } else {
            $blocked_user->update([
                'status_id' => $finished_blocking_status->id,
                'updated_at' => now(),
            ]);

            $user->update([
                'status_id' => $activated_member_status->id,
                'updated_at' => now(),
            ]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            Notification::create([
                'type_id' => $unlocked_account_type->id,
                'status_id' => $unread_notification_status->id,
                'to_user_id' => $user->id
            ]);

            return $this->handleResponse(new ResourcesBlockedUser($blocked_user), __('notifications.update_blocked_user_success'));
        }
    }
}
