<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Message extends JsonResource
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
            'message_content' => $this->message_content,
            'answered_for' => $this->answered_for,
            'seen_by' => $this->seen_by,
            'deleted_by' => $this->deleted_by,
            'type' => Type::make($this->type),
            'status' => Status::make($this->status),
            'user' => User::make($this->user),
            'files' => File::collection($this->files),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_ago' => timeAgo($this->created_at->format('Y-m-d H:i:s')),
            'updated_at_ago' => timeAgo($this->updated_at->format('Y-m-d H:i:s')),
            'addressee_community_id' => $this->addressee_community_id,
            'addressee_team_id' => $this->addressee_team_id,
            'addressee_user_id' => $this->addressee_user_id
        ];
    }
}
