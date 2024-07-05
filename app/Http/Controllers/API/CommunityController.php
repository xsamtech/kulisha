<?php

namespace App\Http\Controllers\API;

use App\Models\Community;
use App\Models\Group;
use App\Models\History;
use App\Models\Notification;
use App\Models\Session;
use App\Models\Status;
use App\Models\Subscription;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Community as ResourcesCommunity;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class CommunityController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $communities = Community::orderByDesc('created_at')->paginate(12);
        $count_communities = Community::count();

        return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.find_all_communities_success'), $communities->lastPage(), $count_communities);
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
        $susbcription_status_group = Group::where('group_name->fr', 'Etat de la souscription')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $post_or_community_status_group = Group::where('group_name->fr', 'Etat du post ou de la communauté')->first();
        $access_type_group = Group::where('group_name->fr', 'Type d’accès')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Status
        $accepted_status = Status::where([['status_name->fr', 'Acceptée'], ['group_id', $susbcription_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        $operational_status = Status::where([['status_name->fr', 'Opérationnel'], ['group_id', $post_or_community_status_group->id]])->first();
        // Type
        $public_type = Type::where([['type_name->fr', 'Public'], ['group_id' => $access_type_group->id]])->first();
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $new_community_type = Type::where([['type_name->fr', 'Nouvelle communauté'], ['group_id', $notification_type_group->id]])->first();
        // Get inputs
        $inputs = [
            'community_name' => $request->community_name,
            'community_description' => $request->community_description,
            'type_id' => isset($request->type_id) ? $request->type_id : $public_type->id,
            'status_id' => isset($request->status_id) ? $request->status_id : $operational_status->id,
            'user_id' => $request->user_id
        ];

        if (trim($inputs['community_name']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['community_name'], __('miscellaneous.public.communities.data.community_name.error'), 400);
        }

        $community = Community::create($inputs);

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        // The community is public
        if ($community->type_id == $public_type->id) {
            // Find all subscribers of the community owner
            $subscribers = Subscription::where([['user_id', $community->user_id], ['status_id', $accepted_status->id]])->get();

            if ($subscribers != null) {
                foreach ($subscribers as $subscriber):
                    Notification::create([
                        'type_id' => $new_community_type->id,
                        'status_id' => $unread_notification_status->id,
                        'from_user_id' => $community->user_id,
                        'to_user_id' => $subscriber->id
                    ]);
                endforeach;
            }

            $notification = Notification::where([['type_id', $new_community_type->id], ['from_user_id', $community->user_id]])->first();

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status->id,
                'from_user_id' => $community->user_id,
                'for_notification_id' => is_null($notification) ? null : $notification->id
            ]);
        }

        return $this->handleResponse(new ResourcesCommunity($community), __('notifications.create_community_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Groups
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Status
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        // Types
        $searched_community_type = Type::where([['type_name->fr', 'Communauté recherchée'], ['group_id', $notification_type_group->id]])->first();
        $search_history_type = Type::where([['type_name->fr', 'Historique des recherches'], ['group_id', $history_type_group->id]])->first();
        $consultation_history_type = Type::where([['type_name->fr', 'Historique des consultations'], ['group_id', $history_type_group->id]])->first();
        // Request
        $community = Community::find($id);

        if (is_null($community)) {
            return $this->handleError(__('notifications.find_community_404'));
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        if ($request->has('origin')) {
            if ($request->input('origin') == 'search') {
                $notification = Notification::create([
                    'type_id' => $searched_community_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $request->has('visitor_id') ? $request->get('visitor_id') : null,
                    'to_user_id' => $community->user_id,
                    'community_id' => $community->id
                ]);

                History::create([
                    'search_content' => $community->community_name,
                    'type_id' => $search_history_type->id,
                    'from_user_id' => $request->has('visitor_id') ? $request->get('visitor_id') : null,
                    'notification_id' => $notification->id
                ]);
            }
        }

        if ($request->hasHeader('X-user-id') and $request->hasHeader('X-ip-address') or $request->hasHeader('X-user-id') and !$request->hasHeader('X-ip-address')) {
            $session = Session::where('user_id', $request->header('X-user-id'))->first();

            if (is_null($session)) {
                $new_session = Session::create([
                    'id' => Str::random(255),
                    'ip_address' =>  $request->hasHeader('X-ip-address') ? $request->header('X-ip-address') : null,
                    'user_agent' => $request->header('X-user-agent'),
                    'user_id' => $request->header('X-user-id')
                ]);

                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => $new_session->user_id,
                    'community_id' => $community->id,
                    'session_id' => $new_session->id
                ]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => $session->user_id,
                    'community_id' => $community->id,
                    'session_id' => $session->id
                ]);
            }
        }

        if ($request->hasHeader('X-ip-address')) {
            $session = Session::where('ip_address', $request->header('X-ip-address'))->first();

            if (is_null($session)) {
                $new_session = Session::create([
                    'id' => Str::random(255),
                    'ip_address' =>  $request->header('X-ip-address'),
                    'user_agent' => $request->header('X-user-agent')
                ]);

                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'community_id' => $community->id,
                    'session_id' => $new_session->id
                ]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => is_null($session->user_id) ? null : $session->user_id,
                    'community_id' => $community->id,
                    'session_id' => $session->id
                ]);
            }
        }

        return $this->handleResponse(new ResourcesCommunity($community), __('notifications.find_community_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Community  $community
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Community $community)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'community_name' => $request->community_name,
            'community_description' => $request->community_description,
            'type_id' => $request->type_id,
            'status_id' => $request->status_id,
            'user_id' => $request->user_id
        ];
        // Select specific community to check unique constraint
        $current_community = Community::find($inputs['id']);

        if (is_null($community)) {
            return $this->handleError(__('notifications.find_community_404'));
        }

        if ($inputs['community_name'] != null) {
            if ($current_community->community_name != $inputs['community_name']) {
                if (trim($inputs['community_name']) == null) {
                    return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['community_name'], __('miscellaneous.public.communities.data.community_name.error'), 400);
                }
            }

            $community->update([
                'community_name' => $inputs['community_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['community_description'] != null) {
            $community->update([
                'community_description' => $inputs['community_description'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['type_id'] != null) {
            $community->update([
                'type_id' => $inputs['type_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['status_id'] != null) {
            $community->update([
                'status_id' => $inputs['status_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['user_id'] != null) {
            $community->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(new ResourcesCommunity($community), __('notifications.update_community_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Community  $community
     * @return \Illuminate\Http\Response
     */
    public function destroy(Community $community)
    {
        $community->delete();

        $communities = Community::orderByDesc('created_at')->paginate(12);
        $count_communities = Community::count();

        return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.delete_community_success'), $communities->lastPage(), $count_communities);
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a community by its name.
     *
     * @param  string $data
     * @param  int $visitor_id
     * @return \Illuminate\Http\Response
     */
    public function search($data, $visitor_id = null)
    {
        // Group
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Type
        $search_history_type = !empty($history_type_group) ? Type::where([['type_name->fr', 'Historique des recherches'], ['group_id', $history_type_group->id]])->first() : Type::where('type_name->fr', 'Historique des recherches')->first();
        // Search request
        $communities = Community::where('community_name', 'LIKE', '%' . $data . '%')->orderByDesc('created_at')->paginate(12);
        $count_communities = Community::where('community_name', 'LIKE', '%' . $data . '%')->count();

        if (is_null($communities)) {
            return $this->handleResponse([], __('miscellaneous.empty_list'));
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        if ($visitor_id != null) {
            $visitor = User::find($visitor_id);

            if (!is_null($visitor)) {
                History::create([
                    'search_content' => $data,
                    'type_id' => $search_history_type->id,
                    'from_user_id' => $visitor->id
                ]);
            }
        }

        return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.find_all_communities_success'), $communities->lastPage(), $count_communities);
    }

    /**
     * Find all communities by type.
     *
     * @param  string $locale
     * @param  string $type_name
     * @return \Illuminate\Http\Response
     */
    public function findByType($locale, $type_name)
    {
        $type = Type::where('type_name->' . $locale, $type_name)->first();

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        $communities = Community::where('type_id', $type->id)->orderByDesc('created_at')->paginate(12);
        $count_communities = Community::where('type_id', $type->id)->count();

        return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.find_all_communities_success'), $communities->lastPage(), $count_communities);
    }

    /**
     * Find all communities by status.
     *
     * @param  string $locale
     * @param  string $status_name
     * @return \Illuminate\Http\Response
     */
    public function findByStatus($locale, $status_name)
    {
        $status = Type::where('status_name->' . $locale, $status_name)->first();

        if (is_null($status)) {
            return $this->handleError(__('notifications.find_status_404'));
        }

        $communities = Community::where('status_id', $status->id)->orderByDesc('created_at')->paginate(12);
        $count_communities = Community::where('status_id', $status->id)->count();

        return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.find_all_communities_success'), $communities->lastPage(), $count_communities);
    }

    /**
     * Find all user communities.
     *
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function findByUser($user_id)
    {
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $communities = Community::where('user_id', $user->id)->orderByDesc('created_at')->paginate(12);
        $count_communities = Community::where('user_id', $user->id)->count();

        return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.find_all_communities_success'), $communities->lastPage(), $count_communities);
    }

    /**
     * Find all user communities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function filterForUser(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (isset($request->type_id) AND isset($request->status_id)) {
            $type = Type::find($request->type_id);

            if (is_null($type)) {
                return $this->handleError(__('notifications.find_type_404'));
            }

            $status = Status::find($request->status_id);

            if (is_null($status)) {
                return $this->handleError(__('notifications.find_status_404'));
            }

            $communities = Community::where([['user_id', $user->id], ['type_id', $type->id], ['status_id', $status->id]])->orderByDesc('created_at')->paginate(12);
            $count_communities = Community::where([['user_id', $user->id], ['type_id', $type->id], ['status_id', $status->id]])->count();

            return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.find_all_communities_success'), $communities->lastPage(), $count_communities);

        } else {
            if (isset($request->type_id) AND !isset($request->status_id)) {
                $type = Type::find($request->type_id);

                if (is_null($type)) {
                    return $this->handleError(__('notifications.find_type_404'));
                }

                $communities = Community::where([['user_id', $user->id], ['type_id', $type->id]])->orderByDesc('created_at')->paginate(12);
                $count_communities = Community::where([['user_id', $user->id], ['type_id', $type->id]])->count();

                return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.find_all_communities_success'), $communities->lastPage(), $count_communities);
            }

            if (!isset($request->type_id) AND isset($request->status_id)) {
                $status = Status::find($request->type_id);

                if (is_null($status)) {
                    return $this->handleError(__('notifications.find_status_404'));
                }

                $communities = Community::where([['user_id', $user->id], ['status_id', $status->id]])->orderByDesc('created_at')->paginate(12);
                $count_communities = Community::where([['user_id', $user->id], ['status_id', $status->id]])->count();

                return $this->handleResponse(ResourcesCommunity::collection($communities), __('notifications.find_all_communities_success'), $communities->lastPage(), $count_communities);
            }
        }
    }

    /**
     * Update community status.
     *
     * @param  int $id
     * @param  int $status_id
     * @param  boolean $notify
     * @return \Illuminate\Http\Response
     */
    public function updateStatus($id, $status_id)
    {
        // Groups
        $susbcription_status_group = Group::where('group_name->fr', 'Etat de la souscription')->first();
        $post_or_community_status_group = Group::where('group_name->fr', 'Etat du post ou de la communauté')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Statuses
        $accepted_status = Status::where([['status_name->fr', 'Acceptée'], ['group_id', $susbcription_status_group->id]])->first();
        $deleted_status = Status::where([['status_name->fr', 'Supprimé'], ['group_id', $post_or_community_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $deleted_community_type = Type::where([['type_name->fr', 'Communauté supprimée'], ['group_id', $notification_type_group->id]])->first();
        // Requests
        $community = Community::find($id);
        $status = Status::where([['id', $status_id], ['group_id', $post_or_community_status_group->id]])->first();

        if (is_null($community)) {
            return $this->handleError(__('notifications.find_community_404'));
        }

        if (is_null($status)) {
            return $this->handleError(__('notifications.find_status_404'));
        }

        // update "status_id" column
        $community->update([
            'status_id' => $status->id,
            'updated_at' => now()
        ]);

        // The user account is disabled
        if ($status->id == $deleted_status->id) {
            // Find all subscribers of the event owner
            $subscribers = Subscription::where([['user_id', $community->user_id], ['status_id', $accepted_status->id]])->get();

            if ($subscribers != null) {
                foreach ($subscribers as $subscriber):
                    Notification::create([
                        'type_id' => $deleted_community_type->id,
                        'status_id' => $unread_notification_status->id,
                        'from_user_id' => $community->user_id,
                        'to_user_id' => $subscriber->id
                    ]);
                endforeach;
            }

            $notification = Notification::where([['type_id', $deleted_community_type->id], ['from_user_id', $community->user_id]])->first();

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status->id,
                'from_user_id' => $community->id,
                'for_notification_id' => is_null($notification) ? null : $notification->id
            ]);
        }

        return $this->handleResponse(new ResourcesCommunity($community), __('notifications.update_community_success'));
    }

    /**
     * Update cover picture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateCover(Request $request, $id)
    {
        $inputs = [
            'community_id' => $request->community_id,
            'image_64' => $request->image_64,
            'x' => $request->x,
            'y' => $request->y,
            'width' => $request->width,
            'height' => $request->height
        ];
        // $extension = explode('/', explode(':', substr($inputs['image_64'], 0, strpos($inputs['image_64'], ';')))[1])[1];
        $replace = substr($inputs['image_64'], 0, strpos($inputs['image_64'], ',') + 1);
        // Find substring from replace here eg: data:image/png;base64,
        $image = str_replace($replace, '', $inputs['image_64']);
        $image = str_replace(' ', '+', $image);

        // Create image URL
		$image_url = 'images/communities/' . $inputs['community_id'] . '/cover/' . Str::random(50) . '.png';

		// Upload image
		Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

		$community = Community::find($id);

        $community->update([
            'cover_photo_path' => $image_url,
            'cover_coordinates' => $inputs['x'] . '-' . $inputs['y'] . '-' . $inputs['width'] . '-' . $inputs['height'],
            'updated_at' => now()
        ]);

        return $this->handleResponse(new ResourcesCommunity($community), __('notifications.update_community_success'));
    }
}
