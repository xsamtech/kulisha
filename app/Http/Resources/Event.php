<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Event extends JsonResource
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
            'event_title' => $this->event_title,
            'event_description' => $this->event_description,
            'cover_photo_path' => !empty($this->cover_photo_path) ? getWebURL() . '/storage/' . $this->cover_photo_path : null,
            'cover_coordinates' => $this->cover_coordinates,
            'users' => User::collection($this->users),
            'files' => File::collection($this->files),
            'posts' => Post::collection($this->posts),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user_id' => $this->user_id
        ];
    }
}
