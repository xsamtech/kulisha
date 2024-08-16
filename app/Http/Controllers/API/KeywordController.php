<?php

namespace App\Http\Controllers\API;

use App\Models\Keyword;
use Illuminate\Http\Request;
use App\Http\Resources\Keyword as ResourcesKeyword;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class KeywordController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keywords = Keyword::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesKeyword::collection($keywords), __('notifications.find_all_keywords_success'));
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
            'post_id' => $request->post_id
        ];
        // Select all user keywords and specific keyword to check unique constraint
        $keywords = Keyword::where('post_id', $inputs['post_id'])->get();

        // Validate required fields
        if (trim($inputs['keyword']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['keyword'], __('validation.required', ['field_name' => __('miscellaneous.public.home.posts.boost.keywords.label')]), 400);
        }

        if (trim($inputs['post_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['post_id'], __('miscellaneous.public.home.posts.boost.keywords.post_error'), 400);
        }

        foreach ($keywords as $another_keyword):
            if ($another_keyword->keyword != $inputs['keyword']) {
                $keyword = Keyword::create($inputs);

                return $this->handleResponse(new ResourcesKeyword($keyword), __('notifications.create_keyword_success'));
            }
        endforeach;
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $keyword = Keyword::find($id);

        if (is_null($keyword)) {
            return $this->handleError(__('notifications.find_keyword_404'));
        }

        return $this->handleResponse(new ResourcesKeyword($keyword), __('notifications.find_keyword_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Keyword  $keyword
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Keyword $keyword)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'keyword' => $request->keyword,
            'post_id' => $request->post_id
        ];
        // Select all user keywords and specific keyword to check unique constraint
        $keywords = Keyword::where('post_id', $inputs['post_id'])->get();
        $current_keyword = Keyword::find($inputs['id']);

        if ($inputs['keyword'] != null) {
            foreach ($keywords as $another_keyword):
                if ($current_keyword->keyword != $inputs['keyword']) {
                    if ($another_keyword->keyword != $inputs['keyword']) {
                        $keyword->update([
                            'keyword' => $inputs['keyword'],
                            'updated_at' => now(),
                        ]);
                    }
                }
            endforeach;
        }

        if ($inputs['post_id'] != null) {
            $keyword->update([
                'post_id' => $inputs['post_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesKeyword($keyword), __('notifications.update_keyword_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Keyword  $keyword
     * @return \Illuminate\Http\Response
     */
    public function destroy(Keyword $keyword)
    {
        $keyword->delete();

        $keywords = Keyword::all();

        return $this->handleResponse(ResourcesKeyword::collection($keywords), __('notifications.delete_keyword_success'));
    }
}
