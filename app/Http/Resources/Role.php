<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Role extends JsonResource
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
            'role_name' => $this->role_name,
            'role_name_fr' => $this->getTranslation('role_name', 'fr'),
            'role_name_en' => $this->getTranslation('role_name', 'en'),
            'role_name_ln' => $this->getTranslation('role_name', 'ln'),
            'role_description' => $this->role_description,
            'role_description_fr' => $this->getTranslation('role_description', 'fr'),
            'role_description_en' => $this->getTranslation('role_description', 'en'),
            'role_description_ln' => $this->getTranslation('role_description', 'ln'),
            'color' => $this->color,
            'icon_font' => $this->icon_font,
            'icon_svg' => $this->icon_svg,
            'image_url' => !empty($this->image_url) ? getWebURL() . '/' . $this->image_url : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
