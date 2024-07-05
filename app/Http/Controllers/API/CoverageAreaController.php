<?php

namespace App\Http\Controllers\API;

use App\Models\CoverageArea;
use Illuminate\Http\Request;
use App\Http\Resources\CoverageArea as ResourcesCoverageArea;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class CoverageAreaController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coverage_areas = CoverageArea::all();

        return $this->handleResponse(ResourcesCoverageArea::collection($coverage_areas), __('notifications.find_all_coverage_areas_success'));
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
            'area_name' => [
                'af' => $request->area_name_af,
                'de' => $request->area_name_de,
                'ar' => $request->area_name_ar,
                'zh' => $request->area_name_zh,
                'en' => $request->area_name_en,
                'es' => $request->area_name_es,
                'fr' => $request->area_name_fr,
                'it' => $request->area_name_it,
                'ja' => $request->area_name_ja,
                'ru' => $request->area_name_ru,
                'sw' => $request->area_name_sw,
                'tr' => $request->area_name_tr,
                'cs' => $request->area_name_cs,
                'eo' => $request->area_name_eo
            ],
            'area_description' => [
                'af' => $request->area_description_af,
                'de' => $request->area_description_de,
                'ar' => $request->area_description_ar,
                'zh' => $request->area_description_zh,
                'en' => $request->area_description_en,
                'es' => $request->area_description_es,
                'fr' => $request->area_description_fr,
                'it' => $request->area_description_it,
                'ja' => $request->area_description_ja,
                'ru' => $request->area_description_ru,
                'sw' => $request->area_description_sw,
                'tr' => $request->area_description_tr,
                'cs' => $request->area_description_cs,
                'eo' => $request->area_description_eo
            ],
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'percentage' => $request->percentage
        ];
        // Select all areas to check unique constraint
        $areas = CoverageArea::all();

        // Validate required fields
        if ($inputs['area_name'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['area_name'], __('validation.required', ['field_name' => __('miscellaneous.admin.miscellaneous.coverage_area.data.area_name')]), 400);
        }

        // Check if area name already exists
        foreach ($areas as $another_area):
            if ($another_area->area_name == $inputs['area_name']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['area_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $area = CoverageArea::create($inputs);

        return $this->handleResponse(new ResourcesCoverageArea($area), __('notifications.create_area_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coverage_area = CoverageArea::find($id);

        if (is_null($coverage_area)) {
            return $this->handleError(__('notifications.find_coverage_area_404'));
        }

        return $this->handleResponse(new ResourcesCoverageArea($coverage_area), __('notifications.find_coverage_area_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CoverageArea  $coverage_area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CoverageArea $coverage_area)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'area_name' => [
                'af' => $request->area_name_af,
                'de' => $request->area_name_de,
                'ar' => $request->area_name_ar,
                'zh' => $request->area_name_zh,
                'en' => $request->area_name_en,
                'es' => $request->area_name_es,
                'fr' => $request->area_name_fr,
                'it' => $request->area_name_it,
                'ja' => $request->area_name_ja,
                'ru' => $request->area_name_ru,
                'sw' => $request->area_name_sw,
                'tr' => $request->area_name_tr,
                'cs' => $request->area_name_cs,
                'eo' => $request->area_name_eo
            ],
            'area_description' => [
                'af' => $request->area_description_af,
                'de' => $request->area_description_de,
                'ar' => $request->area_description_ar,
                'zh' => $request->area_description_zh,
                'en' => $request->area_description_en,
                'es' => $request->area_description_es,
                'fr' => $request->area_description_fr,
                'it' => $request->area_description_it,
                'ja' => $request->area_description_ja,
                'ru' => $request->area_description_ru,
                'sw' => $request->area_description_sw,
                'tr' => $request->area_description_tr,
                'cs' => $request->area_description_cs,
                'eo' => $request->area_description_eo
            ],
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'percentage' => $request->percentage
        ];
        // Select all areas and specific area to check unique constraint
        $coverage_areas = CoverageArea::all();
        $current_coverage_area = CoverageArea::find($inputs['id']);

        if ($inputs['area_name'] != null) {
            foreach ($coverage_areas as $another_area):
                if ($current_coverage_area->area_name != $inputs['area_name']) {
                    if ($another_area->area_name == $inputs['area_name']) {
                        return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['area_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $coverage_area->update([
                'area_name' => $inputs['area_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['area_description'] != null) {
            $coverage_area->update([
                'area_description' => $inputs['area_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['color'] != null) {
            $coverage_area->update([
                'color' => $inputs['color'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $coverage_area->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $coverage_area->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['image_url'] != null) {
            $coverage_area->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['percentage'] != null) {
            $coverage_area->update([
                'percentage' => $inputs['percentage'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesCoverageArea($coverage_area), __('notifications.update_coverage_area_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CoverageArea  $coverage_area
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoverageArea $coverage_area)
    {
        $coverage_area->delete();

        $coverage_areas = CoverageArea::all();

        return $this->handleResponse(ResourcesCoverageArea::collection($coverage_areas), __('notifications.delete_coverage_area_success'));
    }
}
