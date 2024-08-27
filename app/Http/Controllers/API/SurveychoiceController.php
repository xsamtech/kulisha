<?php

namespace App\Http\Controllers\API;

use App\Models\Surveychoice;
use Illuminate\Http\Request;
use App\Http\Resources\Surveychoice as ResourcesSurveychoice;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class SurveychoiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $surveychoices = Surveychoice::orderByDesc('created_at')->paginate(30);
        $count_surveychoices = Surveychoice::count();

        return $this->handleResponse(ResourcesSurveychoice::collection($surveychoices), __('notifications.find_all_surveychoices_success'), $surveychoices->lastPage(), $count_surveychoices);
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
            'choice_content' => $request->choice_content,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'post_id' => $request->post_id
        ];

        // Validate required fields
        if ($inputs['choice_content'] == null OR $inputs['choice_content'] == ' ') {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['choice_content'], __('validation.required', ['field_name' => __('miscellaneous.public.home.posts.type.poll.choice.content')]), 400);
        }

        if (!is_numeric($inputs['post_id']) OR trim($inputs['post_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['post_id'], __('miscellaneous.public.home.posts.type.poll.post_error'), 400);
        }

        $surveychoice = Surveychoice::create($inputs);

        return $this->handleResponse(new ResourcesSurveychoice($surveychoice), __('notifications.create_surveychoice_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $surveychoice = Surveychoice::find($id);

        if (is_null($surveychoice)) {
            return $this->handleError(__('notifications.find_surveychoice_404'));
        }

        return $this->handleResponse(new ResourcesSurveychoice($surveychoice), __('notifications.find_surveychoice_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Surveychoice  $surveychoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Surveychoice $surveychoice)
    {
        // Get inputs
        $inputs = [
            'choice_content' => $request->choice_content,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'post_id' => $request->post_id
        ];

        if ($inputs['choice_content'] != null) {
            $surveychoice->update([
                'choice_content' => $inputs['choice_content'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $surveychoice->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $surveychoice->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['image_url'] != null) {
            $surveychoice->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['post_id'] != null) {
            $surveychoice->update([
                'post_id' => $inputs['post_id'],
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(new ResourcesSurveychoice($surveychoice), __('notifications.update_surveychoice_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Surveychoice  $surveychoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Surveychoice $surveychoice)
    {
        $surveychoice->delete();

        $surveychoices = Surveychoice::all();

        return $this->handleResponse(ResourcesSurveychoice::collection($surveychoices), __('notifications.delete_surveychoice_success'));
    }
}
