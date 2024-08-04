<?php

namespace App\Http\Controllers\API;

use App\Models\Hashtag;
use Illuminate\Http\Request;
use App\Http\Resources\Hashtag as ResourcesHashtag;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class HashtagController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hashtags = Hashtag::orderByDesc('created_at')->paginate(100);
        $count_hashtags = Hashtag::count();

        return $this->handleResponse(ResourcesHashtag::collection($hashtags), __('notifications.find_all_hashtags_success'), $hashtags->lastPage(), $count_hashtags);
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
            'keyword' => $request->keyword,
        ];
        // Select all hashtags to check unique constraint
        $hashtags = Hashtag::all();

        // Validate required fields
        if (trim($inputs['keyword']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['keyword'], __('validation.required', ['field_name' => __('miscellaneous.public.home.trends.keyword')]), 400);
        }

        // Check if keyword already exists
        foreach ($hashtags as $another_hashtag):
            if ($another_hashtag->keyword == $inputs['keyword']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['keyword'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $hashtag = Hashtag::create($inputs);

        return $this->handleResponse(new ResourcesHashtag($hashtag), __('notifications.create_hashtag_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $hashtag = Hashtag::find($id);

        if (is_null($hashtag)) {
            return $this->handleError(__('notifications.find_hashtag_404'));
        }

        return $this->handleResponse(new ResourcesHashtag($hashtag), __('notifications.find_hashtag_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hashtag  $hashtag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hashtag $hashtag)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'keyword' => $request->keyword,
            'updated_at' => now()
        ];
        // Select all hashtags and specific hashtag to check unique constraint
        $hashtags = Hashtag::all();
        $current_hashtag = Hashtag::find($inputs['id']);

        if (trim($inputs['keyword']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['keyword'], __('validation.required', ['field_name' => __('miscellaneous.public.home.trends.keyword')]), 400);
        }

        // Check if keyword already exists
        foreach ($hashtags as $another_hashtag):
            if ($current_hashtag->keyword != $inputs['keyword']) {
                if ($another_hashtag->keyword == $inputs['keyword']) {
                    return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['keyword'], __('validation.custom.name.exists'), 400);
                }
            }
        endforeach;

        $hashtag->update($inputs);

        return $this->handleResponse(new ResourcesHashtag($hashtag), __('notifications.update_hashtag_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hashtag  $hashtag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hashtag $hashtag)
    {
        $hashtag->delete();

        $hashtags = Hashtag::all();

        return $this->handleResponse(ResourcesHashtag::collection($hashtags), __('notifications.delete_hashtag_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Find the most cited hashtags in posts.
     *
     * @param  int  $year
     * @return \Illuminate\Http\Response
     */
    public function trends($year)
    {
        $hashtags = Hashtag::whereHas('posts', function ($query) use ($year) { $query->whereYear('hashtag_post.created_at', '=', $year); })->distinct()->orderByDesc('created_at')->get();
        $count_hashtags = Hashtag::whereHas('posts', function ($query) use ($year) { $query->whereYear('hashtag_post.created_at', '=', $year); })->distinct()->orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesHashtag::collection($hashtags), __('notifications.find_all_hashtags_success'), null, $count_hashtags);
    }
}
