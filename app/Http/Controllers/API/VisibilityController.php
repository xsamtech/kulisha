<?php

namespace App\Http\Controllers\API;

use App\Models\Visibility;
use Illuminate\Http\Request;
use App\Http\Resources\Visibility as ResourcesVisibility;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class VisibilityController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $visibilities = Visibility::all();

        return $this->handleResponse(ResourcesVisibility::collection($visibilities), __('notifications.find_all_visibilities_success'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get inputs
        $inputs = [
            'visibility_name' => [
                'af' => $request->visibility_name_af,
                'de' => $request->visibility_name_de,
                'ar' => $request->visibility_name_ar,
                'zh' => $request->visibility_name_zh,
                'en' => $request->visibility_name_en,
                'es' => $request->visibility_name_es,
                'fr' => $request->visibility_name_fr,
                'it' => $request->visibility_name_it,
                'ja' => $request->visibility_name_ja,
                'ru' => $request->visibility_name_ru,
                'sw' => $request->visibility_name_sw,
                'tr' => $request->visibility_name_tr,
                'cs' => $request->visibility_name_cs,
                'eo' => $request->visibility_name_eo
            ],
            'visibility_description' => [
                'af' => $request->visibility_description_af,
                'de' => $request->visibility_description_de,
                'ar' => $request->visibility_description_ar,
                'zh' => $request->visibility_description_zh,
                'en' => $request->visibility_description_en,
                'es' => $request->visibility_description_es,
                'fr' => $request->visibility_description_fr,
                'it' => $request->visibility_description_it,
                'ja' => $request->visibility_description_ja,
                'ru' => $request->visibility_description_ru,
                'sw' => $request->visibility_description_sw,
                'tr' => $request->visibility_description_tr,
                'cs' => $request->visibility_description_cs,
                'eo' => $request->visibility_description_eo
            ],
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url
        ];
        // Select all visibilities to check unique constraint
        $visibilities = Visibility::all();

        // Validate required fields
        if ($inputs['visibility_name'] == null) {
            return $this->handleError($inputs['visibility_name'], __('validation.required'), 400);
        }

        // Check if visibility name already exists
        foreach ($visibilities as $another_visibility):
            if ($another_visibility->visibility_name == $inputs['visibility_name']) {
                return $this->handleError($inputs['visibility_name'], __('validation.custom.visibility_name.exists'), 400);
            }
        endforeach;

        $visibility = Visibility::create($inputs);

        return $this->handleResponse(new ResourcesVisibility($visibility), __('notifications.create_visibility_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $visibility = Visibility::find($id);

        if (is_null($visibility)) {
            return $this->handleError(__('notifications.find_visibility_404'));
        }

        return $this->handleResponse(new ResourcesVisibility($visibility), __('notifications.find_visibility_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Visibility  $visibility
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Visibility $visibility)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'visibility_name' => [
                'af' => $request->visibility_name_af,
                'de' => $request->visibility_name_de,
                'ar' => $request->visibility_name_ar,
                'zh' => $request->visibility_name_zh,
                'en' => $request->visibility_name_en,
                'es' => $request->visibility_name_es,
                'fr' => $request->visibility_name_fr,
                'it' => $request->visibility_name_it,
                'ja' => $request->visibility_name_ja,
                'ru' => $request->visibility_name_ru,
                'sw' => $request->visibility_name_sw,
                'tr' => $request->visibility_name_tr,
                'cs' => $request->visibility_name_cs,
                'eo' => $request->visibility_name_eo
            ],
            'visibility_description' => [
                'af' => $request->visibility_description_af,
                'de' => $request->visibility_description_de,
                'ar' => $request->visibility_description_ar,
                'zh' => $request->visibility_description_zh,
                'en' => $request->visibility_description_en,
                'es' => $request->visibility_description_es,
                'fr' => $request->visibility_description_fr,
                'it' => $request->visibility_description_it,
                'ja' => $request->visibility_description_ja,
                'ru' => $request->visibility_description_ru,
                'sw' => $request->visibility_description_sw,
                'tr' => $request->visibility_description_tr,
                'cs' => $request->visibility_description_cs,
                'eo' => $request->visibility_description_eo
            ],
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url
        ];
        // Select all visibilities and specific visibility to check unique constraint
        $visibilities = Visibility::all();
        $current_visibility = Visibility::find($inputs['id']);

        if ($inputs['visibility_name'] != null) {
            foreach ($visibilities as $another_visibility):
                if ($current_visibility->visibility_name != $inputs['visibility_name']) {
                    if ($another_visibility->visibility_name == $inputs['visibility_name']) {
                        return $this->handleError($inputs['visibility_name'], __('validation.custom.visibility_name.exists'), 400);
                    }
                }
            endforeach;

            $visibility->update([
                'visibility_name' => $inputs['visibility_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['visibility_description'] != null) {
            $visibility->update([
                'visibility_description' => $inputs['visibility_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['color'] != null) {
            $visibility->update([
                'color' => $inputs['color'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $visibility->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $visibility->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['image_url'] != null) {
            $visibility->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesVisibility($visibility), __('notifications.update_visibility_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Visibility  $visibility
     * @return \Illuminate\Http\Response
     */
    public function destroy(Visibility $visibility)
    {
        $visibility->delete();

        $visibilities = Visibility::all();

        return $this->handleResponse(ResourcesVisibility::collection($visibilities), __('notifications.delete_visibility_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a visibility by its name.
     *
     * @param  string $locale
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($locale, $data)
    {
        $visibility = Visibility::where('visibility_name->' . $locale, $data)->first();

        if (is_null($visibility)) {
            return $this->handleError(__('notifications.find_visibility_404'));
        }

        return $this->handleResponse(new ResourcesVisibility($visibility), __('notifications.find_visibility_success'));
    }
}
