<?php

namespace App\Http\Controllers\API;

use App\Models\Group;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Resources\Status as ResourcesStatus;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class StatusController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = Status::all();

        return $this->handleResponse(ResourcesStatus::collection($statuses), __('notifications.find_all_statuses_success'));
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
            'status_name' => [
                'af' => $request->status_name_af,
                'de' => $request->status_name_de,
                'ar' => $request->status_name_ar,
                'zh' => $request->status_name_zh,
                'en' => $request->status_name_en,
                'es' => $request->status_name_es,
                'fr' => $request->status_name_fr,
                'it' => $request->status_name_it,
                'ja' => $request->status_name_ja,
                'ru' => $request->status_name_ru,
                'sw' => $request->status_name_sw,
                'tr' => $request->status_name_tr,
                'cs' => $request->status_name_cs,
                'eo' => $request->status_name_eo
            ],
            'status_description' => [
                'af' => $request->status_description_af,
                'de' => $request->status_description_de,
                'ar' => $request->status_description_ar,
                'zh' => $request->status_description_zh,
                'en' => $request->status_description_en,
                'es' => $request->status_description_es,
                'fr' => $request->status_description_fr,
                'it' => $request->status_description_it,
                'ja' => $request->status_description_ja,
                'ru' => $request->status_description_ru,
                'sw' => $request->status_description_sw,
                'tr' => $request->status_description_tr,
                'cs' => $request->status_description_cs,
                'eo' => $request->status_description_eo
            ],
            'alias' => $request->alias,
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'group_id' => $request->group_id
        ];
        // Select all group statuses to check unique constraint
        $statuses = Status::where('group_id', $inputs['group_id'])->get();

        // Validate required fields
        if ($inputs['status_name'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['status_name'], __('validation.required', ['field_name' => __('miscellaneous.admin.group.status.data.status_name')]), 400);
        }

        if (!is_numeric($inputs['group_id']) OR trim($inputs['group_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['group_id'], __('miscellaneous.admin.group.choose_group'), 400);
        }

        // Check if status name already exists
        foreach ($statuses as $another_status):
            if ($another_status->status_name == $inputs['status_name']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['status_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $status = Status::create($inputs);

        return $this->handleResponse(new ResourcesStatus($status), __('notifications.create_status_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $status = Status::find($id);

        if (is_null($status)) {
            return $this->handleError(__('notifications.find_status_404'));
        }

        return $this->handleResponse(new ResourcesStatus($status), __('notifications.find_status_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Status $status)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'status_name' => [
                'af' => $request->status_name_af,
                'de' => $request->status_name_de,
                'ar' => $request->status_name_ar,
                'zh' => $request->status_name_zh,
                'en' => $request->status_name_en,
                'es' => $request->status_name_es,
                'fr' => $request->status_name_fr,
                'it' => $request->status_name_it,
                'ja' => $request->status_name_ja,
                'ru' => $request->status_name_ru,
                'sw' => $request->status_name_sw,
                'tr' => $request->status_name_tr,
                'cs' => $request->status_name_cs,
                'eo' => $request->status_name_eo
            ],
            'status_description' => [
                'af' => $request->status_description_af,
                'de' => $request->status_description_de,
                'ar' => $request->status_description_ar,
                'zh' => $request->status_description_zh,
                'en' => $request->status_description_en,
                'es' => $request->status_description_es,
                'fr' => $request->status_description_fr,
                'it' => $request->status_description_it,
                'ja' => $request->status_description_ja,
                'ru' => $request->status_description_ru,
                'sw' => $request->status_description_sw,
                'tr' => $request->status_description_tr,
                'cs' => $request->status_description_cs,
                'eo' => $request->status_description_eo
            ],
            'alias' => $request->alias,
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'group_id' => $request->group_id
        ];
        // Select all statuses and specific status to check unique constraint
        $statuses = Status::where('group_id', $inputs['group_id'])->get();
        $current_status = Status::find($inputs['id']);

        if ($inputs['status_name'] != null) {
            foreach ($statuses as $another_status):
                if ($current_status->status_name != $inputs['status_name']) {
                    if ($another_status->status_name == $inputs['status_name']) {
                        return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['status_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $status->update([
                'status_name' => $inputs['status_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['status_description'] != null) {
            $status->update([
                'status_description' => $inputs['status_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['alias'] != null) {
            $status->update([
                'alias' => $inputs['alias'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['color'] != null) {
            $status->update([
                'color' => $inputs['color'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $status->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $status->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['image_url'] != null) {
            $status->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['group_id'] != null) {
            $status->update([
                'group_id' => $request->group_id,
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesStatus($status), __('notifications.update_status_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Http\Response
     */
    public function destroy(Status $status)
    {
        $status->delete();

        $statuses = Status::all();

        return $this->handleResponse(ResourcesStatus::collection($statuses), __('notifications.delete_status_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a status by its name.
     *
     * @param  string $locale
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($locale, $data)
    {
        $status = Status::where('status_name->' . $locale, $data)->first();

        if (is_null($status)) {
            return $this->handleError(__('notifications.find_status_404'));
        }

        return $this->handleResponse(new ResourcesStatus($status), __('notifications.find_status_success'));
    }

    /**
     * Find all statuses by group.
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

        $statuses = Status::where('group_id', $group->id)->get();

        return $this->handleResponse(ResourcesStatus::collection($statuses), __('notifications.find_all_statuses_success'));
    }
}
