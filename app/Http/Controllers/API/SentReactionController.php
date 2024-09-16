<?php

namespace App\Http\Controllers\API;

use App\Models\BlockedUser;
use App\Models\Group;
use App\Models\History;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\ReactionReason;
use App\Models\SentReaction;
use App\Models\Status;
use App\Models\Subscription;
use App\Models\Type;
use App\Models\User;
use App\Http\Resources\SentReaction as ResourcesSentReaction;
use Illuminate\Http\Request;

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
        $sent_reactions = SentReaction::orderByDesc('updated_at')->get();

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
        $member_status_group = Group::where('group_name->fr', 'Etat du membre')->first();
        $blocked_member_status_group = Group::where('group_name->fr', 'Etat du membre bloqué')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $reaction_on_member_or_post_group = Group::where('group_name->fr', 'Réaction sur membre ou post')->first();
        // Statuses
        $blocked_member_status = Status::where([['status_name->fr', 'Bloqué'], ['group_id', $member_status_group->id]])->first();
        $in_progress_blocking_status = Status::where([['status_name->fr', 'Blocage en cours'], ['group_id', $blocked_member_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $imminent_account_blocking_type = Type::where([['type_name->fr', 'Blocage de compte imminent'], ['group_id', $notification_type_group->id]])->first();
        $blocked_account_type = Type::where([['type_name->fr', 'Compte bloqué'], ['group_id', $notification_type_group->id]])->first();
        $reaction_type = Type::where([['type_name->fr', 'Réaction'], ['group_id', $notification_type_group->id]])->first();
        $connection_suggestion_type = Type::where([['type_name->fr', 'Suggestion de connexion'], ['group_id', $notification_type_group->id]])->first();
        // Reactions
        $reported_reaction = Reaction::where([['reaction_name->fr', 'Signalé'], ['group_id', $reaction_on_member_or_post_group->id]])->first();
        // Reaction reason
        $other_reaction_reason = ReactionReason::where('reaction_name->fr', 'Autre motif')->first();
        // Get inputs
        $inputs = [
            'reaction_description' => $request->reaction_description,
            'to_user_id' => $request->to_user_id,
            'to_post_id' => $request->to_post_id,
            'to_notification_type_id' => $request->to_notification_type_id,
            'reaction_id' => $request->reaction_id,
            'reaction_reason_id' => isset($request->reaction_reason_id) ? $request->reaction_reason_id : $other_reaction_reason->id,
            'user_id' => $request->user_id
        ];

        // Validate required fields
        if (!is_numeric($inputs['user_id']) OR trim($inputs['user_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['user_id'], __('validation.required', ['field_name' => __('miscellaneous.choose_user')]), 400);
        }

        if (trim($inputs['to_user_id']) == null AND trim($inputs['to_post_id']) == null AND trim($inputs['to_notification_type_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['to_user_id'], __('validation.custom.owner.required'), 400);
        }

        if (!is_numeric($inputs['reaction_id']) OR trim($inputs['reaction_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['reaction_id'], __('miscellaneous.admin.group.reaction.add'), 400);
        }

        if (!is_numeric($inputs['reaction_reason_id']) OR trim($inputs['reaction_reason_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['reaction_reason_id'], __('miscellaneous.admin.miscellaneous.reason.add'), 400);
        }

        $from_current_user = User::find($inputs['user_id']);

        if (is_null($from_current_user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $sent_reaction = SentReaction::create($inputs);
        $reaction = Reaction::find($sent_reaction->reaction_id);

        // If the reaction is "Signalé", check if we can block the member
        if ($inputs['reaction_id'] == $reported_reaction->id) {
            // If the reaction is on member
            if ($inputs['to_user_id'] != null) {
                $to_current_user = User::find($inputs['to_user_id']);

                if (is_null($from_current_user)) {
                    return $this->handleError(__('notifications.find_addressee_404'));
                }

                // Count the number of people who reported to know if we should send a warning to the member
                $count_reaction = SentReaction::where(['to_user_id', $to_current_user->id], ['reaction_id', $reported_reaction->id])->count();
                $reaction_reason = ReactionReason::find($inputs['reaction_reason_id']);

                if (!empty($reaction_reason->report_count)) {
                    if ($count_reaction == $reaction_reason->report_count) {
                        BlockedUser::create([
                            'user_id' => $to_current_user->id,
                            'reaction_reason_id' => $inputs['reaction_reason_id'],
                            'status_id' => $in_progress_blocking_status->id,
                        ]);

                        $to_current_user->update([
                            'status_id' => $blocked_member_status->id,
                            'updated_at' => now()
                        ]);

                        Notification::create([
                            'days_before_blocking' => $count_reaction,
                            'type_id' => $imminent_account_blocking_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $from_current_user->id,
                            'to_user_id' => $to_current_user->id,
                            'reaction_id' => $reaction->id
                        ]);
                    }

                    if ($count_reaction >= ($reaction_reason->report_count - 5) AND $count_reaction < $reaction_reason->report_count) {
                        Notification::create([
                            'days_before_blocking' => $count_reaction,
                            'type_id' => $imminent_account_blocking_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $from_current_user->id,
                            'to_user_id' => $to_current_user->id,
                            'reaction_id' => $reaction->id
                        ]);
                    }
                }
            }

            // If the reaction is on post
            if ($inputs['to_post_id'] != null) {
                $to_post = Post::find($inputs['to_post_id']);
                $to_current_user = User::find($to_post->user_id);

                // Count the number of people who reported to know if we should send a warning to the member
                $count_reaction = SentReaction::where(['to_post_id', $to_post->id], ['reaction_id', $reported_reaction->id])->count();
                $reaction_reason = ReactionReason::find($inputs['reaction_reason_id']);

                if (!empty($reaction_reason->report_count)) {
                    if ($count_reaction == $reaction_reason->report_count) {
                        BlockedUser::create([
                            'user_id' => $to_current_user->id,
                            'reaction_reason_id' => $inputs['reaction_reason_id'],
                            'status_id' => $in_progress_blocking_status->id,
                        ]);

                        $to_current_user->update([
                            'status_id' => $blocked_member_status->id,
                            'updated_at' => now()
                        ]);

                        Notification::create([
                            'days_before_blocking' => $count_reaction,
                            'type_id' => $blocked_account_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $from_current_user->id,
                            'to_user_id' => $to_current_user->id,
                            'reaction_id' => $reaction->id
                        ]);
                    }

                    if ($count_reaction >= ($reaction_reason->report_count - 5) AND $count_reaction < $reaction_reason->report_count) {
                        Notification::create([
                            'days_before_blocking' => $count_reaction,
                            'type_id' => $imminent_account_blocking_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $from_current_user->id,
                            'to_user_id' => $to_current_user->id,
                            'reaction_id' => $reaction->id
                        ]);
                    }
                }
            }

        } else {
            if ($inputs['to_user_id'] != null) {
                $to_current_user = User::find($inputs['to_user_id']);

                Notification::create([
                    'type_id' => $reaction_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $from_current_user->id,
                    'to_user_id' => $to_current_user->id,
                    'reaction_id' => $reaction->id
                ]);
            }

            if ($inputs['to_post_id'] != null) {
                $to_post = Post::find($inputs['to_post_id']);
                $to_current_user = User::find($to_post->user_id);
                $subscription = Subscription::where([['user_id', $to_post->user_id], ['subscriber_id', $inputs['user_id']]])
                                                ->orWhere([['user_id', $inputs['user_id']], ['subscriber_id', $to_post->user_id]])->first();

                Notification::create([
                    'type_id' => $reaction_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $from_current_user->id,
                    'to_user_id' => $to_current_user->id,
                    'reaction_id' => $reaction->id
                ]);

                if (is_null($subscription)) {
                    Notification::create([
                        'type_id' => $connection_suggestion_type->id,
                        'status_id' => $unread_notification_status->id,
                        'from_user_id' => $to_current_user->id,
                        'to_user_id' => $inputs['user_id']
                    ]);
                }
            }
        }

        $notification = Notification::where('from_user_id', $from_current_user->id)->whereNotNull('reaction_id')->first();

        History::create([
            'type_id' => $activities_history_type->id,
            'status_id' => $unread_history_status->id,
            'from_user_id' => $from_current_user->id,
            'reaction_id' => $reaction->id,
            'for_notification_id' => $notification->id
        ]);

        return $this->handleResponse(new ResourcesSentReaction($sent_reaction), __('notifications.create_sent_reaction_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sent_reaction = SentReaction::find($id);

        if (is_null($sent_reaction)) {
            return $this->handleError(__('notifications.find_sent_reaction_404'));
        }

        return $this->handleResponse(new ResourcesSentReaction($sent_reaction), __('notifications.find_sent_reaction_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SentReaction  $sent_reaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SentReaction $sent_reaction)
    {
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

        if ($inputs['reaction_description'] != null) {
            $sent_reaction->update([
                'reaction_description' => $inputs['reaction_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['to_user_id'] != null) {
            $sent_reaction->update([
                'to_user_id' => $inputs['to_user_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['to_post_id'] != null) {
            $sent_reaction->update([
                'to_post_id' => $inputs['to_post_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['to_notification_type_id'] != null) {
            $sent_reaction->update([
                'to_notification_type_id' => $inputs['to_notification_type_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['reaction_id'] != null) {
            $sent_reaction->update([
                'reaction_id' => $inputs['reaction_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['reaction_reason_id'] != null) {
            $sent_reaction->update([
                'reaction_reason_id' => $inputs['reaction_reason_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $sent_reaction->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesSentReaction($sent_reaction), __('notifications.update_sent_reaction_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SentReaction  $sent_reaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(SentReaction $sent_reaction)
    {
        $sent_reaction->delete();

        $sent_reactions = SentReaction::all();

        return $this->handleResponse(ResourcesSentReaction::collection($sent_reactions), __('notifications.delete_sent_reaction_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Select all sent reactions by entity.
     *
     * @param  string $entity
     * @param  int $entity_id
     * @return \Illuminate\Http\Response
     */
    public function selectByEntity($entity, $entity_id)
    {
        if ($entity == 'user') {
            $user = User::find($entity_id);

            if (is_null($user)) {
                return $this->handleError(__('notifications.find_user_404'));
            }

            $sent_reactions = SentReaction::where('to_user_id', $user->id)->orderByDesc('created_at')->get();

            return $this->handleResponse(ResourcesSentReaction::collection($sent_reactions), __('notifications.find_all_sent_reactions_success'));
        }

        if ($entity == 'post') {
            $post = Post::find($entity_id);

            if (is_null($post)) {
                return $this->handleError(__('notifications.find_post_404'));
            }

            $sent_reactions = SentReaction::where('to_post_id', $post->id)->orderByDesc('created_at')->get();

            return $this->handleResponse(ResourcesSentReaction::collection($sent_reactions), __('notifications.find_all_sent_reactions_success'));
        }

        if ($entity == 'notification_type') {
            $type = Type::find($entity_id);

            if (is_null($type)) {
                return $this->handleError(__('notifications.find_type_404'));
            }

            $sent_reactions = SentReaction::where('to_notification_type_id', $type->id)->orderByDesc('created_at')->get();

            return $this->handleResponse(ResourcesSentReaction::collection($sent_reactions), __('notifications.find_all_sent_reactions_success'));
        }
    }

    /**
     * Select all sent reactions by entity and reaction.
     *
     * @param  string $entity
     * @param  int $entity_id
     * @param  int $reaction_id
     * @return \Illuminate\Http\Response
     */
    public function selectByEntityReaction($entity, $entity_id, $reaction_id)
    {
        $reaction = Reaction::find($reaction_id);

        if (is_null($reaction)) {
            return $this->handleError(__('notifications.find_reaction_404'));
        }

        if ($entity == 'user') {
            $user = User::find($entity_id);

            if (is_null($user)) {
                return $this->handleError(__('notifications.find_user_404'));
            }

            $sent_reactions = SentReaction::where([['to_user_id', $user->id], ['reaction_id', $reaction->id]])->orderByDesc('created_at')->get();

            return $this->handleResponse(ResourcesSentReaction::collection($sent_reactions), __('notifications.find_all_sent_reactions_success'));
        }

        if ($entity == 'post') {
            $post = Post::find($entity_id);

            if (is_null($post)) {
                return $this->handleError(__('notifications.find_post_404'));
            }

            $sent_reactions = SentReaction::where([['to_post_id', $post->id], ['reaction_id', $reaction->id]])->orderByDesc('created_at')->get();

            return $this->handleResponse(ResourcesSentReaction::collection($sent_reactions), __('notifications.find_all_sent_reactions_success'));
        }

        if ($entity == 'notification_type') {
            $type = Type::find($entity_id);

            if (is_null($type)) {
                return $this->handleError(__('notifications.find_type_404'));
            }

            $sent_reactions = SentReaction::where([['to_notification_type_id', $type->id], ['reaction_id', $reaction->id]])->orderByDesc('created_at')->get();

            return $this->handleResponse(ResourcesSentReaction::collection($sent_reactions), __('notifications.find_all_sent_reactions_success'));
        }
    }
}
