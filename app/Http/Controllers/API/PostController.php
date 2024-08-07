<?php

namespace App\Http\Controllers\API;

use stdClass;
use App\Models\File;
use App\Models\Group;
use App\Models\Hashtag;
use App\Models\History;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Restriction;
use App\Models\SentReaction;
use App\Models\Session;
use App\Models\Status;
use App\Models\Subscription;
use App\Models\Type;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\History as ResourcesHistory;
use App\Http\Resources\Post as ResourcesPost;
use App\Http\Resources\Session as ResourcesSession;
use App\Http\Resources\SentReaction as ResourcesSentReaction;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

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
        $posts = Post::orderByDesc('created_at')->paginate(50);
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
            'currency' => $request->currency,
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
                $existing_hashtag = Hashtag::where('keyword', $keyword)->first();

                if ($existing_hashtag != null) {
                    if (count($existing_hashtag->posts) == 0) {
                        $existing_hashtag->posts()->attach([$post->id]);
                    }

                    if (count($existing_hashtag->posts) > 0) {
                        $existing_hashtag->posts()->syncWithoutDetaching([$post->id]);
                    }

                } else {
                    $hashtag = Hashtag::create(['keyword' => $keyword]);

                    if (count($hashtag->posts) == 0) {
                        $hashtag->posts()->attach([$post->id]);
                    }

                    if (count($hashtag->posts) > 0) {
                        $hashtag->posts()->syncWithoutDetaching([$post->id]);
                    }
                }
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
        if ($post->type_id == $comment_type->id) {
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
                    // Find all subscribers of the post owner
                    $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

                    if ($subscriptions != null) {
                        foreach ($subscriptions as $subscription):
                            Notification::create([
                                'type_id' => $shared_post_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $subscription->subscriber_id,
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
                    // Find all subscribers of the post owner
                    $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

                    if ($subscriptions != null) {
                        foreach ($subscriptions as $subscription):
                            Notification::create([
                                'type_id' => $new_link_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $subscription->subscriber_id,
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
                    // Find all subscribers of the post owner
                    $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

                    if ($subscriptions != null) {
                        foreach ($subscriptions as $subscription):
                            Notification::create([
                                'type_id' => $new_post_type->id,
                                'status_id' => $unread_notification_status->id,
                                'from_user_id' => $post->user_id,
                                'to_user_id' => $subscription->subscriber_id,
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
        if ($request->hasHeader('X-user-id') AND $request->hasHeader('X-ip-address') OR $request->hasHeader('X-user-id') AND !$request->hasHeader('X-ip-address')) {
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

                $new_session->posts()->attach([$post->id]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => $session->user_id,
                    'to_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'session_id' => $session->id
                ]);

                if (count($session->posts) == 0) {
                    $session->posts()->attach([$post->id]);
                }

                if (count($session->posts) > 0) {
                    $session->posts()->syncWithoutDetaching([$post->id]);
                }
            }
        }

        if (!$request->hasHeader('X-user-id') AND $request->hasHeader('X-ip-address')) {
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

                $new_session->posts()->syncWithPivotValues([$post->id], ['is_visitor' => 1]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => is_null($session->user_id) ? null : $session->user_id,
                    'to_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'session_id' => $session->id
                ]);

                if (count($session->posts) == 0) {
                    $session->posts()->syncWithPivotValues([$post->id], ['is_visitor' => 1]);
                }

                if (count($session->posts) > 0) {
                    $session->posts()->syncWithoutDetaching([$post->id => ['is_visitor' => 1]]);
                }
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
        $post_price_updated_type = Type::where([['type_name->fr', 'Prix du post modifié'], ['group_id', $notification_type_group->id]])->first();
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
            'currency' => $request->currency,
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

            // Find all subscribers of the post owner
            $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

            if ($subscriptions != null) {
                foreach ($subscriptions as $subscription):
                    Notification::create([
                        'type_id' => $post_price_updated_type->id,
                        'status_id' => $unread_notification_status->id,
                        'from_user_id' => $post->user_id,
                        'to_user_id' => $subscription->subscriber_id,
                        'post_id' => $post->id
                    ]);
                endforeach;

            } else {
                Notification::create([
                    'type_id' => $post_price_updated_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $post->user_id,
                    'post_id' => $post->id
                ]);
            }

            $notification = Notification::where([['type_id', $post_price_updated_type->id], ['from_user_id', $post->user_id], ['post_id', $post->id]])->first();

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status->id,
                'from_user_id' => $post->user_id,
                'post_id' => $post->id,
                'for_notification_id' => $notification->id
            ]);
        }

        if ($inputs['currency'] != null) {
            $post->update([
                'currency' => $inputs['currency'],
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
        // Find all subscribers of the post owner
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
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        $posts = Post::orderByDesc('created_at')->paginate(50);
        $count_posts = Post::count();
        $notifications = Notification::where('post_id', $post->id)->get();
        $histories = History::where('post_id', $post->id)->get();
        $image_directory = $_SERVER['DOCUMENT_ROOT'] . '/public/storage/images/posts/' . $post->id;
        $document_directory = $_SERVER['DOCUMENT_ROOT'] . '/public/storage/documents/posts/' . $post->id;
        $audio_directory = $_SERVER['DOCUMENT_ROOT'] . '/public/storage/audios/posts/' . $post->id;

        $post->delete();

        if (!is_null($notifications)) {
            foreach ($notifications as $notification):
                $notification->delete();
            endforeach;
        }

        if (!is_null($histories)) {
            foreach ($histories as $history):
                $history->delete();
            endforeach;
        }

        if (Storage::exists($image_directory)) {
            Storage::deleteDirectory($image_directory);
        }

        if (Storage::exists($document_directory)) {
            Storage::deleteDirectory($document_directory);
        }

        if (Storage::exists($audio_directory)) {
            Storage::deleteDirectory($audio_directory);
        }

        return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.delete_post_success'), $posts->lastPage(), $count_posts);
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a member
     *
     * @param  string $data
     * @param  int $visitor_id
     * @return \Illuminate\Http\Response
     */
    public function search($data, $visitor_id = null)
    {
        // Groups
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $post_type_group = Group::where('group_name->fr', 'Type de post')->first();
        // Types
        $product_type = Type::where([['type_name->fr', 'Produit'], ['group_id', $post_type_group->id]])->first();
        $service_type = Type::where([['type_name->fr', 'Service'], ['group_id', $post_type_group->id]])->first();
        $article_type = Type::where([['type_name->fr', 'Article'], ['group_id', $post_type_group->id]])->first();
        $search_history_type = Type::where([['type_name->fr', 'Historique des recherches'], ['group_id', $history_type_group->id]])->first();
        // Search request
        $posts = Post::where([['post_title', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                            $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                        }])->orWhere([['post_content', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                            $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                        }])->paginate(12);
        $count_posts = Post::where([['post_title', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                            $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                        }])->orWhere([['post_content', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                            $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                        }])->count();

        if (is_null($posts)) {
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
                    'from_user_id' => $visitor->id,
                ]);
            }
        }

        return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.find_all_posts_success'), $posts->lastPage(), $count_posts);
    }

    /**
     * Find all post reactions.
     *
     * @param  int  $post_id
     * @return \Illuminate\Http\Response
     */
    public function reactions($post_id)
    {
        // Groups
        $post_type_group = Group::where('group_name->fr', 'Type de post')->first();
        $reaction_on_post_group = Group::where('group_name->fr', 'Réaction sur post')->first();
        $reaction_on_comment_group = Group::where('group_name->fr', 'Réaction sur commentaire')->first();
        // Types
        $comment_type = Type::where([['type_name->fr', 'Commentaire'], ['group_id', $post_type_group->id]])->first();
        // Reactions
        $bravo_reaction = Reaction::where([['reaction_name->fr', 'Bravo'], ['group_id', $reaction_on_post_group->id]])->first();
        $i_like_post_reaction = Reaction::where([['reaction_name->fr', 'J’aime'], ['group_id', $reaction_on_post_group->id]])->first();
        $i_support_reaction = Reaction::where([['reaction_name->fr', 'Je soutiens'], ['group_id', $reaction_on_post_group->id]])->first();
        $interesting_reaction = Reaction::where([['reaction_name->fr', 'Intéressant'], ['group_id', $reaction_on_post_group->id]])->first();
        $i_like_comment_reaction = Reaction::where([['reaction_name->fr', 'J’aime'], ['group_id', $reaction_on_comment_group->id]])->first();
        // Request
        $post = Post::find($post_id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        // If the post is a comment, find all LIKEs
        if ($post->type_id == $comment_type->id) {
            $likes = SentReaction::where([['to_post_id', $post->id], ['reaction_id', $i_like_comment_reaction->id]])->get();
            $count_all = SentReaction::where([['to_post_id', $post->id], ['reaction_id', $i_like_comment_reaction->id]])->count();

            $object = new stdClass();
            $object->i_like = ResourcesSentReaction::collection($likes);

            return $this->handleResponse($object, __('notifications.find_all_sent_reactions_success'), null, $count_all);

        // Otherwise, find all kinds of reactions
        } else {
            // All reactions
            $all_reactions = SentReaction::where('to_post_id', $post->id)->get();
            $count_all = SentReaction::where('to_post_id', $post->id)->get();
            // Specific kinds of reactions
            $bravos = SentReaction::where([['to_post_id', $post->id], ['reaction_id', $bravo_reaction->id]])->get();
            $likes = SentReaction::where([['to_post_id', $post->id], ['reaction_id', $i_like_post_reaction->id]])->get();
            $supports = SentReaction::where([['to_post_id', $post->id], ['reaction_id', $i_support_reaction->id]])->get();
            $interests = SentReaction::where([['to_post_id', $post->id], ['reaction_id', $interesting_reaction->id]])->get();

            $object = new stdClass();
            $object->all_reactions = ResourcesSentReaction::collection($all_reactions);
            $object->bravo = ResourcesSentReaction::collection($bravos);
            $object->i_like = ResourcesSentReaction::collection($likes);
            $object->i_support = ResourcesSentReaction::collection($supports);
            $object->interesting = ResourcesSentReaction::collection($interests);

            return $this->handleResponse($object, __('notifications.find_all_sent_reactions_success'), null, $count_all);
        }
    }

    /**
     * Find all post views.
     *
     * @param  int  $post_id
     * @return \Illuminate\Http\Response
     */
    public function views($post_id)
    {
        // Groups
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Types
        $consultation_history_type = !empty($history_type_group) ? Type::where([['type_name->fr', 'Historique des consultations'], ['group_id', $history_type_group->id]])->first() : Type::where('type_name->fr', 'Historique des consultations')->first();
        // Request
        $post = Post::find($post_id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        // Members
        $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->paginate(50);
        $members_count = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->count();
        // Non-identified visitors
        $visitors = Session::whereHas('posts', function($query) { $query->where('post_session.is_visitor', 1); })->get();
        $visitors_count = Session::whereHas('posts', function($query) { $query->where('post_session.is_visitor', 1); })->count();
        $count_all = $members_count + $visitors_count;

        // Main object
        $object = new stdClass();
        $object->members = ResourcesHistory::collection($members);
        $object->visitors = ResourcesSession::collection($visitors);

        return $this->handleResponse($object, __('notifications.find_all_post_views_success'), $members->lastPage(), $count_all);
    }

    /**
     * Find post views for a period (weekly, monthly, quarterly, half_yearly, yearly).
     *
     * @param  int  $post_id
     * @param  string  $period
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @return \Illuminate\Http\Response
     */
    public function periodViews($post_id, $period, $year = null, $month = null, $day = null)
    {
        // Groups
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Types
        $consultation_history_type = !empty($history_type_group) ? Type::where([['type_name->fr', 'Historique des consultations'], ['group_id', $history_type_group->id]])->first() : Type::where('type_name->fr', 'Historique des consultations')->first();
        // Request
        $post = Post::find($post_id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        if ($period == 'weekly') {
            if (is_null(trim($year)) OR is_null(trim($month)) OR is_null(trim($day))) {
                return $this->handleError(null, __('validation.custom.year_month_day.required'), 400);
            }

            try {
                $date = Carbon::parse($year . '-' . $month . '-' . $day);

                if (date_timestamp_get($date) > date_timestamp_get(now())) {
                    return $this->handleError($date, __('notifications.current_day_error'), 400);
                }

                // Get the start and end of the week by the given date
                $week_number = $date->weekNumberInMonth; // Get week number from the parsed date
                $week_start_end_in_month = getStartAndEndOfWeekInMonth($year, $month, $week_number);
                // Start of the week
                $week_start = $week_start_end_in_month['start'];
                // End of the week
                $week_end = $week_start_end_in_month['end'];
                // Members
                $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week_start, $week_end])->get();
                $members_sunday = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week_start, $week_end])->where(DB::raw('DAYOFWEEK(created_at)'), Carbon::SUNDAY)->get();
                $members_monday = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week_start, $week_end])->where(DB::raw('DAYOFWEEK(created_at)'), Carbon::MONDAY)->get();
                $members_tuesday = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week_start, $week_end])->where(DB::raw('DAYOFWEEK(created_at)'), Carbon::TUESDAY)->get();
                $members_wednesday = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week_start, $week_end])->where(DB::raw('DAYOFWEEK(created_at)'), Carbon::WEDNESDAY)->get();
                $members_thursday = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week_start, $week_end])->where(DB::raw('DAYOFWEEK(created_at)'), Carbon::THURSDAY)->get();
                $members_friday = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week_start, $week_end])->where(DB::raw('DAYOFWEEK(created_at)'), Carbon::FRIDAY)->get();
                $members_saturday = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week_start, $week_end])->where(DB::raw('DAYOFWEEK(created_at)'), Carbon::SATURDAY)->get();
                // Non-identified visitors
                $visitors = Session::whereHas('posts', function($query) use ($week_start, $week_end) { $query->whereBetween('post_session.created_at', [$week_start, $week_end])->where('post_session.is_visitor', 1); })->get();
                $visitors_sunday = Session::whereHas('posts', function($query) use ($week_start, $week_end) { $query->whereBetween('post_session.created_at', [$week_start, $week_end])->where('post_session.is_visitor', 1)->where(DB::raw('DAYOFWEEK(post_session.created_at)'), Carbon::SUNDAY); })->get();
                $visitors_monday = Session::whereHas('posts', function($query) use ($week_start, $week_end) { $query->whereBetween('post_session.created_at', [$week_start, $week_end])->where('post_session.is_visitor', 1)->where(DB::raw('DAYOFWEEK(post_session.created_at)'), Carbon::MONDAY); })->get();
                $visitors_tuesday = Session::whereHas('posts', function($query) use ($week_start, $week_end) { $query->whereBetween('post_session.created_at', [$week_start, $week_end])->where('post_session.is_visitor', 1)->where(DB::raw('DAYOFWEEK(post_session.created_at)'), Carbon::TUESDAY); })->get();
                $visitors_wednesday = Session::whereHas('posts', function($query) use ($week_start, $week_end) { $query->whereBetween('post_session.created_at', [$week_start, $week_end])->where('post_session.is_visitor', 1)->where(DB::raw('DAYOFWEEK(post_session.created_at)'), Carbon::WEDNESDAY); })->get();
                $visitors_thursday = Session::whereHas('posts', function($query) use ($week_start, $week_end) { $query->whereBetween('post_session.created_at', [$week_start, $week_end])->where('post_session.is_visitor', 1)->where(DB::raw('DAYOFWEEK(post_session.created_at)'), Carbon::THURSDAY); })->get();
                $visitors_friday = Session::whereHas('posts', function($query) use ($week_start, $week_end) { $query->whereBetween('post_session.created_at', [$week_start, $week_end])->where('post_session.is_visitor', 1)->where(DB::raw('DAYOFWEEK(post_session.created_at)'), Carbon::FRIDAY); })->get();
                $visitors_saturday = Session::whereHas('posts', function($query) use ($week_start, $week_end) { $query->whereBetween('post_session.created_at', [$week_start, $week_end])->where('post_session.is_visitor', 1)->where(DB::raw('DAYOFWEEK(post_session.created_at)'), Carbon::SATURDAY); })->get();
                // OBJECTS
                // Members days object
                $object_members_days = new stdClass();
                $object_members_days->sunday = ResourcesHistory::collection($members_sunday);
                $object_members_days->monday = ResourcesHistory::collection($members_monday);
                $object_members_days->tuesday = ResourcesHistory::collection($members_tuesday);
                $object_members_days->wednesday = ResourcesHistory::collection($members_wednesday);
                $object_members_days->thursday = ResourcesHistory::collection($members_thursday);
                $object_members_days->friday = ResourcesHistory::collection($members_friday);
                $object_members_days->saturday = ResourcesHistory::collection($members_saturday);
                // Visitors days object
                $object_visitors_days = new stdClass();
                $object_visitors_days->sunday = ResourcesHistory::collection($visitors_sunday);
                $object_visitors_days->monday = ResourcesHistory::collection($visitors_monday);
                $object_visitors_days->tuesday = ResourcesHistory::collection($visitors_tuesday);
                $object_visitors_days->wednesday = ResourcesHistory::collection($visitors_wednesday);
                $object_visitors_days->thursday = ResourcesHistory::collection($visitors_thursday);
                $object_visitors_days->friday = ResourcesHistory::collection($visitors_friday);
                $object_visitors_days->saturday = ResourcesHistory::collection($visitors_saturday);
                // Main object
                $object = new stdClass();
                $object->members = ResourcesHistory::collection($members);
                $object->members_days = $object_members_days;
                $object->visitors = ResourcesSession::collection($visitors);
                $object->visitors_days = $object_visitors_days;

                return $this->handleResponse($object, __('notifications.find_all_post_views_success'));

            } catch (InvalidFormatException $e) {
                return $this->handleError(__('miscellaneous.year_singular') . __('miscellaneous.colon_after_word') . ' ' . $year . ', ' . __('miscellaneous.month_singular') . __('miscellaneous.colon_after_word') . ' ' . $month . ', ' . __('miscellaneous.day_singular') . __('miscellaneous.colon_after_word') . ' ' . $day, __('validation.custom.year_month_day.incorrect'), 400);
            }
        }

        if ($period == 'monthly') {
            if (is_null(trim($year)) OR is_null(trim($month))) {
                return $this->handleError(null, __('validation.custom.year_month.required'), 400);
            }

            try {
                $given_date = Carbon::parse($year . '-' . $month);
                $current_date = Carbon::now();

                if (date_timestamp_get($given_date) > date_timestamp_get($current_date)) {
                    return $this->handleError($year . '-' . $month, __('notifications.current_month_error'), 400);
                }

                // Members
                $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->get();
                // Non-identified visitors
                $visitors = Session::whereHas('posts', function($query) use ($year, $month) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', $month)->where('post_session.is_visitor', 1); })->get();
                // Weeks of given month
                $weeks = getWeeksOfMonth($year, $month);
                // OBJECTS
                // Members weeks object
                $object_members_weeks = new stdClass();
                // Non-identified visitors weeks object
                $object_visitors_weeks = new stdClass();
                // Main object
                $object = new stdClass();

                // Members for all weeks
                foreach ($weeks as $key => $week) {
                    $members_week = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$week['start'], $week['end']])->get();

                    $object_members_weeks->$key = ResourcesHistory::collection($members_week);
                }

                // Non-identified visitors for all weeks
                foreach ($weeks as $key => $week) {
                    $visitors_week = Session::whereHas('posts', function($query) use ($week) { $query->whereBetween('post_session.created_at', [$week['start'], $week['end']])->where('post_session.is_visitor', 1); })->get();

                    $object_visitors_weeks->$key = ResourcesSession::collection($visitors_week);
                }

                $object->members = ResourcesHistory::collection($members);
                $object->members_weeks = $object_members_weeks;
                $object->visitors = ResourcesSession::collection($visitors);
                $object->visitors_weeks = $object_visitors_weeks;

                return $this->handleResponse($object, __('notifications.find_all_post_views_success'));

            } catch (InvalidFormatException $e) {
                return $this->handleError(__('miscellaneous.year_singular') . __('miscellaneous.colon_after_word') . ' ' . $year . ', ' . __('miscellaneous.month_singular') . __('miscellaneous.colon_after_word') . ' ' . $month, __('validation.custom.year_month.incorrect'), 400);
            }
        }

        if ($period == 'quarterly') {
            if (is_null(trim($year))) {
                return $this->handleError($year, __('validation.custom.year.required'), 400);
            }

            if ($year > Carbon::now()->year) {
                return $this->handleError($year, __('notifications.current_year_error'), 400);
            }

            if (Carbon::now()->month <= Carbon::MARCH) {
                // Get the 1st quarter dates
                $quarter = 1;
                $dates = getQuarterDates($year, $quarter);
                // Members
                $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$dates['start'], $dates['end']])->get();
                $members_january = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JANUARY)->get();
                $members_february = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::FEBRUARY)->get();
                $members_march = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::MARCH)->get();
                // Non-identified visitors
                $visitors = Session::whereHas('posts', function($query) use ($dates) { $query->whereBetween('post_session.created_at', [$dates['start'], $dates['end']])->where('post_session.is_visitor', 1); })->get();
                $visitors_january = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JANUARY)->where('post_session.is_visitor', 1); })->get();
                $visitors_february = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::FEBRUARY)->where('post_session.is_visitor', 1); })->get();
                $visitors_march = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::MARCH)->where('post_session.is_visitor', 1); })->get();
                // OBJECTS
                // Members months object
                $object_members_months = new stdClass();
                $object_members_months->january = ResourcesHistory::collection($members_january);
                $object_members_months->february = ResourcesHistory::collection($members_february);
                $object_members_months->march = ResourcesHistory::collection($members_march);
                // Visitors months object
                $object_visitors_months = new stdClass();
                $object_visitors_months->january = ResourcesHistory::collection($visitors_january);
                $object_visitors_months->february = ResourcesHistory::collection($visitors_february);
                $object_visitors_months->march = ResourcesHistory::collection($visitors_march);
                // Main object
                $object = new stdClass();
                $object->members = ResourcesHistory::collection($members);
                $object->members_months = $object_members_months;
                $object->visitors = ResourcesSession::collection($visitors);
                $object->visitors_months = $object_visitors_months;

                return $this->handleResponse($object, __('notifications.find_all_post_views_success'));
            }

            if (Carbon::now()->month > Carbon::MARCH AND Carbon::now()->month <= Carbon::JUNE) {
                // Get the 2nd quarter dates
                $quarter = 2;
                $dates = getQuarterDates($year, $quarter);
                // Members
                $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$dates['start'], $dates['end']])->get();
                $members_april = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::APRIL)->get();
                $members_may = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::MAY)->get();
                $members_june = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JUNE)->get();
                // Non-identified visitors
                $visitors = Session::whereHas('posts', function($query) use ($dates) { $query->whereBetween('post_session.created_at', [$dates['start'], $dates['end']])->where('post_session.is_visitor', 1); })->get();
                $visitors_april = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::APRIL)->where('post_session.is_visitor', 1); })->get();
                $visitors_may = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::MAY)->where('post_session.is_visitor', 1); })->get();
                $visitors_june = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JUNE)->where('post_session.is_visitor', 1); })->get();
                // OBJECTS
                // Members months object
                $object_members_months = new stdClass();
                $object_members_months->april = ResourcesHistory::collection($members_april);
                $object_members_months->may = ResourcesHistory::collection($members_may);
                $object_members_months->june = ResourcesHistory::collection($members_june);
                // Visitors months object
                $object_visitors_months = new stdClass();
                $object_visitors_months->april = ResourcesHistory::collection($visitors_april);
                $object_visitors_months->may = ResourcesHistory::collection($visitors_may);
                $object_visitors_months->june = ResourcesHistory::collection($visitors_june);
                // Main object
                $object = new stdClass();
                $object->members = ResourcesHistory::collection($members);
                $object->members_months = $object_members_months;
                $object->visitors = ResourcesSession::collection($visitors);
                $object->visitors_months = $object_visitors_months;

                return $this->handleResponse($object, __('notifications.find_all_post_views_success'));
            }

            if (Carbon::now()->month > Carbon::JUNE AND Carbon::now()->month <= Carbon::SEPTEMBER) {
                // Get the 3rd quarter dates
                $quarter = 3;
                $dates = getQuarterDates($year, $quarter);
                // Members
                $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$dates['start'], $dates['end']])->get();
                $members_july = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JULY)->get();
                $members_august = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::AUGUST)->get();
                $members_september = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::SEPTEMBER)->get();
                // Non-identified visitors
                $visitors = Session::whereHas('posts', function($query) use ($dates) { $query->whereBetween('post_session.created_at', [$dates['start'], $dates['end']])->where('post_session.is_visitor', 1); })->get();
                $visitors_july = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JULY)->where('post_session.is_visitor', 1); })->get();
                $visitors_august = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::AUGUST)->where('post_session.is_visitor', 1); })->get();
                $visitors_september = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::SEPTEMBER)->where('post_session.is_visitor', 1); })->get();
                // OBJECTS
                // Members months object
                $object_members_months = new stdClass();
                $object_members_months->july = ResourcesHistory::collection($members_july);
                $object_members_months->august = ResourcesHistory::collection($members_august);
                $object_members_months->september = ResourcesHistory::collection($members_september);
                // Visitors months object
                $object_visitors_months = new stdClass();
                $object_visitors_months->july = ResourcesHistory::collection($visitors_july);
                $object_visitors_months->august = ResourcesHistory::collection($visitors_august);
                $object_visitors_months->september = ResourcesHistory::collection($visitors_september);
                // Main object
                $object = new stdClass();
                $object->members = ResourcesHistory::collection($members);
                $object->members_months = $object_members_months;
                $object->visitors = ResourcesSession::collection($visitors);
                $object->visitors_months = $object_visitors_months;

                return $this->handleResponse($object, __('notifications.find_all_post_views_success'));
            }

            if (Carbon::now()->month > Carbon::SEPTEMBER AND Carbon::now()->month <= Carbon::DECEMBER) {
                // Get the 4th quarter dates
                $quarter = 4;
                $dates = getQuarterDates($year, $quarter);
                // Members
                $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$dates['start'], $dates['end']])->get();
                $members_october = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::OCTOBER)->get();
                $members_november = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::NOVEMBER)->get();
                $members_december = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::DECEMBER)->get();
                // Non-identified visitors
                $visitors = Session::whereHas('posts', function($query) use ($dates) { $query->whereBetween('post_session.created_at', [$dates['start'], $dates['end']])->where('post_session.is_visitor', 1); })->get();
                $visitors_october = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::OCTOBER)->where('post_session.is_visitor', 1); })->get();
                $visitors_november = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::NOVEMBER)->where('post_session.is_visitor', 1); })->get();
                $visitors_december = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::DECEMBER)->where('post_session.is_visitor', 1); })->get();
                // OBJECTS
                // Members months object
                $object_members_months = new stdClass();
                $object_members_months->october = ResourcesHistory::collection($members_october);
                $object_members_months->november = ResourcesHistory::collection($members_november);
                $object_members_months->december = ResourcesHistory::collection($members_december);
                // Visitors months object
                $object_visitors_months = new stdClass();
                $object_visitors_months->october = ResourcesHistory::collection($visitors_october);
                $object_visitors_months->november = ResourcesHistory::collection($visitors_november);
                $object_visitors_months->december = ResourcesHistory::collection($visitors_december);
                // Main object
                $object = new stdClass();
                $object->members = ResourcesHistory::collection($members);
                $object->members_months = $object_members_months;
                $object->visitors = ResourcesSession::collection($visitors);
                $object->visitors_months = $object_visitors_months;

                return $this->handleResponse($object, __('notifications.find_all_post_views_success'));
            }
        }

        if ($period == 'half_yearly') {
            if (is_null(trim($year))) {
                return $this->handleError($year, __('validation.custom.year.required'), 400);
            }

            if ($year > Carbon::now()->year) {
                return $this->handleError($year, __('notifications.current_year_error'), 400);
            }

            if (Carbon::now()->month <= Carbon::JUNE) {
                // Get the 1st half-year dates
                $portion = 1;
                $dates = getHalfYearDates($year, $portion);
                // Members
                $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$dates['start'], $dates['end']])->get();
                $members_january = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JANUARY)->get();
                $members_february = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::FEBRUARY)->get();
                $members_march = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::MARCH)->get();
                $members_april = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::APRIL)->get();
                $members_may = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::MAY)->get();
                $members_june = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JUNE)->get();
                // Non-identified visitors
                $visitors = Session::whereHas('posts', function($query) use ($dates) { $query->whereBetween('post_session.created_at', [$dates['start'], $dates['end']])->where('post_session.is_visitor', 1); })->get();
                $visitors_january = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JANUARY)->where('post_session.is_visitor', 1); })->get();
                $visitors_february = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::FEBRUARY)->where('post_session.is_visitor', 1); })->get();
                $visitors_march = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::MARCH)->where('post_session.is_visitor', 1); })->get();
                $visitors_april = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::APRIL)->where('post_session.is_visitor', 1); })->get();
                $visitors_may = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::MAY)->where('post_session.is_visitor', 1); })->get();
                $visitors_june = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JUNE)->where('post_session.is_visitor', 1); })->get();
                // OBJECTS
                // Members months object
                $object_members_months = new stdClass();
                $object_members_months->january = ResourcesHistory::collection($members_january);
                $object_members_months->february = ResourcesHistory::collection($members_february);
                $object_members_months->march = ResourcesHistory::collection($members_march);
                $object_members_months->april = ResourcesHistory::collection($members_april);
                $object_members_months->may = ResourcesHistory::collection($members_may);
                $object_members_months->june = ResourcesHistory::collection($members_june);
                // Visitors months object
                $object_visitors_months = new stdClass();
                $object_visitors_months->january = ResourcesHistory::collection($visitors_january);
                $object_visitors_months->february = ResourcesHistory::collection($visitors_february);
                $object_visitors_months->march = ResourcesHistory::collection($visitors_march);
                $object_visitors_months->april = ResourcesHistory::collection($visitors_april);
                $object_visitors_months->may = ResourcesHistory::collection($visitors_may);
                $object_visitors_months->june = ResourcesHistory::collection($visitors_june);
                // Main object
                $object = new stdClass();
                $object->members = ResourcesHistory::collection($members);
                $object->members_months = $object_members_months;
                $object->visitors = ResourcesSession::collection($visitors);
                $object->visitors_months = $object_visitors_months;

                return $this->handleResponse($object, __('notifications.find_all_post_views_success'));
            }

            if (Carbon::now()->month > Carbon::JULY AND Carbon::now()->month <= Carbon::DECEMBER) {
                // Get the 2nd half-year dates
                $portion = 2;
                $dates = getHalfYearDates($year, $portion);
                // Members
                $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereBetween('created_at', [$dates['start'], $dates['end']])->get();
                $members_july = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JULY)->get();
                $members_august = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::AUGUST)->get();
                $members_september = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::SEPTEMBER)->get();
                $members_october = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::OCTOBER)->get();
                $members_november = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::NOVEMBER)->get();
                $members_december = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::DECEMBER)->get();
                // Non-identified visitors
                $visitors = Session::whereHas('posts', function($query) use ($dates) { $query->whereBetween('post_session.created_at', [$dates['start'], $dates['end']])->where('post_session.is_visitor', 1); })->get();
                $visitors_july = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JULY)->where('post_session.is_visitor', 1); })->get();
                $visitors_august = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::AUGUST)->where('post_session.is_visitor', 1); })->get();
                $visitors_september = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::SEPTEMBER)->where('post_session.is_visitor', 1); })->get();
                $visitors_october = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::OCTOBER)->where('post_session.is_visitor', 1); })->get();
                $visitors_november = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::NOVEMBER)->where('post_session.is_visitor', 1); })->get();
                $visitors_december = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::DECEMBER)->where('post_session.is_visitor', 1); })->get();
                // OBJECTS
                // Members months object
                $object_members_months = new stdClass();
                $object_members_months->july = ResourcesHistory::collection($members_july);
                $object_members_months->august = ResourcesHistory::collection($members_august);
                $object_members_months->september = ResourcesHistory::collection($members_september);
                $object_members_months->october = ResourcesHistory::collection($members_october);
                $object_members_months->november = ResourcesHistory::collection($members_november);
                $object_members_months->december = ResourcesHistory::collection($members_december);
                // Visitors months object
                $object_visitors_months = new stdClass();
                $object_visitors_months->july = ResourcesHistory::collection($visitors_july);
                $object_visitors_months->august = ResourcesHistory::collection($visitors_august);
                $object_visitors_months->september = ResourcesHistory::collection($visitors_september);
                $object_visitors_months->october = ResourcesHistory::collection($visitors_october);
                $object_visitors_months->november = ResourcesHistory::collection($visitors_november);
                $object_visitors_months->december = ResourcesHistory::collection($visitors_december);
                // Main object
                $object = new stdClass();
                $object->members = ResourcesHistory::collection($members);
                $object->members_months = $object_members_months;
                $object->visitors = ResourcesSession::collection($visitors);
                $object->visitors_months = $object_visitors_months;

                return $this->handleResponse($object, __('notifications.find_all_post_views_success'));
            }
        }

        if ($period == 'yearly') {
            if (is_null(trim($year))) {
                return $this->handleError($year, __('validation.custom.year.required'), 400);
            }

            if ($year > Carbon::now()->year) {
                return $this->handleError($year, __('notifications.current_year_error'), 400);
            }

            // Members
            $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->get();
            $members_january = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JANUARY)->get();
            $members_february = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::FEBRUARY)->get();
            $members_march = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::MARCH)->get();
            $members_april = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::APRIL)->get();
            $members_may = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::MAY)->get();
            $members_june = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JUNE)->get();
            $members_july = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::JULY)->get();
            $members_august = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::AUGUST)->get();
            $members_september = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::SEPTEMBER)->get();
            $members_october = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::OCTOBER)->get();
            $members_november = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::NOVEMBER)->get();
            $members_december = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', Carbon::DECEMBER)->get();
            // Non-identified visitors
            $visitors = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->where('post_session.is_visitor', 1); })->get();
            $visitors_january = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JANUARY)->where('post_session.is_visitor', 1); })->get();
            $visitors_february = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::FEBRUARY)->where('post_session.is_visitor', 1); })->get();
            $visitors_march = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::MARCH)->where('post_session.is_visitor', 1); })->get();
            $visitors_april = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::APRIL)->where('post_session.is_visitor', 1); })->get();
            $visitors_may = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::MAY)->where('post_session.is_visitor', 1); })->get();
            $visitors_june = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JUNE)->where('post_session.is_visitor', 1); })->get();
            $visitors_july = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::JULY)->where('post_session.is_visitor', 1); })->get();
            $visitors_august = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::AUGUST)->where('post_session.is_visitor', 1); })->get();
            $visitors_september = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::SEPTEMBER)->where('post_session.is_visitor', 1); })->get();
            $visitors_october = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::OCTOBER)->where('post_session.is_visitor', 1); })->get();
            $visitors_november = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::NOVEMBER)->where('post_session.is_visitor', 1); })->get();
            $visitors_december = Session::whereHas('posts', function($query) use ($year) { $query->whereYear('post_session.created_at', '=', $year)->whereMonth('post_session.created_at', '=', Carbon::DECEMBER)->where('post_session.is_visitor', 1); })->get();
            // OBJECTS
            // Members months object
            $object_members_months = new stdClass();
            $object_members_months->january = ResourcesHistory::collection($members_january);
            $object_members_months->february = ResourcesHistory::collection($members_february);
            $object_members_months->march = ResourcesHistory::collection($members_march);
            $object_members_months->april = ResourcesHistory::collection($members_april);
            $object_members_months->may = ResourcesHistory::collection($members_may);
            $object_members_months->june = ResourcesHistory::collection($members_june);
            $object_members_months->july = ResourcesHistory::collection($members_july);
            $object_members_months->august = ResourcesHistory::collection($members_august);
            $object_members_months->september = ResourcesHistory::collection($members_september);
            $object_members_months->october = ResourcesHistory::collection($members_october);
            $object_members_months->november = ResourcesHistory::collection($members_november);
            $object_members_months->december = ResourcesHistory::collection($members_december);
            // Visitors months object
            $object_visitors_months = new stdClass();
            $object_visitors_months->january = ResourcesHistory::collection($visitors_january);
            $object_visitors_months->february = ResourcesHistory::collection($visitors_february);
            $object_visitors_months->march = ResourcesHistory::collection($visitors_march);
            $object_visitors_months->april = ResourcesHistory::collection($visitors_april);
            $object_visitors_months->may = ResourcesHistory::collection($visitors_may);
            $object_visitors_months->june = ResourcesHistory::collection($visitors_june);
            $object_visitors_months->july = ResourcesHistory::collection($visitors_july);
            $object_visitors_months->august = ResourcesHistory::collection($visitors_august);
            $object_visitors_months->september = ResourcesHistory::collection($visitors_september);
            $object_visitors_months->october = ResourcesHistory::collection($visitors_october);
            $object_visitors_months->november = ResourcesHistory::collection($visitors_november);
            $object_visitors_months->december = ResourcesHistory::collection($visitors_december);
            // Main object
            $object = new stdClass();
            $object->members = ResourcesHistory::collection($members);
            $object->members_months = $object_members_months;
            $object->visitors = ResourcesSession::collection($visitors);
            $object->visitors_months = $object_visitors_months;

            return $this->handleResponse($object, __('notifications.find_all_post_views_success'));
        }
    }

    /**
     * Save/unsave post to see later.
     *
     * @param  int  $user_id
     * @param  int  $post_id
     * @param  string  $action
     * @return \Illuminate\Http\Response
     */
    public function saveForLater($user_id, $post_id, $action)
    {
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $post = Post::find($post_id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        if ($action == 'save') {
            if (count($user->posts) == 0) {
                $user->posts()->attach([$post->id]);
            }

            if (count($user->posts) > 0) {
                $user->posts()->syncWithoutDetaching([$post->id]);
            }
        }

        if ($action == 'unsave') {
            $user->posts()->detach([$post->id]);
        }

        return $this->handleResponse(new ResourcesPost($post), __('notifications.find_post_success'));
    }

    /**
     * Boost post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function boost(Request $request, $id)
    {
        # code...
    }

    /**
     * Upload post files in storage.
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
        // Current post
        $post = Post::find($id);

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        if ($request->hasFile('file_url')) {
            if ($type->getTranslation('type_name', 'fr') == 'Document') {
                $file_url = 'documents/posts/' . $post->id . '/' . Str::random(50) . '.' . $request->file('file_url')->extension();

                // Upload file
                $dir_result = Storage::url(Storage::disk('public')->put($file_url, $request->file('file_url')));

                File::create([
                    'file_name' => trim($request->file_name) != null ? $request->file_name : null,
                    'file_url' => $dir_result,
                    'type_id' => $type->id,
                    'post_id' => $post->id
                ]);

                return $this->handleResponse(new ResourcesPost($post), __('notifications.update_post_success'));
            }

            if ($type->getTranslation('type_name', 'fr') == 'Image') {
                $file_url = 'images/posts/' . $post->id . '/' . Str::random(50) . '.' . $request->file('file_url')->extension();

                // Upload file
                $dir_result = Storage::url(Storage::disk('public')->put($file_url, $request->file('file_url')));

                File::create([
                    'file_name' => trim($request->file_name) != null ? $request->file_name : null,
                    'file_url' => $dir_result,
                    'type_id' => $type->id,
                    'post_id' => $post->id
                ]);

                return $this->handleResponse(new ResourcesPost($post), __('notifications.update_post_success'));
            }

            if ($type->getTranslation('type_name', 'fr') == 'Audio') {
                $file_url = 'audios/posts/' . $post->id . '/' . Str::random(50) . '.' . $request->file('file_url')->extension();

                // Upload file
                $dir_result = Storage::url(Storage::disk('public')->put($file_url, $request->file('file_url')));

                File::create([
                    'file_name' => trim($request->file_name) != null ? $request->file_name : null,
                    'file_url' => $dir_result,
                    'type_id' => $type->id,
                    'post_id' => $post->id
                ]);

                return $this->handleResponse(new ResourcesPost($post), __('notifications.update_post_success'));
            }
        }
    }
}
