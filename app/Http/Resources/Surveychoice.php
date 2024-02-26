<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Surveychoice extends JsonResource
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
            'choice_content' => $this->choice_content,
            'color' => $this->color,
            'icon_font' => $this->icon_font,
            'icon_svg' => $this->icon_svg,
            'image_url' => !empty($this->image_url) ? getWebURL() . '/storage/' . $this->image_url : null,
            'users' => User::collection($this->users),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'post_id' => $this->post_id
        ];
    }
}
