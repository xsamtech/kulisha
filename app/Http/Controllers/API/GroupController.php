<?php

namespace App\Http\Controllers\API;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Resources\Group as ResourcesGroup;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class GroupController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::all();

        return $this->handleResponse(ResourcesGroup::collection($groups), __('notifications.find_all_groups_success'));
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
            'group_name' => [
                'af' => $request->group_name_af,
                'de' => $request->group_name_de,
                'ar' => $request->group_name_ar,
                'zh' => $request->group_name_zh,
                'en' => $request->group_name_en,
                'es' => $request->group_name_es,
                'fr' => $request->group_name_fr,
                'it' => $request->group_name_it,
                'ja' => $request->group_name_ja,
                'ru' => $request->group_name_ru,
                'sw' => $request->group_name_sw,
                'tr' => $request->group_name_tr,
                'cs' => $request->group_name_cs,
                'eo' => $request->group_name_eo
            ],
            'group_description' => [
                'af' => $request->group_description_af,
                'de' => $request->group_description_de,
                'ar' => $request->group_description_ar,
                'zh' => $request->group_description_zh,
                'en' => $request->group_description_en,
                'es' => $request->group_description_es,
                'fr' => $request->group_description_fr,
                'it' => $request->group_description_it,
                'ja' => $request->group_description_ja,
                'ru' => $request->group_description_ru,
                'sw' => $request->group_description_sw,
                'tr' => $request->group_description_tr,
                'cs' => $request->group_description_cs,
                'eo' => $request->group_description_eo
            ]
        ];
        // Select all groups to check unique constraint
        $groups = Group::all();

        // Validate required fields
        if ($inputs['group_name'] == null) {
            return $this->handleError($inputs['group_name'], __('validation.required', ['field_name' => __('miscellaneous.admin.group.data.group_name')]), 400);
        }

        // Check if group name already exists
        foreach ($groups as $another_group):
            if ($another_group->group_name == $inputs['group_name']) {
                return $this->handleError($inputs['group_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $group = Group::create($inputs);

        return $this->handleResponse(new ResourcesGroup($group), __('notifications.create_group_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::find($id);

        if (is_null($group)) {
            return $this->handleError(__('notifications.find_group_404'));
        }

        return $this->handleResponse(new ResourcesGroup($group), __('notifications.find_group_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'group_name' => [
                'af' => $request->group_name_af,
                'de' => $request->group_name_de,
                'ar' => $request->group_name_ar,
                'zh' => $request->group_name_zh,
                'en' => $request->group_name_en,
                'es' => $request->group_name_es,
                'fr' => $request->group_name_fr,
                'it' => $request->group_name_it,
                'ja' => $request->group_name_ja,
                'ru' => $request->group_name_ru,
                'sw' => $request->group_name_sw,
                'tr' => $request->group_name_tr,
                'cs' => $request->group_name_cs,
                'eo' => $request->group_name_eo
            ],
            'group_description' => [
                'af' => $request->group_description_af,
                'de' => $request->group_description_de,
                'ar' => $request->group_description_ar,
                'zh' => $request->group_description_zh,
                'en' => $request->group_description_en,
                'es' => $request->group_description_es,
                'fr' => $request->group_description_fr,
                'it' => $request->group_description_it,
                'ja' => $request->group_description_ja,
                'ru' => $request->group_description_ru,
                'sw' => $request->group_description_sw,
                'tr' => $request->group_description_tr,
                'cs' => $request->group_description_cs,
                'eo' => $request->group_description_eo
            ]
        ];
        // Select all groups and specific group to check unique constraint
        $groups = Group::all();
        $current_group = Group::find($inputs['id']);

        if ($inputs['group_name'] != null) {
            foreach ($groups as $another_group):
                if ($current_group->group_name != $inputs['group_name']) {
                    if ($another_group->group_name == $inputs['group_name']) {
                        return $this->handleError($inputs['group_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $group->update([
                'group_name' => $inputs['group_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['group_description'] != null) {
            $group->update([
                'group_description' => $inputs['group_description'],
                'updated_at' => now(),
            ]);
        }

        $group->update($inputs);

        return $this->handleResponse(new ResourcesGroup($group), __('notifications.update_group_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->delete();

        $groups = Group::all();

        return $this->handleResponse(ResourcesGroup::collection($groups), __('notifications.delete_group_success'));
    }
}
