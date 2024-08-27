<?php

namespace App\Http\Controllers\API;

use App\Models\BlockedUser;
use Illuminate\Http\Request;
use App\Http\Resources\BlockedUser as ResourcesBlockedUser;

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
}
