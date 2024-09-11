<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Reaction extends JsonResource
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
            'reaction_name' => $this->reaction_name,
            'reaction_name_fr' => $this->getTranslation('reaction_name', 'fr'),
            'reaction_name_en' => $this->getTranslation('reaction_name', 'en'),
            'reaction_name_ln' => $this->getTranslation('reaction_name', 'ln'),
            'reaction_description' => $this->reaction_description,
            'reaction_description_fr' => $this->getTranslation('reaction_description', 'fr'),
            'reaction_description_en' => $this->getTranslation('reaction_description', 'en'),
            'reaction_description_ln' => $this->getTranslation('reaction_description', 'ln'),
            'alias' => $this->alias,
            'color' => $this->color,
            'icon_font' => $this->icon_font,
            'icon_svg' => $this->icon_svg,
            'image_url' => !empty($this->image_url) ? getWebURL() . '/' . $this->image_url : null,
            'group' => Group::make($this->group),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'group_id' => $this->group_id
        ];
    }
}
