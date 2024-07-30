<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Post extends JsonResource
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
            'post_url' => $this->post_url,
            'post_title' => $this->post_title,
            'post_content' => $this->post_content,
            'shared_post_id' => $this->shared_post_id,
            'price' => $this->price,
            'currency' => $this->currency,
            'quantity' => $this->quantity,
            'answered_for' => $this->answered_for,
            'status' => Status::make($this->status),
            'type' => Type::make($this->type),
            'category' => Category::make($this->category),
            'visibility' => Visibility::make($this->visibility),
            'user' => User::make($this->user),
            'coverage_area' => CoverageArea::make($this->coverage_area),
            'budget' => Budget::make($this->budget),
            'surveychoices' => Surveychoice::collection($this->surveychoices),
            'files' => File::collection($this->files),
            'restrictions' => Restriction::collection($this->restrictions),
            'keywords' => Keyword::collection($this->keywords),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at_explicit' => $this->created_at->format('Y') == date('Y') ? explicitDayMonth($this->created_at->format('Y-m-d H:i:s')) : explicitDate($this->created_at->format('Y-m-d H:i:s')),
            'updated_at_explicit' => $this->updated_at->format('Y') == date('Y') ? explicitDayMonth($this->updated_at->format('Y-m-d H:i:s')) : explicitDate($this->updated_at->format('Y-m-d H:i:s')),
            'created_at_ago' => timeAgo($this->created_at->format('Y-m-d H:i:s')),
            'updated_at_ago' => timeAgo($this->updated_at->format('Y-m-d H:i:s')),
            'community_id' => $this->community_id,
            'event_id' => $this->event_id
        ];
    }
}
