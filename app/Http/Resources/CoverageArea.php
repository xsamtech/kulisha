<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class CoverageArea extends JsonResource
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
            'area_name' => $this->area_name,
            'area_name_fr' => $this->getTranslation('area_name', 'fr'),
            'area_name_en' => $this->getTranslation('area_name', 'en'),
            'area_name_ln' => $this->getTranslation('area_name', 'ln'),
            'area_description' => $this->area_description,
            'area_description_fr' => $this->getTranslation('area_description', 'fr'),
            'area_description_en' => $this->getTranslation('area_description', 'en'),
            'area_description_ln' => $this->getTranslation('area_description', 'ln'),
            'color' => $this->color,
            'icon_font' => $this->icon_font,
            'icon_svg' => $this->icon_svg,
            'image_url' => !empty($this->image_url) ? getWebURL() . '/' . $this->image_url : null,
            'percentage' => $this->percentage,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
