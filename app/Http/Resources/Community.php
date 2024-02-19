<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Community extends JsonResource
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
            'community_name' => $this->community_name,
            'community_description' => $this->community_description,
            'cover_photo_path' => !empty($this->cover_photo_path) ? getWebURL() . '/storage/' . $this->cover_photo_path : null,
            'cover_coordinates' => $this->cover_coordinates,
            'type' => Type::make($this->type),
            'status' => Status::make($this->status),
            'owner' => User::make($this->user),
            'members' => User::collection($this->users),
            'posts' => Post::collection($this->posts),
            'messages' => Message::collection($this->messages),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
