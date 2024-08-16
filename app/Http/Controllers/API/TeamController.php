<?php

namespace App\Http\Controllers\API;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Resources\Team as ResourcesTeam;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class TeamController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::all();

        return $this->handleResponse(ResourcesTeam::collection($teams), __('notifications.find_all_teams_success'));
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
            'name' => $request->name,
            'personal_team' => $request->personal_team,
            'user_id' => $request->user_id
        ];

        if (!is_numeric($inputs['user_id']) OR trim($inputs['user_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['user_id'], __('auth.username'), 400);
        }

        $team = Team::create($inputs);

        return $this->handleResponse(new ResourcesTeam($team), __('notifications.create_team_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $team = Team::find($id);

        if (is_null($team)) {
            return $this->handleError(__('notifications.find_team_404'));
        }

        return $this->handleResponse(new ResourcesTeam($team), __('notifications.find_team_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        // Get inputs
        $inputs = [
            'name' => $request->name,
            'personal_team' => $request->personal_team,
            'user_id' => $request->user_id
        ];

        if ($inputs['name'] != null) {
            $team->update([
                'name' => $inputs['name'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['personal_team'] != null) {
            $team->update([
                'personal_team' => $inputs['personal_team'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $team->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesTeam($team), __('notifications.update_team_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $team->delete();

        $teams = Team::all();

        return $this->handleResponse(ResourcesTeam::collection($teams), __('notifications.delete_team_success'));
    }
}
