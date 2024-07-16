<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\Post as ResourcesPost;
use App\Models\Group;
use App\Models\Hashtag;
use App\Models\History;
use App\Models\Notification;
use App\Models\Status;
use App\Models\Subscription;
use App\Models\Type;
use App\Models\User;
use App\Models\Visibility;

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
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $posts_visibility_group = Group::where('group_name->fr', 'Visibilité pour les posts')->first();
        // Statuses
        $accepted_status = Status::where([['status_name->fr', 'Acceptée'], ['group_id', $susbcription_status_group->id]])->first();
        $operational_status = Status::where([['status_name->fr', 'Opérationnel'], ['group_id', $post_or_community_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $mention_type = Type::where([['type_name->fr', 'Mention'], ['group_id', $notification_type_group->id]])->first();
        $new_post_type = Type::where([['type_name->fr', 'Nouveau post'], ['group_id', $notification_type_group->id]])->first();
        // Visibility
        $everybody_visibility = Visibility::where([['visibility_name->fr', 'Tout le monde'], ['group_id', $posts_visibility_group->id]])->first();
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

        // The post is public
        // Find all subscribers of the event owner
        $subscribers = Subscription::where([['user_id', $post->user_id], ['status_id', $accepted_status->id]])->get();

        if ($subscribers != null) {
            foreach ($subscribers as $subscriber):
                Notification::create([
                    'type_id' => $new_post_type->id,
                    'status_id' => $unread_notification_status->id,
                    'from_user_id' => $post->user_id,
                    'to_user_id' => $subscriber->id,
                    'post_id' => $post->id
                ]);
            endforeach;
        }

        $notification = Notification::where([['type_id', $new_post_type->id], ['from_user_id', $post->user_id]])->first();

        History::create([
            'type_id' => $activities_history_type->id,
            'status_id' => $unread_history_status->id,
            'from_user_id' => $post->user_id,
            'for_notification_id' => is_null($notification) ? null : $notification->id
        ]);

        return $this->handleResponse(new ResourcesPost($post), __('notifications.create_post_success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
