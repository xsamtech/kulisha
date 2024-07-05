<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Resources\Role as ResourcesRole;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        return $this->handleResponse(ResourcesRole::collection($roles), __('notifications.find_all_roles_success'));
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
            'role_name' => [
                'af' => $request->role_name_af,
                'de' => $request->role_name_de,
                'ar' => $request->role_name_ar,
                'zh' => $request->role_name_zh,
                'en' => $request->role_name_en,
                'es' => $request->role_name_es,
                'fr' => $request->role_name_fr,
                'it' => $request->role_name_it,
                'ja' => $request->role_name_ja,
                'ru' => $request->role_name_ru,
                'sw' => $request->role_name_sw,
                'tr' => $request->role_name_tr,
                'cs' => $request->role_name_cs,
                'eo' => $request->role_name_eo
            ],
            'role_description' => [
                'af' => $request->role_description_af,
                'de' => $request->role_description_de,
                'ar' => $request->role_description_ar,
                'zh' => $request->role_description_zh,
                'en' => $request->role_description_en,
                'es' => $request->role_description_es,
                'fr' => $request->role_description_fr,
                'it' => $request->role_description_it,
                'ja' => $request->role_description_ja,
                'ru' => $request->role_description_ru,
                'sw' => $request->role_description_sw,
                'tr' => $request->role_description_tr,
                'cs' => $request->role_description_cs,
                'eo' => $request->role_description_eo
            ],
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url
        ];
        // Select all roles to check unique constraint
        $roles = Role::all();

        // Validate required fields
        if ($inputs['role_name'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['role_name'], __('validation.required', ['field_name' => __('miscellaneous.admin.miscellaneous.role.data.role_name')]), 400);
        }

        // Check if role name already exists
        foreach ($roles as $another_role):
            if ($another_role->role_name == $inputs['role_name']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['role_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $role = Role::create($inputs);

        return $this->handleResponse(new ResourcesRole($role), __('notifications.create_role_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (is_null($role)) {
            return $this->handleError(__('notifications.find_role_404'));
        }

        return $this->handleResponse(new ResourcesRole($role), __('notifications.find_role_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'role_name' => [
                'af' => $request->role_name_af,
                'de' => $request->role_name_de,
                'ar' => $request->role_name_ar,
                'zh' => $request->role_name_zh,
                'en' => $request->role_name_en,
                'es' => $request->role_name_es,
                'fr' => $request->role_name_fr,
                'it' => $request->role_name_it,
                'ja' => $request->role_name_ja,
                'ru' => $request->role_name_ru,
                'sw' => $request->role_name_sw,
                'tr' => $request->role_name_tr,
                'cs' => $request->role_name_cs,
                'eo' => $request->role_name_eo
            ],
            'role_description' => [
                'af' => $request->role_description_af,
                'de' => $request->role_description_de,
                'ar' => $request->role_description_ar,
                'zh' => $request->role_description_zh,
                'en' => $request->role_description_en,
                'es' => $request->role_description_es,
                'fr' => $request->role_description_fr,
                'it' => $request->role_description_it,
                'ja' => $request->role_description_ja,
                'ru' => $request->role_description_ru,
                'sw' => $request->role_description_sw,
                'tr' => $request->role_description_tr,
                'cs' => $request->role_description_cs,
                'eo' => $request->role_description_eo
            ],
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url
        ];
        // Select all roles and specific role to check unique constraint
        $roles = Role::all();
        $current_role = Role::find($inputs['id']);

        if ($inputs['role_name'] != null) {
            foreach ($roles as $another_role):
                if ($current_role->role_name != $inputs['role_name']) {
                    if ($another_role->role_name == $inputs['role_name']) {
                        return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['role_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $role->update([
                'role_name' => $inputs['role_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['role_description'] != null) {
            $role->update([
                'role_description' => $inputs['role_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['color'] != null) {
            $role->update([
                'color' => $inputs['color'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $role->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $role->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['image_url'] != null) {
            $role->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesRole($role), __('notifications.update_role_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        $roles = Role::all();

        return $this->handleResponse(ResourcesRole::collection($roles), __('notifications.delete_role_success'));
    }
}
