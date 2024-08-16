<?php

namespace App\Http\Controllers\API;

use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use App\Http\Resources\PersonalAccessToken as ResourcesPersonalAccessToken;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class PersonalAccessTokenController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $personal_access_tokens = PersonalAccessToken::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesPersonalAccessToken::collection($personal_access_tokens), __('notifications.find_all_personal_access_tokens_success'));
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
            'tokenable_type' => $request->tokenable_type,
            'tokenable_id' => $request->tokenable_id,
            'name' => $request->name,
            'token' => $request->token,
            'abilities' => $request->abilities,
            'last_used_at' => $request->last_used_at,
            'expires_at' => $request->expires_at
        ];
        // Select all personal access tokens to check unique constraint
        $personal_access_tokens = PersonalAccessToken::all();

        // Validate required fields
        if (trim($inputs['token']) == null) {
            return $this->handleError($inputs['token'], __('validation.required'), 400);
        }

        // Check if token already exists
        foreach ($personal_access_tokens as $another_personal_access_token):
            if ($another_personal_access_token->token == $inputs['token']) {
                return $this->handleError($inputs['token'], __('validation.custom.token.exists'), 400);
            }
        endforeach;

        $personal_access_token = PersonalAccessToken::create($inputs);

        return $this->handleResponse(new ResourcesPersonalAccessToken($personal_access_token), __('notifications.create_personal_access_token_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $personal_access_token = PersonalAccessToken::find($id);

        if (is_null($personal_access_token)) {
            return $this->handleError(__('notifications.find_personal_access_token_404'));
        }

        return $this->handleResponse(new ResourcesPersonalAccessToken($personal_access_token), __('notifications.find_personal_access_token_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PersonalAccessToken $personal_access_token)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'tokenable_type' => $request->tokenable_type,
            'tokenable_id' => $request->tokenable_id,
            'name' => $request->name,
            'token' => $request->token,
            'abilities' => $request->abilities,
            'last_used_at' => $request->last_used_at,
            'expires_at' => $request->expires_at
        ];
        // Select all personal access tokens to check unique constraint
        $personal_access_tokens = PersonalAccessToken::all();
        $current_personal_access_token = PersonalAccessToken::find($inputs['id']);

        if ($inputs['tokenable_type'] != null) {
            $personal_access_token->update([
                'tokenable_type' => $request->tokenable_type,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['tokenable_id'] != null) {
            $personal_access_token->update([
                'tokenable_id' => $request->tokenable_id,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['name'] != null) {
            $personal_access_token->update([
                'name' => $request->name,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['token'] != null) {
            foreach ($personal_access_tokens as $another_personal_access_token):
                if ($current_personal_access_token->token != $inputs['token']) {
                    if ($another_personal_access_token->token == $inputs['token']) {
                        return $this->handleError($inputs['token'], __('validation.custom.token.exists'), 400);
                    }
                }
            endforeach;

            $personal_access_token->update([
                'token' => $request->token,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['abilities'] != null) {
            $personal_access_token->update([
                'abilities' => $request->abilities,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['last_used_at'] != null) {
            $personal_access_token->update([
                'last_used_at' => $request->last_used_at,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['expires_at'] != null) {
            $personal_access_token->update([
                'expires_at' => $request->expires_at,
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesPersonalAccessToken($personal_access_token), __('notifications.update_personal_access_token_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PersonalAccessToken  $personal_access_token
     * @return \Illuminate\Http\Response
     */
    public function destroy(PersonalAccessToken $personal_access_token)
    {
        $personal_access_token->delete();

        $personal_access_tokens = PersonalAccessToken::all();

        return $this->handleResponse(ResourcesPersonalAccessToken::collection($personal_access_tokens), __('notifications.delete_personal_access_token_success'));
    }
}
