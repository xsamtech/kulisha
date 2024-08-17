<?php

namespace App\Http\Controllers\API;

use App\Models\SentReaction;
use Illuminate\Http\Request;
use App\Http\Resources\SentReaction as ResourcesSentReaction;
use App\Models\Group;
use App\Models\Reaction;
use App\Models\Status;
use App\Models\Type;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class SentReactionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sent_reactions = SentReaction::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesSentReaction::collection($sent_reactions), __('notifications.find_all_sent_reactions_success'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Groups
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $reaction_on_member_or_post_group = Group::where('group_name->fr', 'Réaction sur membre ou post')->first();
        $reaction_on_post_group = Group::where('group_name->fr', 'Réaction sur post')->first();
        $reaction_on_comment_group = Group::where('group_name->fr', 'Réaction sur commentaire')->first();
        // Statuses
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $reaction_type = Type::where([['type_name->fr', 'Réaction'], ['group_id', $notification_type_group->id]])->first();
        // Reactions
        $reported_reaction = Reaction::where([['reaction_name->fr', 'Signalé'], ['group_id', $reaction_on_member_or_post_group->id]])->first();
        $bravo_reaction = Reaction::where([['reaction_name->fr', 'Bravo'], ['group_id', $reaction_on_post_group->id]])->first();
        $i_like_reaction = Reaction::where([['reaction_name->fr', 'J’aime'], ['group_id', $reaction_on_post_group->id]])->first();
        $i_support_reaction = Reaction::where([['reaction_name->fr', 'Je soutiens'], ['group_id', $reaction_on_post_group->id]])->first();
        $interesting_reaction = Reaction::where([['reaction_name->fr', 'Intéressant'], ['group_id', $reaction_on_post_group->id]])->first();
        $i_like_reaction = Reaction::where([['reaction_name->fr', 'J’aime'], ['group_id', $reaction_on_comment_group->id]])->first();
        // Get inputs
        $inputs = [
            'reaction_description' => $request->reaction_description,
            'to_user_id' => $request->to_user_id,
            'to_post_id' => $request->to_post_id,
            'to_notification_type_id' => $request->to_notification_type_id,
            'reaction_id' => $request->reaction_id,
            'reaction_reason_id' => $request->reaction_reason_id,
            'user_id' => $request->user_id
        ];

        // Validate required fields
        if (trim($inputs['user_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['user_id'], __('validation.required', ['field_name' => __('miscellaneous.choose_user')]), 400);
        }

        if (trim($inputs['to_user_id']) == null AND trim($inputs['to_post_id']) == null AND trim($inputs['to_notification_type_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['to_user_id'], __('validation.custom.owner.required'), 400);
        }

        if ($inputs['user_id']) {
            # code...
        } else {
            # code...
        }

        $sent_reaction = SentReaction::create($inputs);

        return $this->handleResponse(new ResourcesSentReaction($sent_reaction), __('notifications.create_sent_reaction_success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(SentReaction $sent_reaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SentReaction $sent_reaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SentReaction $sent_reaction)
    {
        //
    }
}
