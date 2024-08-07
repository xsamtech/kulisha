<?php

namespace App\Http\Controllers\API;

use App\Models\Field;
use Illuminate\Http\Request;
use App\Http\Resources\Field as ResourcesField;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class FieldController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fields = Field::all();

        return $this->handleResponse(ResourcesField::collection($fields), __('notifications.find_all_fields_success'));
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
            'field_name' => [
                'af' => $request->field_name_af,
                'de' => $request->field_name_de,
                'ar' => $request->field_name_ar,
                'zh' => $request->field_name_zh,
                'en' => $request->field_name_en,
                'es' => $request->field_name_es,
                'fr' => $request->field_name_fr,
                'it' => $request->field_name_it,
                'ja' => $request->field_name_ja,
                'ru' => $request->field_name_ru,
                'sw' => $request->field_name_sw,
                'tr' => $request->field_name_tr,
                'cs' => $request->field_name_cs,
                'eo' => $request->field_name_eo
            ],
            'field_description' => [
                'af' => $request->field_description_af,
                'de' => $request->field_description_de,
                'ar' => $request->field_description_ar,
                'zh' => $request->field_description_zh,
                'en' => $request->field_description_en,
                'es' => $request->field_description_es,
                'fr' => $request->field_description_fr,
                'it' => $request->field_description_it,
                'ja' => $request->field_description_ja,
                'ru' => $request->field_description_ru,
                'sw' => $request->field_description_sw,
                'tr' => $request->field_description_tr,
                'cs' => $request->field_description_cs,
                'eo' => $request->field_description_eo
            ],
            'alias' => $request->alias,
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url
        ];
        // Select all fields to check unique constraint
        $fields = Field::all();

        // Validate required fields
        if ($inputs['field_name'] == null OR $inputs['field_name'] == ' ') {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['field_name'], __('validation.required', ['field_name' => __('miscellaneous.admin.field.data.field_name')]), 400);
        }

        // Check if field name already exists
        foreach ($fields as $another_field):
            if ($another_field->field_name == $inputs['field_name']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['field_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $field = Field::create($inputs);

        return $this->handleResponse(new ResourcesField($field), __('notifications.create_field_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $field = Field::find($id);

        if (is_null($field)) {
            return $this->handleError(__('notifications.find_field_404'));
        }

        return $this->handleResponse(new ResourcesField($field), __('notifications.find_field_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Field  $field
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Field $field)
    {
        // Get inputs
        $inputs = [
            'field_name' => [
                'af' => $request->field_name_af,
                'de' => $request->field_name_de,
                'ar' => $request->field_name_ar,
                'zh' => $request->field_name_zh,
                'en' => $request->field_name_en,
                'es' => $request->field_name_es,
                'fr' => $request->field_name_fr,
                'it' => $request->field_name_it,
                'ja' => $request->field_name_ja,
                'ru' => $request->field_name_ru,
                'sw' => $request->field_name_sw,
                'tr' => $request->field_name_tr,
                'cs' => $request->field_name_cs,
                'eo' => $request->field_name_eo
            ],
            'field_description' => [
                'af' => $request->field_description_af,
                'de' => $request->field_description_de,
                'ar' => $request->field_description_ar,
                'zh' => $request->field_description_zh,
                'en' => $request->field_description_en,
                'es' => $request->field_description_es,
                'fr' => $request->field_description_fr,
                'it' => $request->field_description_it,
                'ja' => $request->field_description_ja,
                'ru' => $request->field_description_ru,
                'sw' => $request->field_description_sw,
                'tr' => $request->field_description_tr,
                'cs' => $request->field_description_cs,
                'eo' => $request->field_description_eo
            ],
            'alias' => $request->alias,
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url
        ];

        // Select all fields and specific field to check unique constraint
        $fields = Field::all();
        $current_field = Field::find($inputs['id']);

        if ($inputs['field_name'] != null) {
            foreach ($fields as $another_field):
                if ($current_field->field_name != $inputs['field_name']) {
                    if ($another_field->field_name == $inputs['field_name']) {
                        return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['field_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $field->update([
                'field_name' => $inputs['field_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['field_description'] != null) {
            $field->update([
                'field_description' => $inputs['field_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['alias'] != null) {
            $field->update([
                'alias' => $inputs['alias'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['color'] != null) {
            $field->update([
                'color' => $inputs['color'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $field->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $field->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['image_url'] != null) {
            $field->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesField($field), __('notifications.update_field_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Field  $field
     * @return \Illuminate\Http\Response
     */
    public function destroy(Field $field)
    {
        $field->delete();

        $fields = Field::all();

        return $this->handleResponse(ResourcesField::collection($fields), __('notifications.delete_field_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a field by its name.
     *
     * @param  string $locale
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($locale, $data)
    {
        $field = Field::where('field_name->' . $locale, $data)->first();

        if (is_null($field)) {
            return $this->handleError(__('notifications.find_field_404'));
        }

        return $this->handleResponse(new ResourcesField($field), __('notifications.find_field_success'));
    }
}
