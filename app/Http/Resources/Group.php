<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Group extends JsonResource
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
            'group_name' => $this->group_name,
            'group_name_fr' => $this->getTranslation('group_name', 'fr'),
            'group_name_en' => $this->getTranslation('group_name', 'en'),
            'group_description' => $this->group_description,
            'group_description_fr' => $this->getTranslation('group_description', 'fr'),
            'group_description_en' => $this->getTranslation('group_description', 'en'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
