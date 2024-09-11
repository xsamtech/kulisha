<?php

namespace App\Http\Controllers\API;

use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use App\Http\Resources\TeamInvitation as ResourcesTeamInvitation;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class TeamInvitationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams_invitations = TeamInvitation::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesTeamInvitation::collection($teams_invitations), __('notifications.find_all_teams_invitations_success'));
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
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $request->role,
            'team_id' => $request->team_id
        ];

        // Validate required fields
        if (trim($inputs['email']) == null AND trim($inputs['phone']) == null) {
            return $this->handleError(__('validation.email_or_phone.required'), 400);
        }

        if (!is_numeric($inputs['team_id']) OR trim($inputs['team_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['team_id'], __('notifications.find_team_404'), 400);
        }

        $team_invitation = TeamInvitation::create($inputs);

        return $this->handleResponse(new ResourcesTeamInvitation($team_invitation), __('notifications.create_team_invitation_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $team_invitation = TeamInvitation::find($id);

        if (is_null($team_invitation)) {
            return $this->handleError(__('notifications.find_team_invitation_404'));
        }

        return $this->handleResponse(new ResourcesTeamInvitation($team_invitation), __('notifications.find_team_invitation_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TeamInvitation  $team_invitation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeamInvitation $team_invitation)
    {
        // Get inputs
        $inputs = [
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $request->role,
            'team_id' => $request->team_id
        ];

        if ($inputs['phone'] != null) {
            $team_invitation->update([
                'phone' => $inputs['phone'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['email'] != null) {
            $team_invitation->update([
                'email' => $inputs['email'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['role'] != null) {
            $team_invitation->update([
                'role' => $inputs['role'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['team_id'] != null) {
            $team_invitation->update([
                'team_id' => $inputs['team_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesTeamInvitation($team_invitation), __('notifications.update_team_invitation_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TeamInvitation  $team_invitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeamInvitation $team_invitation)
    {
        $team_invitation->delete();

        $teams_invitations = TeamInvitation::all();

        return $this->handleResponse(ResourcesTeamInvitation::collection($teams_invitations), __('notifications.delete_team_invitation_success'));
    }
}
