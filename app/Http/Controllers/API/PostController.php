<?php

namespace App\Http\Controllers\API;

use stdClass;
use App\Mail\ShortMail;
use App\Models\Community;
use App\Models\Event;
use App\Models\File;
use App\Models\Group;
use App\Models\Hashtag;
use App\Models\History;
use App\Models\Keyword;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Restriction;
use App\Models\SentReaction;
use App\Models\Session;
use App\Models\Status;
use App\Models\Subscription;
use App\Models\Surveychoice;
use App\Models\Type;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
        $poll_type = Type::where([['type_name->fr', 'Sondage'], ['group_id', $post_type_group->id]])->first();
        $mention_type = Type::where([['type_name->fr', 'Mention'], ['group_id', $notification_type_group->id]])->first();
        $new_post_type = Type::where([['type_name->fr', 'Nouveau post'], ['group_id', $notification_type_group->id]])->first();
        $new_poll_type = Type::where([['type_name->fr', 'Nouveau sondage'], ['group_id', $notification_type_group->id]])->first();
        $shared_post_type = Type::where([['type_name->fr', 'Post partagé'], ['group_id', $notification_type_group->id]])->first();
        $new_link_type = Type::where([['type_name->fr', 'Nouveau lien'], ['group_id', $notification_type_group->id]])->first();
        $comment_on_post_type = Type::where([['type_name->fr', 'Commentaire sur publication'], ['group_id', $notification_type_group->id]])->first();
        $anonymous_question_type = Type::where([['type_name->fr', 'Question anonyme'], ['group_id', $notification_type_group->id]])->first();
        $connection_suggestion_type = Type::where([['type_name->fr', 'Suggestion de connexion'], ['group_id', $notification_type_group->id]])->first();
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
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'region' => $request->region,
            'country' => $request->country,
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

        // Validate the coverage area
        if ($inputs['coverage_area_id'] == null OR !is_numeric($inputs['coverage_area_id'])) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['coverage_area_id'], __('validation.required', ['field_name' => __('miscellaneous.public.home.posts.boost.coverage_area')]), 400);
        }

        if ($inputs['type_id'] == $poll_type->id) {
            if (count($request->choices_contents) == 0) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $request->choices_contents, __('miscellaneous.public.home.posts.create_poll_choices'), 400);
            }

            $post = Post::create($inputs);

            foreach ($request->choices_contents as $key => $choice_content) {
                Surveychoice::create([
                    'choice_content' => $choice_content,
                    'icon_font' => $request->icons_fonts[$key],
                    'icon_svg' => $request->icons_svgs[$key],
                    'image_url' => $request->images_urls[$key],
                    'post_id' => $post->id
                ]);
            }

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

            // If the post is for everybody
            if ($post->visibility_id == $everybody_visibility->id) {
                // Find all subscribers of the post owner
                $subscriptions = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

                if ($subscriptions != null) {
                    foreach ($subscriptions as $subscription):
                        Notification::create([
                            'type_id' => $new_poll_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'to_user_id' => $subscription->subscriber_id,
                            'post_id' => $post->id
                        ]);
                    endforeach;

                } else {
                    Notification::create([
                        'type_id' => $new_poll_type->id,
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
                            'type_id' => $new_poll_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'to_user_id' => $member_id,
                            'post_id' => $post->id
                        ]);
                    endforeach;

                } else {
                    Notification::create([
                        'type_id' => $new_poll_type->id,
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
                            'type_id' => $new_poll_type->id,
                            'status_id' => $unread_notification_status->id,
                            'from_user_id' => $post->user_id,
                            'to_user_id' => $restriction->user_id,
                            'post_id' => $post->id
                        ]);
                    endforeach;

                } else {
                    Notification::create([
                        'type_id' => $new_poll_type->id,
                        'status_id' => $unread_notification_status->id,
                        'from_user_id' => $post->user_id,
                        'post_id' => $post->id
                    ]);
                }
            }

            $notification = Notification::where([['type_id', $new_poll_type->id], ['from_user_id', $post->user_id], ['post_id', $post->id]])->first();

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status->id,
                'from_user_id' => $post->user_id,
                'post_id' => $post->id,
                'for_notification_id' => $notification->id
            ]);

            return $this->handleResponse(new ResourcesPost($post), __('notifications.create_post_success'));

        } else {
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

                // Anonymous question
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

                // Answer for a post
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

                $subscription = Subscription::where([['user_id', $parent_post->user_id], ['subscriber_id', $post->user_id]])
                                                ->orWhere([['user_id', $post->user_id], ['subscriber_id', $parent_post->user_id]])->first();


                if (is_null($subscription)) {
                    Notification::create([
                        'type_id' => $connection_suggestion_type->id,
                        'status_id' => $unread_notification_status->id,
                        'from_user_id' => $parent_post->user_id,
                        'to_user_id' => $post->user_id
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
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'region' => $request->region,
            'country' => $request->country,
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

        if ($inputs['latitude'] != null) {
            $post->update([
                'latitude' => $inputs['latitude'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['longitude'] != null) {
            $post->update([
                'longitude' => $inputs['longitude'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['city'] != null) {
            $post->update([
                'city' => $inputs['city'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['region'] != null) {
            $post->update([
                'region' => $inputs['region'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['country'] != null) {
            $post->update([
                'country' => $inputs['country'],
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

        $posts = Post::orderByDesc('created_at')->paginate(10);
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
     * News feed.
     *
     * @param  string $type_aliases
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function newsFeed($type_aliases, $user_id)
    {
        // Groups
        $member_status_group = Group::where('group_name->fr', 'Etat du membre')->first();
        $post_or_community_status_group = Group::where('group_name->fr', 'Etat du post ou de la communauté')->first();
        $posts_visibility_group = Group::where('group_name->fr', 'Visibilité pour les posts')->first();
        $reaction_on_member_or_post_group = Group::where('group_name->fr', 'Réaction sur membre ou post')->first();
        // Statuses
        $blocked_member_status = Status::where([['status_name->fr', 'Bloqué'], ['group_id', $member_status_group->id]])->first();
        $operational_status = Status::where([['status_name->fr', 'Opérationnel'], ['group_id', $post_or_community_status_group->id]])->first();
        $boosted_status = Status::where([['status_name->fr', 'Boosté'], ['group_id', $post_or_community_status_group->id]])->first();
        // Visibilities
        $everybody_visibility = Visibility::where([['visibility_name->fr', 'Tout le monde'], ['group_id', $posts_visibility_group->id]])->first();
        $nobody_except_visibility = Visibility::where([['visibility_name->fr', 'Personne, sauf …'], ['group_id', $posts_visibility_group->id]])->first();
        $connections_only_visibility = Visibility::where([['visibility_name->fr', 'Mes connexions uniquement'], ['group_id', $posts_visibility_group->id]])->first();
        // Reaction
        $muted_reaction = Reaction::where([['reaction_name->fr', 'En sourdine'], ['group_id', $reaction_on_member_or_post_group->id]])->first();
        $reported_reaction = Reaction::where([['reaction_name->fr', 'Signalé'], ['group_id', $reaction_on_member_or_post_group->id]])->first();
        // Requests
        // Convert comma separated aliases to an array of aliases
        $aliases = explode(',', $type_aliases);

        if (count($aliases) == 0) {
            return $this->handleError(__('validation.custom.type.required'));
        }

        // By the array of aliases, find array of IDs
        $types_ids = Type::whereIn('alias', $aliases)->pluck('id')->toArray();

        if (count($types_ids) == 0) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        // If the user is unknown, show each post, check some constraints such as:
        // -> The post belongs neither to a community, nor to an event;
        // -> The post doesn't belong to blocked user;
        // -> The post is operational or boosted
        // -> The post is visible to everybody
        if ($user_id == 0) {
            $posts = Post::whereNull('posts.community_id')->whereNull('posts.event_id')
                            ->whereIn('posts.type_id', $types_ids)
                            ->whereHas('users', function ($query) use ($blocked_member_status) {
                                $query->where('users.status_id', '<>', $blocked_member_status->id);
                            })
                            ->where(function ($query) use ($operational_status, $boosted_status) {
                                $query->where('posts.status_id', $operational_status->id)
                                        ->orWhere('posts.status_id', $boosted_status->id);
                            })->where('posts.visibility_id', $everybody_visibility->id)->orderByDesc('posts.created_at')->paginate(10);

            return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.find_all_posts_success'), $posts->lastPage());

        // Otherwise, to show each post, check some constraints such as:
        // -> The post belongs neither to a community, nor to an event;
        // -> The user did not report the post or the post owner;
        // -> The user has not muted the post or the post owner;
        // -> The post is operational or boosted
        // -> The post doesn't belong to blocked user;
        // -> The post is visible to everybody, or the owner has reserved it for his connections of which the user is a part, 
        //    or rather the user is one of the only people who can see this post
        } else {
            $current_user = User::find($user_id);

            if (is_null($current_user)) {
                return $this->handleError(__('notifications.find_user_404'));
            }

            // Get the IDs of the posts or users that are muted or reported by the current user
            $with_sent_reactions_post_ids = SentReaction::where([['reaction_id', $muted_reaction->id], ['user_id', $current_user->id]])
                                                            ->orWhere([['reaction_id', $reported_reaction->id], ['user_id', $current_user->id]])
                                                            ->pluck('to_post_id')->toArray();
            $with_sent_reactions_post_ids = $with_sent_reactions_post_ids != null ? $with_sent_reactions_post_ids : [0];
            $with_sent_reactions_user_ids = SentReaction::where([['reaction_id', $muted_reaction->id], ['user_id', $current_user->id]])
                                                            ->orWhere([['reaction_id', $reported_reaction->id], ['user_id', $current_user->id]])
                                                            ->pluck('to_user_id')->toArray();
            $with_sent_reactions_user_ids = $with_sent_reactions_user_ids != null ? $with_sent_reactions_user_ids : [0];
            // Get the IDs of the users whose current user is subscribed
            $with_subscriptions_user_ids = Subscription::where('subscriber_id', $current_user->id)->pluck('user_id')->toArray();
            // Get the IDs of the users who are subscribed to the current user
            $with_subscriptions_subscriber_ids = Subscription::where('user_id', $current_user->id)->pluck('subscriber_id')->toArray();
            // Get the IDs of the users connected to the current user
            $connected_users_ids = User::whereIn('id', $with_subscriptions_user_ids)->orWhereIn('id', $with_subscriptions_subscriber_ids)->pluck('id')->toArray();
            // THE MAIN QUERY STATEMENT
            $posts = Post::whereNull('posts.community_id')->whereNull('posts.event_id')
                            ->whereNotIn('posts.id', $with_sent_reactions_post_ids)->whereNotIn('posts.user_id', $with_sent_reactions_user_ids)
                            ->whereIn('posts.type_id', $types_ids)
                            ->where(function ($query) use ($operational_status, $boosted_status) {
                                $query->where('posts.status_id', $operational_status->id)
                                        ->orWhere('posts.status_id', $boosted_status->id);
                            })
                            ->whereHas('users', function ($query) use ($blocked_member_status) {
                                $query->where('users.status_id', '<>', $blocked_member_status->id);
                            })
                            ->where(function ($query) use ($current_user, $everybody_visibility, $nobody_except_visibility, $connections_only_visibility, $connected_users_ids) {
                                $query->where('posts.visibility_id', $everybody_visibility->id)
                                        ->orWhere(function ($q1) use ($current_user, $nobody_except_visibility) {
                                            $q1->where('posts.visibility_id', $nobody_except_visibility->id)
                                                ->whereHas('restrictions', function ($q2) use ($current_user, $nobody_except_visibility) {
                                                    $q2->where([
                                                        ['restrictions.user_id', $current_user->id],
                                                        ['restrictions.visibility_id', $nobody_except_visibility->id]
                                                    ]);
                                                });
                                            })
                                            ->orWhere(function ($q1) use ($current_user, $connections_only_visibility, $connected_users_ids) {
                                                $q1->where('posts.visibility_id', $connections_only_visibility->id)
                                                    ->whereHas('subscriptions', function ($q2) use ($current_user, $connected_users_ids) {
                                                        $q2->where(function ($q3) use ($current_user, $connected_users_ids) {
                                                            $q3->where('subscriptions.user_id', $current_user->id)
                                                                ->whereIn('subscriptions.subscriber_id', $connected_users_ids);
                                                        })->orWhere(function ($q3) use ($current_user, $connected_users_ids) {
                                                            $q3->where('subscriptions.subscriber_id', $current_user->id)
                                                                ->whereIn('subscriptions.user_id', $connected_users_ids);
                                                        });
                                                    });
                                                });
                            })->orderByDesc('posts.created_at')->paginate(10);

            return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.find_all_posts_success'), $posts->lastPage());
        }
    }

    /**
     * News feed in community.
     *
     * @param  string $type_aliases
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function newsFeedCommunity($type_aliases, $user_id)
    {
        // Groups
        $member_status_group = Group::where('group_name->fr', 'Etat du membre')->first();
        $post_or_community_status_group = Group::where('group_name->fr', 'Etat du post ou de la communauté')->first();
        $posts_visibility_group = Group::where('group_name->fr', 'Visibilité pour les posts')->first();
        $reaction_on_member_or_post_group = Group::where('group_name->fr', 'Réaction sur membre ou post')->first();
        // Statuses
        $blocked_member_status = Status::where([['status_name->fr', 'Bloqué'], ['group_id', $member_status_group->id]])->first();
        $operational_status = Status::where([['status_name->fr', 'Opérationnel'], ['group_id', $post_or_community_status_group->id]])->first();
        $boosted_status = Status::where([['status_name->fr', 'Boosté'], ['group_id', $post_or_community_status_group->id]])->first();
        // Visibilities
        $everybody_visibility = Visibility::where([['visibility_name->fr', 'Tout le monde'], ['group_id', $posts_visibility_group->id]])->first();
        $nobody_except_visibility = Visibility::where([['visibility_name->fr', 'Personne, sauf …'], ['group_id', $posts_visibility_group->id]])->first();
        // Reaction
        $muted_reaction = Reaction::where([['reaction_name->fr', 'En sourdine'], ['group_id', $reaction_on_member_or_post_group->id]])->first();
        $reported_reaction = Reaction::where([['reaction_name->fr', 'Signalé'], ['group_id', $reaction_on_member_or_post_group->id]])->first();
        // Requests
        // Convert comma separated aliases to an array of aliases
        $aliases = explode(',', $type_aliases);

        if (count($aliases) == 0) {
            return $this->handleError(__('validation.custom.type.required'));
        }

        // By the array of aliases, find array of IDs
        $types_ids = Type::whereIn('alias', $aliases)->pluck('id')->toArray();

        if (count($types_ids) == 0) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        // To show each post, check some constraints such as:
        // -> The post belongs to a community;
        // -> The user did not report the post or the post owner;
        // -> The user has not muted the post or the post owner.
        $current_user = User::find($user_id);

        if (is_null($current_user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        // Get the IDs of the posts or users that are muted or reported by the current user
        $with_sent_reactions_post_ids = SentReaction::where([['reaction_id', $muted_reaction->id], ['user_id', $current_user->id]])
                                                    ->orWhere([['reaction_id', $reported_reaction->id], ['user_id', $current_user->id]])
                                                    ->pluck('to_post_id')->toArray();
        $with_sent_reactions_post_ids = $with_sent_reactions_post_ids != null ? $with_sent_reactions_post_ids : [0];
        $with_sent_reactions_user_ids = SentReaction::where([['reaction_id', $muted_reaction->id], ['user_id', $current_user->id]])
                                                    ->orWhere([['reaction_id', $reported_reaction->id], ['user_id', $current_user->id]])
                                                    ->pluck('to_user_id')->toArray();
        $with_sent_reactions_user_ids = $with_sent_reactions_user_ids != null ? $with_sent_reactions_user_ids : [0];
        // THE MAIN QUERY STATEMENT
        $posts = Post::whereNotNull('posts.community_id')->whereNotIn('posts.id', $with_sent_reactions_post_ids)
                        ->whereNotIn('posts.user_id', $with_sent_reactions_user_ids)->whereIn('posts.type_id', $types_ids)
                        ->where(function ($query) use ($operational_status, $boosted_status) {
                            $query->where('posts.status_id', $operational_status->id)
                                    ->orWhere('posts.status_id', $boosted_status->id);
                        })
                        ->whereHas('users', function ($query) use ($blocked_member_status) {
                            $query->where('users.status_id', '<>', $blocked_member_status->id);
                        })
                        ->where(function ($query) use ($everybody_visibility, $nobody_except_visibility) {
                            $query->where('posts.visibility_id', $everybody_visibility->id)
                                    ->orWhere('posts.visibility_id', $nobody_except_visibility->id);
                        })->whereHas('restrictions', function ($query) use ($current_user, $nobody_except_visibility) {
                            $query->where([
                                ['restrictions.user_id', $current_user->id],
                                ['restrictions.visibility_id', $nobody_except_visibility->id]
                            ]);
                        })->orderByDesc('posts.created_at')->paginate(10);

        return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.find_all_posts_success'), $posts->lastPage());
    }

    /**
     * News feed in event.
     *
     * @param  string $type_aliases
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function newsFeedEvent($type_aliases, $user_id)
    {
        // Groups
        $member_status_group = Group::where('group_name->fr', 'Etat du membre')->first();
        $post_or_community_status_group = Group::where('group_name->fr', 'Etat du post ou de la communauté')->first();
        $posts_visibility_group = Group::where('group_name->fr', 'Visibilité pour les posts')->first();
        $reaction_on_member_or_post_group = Group::where('group_name->fr', 'Réaction sur membre ou post')->first();
        // Statuses
        $blocked_member_status = Status::where([['status_name->fr', 'Bloqué'], ['group_id', $member_status_group->id]])->first();
        $operational_status = Status::where([['status_name->fr', 'Opérationnel'], ['group_id', $post_or_community_status_group->id]])->first();
        $boosted_status = Status::where([['status_name->fr', 'Boosté'], ['group_id', $post_or_community_status_group->id]])->first();
        // Visibilities
        $everybody_visibility = Visibility::where([['visibility_name->fr', 'Tout le monde'], ['group_id', $posts_visibility_group->id]])->first();
        $nobody_except_visibility = Visibility::where([['visibility_name->fr', 'Personne, sauf …'], ['group_id', $posts_visibility_group->id]])->first();
        // Reaction
        $muted_reaction = Reaction::where([['reaction_name->fr', 'En sourdine'], ['group_id', $reaction_on_member_or_post_group->id]])->first();
        $reported_reaction = Reaction::where([['reaction_name->fr', 'Signalé'], ['group_id', $reaction_on_member_or_post_group->id]])->first();
        // Requests
        // Convert comma separated aliases to an array of aliases
        $aliases = explode(',', $type_aliases);

        if (count($aliases) == 0) {
            return $this->handleError(__('validation.custom.type.required'));
        }

        // By the array of aliases, find array of IDs
        $types_ids = Type::whereIn('alias', $aliases)->pluck('id')->toArray();

        if (count($types_ids) == 0) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        // To show each post, check some constraints such as:
        // -> The post belongs to an event;
        // -> The user did not report the post or the post owner;
        // -> The user has not muted the post or the post owner.
        $current_user = User::find($user_id);

        if (is_null($current_user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        // Get the IDs of the posts or users that are muted or reported by the current user
        $with_sent_reactions_post_ids = SentReaction::where([['reaction_id', $muted_reaction->id], ['user_id', $current_user->id]])
                                                    ->orWhere([['reaction_id', $reported_reaction->id], ['user_id', $current_user->id]])
                                                    ->pluck('to_post_id')->toArray();
        $with_sent_reactions_post_ids = $with_sent_reactions_post_ids != null ? $with_sent_reactions_post_ids : [0];
        $with_sent_reactions_user_ids = SentReaction::where([['reaction_id', $muted_reaction->id], ['user_id', $current_user->id]])
                                                    ->orWhere([['reaction_id', $reported_reaction->id], ['user_id', $current_user->id]])
                                                    ->pluck('to_user_id')->toArray();
        $with_sent_reactions_user_ids = $with_sent_reactions_user_ids != null ? $with_sent_reactions_user_ids : [0];
        // THE MAIN QUERY STATEMENT
        $posts = Post::whereNotNull('posts.event_id')->whereNotIn('posts.id', $with_sent_reactions_post_ids)
                        ->whereNotIn('posts.user_id', $with_sent_reactions_user_ids)->whereIn('posts.type_id', $types_ids)
                        ->where(function ($query) use ($operational_status, $boosted_status) {
                            $query->where('posts.status_id', $operational_status->id)
                                    ->orWhere('posts.status_id', $boosted_status->id);
                        })
                        ->whereHas('users', function ($query) use ($blocked_member_status) {
                            $query->where('users.status_id', '<>', $blocked_member_status->id);
                        })
                        ->where(function ($query) use ($everybody_visibility, $nobody_except_visibility) {
                            $query->where('posts.visibility_id', $everybody_visibility->id)
                                    ->orWhere('posts.visibility_id', $nobody_except_visibility->id);
                        })->whereHas('restrictions', function ($query) use ($current_user, $nobody_except_visibility) {
                            $query->where([
                                ['restrictions.user_id', $current_user->id],
                                ['restrictions.visibility_id', $nobody_except_visibility->id]
                            ]);
                        })->orderByDesc('posts.created_at')->paginate(10);

        return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.find_all_posts_success'), $posts->lastPage());
    }

    /**
     * Search a post
     *
     * @param  string $data
     * @param  int $visitor_id
     * @return \Illuminate\Http\Response
     */
    public function search($data, $visitor_id)
    {
        // Groups
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $post_type_group = Group::where('group_name->fr', 'Type de post')->first();
        // Types
        $product_type = Type::where([['type_name->fr', 'Produit'], ['group_id', $post_type_group->id]])->first();
        $service_type = Type::where([['type_name->fr', 'Service'], ['group_id', $post_type_group->id]])->first();
        $article_type = Type::where([['type_name->fr', 'Article'], ['group_id', $post_type_group->id]])->first();
        $search_history_type = Type::where([['type_name->fr', 'Historique des recherches'], ['group_id', $history_type_group->id]])->first();

        if ($visitor_id != 0) {
            $visitor = User::find($visitor_id);

            if (is_null($visitor)) {
                return $this->handleResponse([], __('notifications.find_visitor_404'));
            }

            // Get array of community IDs of the current user
            $communities_ids = Community::whereHas('users', function($query) use ($visitor) { $query->where('community_user.user_id', $visitor->id); })->pluck('communities.id')->toArray();
            // Get array of event IDs of the current user
            $events_ids = Event::whereHas('users', function($query) use ($visitor) { $query->where('event_user.user_id', $visitor->id); })->pluck('events.id')->toArray();
            // THE MAIN QUERY STATEMENT
            $posts = Post::whereIn('posts.community_id', $communities_ids)->orWhereIn('posts.event_id', $events_ids)
                            ->where([['post_title', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                                $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                            }])->orWhere([['post_content', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                                $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                            }])->paginate(30);
            $count_posts = Post::whereIn('posts.community_id', $communities_ids)->orWhereIn('posts.event_id', $events_ids)
                                    ->where([['post_title', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                                        $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                                    }])->orWhere([['post_content', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                                        $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                                    }])->count();

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            History::create([
                'search_content' => $data,
                'type_id' => $search_history_type->id,
                'from_user_id' => $visitor->id,
            ]);

            if (is_null($posts)) {
                return $this->handleResponse([], __('miscellaneous.empty_list'));
            }

            return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.find_all_posts_success'), $posts->lastPage(), $count_posts);

        } else {
            $posts = Post::whereNull('posts.community_id')->whereNull('posts.event_id')
                            ->where([['post_title', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                                $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                            }])->orWhere([['post_content', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                                $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                            }])->paginate(30);
            $count_posts = Post::whereNull('posts.community_id')->whereNull('posts.event_id')
                                    ->where([['post_title', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                                        $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                                    }])->orWhere([['post_content', 'LIKE', '%' . $data . '%'], function ($query) use ($product_type, $service_type, $article_type) {
                                        $query->where('type_id', $product_type->id)->orWhere('type_id', $service_type->id)->orWhere('type_id', $article_type->id);
                                    }])->count();

            if (is_null($posts)) {
                return $this->handleResponse([], __('miscellaneous.empty_list'));
            }

            return $this->handleResponse(ResourcesPost::collection($posts), __('notifications.find_all_posts_success'), $posts->lastPage(), $count_posts);
        }
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
        $disappointing_reaction = Reaction::where([['reaction_name->fr', 'Décevant'], ['group_id', $reaction_on_post_group->id]])->first();
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

            return $this->handleResponse(ResourcesSentReaction::collection($likes), __('notifications.find_all_sent_reactions_success'), null, $count_all);

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
            $disappoints = SentReaction::where([['to_post_id', $post->id], ['reaction_id', $disappointing_reaction->id]])->get();

            $object = new stdClass();
            $object->all_reactions = ResourcesSentReaction::collection($all_reactions);
            $object->bravo = ResourcesSentReaction::collection($bravos);
            $object->i_like = ResourcesSentReaction::collection($likes);
            $object->i_support = ResourcesSentReaction::collection($supports);
            $object->interesting = ResourcesSentReaction::collection($interests);
            $object->disappointing = ResourcesSentReaction::collection($disappoints);

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
        $consultation_history_type = Type::where([['type_name->fr', 'Historique des consultations'], ['group_id', $history_type_group->id]])->first();
        // Request
        $post = Post::find($post_id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        // Members
        $members = History::where([['type_id',  $consultation_history_type->id], ['post_id', $post->id]])->paginate(10);
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
        // FlexPay accessing data
        $gateway_mobile = config('services.flexpay.gateway_mobile');
        $gateway_card = config('services.flexpay.gateway_card_v2');
        // Vonage accessing data
        // $basic  = new \Vonage\Client\Credentials\Basic(config('vonage.api_key'), config('vonage.api_secret'));
        // $client = new \Vonage\Client($basic);
        // Groups
        $transaction_status_group = Group::where('group_name->fr', 'Etat de la transaction')->first();
        $transaction_type_group = Group::where('group_name->fr', 'Type de transaction')->first();
        // Status
        $done_transaction_status = Status::where([['status_name->fr', 'Effectué'], ['group_id', $transaction_status_group->id]])->first();
        // Types
        $mobile_money_type = Type::where([['type_name->fr', 'Mobile money'], ['group_id', $transaction_type_group->id]])->first();
        $bank_card_type = Type::where([['type_name->fr', 'Carte bancaire'], ['group_id', $transaction_type_group->id]])->first();

        if (is_null($mobile_money_type)) {
            return $this->handleError(__('miscellaneous.public.home.posts.boost.transaction_type.mobile_money'), __('notifications.find_type_404'), 404);
        }

        if (is_null($bank_card_type)) {
            return $this->handleError(__('miscellaneous.public.home.posts.boost.transaction_type.bank_card'), __('notifications.find_type_404'), 404);
        }

        // Request
        $post = Post::find($id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        // Validations
        if ($request->transaction_type_id == null OR !is_numeric($request->transaction_type_id)) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $request->transaction_type_id, __('validation.required', ['field_name' => __('miscellaneous.public.home.posts.boost.transaction_type.title')]), 400);
        }

        if ($request->budget_id == null OR !is_numeric($request->budget_id)) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $request->budget_id, __('validation.required', ['field_name' => __('miscellaneous.public.home.posts.boost.budget')]), 400);
        }

        if ($request->subject_url == null OR !is_numeric($request->subject_url)) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $request->subject_url, __('validation.custom.url.required'), 400);
        }

        $budget = Post::find($request->budget_id);

        if (is_null($budget)) {
            return $this->handleError(__('notifications.find_budget_404'));
        }

        // If the transaction is via mobile money
        if ($request->transaction_type_id == $mobile_money_type->id) {
            $current_user = User::find($post->user_id);

            if ($current_user != null) {
                $reference_code = 'REF-' . ((string) random_int(10000000, 99999999)) . '-' . $current_user->id;

                // Create response by sending request to FlexPay
                $data = array(
                    'merchant' => config('services.flexpay.merchant'),
                    'type' => 1,
                    'phone' => $request->other_phone,
                    'reference' => $reference_code,
                    'amount' => $budget->amount,
                    'currency' => 'USD',
                    'callbackUrl' => getApiURL() . '/payment/store'
                );
                $data = json_encode($data);
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $gateway_mobile);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . config('services.flexpay.api_token')
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    return $this->handleError(curl_errno($ch), __('notifications.transaction_request_failed'), 400);

                } else {
                    curl_close($ch); 

                    $jsonRes = json_decode($response, true);
                    $code = $jsonRes['code']; // Push sending status

                    if ($code != '0') {
                        if (!empty($current_user->email)) {
                            Mail::to($current_user->email)->send(new ShortMail(null, 'payment', __('notifications.transaction_push_failed')));
                        }

                        // if (!empty($current_user->phone)) {
                        //     try {
                        //         $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', __('notifications.transaction_push_failed')));

                        //     } catch (\Throwable $th) {
                        //         return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        //     }
                        // }

                        return $this->handleError(__('miscellaneous.error_label'), __('notifications.transaction_push_failed'), 400);

                    } else {
                        $object = new stdClass();

                        $object->result_response = [
                            'message' => $jsonRes['message'],
                            'order_number' => $jsonRes['orderNumber']
                        ];

                        // The post is updated only if the processing succeed
                        $post->update(['budget_id' => $budget->id]);

                        if (count($request->keywords) > 0) {
                            foreach ($request->keywords as $keyword):
                                Keyword::create([
                                    'keyword' => $keyword,
                                    'post_id' => $post->id
                                ]);
                            endforeach;
                        }

                        $object->post = new ResourcesPost($post);

                        // Register payment, even if FlexPay will
                        $payment = Payment::where('order_number', $jsonRes['orderNumber'])->first();

                        if (is_null($payment)) {
                            Payment::create([
                                'reference' => $reference_code,
                                'order_number' => $jsonRes['orderNumber'],
                                'amount' => $budget->amount,
                                'phone' => $request->other_phone,
                                'currency' => 'USD',
                                'type_id' => $request->transaction_type_id,
                                'status_id' => $done_transaction_status->id,
                                'subject_url' => $request->subject_url,
                                'user_id' => $current_user->id
                            ]);
                        }

                        if (!empty($current_user->email)) {
                            Mail::to($current_user->email)->send(new ShortMail(null, 'payment', __('notifications.boost_post_success')));
                        }

                        // if (!empty($current_user->phone)) {
                        //     try {
                        //         $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', __('notifications.boost_post_success')));

                        //     } catch (\Throwable $th) {
                        //         return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        //     }
                        // }

                        return $this->handleResponse($object, __('notifications.boost_post_success'));
                    }
                }

            } else {
                return $this->handleError(__('notifications.find_user_404'));
            }
        }

        // If the transaction is via bank card
        if ($request->transaction_type_id == $bank_card_type->id) {
            $current_user = User::find($post->user_id);

            if ($current_user != null) {
                $reference_code = 'REF-' . ((string) random_int(10000000, 99999999)) . '-' . $current_user->id;

                // Create response by sending request to FlexPay
                $body = json_encode(array(
                    'authorization' => 'Bearer ' . config('services.flexpay.api_token'),
                    'merchant' => config('services.flexpay.merchant'),
                    'reference' => $reference_code,
                    'amount' => $budget->amount,
                    'currency' => 'USD',
                    'description' => __('miscellaneous.bank_transaction_description'),
                    'callback_url' => getApiURL() . '/payment/store',
                    'approve_url' => $request->app_url . '/boosted/' . $budget->amount . '/USD/0/' . $current_user->id,
                    'cancel_url' => $request->app_url . '/boosted/' . $budget->amount . '/USD/1/' . $current_user->id,
                    'decline_url' => $request->app_url . '/boosted/' . $budget->amount . '/USD/2/' . $current_user->id,
                    'home_url' => $request->app_url . '/posts/' . $post->id,
                ));

                $curl = curl_init($gateway_card);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $curlResponse = curl_exec($curl);

                $jsonRes = json_decode($curlResponse, true);
                $code = $jsonRes['code'];
                $message = $jsonRes['message'];

                if (!empty($jsonRes['error'])) {
                    return $this->handleError($jsonRes['error'], $message, $jsonRes['status']);

                } else {
                    if ($code != '0') {
                        if (!empty($current_user->email)) {
                            Mail::to($current_user->email)->send(new ShortMail(null, 'payment', $message));
                        }

                        // if (!empty($current_user->phone)) {
                        //     try {
                        //         $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', $message));

                        //     } catch (\Throwable $th) {
                        //         return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        //     }
                        // }

                        return $this->handleError($code, $message, 400);

                    } else {
                        $url = $jsonRes['url'];
                        $orderNumber = $jsonRes['orderNumber'];
                        $object = new stdClass();

                        $object->result_response = [
                            'message' => $message,
                            'order_number' => $orderNumber,
                            'url' => $url
                        ];

                        // The post is updated only if the processing succeed
                        $post->update(['budget_id' => $budget->id]);

                        if (count($request->keywords) > 0) {
                            foreach ($request->keywords as $keyword):
                                Keyword::create([
                                    'keyword' => $keyword,
                                    'post_id' => $post->id
                                ]);
                            endforeach;
                        }

                        $object->post = new ResourcesPost($post);

                        // Register payment, even if FlexPay will
                        $payment = Payment::where('order_number', $jsonRes['orderNumber'])->first();

                        if (is_null($payment)) {
                            Payment::create([
                                'reference' => $reference_code,
                                'order_number' => $jsonRes['orderNumber'],
                                'amount' => $budget->amount,
                                'currency' => 'USD',
                                'type_id' => $request->transaction_type_id,
                                'status_id' => $done_transaction_status->id,
                                'subject_url' => $request->subject_url,
                                'user_id' => $current_user->id
                            ]);
                        }

                        if (!empty($current_user->email)) {
                            Mail::to($current_user->email)->send(new ShortMail(null, 'payment', __('notifications.boost_post_success')));
                        }

                        // if (!empty($current_user->phone)) {
                        //     try {
                        //         $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', __('notifications.boost_post_success')));

                        //     } catch (\Throwable $th) {
                        //         return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        //     }
                        // }

                        return $this->handleResponse($object, __('notifications.boost_post_success'));
                    }
                }

            } else {
                return $this->handleError(__('notifications.find_user_404'));
            }
        }
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
