<?php

namespace App\Http\Controllers\API;

use App\Models\Group;
use App\Models\Type;
use Illuminate\Http\Request;
use App\Http\Resources\Type as ResourcesType;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class TypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = Type::all();

        return $this->handleResponse(ResourcesType::collection($types), __('notifications.find_all_types_success'));
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
            'type_name' => [
                'af' => $request->type_name_af,
                'de' => $request->type_name_de,
                'ar' => $request->type_name_ar,
                'zh' => $request->type_name_zh,
                'en' => $request->type_name_en,
                'es' => $request->type_name_es,
                'fr' => $request->type_name_fr,
                'it' => $request->type_name_it,
                'ja' => $request->type_name_ja,
                'ru' => $request->type_name_ru,
                'sw' => $request->type_name_sw,
                'tr' => $request->type_name_tr,
                'cs' => $request->type_name_cs,
                'eo' => $request->type_name_eo
            ],
            'type_description' => [
                'af' => $request->type_description_af,
                'de' => $request->type_description_de,
                'ar' => $request->type_description_ar,
                'zh' => $request->type_description_zh,
                'en' => $request->type_description_en,
                'es' => $request->type_description_es,
                'fr' => $request->type_description_fr,
                'it' => $request->type_description_it,
                'ja' => $request->type_description_ja,
                'ru' => $request->type_description_ru,
                'sw' => $request->type_description_sw,
                'tr' => $request->type_description_tr,
                'cs' => $request->type_description_cs,
                'eo' => $request->type_description_eo
            ],
            'alias' => $request->alias,
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'group_id' => $request->group_id
        ];
        // Select all group types to check unique constraint
        $types = Type::where('group_id', $inputs['group_id'])->get();

        // Validate required fields
        if ($inputs['type_name'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['type_name'], __('validation.required', ['field_name' => __('miscellaneous.admin.group.type.data.type_name')]), 400);
        }

        if (!is_numeric($inputs['group_id']) OR trim($inputs['group_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['group_id'], __('miscellaneous.admin.group.choose_group'), 400);
        }

        // Check if type name already exists
        foreach ($types as $another_type):
            if ($another_type->type_name == $inputs['type_name']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['type_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $type = Type::create($inputs);

        return $this->handleResponse(new ResourcesType($type), __('notifications.create_type_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type = Type::find($id);

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        return $this->handleResponse(new ResourcesType($type), __('notifications.find_type_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Type $type)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'type_name' => [
                'af' => $request->type_name_af,
                'de' => $request->type_name_de,
                'ar' => $request->type_name_ar,
                'zh' => $request->type_name_zh,
                'en' => $request->type_name_en,
                'es' => $request->type_name_es,
                'fr' => $request->type_name_fr,
                'it' => $request->type_name_it,
                'ja' => $request->type_name_ja,
                'ru' => $request->type_name_ru,
                'sw' => $request->type_name_sw,
                'tr' => $request->type_name_tr,
                'cs' => $request->type_name_cs,
                'eo' => $request->type_name_eo
            ],
            'type_description' => [
                'af' => $request->type_description_af,
                'de' => $request->type_description_de,
                'ar' => $request->type_description_ar,
                'zh' => $request->type_description_zh,
                'en' => $request->type_description_en,
                'es' => $request->type_description_es,
                'fr' => $request->type_description_fr,
                'it' => $request->type_description_it,
                'ja' => $request->type_description_ja,
                'ru' => $request->type_description_ru,
                'sw' => $request->type_description_sw,
                'tr' => $request->type_description_tr,
                'cs' => $request->type_description_cs,
                'eo' => $request->type_description_eo
            ],
            'alias' => $request->alias,
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'group_id' => $request->group_id
        ];
        // Select all group types and specific type to check unique constraint
        $types = Type::where('group_id', $inputs['group_id'])->get();
        $current_type = Type::find($inputs['id']);

        if ($inputs['type_name'] != null) {
            foreach ($types as $another_type):
                if ($current_type->type_name != $inputs['type_name']) {
                    if ($another_type->type_name == $inputs['type_name']) {
                        return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['type_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $type->update([
                'type_name' => $inputs['type_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['type_description'] != null) {
            $type->update([
                'type_description' => $inputs['type_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['alias'] != null) {
            $type->update([
                'alias' => $inputs['alias'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['color'] != null) {
            $type->update([
                'color' => $inputs['color'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $type->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $type->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['image_url'] != null) {
            $type->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['group_id'] != null) {
            $type->update([
                'group_id' => $request->group_id,
                'updated_at' => now(),
            ]);
        }

        $type->update($inputs);

        return $this->handleResponse(new ResourcesType($type), __('notifications.update_type_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Type $type)
    {
        $type->delete();

        $types = Type::all();

        return $this->handleResponse(ResourcesType::collection($types), __('notifications.delete_type_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a type by its name.
     *
     * @param  string $locale
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($locale, $data)
    {
        $type = Type::where('type_name->' . $locale, $data)->first();

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        return $this->handleResponse(new ResourcesType($type), __('notifications.find_type_success'));
    }

    /**
     * Find all types by group.
     *
     * @param  string $locale
     * @param  string $group_name
     * @return \Illuminate\Http\Response
     */
    public function findByGroup($locale, $group_name)
    {
        $group = Group::where('group_name->' . $locale, $group_name)->first();

        if (is_null($group)) {
            return $this->handleError(__('notifications.find_group_404'));
        }

        $types = Type::where('group_id', $group->id)->get();

        return $this->handleResponse(ResourcesType::collection($types), __('notifications.find_all_types_success'));
    }
}
