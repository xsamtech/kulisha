<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Notification extends JsonResource
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
            'color' => $this->color,
            'icon_font' => $this->icon_font,
            'icon_svg' => $this->icon_svg,
            'image_url' => !empty($this->image_url) ? getWebURL() . '/storage/' . $this->image_url : null,
            'type' => Type::make($this->type),
            'status' => Status::make($this->status),
            'from' => User::make($this->from_user),
            'to' => User::make($this->to_user),
            'post' => Post::make($this->post),
            'event' => Event::make($this->event),
            'community' => Community::make($this->community),
            'message' => Message::make($this->message),
            'team' => Team::make($this->team),
            'reaction' => Reaction::make($this->reaction),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_ago' => timeAgo($this->created_at->format('Y-m-d H:i:s')),
            'updated_at_ago' => timeAgo($this->updated_at->format('Y-m-d H:i:s'))
        ];
    }
}
