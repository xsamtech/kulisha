<?php

namespace App\Http\Controllers\API;

use App\Models\Website;
use Illuminate\Http\Request;
use App\Http\Resources\Website as ResourcesWebsite;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class WebsiteController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $websites = Website::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesWebsite::collection($websites), __('notifications.find_all_websites_success'));
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
            'website_name' => $request->website_name,
            'website_icon' => $request->website_icon,
            'website_url' => $request->website_url,
            'type_id' => $request->type_id,
            'user_id' => $request->user_id
        ];
        // Select all user websites to check unique constraint
        $websites = Website::where('user_id', $inputs['user_id'])->get();

        // Validate required fields
        if (trim($inputs['user_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['user_id'], __('validation.required', ['field_name' => __('miscellaneous.choose_user')]), 400);
        }

        if (trim($inputs['website_url']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['website_url'], __('validation.required', ['field_name' => __('miscellaneous.website.url')]), 400);
        }

        // Check if website name already exists
        foreach ($websites as $another_website):
            if ($another_website->website_name == $inputs['website_name']) {
                return $this->handleError($inputs['website_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $website = Website::create($inputs);

        return $this->handleResponse(new ResourcesWebsite($website), __('notifications.create_website_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $website = Website::find($id);

        if (is_null($website)) {
            return $this->handleError(__('notifications.find_website_404'));
        }

        return $this->handleResponse(new ResourcesWebsite($website), __('notifications.find_website_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Website $website)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'website_name' => $request->website_name,
            'website_icon' => $request->website_icon,
            'website_url' => $request->website_url,
            'type_id' => $request->type_id,
            'user_id' => $request->user_id
        ];
        // Select all user websites and specific website to check unique constraint
        $websites = Website::where('user_id', $inputs['user_id'])->get();
        $current_website = Website::find($inputs['id']);

        if ($inputs['website_name'] != null) {
            foreach ($websites as $another_website):
                if ($current_website->website_name != $inputs['website_name']) {
                    if ($another_website->website_name == $inputs['website_name']) {
                        return $this->handleError($inputs['website_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $website->update([
                'website_name' => $inputs['website_name'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['website_icon'] != null) {
            $website->update([
                'website_icon' => $inputs['website_icon'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['website_url'] != null) {
            $website->update([
                'website_url' => $inputs['website_url'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['type_id'] != null) {
            $website->update([
                'type_id' => $inputs['type_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $website->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesWebsite($website), __('notifications.update_website_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\Response
     */
    public function destroy(Website $website)
    {
        $website->delete();

        $websites = Website::all();

        return $this->handleResponse(ResourcesWebsite::collection($websites), __('notifications.delete_website_success'));
    }
}
