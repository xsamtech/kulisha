<?php

namespace App\Http\Controllers\API;

use App\Models\Community;
use App\Models\File;
use App\Models\Group;
use App\Models\History;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Status;
use App\Models\Team;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Resources\Message as ResourcesMessage;
use App\Http\Resources\User as ResourcesUser;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class MessageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = Message::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'));
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
        $message_status_group = Group::where('group_name->fr', 'Etat du message')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $message_type_group = Group::where('group_name->fr', 'Type de message')->first();
        // Statuses
        $unread_message_status = Status::where([['status_name->fr', 'Non lu'], ['group_id', $message_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $new_chat_type = Type::where([['type_name->fr', 'Nouvelle discussion'], ['group_id', $notification_type_group->id]])->first();
        $message_answer_type = Type::where([['type_name->fr', 'Réponse au message'], ['group_id', $notification_type_group->id]])->first();
        $message_in_community_type = Type::where([['type_name->fr', 'Message dans la communauté'], ['group_id', $notification_type_group->id]])->first();
        $chat_type = Type::where([['type_name->fr', 'Discussion'], ['group_id', $message_type_group->id]])->first();
        // Get inputs
        $inputs = [
            'message_content' => $request->message_content,
            'answered_for' => $request->answered_for,
            'type_id' => isset($request->type_id) ? $request->type_id : $chat_type->id,
            'status_id' => !empty($request->addressee_community_id) OR !empty($request->addressee_team_id) ? null : (isset($request->status_id) ? $request->status_id : $unread_message_status->id),
            'user_id' => $request->user_id,
            'addressee_user_id' => $request->addressee_user_id,
            'addressee_community_id' => $request->addressee_community_id,
            'addressee_team_id' => $request->addressee_team_id
        ];

        // Validate required fields
        if ($inputs['message_content'] == null OR $inputs['message_content'] == ' ') {
            return $this->handleError($inputs['message_content'], __('validation.required'), 400);
        }

        if ($inputs['user_id'] == null OR $inputs['user_id'] == ' ') {
            return $this->handleError($inputs['user_id'], __('validation.required'), 400);
        }

        $message_sender = User::find($inputs['user_id']);

        if (is_null($message_sender)) {
            return $this->handleError(__('notifications.find_sender_404'));
        }

        $message = Message::create($inputs);

        // If the message is sent to a user
        if ($inputs['addressee_user_id'] != null) {
            $addressee_user = User::find($inputs['addressee_user_id']);

            if (is_null($addressee_user)) {
                return $this->handleError(__('notifications.find_addressee_404'));
            }

            // Check if it's the first discussion before send
            $chat_with_addressee = Message::where([['user_id', $message_sender->id], ['addressee_user_id', $addressee_user->id]])->orWhere([['user_id', $addressee_user->id], ['addressee_user_id', $message_sender->id]])->get();

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            if (is_null($chat_with_addressee)) {
                $notification = Notification::create([
                    'type_id' => $new_chat_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $message_sender->id,
                    'to_user_id' => $addressee_user->id,
                    'message_id' => $message->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status->id,
                    'from_user_id' => $message_sender->id,
                    'for_notification_id' => $notification->id
                ]);
            }
        }

        // If the message is sent to the community, notify to all community members
        if ($inputs['addressee_community_id'] != null) {
            $message = Message::create($inputs);

            $addressee_community = Community::find($inputs['addressee_community_id']);
            $community_users = $addressee_community->users;
            $users_ids = $community_users->pluck('id')->toArray();

            $message->users()->syncWithPivotValues($users_ids, ['status_id' => $unread_message_status->id]);

            if (is_null($addressee_community)) {
                return $this->handleError(__('notifications.find_community_404'));
            }

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            // If the message is not an answer to another message, send notification
            if ($message->answered_for == null) {
                foreach ($addressee_community->users as $user):
                    Notification::create([
                        'type_id' => $message_in_community_type->id,
                        'status_id' => $unread_notification_status->id,
                        'from_user_id' => $message_sender->id,
                        'to_user_id' => $user->id,
                        'message_id' => $message->id
                    ]);
                endforeach;

                $notification = Notification::where([['type_id', $message_in_community_type->id], ['message_id', $message->id]])->first();

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status->id,
                    'from_user_id' => $message_sender->id,
                    'for_notification_id' => $notification->id
                ]);
            }
        }

        // If the message is an answer
        if ($inputs['answered_for'] != null) {
            $originating_message = Message::find($inputs['answered_for']);

            if (is_null($originating_message)) {
                return $this->handleError(__('notifications.find_originating_message_404'));
            }

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            Notification::create([
                'type_id' => $message_answer_type->id,
                'status_id' => $unread_notification_status->id,
                'from_user_id' => $message_sender->id,
                'to_user_id' => $originating_message->user_id,
                'message_id' => $message->id
            ]);
        }

        return $this->handleResponse(new ResourcesMessage($message), __('notifications.create_message_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Group
        $message_status_group = Group::where('group_name->fr', 'Etat du message')->first();
        // Status
        $read_message_status = Status::where([['status_name->fr', 'Lu'], ['group_id', $message_status_group->id]])->first();
        // Request
        $message = Message::find($id);

        if (is_null($message)) {
            return $this->handleError(__('notifications.find_message_404'));
        }

        // If the message is sent to the community, identify the user who saw it
        if ($message->addressee_community_id != null OR $message->addressee_team_id != null) {
            if ($request->hasHeader('X-user-id')) {
                $user = User::find($request->header('X-user-id'));

                if (!is_null($user)) {
                    $message->users()->updateExistingPivot($user->id, ['status_id' => $read_message_status->id]);
                }
            }
        }

        return $this->handleResponse(new ResourcesMessage($message), __('notifications.find_message_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        // Get inputs
        $inputs = [
            'message_content' => $request->message_content,
            'answered_for' => $request->answered_for,
            'type_id' => $request->type_id,
            'status_id' => $request->status_id,
            'user_id' => $request->user_id,
            'addressee_user_id' => $request->addressee_user_id,
            'addressee_community_id' => $request->addressee_community_id,
            'addressee_team_id' => $request->addressee_team_id
        ];

        if ($inputs['message_content'] != null) {
            $message->update([
                'message_content' => $inputs['message_content'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['answered_for'] != null) {
            $message->update([
                'answered_for' => $inputs['answered_for'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['type_id'] != null) {
            $message->update([
                'type_id' => $inputs['type_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['status_id'] != null) {
            $message->update([
                'status_id' => $inputs['status_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $message->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['addressee_user_id'] != null) {
            $message->update([
                'addressee_user_id' => $inputs['addressee_user_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['addressee_community_id'] != null) {
            $message->update([
                'addressee_community_id' => $inputs['addressee_community_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['addressee_team_id'] != null) {
            $message->update([
                'addressee_team_id' => $inputs['addressee_team_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesMessage($message), __('notifications.update_message_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        $message->delete();

        $messages = Message::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.delete_message_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a message in chat.
     *
     * @param  string $locale
     * @param  string $type_name
     * @param  string $data
     * @param  int $sender_id
     * @param  int $addressee_id
     * @return \Illuminate\Http\Response
     */
    public function searchInChat($locale, $type_name, $data, $sender_id, $addressee_id)
    {
        // Groups
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Types
        $search_history_type = Type::where([['type_name->fr', 'Historique des recherches'], ['group_id', $history_type_group->id]])->first();
        // Requests
        $message_type = Type::where('type_name->' . $locale, $type_name)->first();

        if (is_null($message_type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        $sender = User::find($sender_id);

        if (is_null($sender)) {
            return $this->handleError(__('notifications.find_sender_404'));
        }

        $addressee = User::find($addressee_id);

        if (is_null($sender)) {
            return $this->handleError(__('notifications.find_addressee_404'));
        }

        $messages = Message::where([['message_content', 'LIKE', '%' . $data . '%'], ['type_id', $message_type->id], ['user_id', $sender->id], ['addressee_user_id', $addressee->id]])->orWhere([['message_content', 'LIKE', '%' . $data . '%'], ['type_id', $message_type->id], ['user_id', $addressee->id], ['addressee_user_id', $sender->id]])->orderByDesc('created_at')->paginate(30);
        $count_messages = Message::where([['message_content', 'LIKE', '%' . $data . '%'], ['type_id', $message_type->id], ['user_id', $sender->id], ['addressee_user_id', $addressee->id]])->orWhere([['message_content', 'LIKE', '%' . $data . '%'], ['type_id', $message_type->id], ['user_id', $addressee->id], ['addressee_user_id', $sender->id]])->count();

        if (is_null($messages)) {
            return $this->handleResponse(null, __('miscellaneous.empty_list'));
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        History::create([
            'search_content' => $data,
            'type_id' => $search_history_type->id,
            'from_user_id' => $sender->id,
        ]);

        return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
    }

    /**
     * Search a message in group (community or team).
     *
     * @param  string $entity
     * @param  int $entity_id
     * @param  int $member_id
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function searchInGroup($entity, $entity_id, $member_id, $data)
    {
        // Groups
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Types
        $search_history_type = Type::where([['type_name->fr', 'Historique des recherches'], ['group_id', $history_type_group->id]])->first();
        // Requests
        $member = User::find($member_id);

        if (is_null($member)) {
            return $this->handleError(__('notifications.find_member_404'));
        }

        if ($entity == 'community') {
            $community = Community::find($entity_id);

            if (is_null($community)) {
                return $this->handleError(__('notifications.find_community_404'));
            }

            $messages = Message::where([['message_content', 'LIKE', '%' . $data . '%'], ['addressee_community_id', $community->id]])->orderByDesc('created_at')->paginate(30);
            $count_messages = Message::where([['message_content', 'LIKE', '%' . $data . '%'], ['addressee_community_id', $community->id]])->count();

            if (is_null($messages)) {
                return $this->handleResponse(null, __('miscellaneous.empty_list'));
            }

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            History::create([
                'search_content' => $data,
                'type_id' => $search_history_type->id,
                'from_user_id' => $member->id,
            ]);

            return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
        }

        if ($entity == 'team') {
            $team = Team::find($entity_id);

            if (is_null($team)) {
                return $this->handleError(__('notifications.find_team_404'));
            }

            $messages = Message::where([['message_content', 'LIKE', '%' . $data . '%'], ['addressee_team_id', $team->id]])->orderByDesc('created_at')->paginate(30);
            $count_messages = Message::where([['message_content', 'LIKE', '%' . $data . '%'], ['addressee_team_id', $team->id]])->count();

            if (is_null($messages)) {
                return $this->handleResponse(null, __('miscellaneous.empty_list'));
            }

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            History::create([
                'search_content' => $data,
                'type_id' => $search_history_type->id,
                'from_user_id' => $member->id,
            ]);

            return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
        }
    }

    /**
     * GET: Display user chat with another.
     *
     * @param  string $locale
     * @param  string $type_name
     * @param  int $sender_id
     * @param  int $addressee_user_id
     * @return \Illuminate\Http\Response
     */
    public function chatWithUser($locale, $type_name, $sender_id, $addressee_user_id)
    {
        $type = Type::where('type_name->' . $locale, $type_name)->first();

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        $sender = User::find($sender_id);

        if (is_null($sender)) {
            return $this->handleError(__('notifications.find_sender_404'));
        }

        $addressee = User::find($addressee_user_id);

        if (is_null($addressee)) {
            return $this->handleError(__('notifications.find_addressee_404'));
        }

        $messages = Message::where([['type_id', $type->id], ['user_id', $sender->id], ['addressee_user_id', $addressee->id]])->orWhere([['type_id', $type->id], ['user_id', $addressee->id], ['addressee_user_id', $sender->id]])->orderByDesc('created_at')->paginate(30);
        $count_messages = Message::where([['type_id', $type->id], ['user_id', $sender->id], ['addressee_user_id', $addressee->id]])->orWhere([['type_id', $type->id], ['user_id', $addressee->id], ['addressee_user_id', $sender->id]])->count();

        return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
    }

    /**
     * GET: Display all group (community or team) messages.
     *
     * @param  string $entity
     * @param  int $entity_id
     * @return \Illuminate\Http\Response
     */
    public function chatWithGroup($entity, $entity_id)
    {
        if ($entity == 'community') {
            $community = Community::find($entity_id);

            if (is_null($community)) {
                return $this->handleError(__('notifications.find_community_404'));
            }

            $messages = Message::where('addressee_community_id', $community->id)->orderByDesc('created_at')->paginate(30);
            $count_messages = Message::where('addressee_community_id', $community->id)->count();

            return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
        }

        if ($entity == 'team') {
            $team = Team::find($entity_id);

            if (is_null($team)) {
                return $this->handleError(__('notifications.find_team_404'));
            }

            $messages = Message::where('addressee_team_id', $team->id)->orderByDesc('created_at')->paginate(30);
            $count_messages = Message::where('addressee_team_id', $team->id)->count();

            return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
        }
    }

    /**
     * GET: Display all community/team members with specific status.
     *
     * @param  string $status_alias
     * @param  int $message_id
     * @return \Illuminate\Http\Response
     */
    public function membersWithMessageStatus($status_alias, $message_id)
    {
        // Group
        $group = Group::where('group_name->fr', 'Etat du message')->first();
        // Status
        $status = Status::where([['alias', $status_alias], ['group_id', $group->id]])->first();

        if (is_null($status)) {
            return $this->handleError(__('notifications.find_status_404'));
        }

        $message = Message::find($message_id);

        if (is_null($message)) {
            return $this->handleError(__('notifications.find_message_404'));
        }

        $users = $message->users()->wherePivot('status_id', $status->id)->orderByDesc('created_at')->paginate(30);
        $count_users = $message->users()->wherePivot('status_id', $status->id)->count();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'), $users->lastPage(), $count_users);
    }

    /**
     * Delete message for a specific user.
     *
     * @param  int $user_id
     * @param  int $message_id
     * @param  string $entity
     * @return \Illuminate\Http\Response
     */
    public function deleteForMyself($user_id, $message_id, $entity)
    {
        $message_status_group = Group::where('group_name->fr', 'Etat du message')->first();
        $deleted_message_status = Status::where([['alias', 'message_deleted'], ['group_id', $message_status_group->id]])->first();
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $message = Message::find($message_id);

        if (is_null($message)) {
            return $this->handleError(__('notifications.find_message_404'));
        }

        if ($entity == 'personal') {
            $message->update([
                'status_id' => $deleted_message_status->id,
                'updated_at' => now()
            ]);
        }

        if ($entity == 'group') {
            $message->users()->updateExistingPivot($user->id, ['status_id' => $deleted_message_status->id]);
        }

        return $this->handleResponse(new ResourcesMessage($message), __('notifications.find_message_success'));
    }

    /**
     * Delete message for everybody.
     *
     * @param  int $message_id
     * @return \Illuminate\Http\Response
     */
    public function deleteForEverybody($message_id)
    {
        $message = Message::find($message_id);

        if (is_null($message)) {
            return $this->handleError(__('notifications.find_message_404'));
        }

        $message->update([
            'message_content' => __('notifications.delete_message_success'),
            'updated_at' => now()
        ]);
    }

    /**
     * GET: Mark all received messages as read.
     *
     * @param  string $locale
     * @param  string $type_name
     * @param  int $sender_id
     * @param  int $addressee_user_id
     * @return \Illuminate\Http\Response
     */
    public function markAllReadUser($locale, $type_name, $sender_id, $addressee_user_id)
    {
        // Group
        $message_status_group = Group::where('group_name->fr', 'Etat du message')->first();
        // Status
        $read_message_status = Status::where([['status_name->fr', 'Lu'], ['group_id', $message_status_group->id]])->first();
        // Requests
        $type = Type::where('type_name->' . $locale, $type_name)->first();

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        $sender = User::find($sender_id);

        if (is_null($sender)) {
            return $this->handleError(__('notifications.find_sender_404'));
        }

        $addressee = User::find($addressee_user_id);

        if (is_null($addressee)) {
            return $this->handleError(__('notifications.find_addressee_404'));
        }

        $all_messages = Message::where([['type_id', $type->id], ['user_id', $sender->id], ['addressee_user_id', $addressee->id]])->get();
        $messages = Message::where([['type_id', $type->id], ['user_id', $sender->id], ['addressee_user_id', $addressee->id]])->orderByDesc('created_at')->paginate(30);
        $count_messages = Message::where([['type_id', $type->id], ['user_id', $sender->id], ['addressee_user_id', $addressee->id]])->count();

        foreach ($all_messages as $message) {
            $message->update([
                'status_id' => $read_message_status->id,
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
    }

    /**
     * GET: Mark all community/team messages as read.
     *
     * @param  int $user_id
     * @param  string $entity
     * @param  int $entity_id
     * @return \Illuminate\Http\Response
     */
    public function markAllReadGroup($user_id, $entity, $entity_id)
    {
        // Group
        $message_status_group = Group::where('group_name->fr', 'Etat du message')->first();
        // Status
        $read_message_status = Status::where([['status_name->fr', 'Lu'], ['group_id', $message_status_group->id]])->first();
        // Requests
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if ($entity == 'community') {
            $community = Community::find($entity_id);

            if (is_null($community)) {
                return $this->handleError(__('notifications.find_community_404'));
            }

            $all_messages = Message::where('addressee_community_id', $community->id)->get();
            $messages = Message::where('addressee_community_id', $community->id)->orderByDesc('created_at')->paginate(30);
            $count_messages = Message::where('addressee_community_id', $community->id)->count();

            foreach ($all_messages as $message) {
                $message->users()->updateExistingPivot($user->id, ['status_id' => $read_message_status->id]);
            }

            return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
        }

        if ($entity == 'team') {
            $team = Team::find($entity_id);

            if (is_null($team)) {
                return $this->handleError(__('notifications.find_team_404'));
            }

            $all_messages = Message::where('addressee_team_id', $team->id)->get();
            $messages = Message::where('addressee_team_id', $team->id)->orderByDesc('created_at')->paginate(30);
            $count_messages = Message::where('addressee_team_id', $team->id)->count();

            foreach ($all_messages as $message) {
                $message->users()->updateExistingPivot($user->id, ['status_id' => $read_message_status->id]);
            }

            return $this->handleResponse(ResourcesMessage::collection($messages), __('notifications.find_all_messages_success'), $messages->lastPage(), $count_messages);
        }
    }

    /**
     * Upload message files in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request, $id)
    {
        if (!isset($request->file_type_id)) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $request->file_type_id, __('validation.custom.type.required'), 400);
        }

        // Group
        $file_type_group = Group::where('group_name->fr', 'Type de fichier')->first();
        // File type
        $type = Type::where([['id', $request->file_type_id], ['group_id', $file_type_group->id]])->first();
        // Current message
        $message = Message::find($id);

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        if (is_null($message)) {
            return $this->handleError(__('notifications.find_message_404'));
        }

        if ($request->hasFile('file_url')) {
            if ($type->getTranslation('type_name', 'fr') == 'Document') {
                $file_url = 'documents/messages/' . $message->id . '/' . Str::random(50) . '.' . $request->file('file_url')->extension();

                // Upload file
                $dir_result = Storage::url(Storage::disk('public')->put($file_url, $request->file('file_url')));

                File::create([
                    'file_name' => trim($request->file_name) != null ? $request->file_name : null,
                    'file_url' => $dir_result,
                    'type_id' => $type->id,
                    'message_id' => $message->id
                ]);

                return $this->handleResponse(new ResourcesMessage($message), __('notifications.update_message_success'));
            }

            if ($type->getTranslation('type_name', 'fr') == 'Image') {
                $file_url = 'images/messages/' . $message->id . '/' . Str::random(50) . '.' . $request->file('file_url')->extension();

                // Upload file
                $dir_result = Storage::url(Storage::disk('public')->put($file_url, $request->file('file_url')));

                File::create([
                    'file_name' => trim($request->file_name) != null ? $request->file_name : null,
                    'file_url' => $dir_result,
                    'type_id' => $type->id,
                    'message_id' => $message->id
                ]);

                return $this->handleResponse(new ResourcesMessage($message), __('notifications.update_message_success'));
            }

            if ($type->getTranslation('type_name', 'fr') == 'Audio') {
                $file_url = 'audios/messages/' . $message->id . '/' . Str::random(50) . '.' . $request->file('file_url')->extension();

                // Upload file
                $dir_result = Storage::url(Storage::disk('public')->put($file_url, $request->file('file_url')));

                File::create([
                    'file_name' => trim($request->file_name) != null ? $request->file_name : null,
                    'file_url' => $dir_result,
                    'type_id' => $type->id,
                    'message_id' => $message->id
                ]);

                return $this->handleResponse(new ResourcesMessage($message), __('notifications.update_message_success'));
            }
        }
    }
}
