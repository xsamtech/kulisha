<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class History extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'search_content' => $this->search_content,
            'type' => Type::make($this->type),
            'status' => Status::make($this->status),
            'to' => User::make($this->to_user),
            'post' => Post::make($this->post),
            'event' => Event::make($this->event),
            'community' => Community::make($this->community),
            'message' => Message::make($this->message),
            'team' => Team::make($this->team),
            'reaction' => Reaction::make($this->reaction),
            'cart' => Cart::make($this->cart),
            'for_notification' => Notification::make($this->for_notification),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_explicit' => $this->created_at->format('Y') == date('Y') ? explicitDayMonth($this->created_at->format('Y-m-d H:i:s')) : explicitDate($this->created_at->format('Y-m-d H:i:s')),
            'updated_at_explicit' => $this->updated_at->format('Y') == date('Y') ? explicitDayMonth($this->updated_at->format('Y-m-d H:i:s')) : explicitDate($this->updated_at->format('Y-m-d H:i:s')),
            'created_at_ago' => timeAgo($this->created_at->format('Y-m-d H:i:s')),
            'updated_at_ago' => timeAgo($this->updated_at->format('Y-m-d H:i:s')),
            'from_user_id' => $this->from_user_id
        ];
    }
}
