<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\Type;
use App\Models\User;
use App\Models\Group;
use App\Models\Status;
use App\Models\Hashtag;
use App\Models\History;
use App\Models\Session;
use App\Models\Visibility;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Resources\Post as ResourcesPost;
use App\Models\Restriction;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderByDesc('created_at')->paginate(10);
        $count_posts = Post::count();

        return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.find_all_posts_success'), $posts->lastPage(), $count_posts);
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
        $post_or_community_status_group = Group::where('group_name->fr', 'Etat du post ou de la communauté')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $post_type_group = Group::where('group_name->fr', 'Type de post')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $posts_visibility_group = Group::where('group_name->fr', 'Visibilité pour les posts')->first();
        // Statuses
        $accepted_status = Status::where([['status_name->fr', 'Acceptée'], ['group_id', $susbcription_status_group->id]])->first();
        $operational_status = Status::where([['status_name->fr', 'Opérationnel'], ['group_id', $post_or_community_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $request_for_anonymous_question_type = Type::where([['type_name->fr', 'Demande de question anonyme'], ['group_id', $post_type_group->id]])->first();
        $comment_type = Type::where([['type_name->fr', 'Commentaire'], ['group_id', $post_type_group->id]])->first();
        $mention_type = Type::where([['type_name->fr', 'Mention'], ['group_id', $notification_type_group->id]])->first();
        $new_post_type = Type::where([['type_name->fr', 'Nouveau post'], ['group_id', $notification_type_group->id]])->first();
        $shared_post_type = Type::where([['type_name->fr', 'Post partagé'], ['group_id', $notification_type_group->id]])->first();
        $new_link_type = Type::where([['type_name->fr', 'Nouveau lien'], ['group_id', $notification_type_group->id]])->first();
        $comment_on_post_type = Type::where([['type_name->fr', 'Commentaire sur publication'], ['group_id', $notification_type_group->id]])->first();
        $anonymous_question_type = Type::where([['type_name->fr', 'Question anonyme'], ['group_id', $notification_type_group->id]])->first();
        // Visibility
        $everybody_visibility = Visibility::where([['visibility_name->fr', 'Tout le monde'], ['group_id', $posts_visibility_group->id]])->first();
        $everybody_except_visibility = Visibility::where([['visibility_name->fr', 'Tout le monde, sauf ...'], ['group_id', $posts_visibility_group->id]])->first();
        $nobody_except_visibility = Visibility::where([['visibility_name->fr', 'Personne, sauf …'], ['group_id', $posts_visibility_group->id]])->first();
        // Get inputs
        $inputs = [
            'post_url' => $request->post_url,
            'post_title' => $request->post_title,
            'post_content' => $request->post_content,
            'shared_post_id' => $request->shared_post_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'answered_for' => $request->answered_for,
            'type_id' => $request->type_id,
            'category_id' => $request->category_id,
            'status_id' => isset($request->status_id) ? $request->status_id : $operational_status->id,
            'visibility_id' => isset($request->visibility_id) ? $request->visibility_id : $everybody_visibility->id,
            'coverage_area_id' => $request->coverage_area_id,
            'budget_id' => $request->budget_id,
            'community_id' => $request->community_id,
            'event_id' => $request->event_id,
            'user_id' => $request->user_id
        ];

        if (!is_numeric($inputs['type_id']) OR trim($inputs['type_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['type_id'], __('validation.custom.type.required'), 400);
        }

        $post = Post::create($inputs);
        // Hashtags management
        $hashtags = getHashtags($post->post_content);

        if (count($hashtags) > 0) {
            foreach ($hashtags as $keyword):
                $hashtag = Hashtag::create(['keyword' => $keyword]);

                $hashtag->posts()->attach([$post]);
            endforeach;
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        // Mentions management
        $mentions = getMentions($post->post_content);

        if (count($mentions) > 0) {
            foreach ($mentions as $mention):
                $mentioned = User::where('username', $mention)->first();

                $notification = Notification::create([
                    'type_id' => $mention_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $post->user_id,
                    'to_user_id' => $mentioned->id,
                    'post_id' => $post->id
                ]);
            endforeach;
        }

        // If it's a comment, check if it's a anonymous question or an answer for a post
        if ($post->type_id = $comment_type->id) {
            $parent_post = Post::find($post->answered_for);

            if (is_null($parent_post)) {
                return $this->handleError(__('notifications.find_post_parent_404'));
            }

            if ($parent_post->type_id != $request_for_anonymous_question_type->id) {
                $notification = Notification::create([
                    'type_id' => $anonymous_question_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $post->user_id,
                    'to_user_id' => $parent_post->user_id,
                    'post_id' => $post->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status->id,
                    'from_user_id' => $post->user_id,
                    'to_user_id' => $parent_post->user_id,
                    'post_id' => $post->id,
                    'for_notification_id' => $notification->id
                ]);

            } else {
                $notification = Notification::create([
                    'type_id' => $comment_on_post_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $post->user_id,
                    'to_user_id' => $parent_post->user_id,
                    'post_id' => $post->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status->id,
                    'from_user_id' => $post->user_id,
                    'to_user_id' => $parent_post->user_id,
                    'post_id' => $post->id,
                    'for_notification_id' => $notification->id
                ]);
            }

        // Otherwise, check if it's a link or a shared post. Or rather check visibilities
        } else {
            if ($post->shared_post_id != null) {
                // If the post is for everybody
                if ($post->visibility_id == $everybody_visibility->id) {
                    // Find all subscribers of the event owner
                    $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

                    if ($subscriptions != null) {
                        foreach ($subscriptions as $subscription):
                            if ($post->type_id != $comment_type->id) {
                                Notification::create([
                                    'type_id' => $shared_post_type->id,
                                    'status_id' => $unread_notification_status->id,
                                    'from_user_id' => $post->user_id,
                                    'to_user_id' => $subscription->subscriber_id,
                                    'post_id' => $post->id
                                ]);
                            }
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $shared_post_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                // If the post is for everybody except some member(s)
                if ($post->visibility_id == $everybody_except_visibility->id) {
                    // Find all subscribers excluding those in the restriction
                    $subscribers = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();
                    $restrictions = Restriction::where([['visibility_id', $everybody_except_visibility->id], ['post_id', $post->id]])->get();

                    if ($subscribers != null AND $restrictions != null) {
                        $members_ids = array_diff(getArrayKeys($subscribers, 'user_id'), getArrayKeys($restrictions, 'user_id'));

                        foreach ($members_ids as $member_id):
                            Notification::create([
                                'type_id' => $shared_post_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $member_id,
                                'post_id' => $post->id
                            ]);
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $shared_post_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                // If the post is for nobody except some member(s)
                if ($post->visibility_id == $nobody_except_visibility->id) {
                    // Find all members included in the restriction
                    $restrictions = Restriction::where([['visibility_id', $nobody_except_visibility->id], ['post_id', $post->id]])->get();

                    if ($restrictions != null) {
                        foreach ($restrictions as $restriction):
                            Notification::create([
                                'type_id' => $shared_post_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $restriction->user_id,
                                'post_id' => $post->id
                            ]);
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $shared_post_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                $notification = Notification::where([['type_id', $shared_post_type->id], ['from_user_id', $post->user_id], ['post_id', $post->id]])->first();

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status->id,
                    'from_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'for_notification_id' => $notification->id
                ]);

            } else if ($post->post_url != null) {
                // If the post is for everybody
                if ($post->visibility_id == $everybody_visibility->id) {
                    // Find all subscribers of the event owner
                    $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

                    if ($subscriptions != null) {
                        foreach ($subscriptions as $subscription):
                            if ($post->type_id != $comment_type->id) {
                                Notification::create([
                                    'type_id' => $new_link_type->id,
                                    'status_id' => $unread_notification_status->id,
                                    'from_user_id' => $post->user_id,
                                    'to_user_id' => $subscription->subscriber_id,
                                    'post_id' => $post->id
                                ]);
                            }
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $new_link_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                // If the post is for everybody except some member(s)
                if ($post->visibility_id == $everybody_except_visibility->id) {
                    // Find all subscribers excluding those in the restriction
                    $subscribers = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();
                    $restrictions = Restriction::where([['visibility_id', $everybody_except_visibility->id], ['post_id', $post->id]])->get();

                    if ($subscribers != null AND $restrictions != null) {
                        $members_ids = array_diff(getArrayKeys($subscribers, 'user_id'), getArrayKeys($restrictions, 'user_id'));

                        foreach ($members_ids as $member_id):
                            Notification::create([
                                'type_id' => $new_link_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $member_id,
                                'post_id' => $post->id
                            ]);
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $new_link_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                // If the post is for nobody except some member(s)
                if ($post->visibility_id == $nobody_except_visibility->id) {
                    // Find all members included in the restriction
                    $restrictions = Restriction::where([['visibility_id', $nobody_except_visibility->id], ['post_id', $post->id]])->get();

                    if ($restrictions != null) {
                        foreach ($restrictions as $restriction):
                            Notification::create([
                                'type_id' => $new_link_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $restriction->user_id,
                                'post_id' => $post->id
                            ]);
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $new_link_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                $notification = Notification::where([['type_id', $new_link_type->id], ['from_user_id', $post->user_id], ['post_id', $post->id]])->first();

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status->id,
                    'from_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'for_notification_id' => $notification->id
                ]);

            } else {
                // If the post is for everybody
                if ($post->visibility_id == $everybody_visibility->id) {
                    // Find all subscribers of the event owner
                    $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

                    if ($subscriptions != null) {
                        foreach ($subscriptions as $subscription):
                            if ($post->type_id != $comment_type->id) {
                                Notification::create([
                                    'type_id' => $new_post_type->id,
                                    'status_id' => $unread_notification_status->id,
                                    'from_user_id' => $post->user_id,
                                    'to_user_id' => $subscription->subscriber_id,
                                    'post_id' => $post->id
                                ]);
                            }
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $new_post_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                // If the post is for everybody except some member(s)
                if ($post->visibility_id == $everybody_except_visibility->id) {
                    // Find all subscribers excluding those in the restriction
                    $subscribers = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();
                    $restrictions = Restriction::where([['visibility_id', $everybody_except_visibility->id], ['post_id', $post->id]])->get();

                    if ($subscribers != null AND $restrictions != null) {
                        $members_ids = array_diff(getArrayKeys($subscribers, 'user_id'), getArrayKeys($restrictions, 'user_id'));

                        foreach ($members_ids as $member_id):
                            Notification::create([
                                'type_id' => $new_post_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $member_id,
                                'post_id' => $post->id
                            ]);
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $new_post_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                // If the post is for nobody except some member(s)
                if ($post->visibility_id == $nobody_except_visibility->id) {
                    // Find all members included in the restriction
                    $restrictions = Restriction::where([['visibility_id', $nobody_except_visibility->id], ['post_id', $post->id]])->get();

                    if ($restrictions != null) {
                        foreach ($restrictions as $restriction):
                            Notification::create([
                                'type_id' => $new_post_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $restriction->user_id,
                                'post_id' => $post->id
                            ]);
                        endforeach;

                    } else {
                        Notification::create([
                            'type_id' => $new_post_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'post_id' => $post->id
                        ]);
                    }
                }

                $notification = Notification::where([['type_id', $new_post_type->id], ['from_user_id', $post->user_id], ['post_id', $post->id]])->first();

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status->id,
                    'from_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'for_notification_id' => $notification->id
                ]);
            }
        }

        return $this->handleResponse(new ResourcesPost($post), __('notifications.create_post_success'));
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
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Types
        $consultation_history_type = !empty($history_type_group) ? Type::where([['type_name->fr', 'Historique des consultations'], ['group_id', $history_type_group->id]])->first() : Type::where('type_name->fr', 'Historique des consultations')->first();
        // Request
        $post = Post::find($id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
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
                    'to_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'session_id' => $new_session->id
                ]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => $session->user_id,
                    'to_user_id' => $post->user_id,
                    'post_id' => $post->id,
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
                    'to_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'session_id' => $new_session->id
                ]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => is_null($session->user_id) ? null : $session->user_id,
                    'to_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'session_id' => $session->id
                ]);
            }
        }

        return $this->handleResponse(new ResourcesPost($post), __('notifications.find_post_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        // Groups
        $susbcription_status_group = Group::where('group_name->fr', 'Etat de la souscription')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Statuses
        $accepted_status = Status::where([['status_name->fr', 'Acceptée'], ['group_id', $susbcription_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $post_updated_type = Type::where([['type_name->fr', 'Post modifié'], ['group_id', $notification_type_group->id]])->first();
        $mention_type = Type::where([['type_name->fr', 'Mention'], ['group_id', $notification_type_group->id]])->first();
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'post_url' => $request->post_url,
            'post_title' => $request->post_title,
            'post_content' => $request->post_content,
            'shared_post_id' => $request->shared_post_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'answered_for' => $request->answered_for,
            'type_id' => $request->type_id,
            'category_id' => $request->category_id,
            'status_id' => $request->status_id,
            'visibility_id' => $request->visibility_id,
            'coverage_area_id' => $request->coverage_area_id,
            'budget_id' => $request->budget_id,
            'community_id' => $request->community_id,
            'event_id' => $request->event_id,
            'user_id' => $request->user_id
        ];
        $current_post = Post::find($inputs['id']);

        if ($inputs['post_url'] != null) {
            $post->update([
                'post_url' => $inputs['post_url'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['post_title'] != null) {
            $post->update([
                'post_title' => $inputs['post_title'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['post_content'] != null) {
            $post->update([
                'post_content' => $inputs['post_content'],
                'updated_at' => now(),
            ]);

            $post->hashtags()->detach();

            // Hashtags management
            $hashtags = getHashtags($post->post_content);

            if (count($hashtags) > 0) {
                foreach ($hashtags as $keyword):
                    $hashtag = Hashtag::create(['keyword' => $keyword]);
    
                    $hashtag->posts()->attach([$post]);
                endforeach;
            }

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            // Mentions management
            $former_mentions = getMentions($current_post->post_content);
            $new_mentions = getMentions($post->post_content);

            if (count($new_mentions) > 0) {
                $mentions = count($former_mentions) > count($new_mentions) ? array_diff($former_mentions, $new_mentions) : array_diff($new_mentions, $former_mentions);;

                if (count($mentions) > 0) {
                    foreach ($mentions as $mention):
                        $mentioned = User::where('username', $mention)->first();

                        $notification = Notification::create([
                            'type_id' => $mention_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'to_user_id' => $mentioned->id,
                            'post_id' => $post->id
                        ]);
                    endforeach;
                }
            }
        }

        if ($inputs['shared_post_id'] != null) {
            $post->update([
                'shared_post_id' => $inputs['shared_post_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['price'] != null) {
            $post->update([
                'price' => $inputs['price'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['quantity'] != null) {
            $post->update([
                'quantity' => $inputs['quantity'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['answered_for'] != null) {
            $post->update([
                'answered_for' => $inputs['answered_for'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['type_id'] != null) {
            $post->update([
                'type_id' => $inputs['type_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['category_id'] != null) {
            $post->update([
                'category_id' => $inputs['category_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['status_id'] != null) {
            $post->update([
                'status_id' => $inputs['status_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['visibility_id'] != null) {
            $post->update([
                'visibility_id' => $inputs['visibility_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['coverage_area_id'] != null) {
            $post->update([
                'coverage_area_id' => $inputs['coverage_area_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['budget_id'] != null) {
            $post->update([
                'budget_id' => $inputs['budget_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['community_id'] != null) {
            $post->update([
                'community_id' => $inputs['community_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['event_id'] != null) {
            $post->update([
                'event_id' => $inputs['event_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $post->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now(),
            ]);
        }

        // The post is public
        // Find all subscribers of the event owner
        $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

        if ($subscriptions != null) {
            foreach ($subscriptions as $subscription):
                Notification::create([
                    'type_id' => $post_updated_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $post->user_id,
                    'to_user_id' => $subscription->subscriber_id,
                    'post_id' => $post->id
                ]);
            endforeach;
        }

        $notification = Notification::where([['type_id', $post_updated_type->id], ['from_user_id', $post->user_id], ['post_id', $post->id]])->first();

        History::create([
            'type_id' => $activities_history_type->id,
            'status_id' => $unread_history_status->id,
            'from_user_id' => $current_post->user_id,
            'post_id' => $current_post->id,
            'for_notification_id' => $notification->id
        ]);

        return $this->handleResponse(new ResourcesPost($post), __('notifications.update_post_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
