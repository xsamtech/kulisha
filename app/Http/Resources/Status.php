<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Status extends JsonResource
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
            'status_name' => $this->status_name,
            'status_name_fr' => $this->getTranslation('status_name', 'fr'),
            'status_name_en' => $this->getTranslation('status_name', 'en'),
            'status_description' => $this->status_description,
            'status_description' => $this->getTranslation('status_description', 'fr'),
            'status_description' => $this->getTranslation('status_description', 'en'),
            'color' => $this->color,
            'icon_font' => $this->icon_font,
            'icon_svg' => $this->icon_svg,
            'image_url' => !empty($this->image_url) ? getWebURL() . '/storage/' . $this->image_url : null,
            'group' => Group::make($this->group),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'group_id' => $this->group_id
        ];
    }
}
