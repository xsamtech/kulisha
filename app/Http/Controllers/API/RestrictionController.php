<?php

namespace App\Http\Controllers\API;

use App\Models\Restriction;
use Illuminate\Http\Request;
use App\Http\Resources\Restriction as ResourcesRestriction;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class RestrictionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restrictions = Restriction::orderByDesc('created_at')->paginate(30);
        $count_restrictions = Restriction::count();

        return $this->handleResponse(ResourcesRestriction::collection($restrictions), __('notifications.find_all_restrictions_success'), $restrictions->lastPage(), $count_restrictions);
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
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
            'visibility_id' => $request->visibility_id
        ];

        // Validate required fields
        if (trim($inputs['user_id']) == null AND trim($inputs['post_id']) == null AND trim($inputs['visibility_id']) == null) {
            return $this->handleError(null, __('validation.custom.owner.required'), 400);
        }

        $restriction = Restriction::create($inputs);

        return $this->handleResponse(new ResourcesRestriction($restriction), __('notifications.create_restriction_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $restriction = Restriction::find($id);

        if (is_null($restriction)) {
            return $this->handleError(__('notifications.find_restriction_404'));
        }

        return $this->handleResponse(new ResourcesRestriction($restriction), __('notifications.find_restriction_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Restriction  $restriction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restriction $restriction)
    {
        // Get inputs
        $inputs = [
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
            'visibility_id' => $request->visibility_id
        ];

        if ($inputs['user_id'] != null) {
            $restriction->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['post_id'] != null) {
            $restriction->update([
                'post_id' => $inputs['post_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['visibility_id'] != null) {
            $restriction->update([
                'visibility_id' => $inputs['visibility_id'],
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(new ResourcesRestriction($restriction), __('notifications.update_restriction_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restriction  $restriction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restriction $restriction)
    {
        $restriction->delete();

        $restrictions = Restriction::orderByDesc('created_at')->paginate(30);
        $count_restrictions = Restriction::count();

        return $this->handleResponse(ResourcesRestriction::collection($restrictions), __('notifications.delete_restriction_success'), $restrictions->lastPage(), $count_restrictions);
    }
}
