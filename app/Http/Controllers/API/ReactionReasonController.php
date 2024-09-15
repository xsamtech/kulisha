<?php

namespace App\Http\Controllers\API;

use App\Models\ReactionReason;
use Illuminate\Http\Request;
use App\Http\Resources\ReactionReason as ResourcesReactionReason;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class ReactionReasonController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reactions_reasons = ReactionReason::all();

        return $this->handleResponse(ResourcesReactionReason::collection($reactions_reasons), __('notifications.find_all_reactions_reasons_success'));
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
            'reason_content' => [
                'af' => $request->reason_content_af,
                'de' => $request->reason_content_de,
                'ar' => $request->reason_content_ar,
                'zh' => $request->reason_content_zh,
                'en' => $request->reason_content_en,
                'es' => $request->reason_content_es,
                'fr' => $request->reason_content_fr,
                'it' => $request->reason_content_it,
                'ja' => $request->reason_content_ja,
                'ln' => $request->reason_content_ln,
                'nl' => $request->reason_content_nl,
                'ru' => $request->reason_content_ru,
                'sw' => $request->reason_content_sw,
                'tr' => $request->reason_content_tr,
                'cs' => $request->reason_content_cs
            ],
            'report_count' => $request->report_count,
            'number_of_days' => $request->number_of_days,
            'is_for_post' => $request->is_for_post
        ];
        // Select all reactions reasons to check unique constraint
        $reactions_reasons = ReactionReason::all();

        // Validate required fields
        if ($inputs['reason_content'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['reason_content'], __('validation.required', ['field_name' => __('miscellaneous.admin.miscellaneous.reason.data.reason_content')]), 400);
        }

        // Check if reaction name already exists
        foreach ($reactions_reasons as $another_reason):
            if ($another_reason->reason_content == $inputs['reason_content']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['reason_content'], __('validation.custom.content.exists'), 400);
            }
        endforeach;

        $reaction_reason = ReactionReason::create($inputs);

        return $this->handleResponse(new ResourcesReactionReason($reaction_reason), __('notifications.create_reaction_reason_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reaction_reason = ReactionReason::find($id);

        if (is_null($reaction_reason)) {
            return $this->handleError(__('notifications.find_reaction_reason_404'));
        }

        return $this->handleResponse(new ResourcesReactionReason($reaction_reason), __('notifications.find_reaction_reason_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ReactionReason  $reaction_reason
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReactionReason $reaction_reason)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'reason_content' => [
                'af' => $request->reason_content_af,
                'de' => $request->reason_content_de,
                'ar' => $request->reason_content_ar,
                'zh' => $request->reason_content_zh,
                'en' => $request->reason_content_en,
                'es' => $request->reason_content_es,
                'fr' => $request->reason_content_fr,
                'it' => $request->reason_content_it,
                'ja' => $request->reason_content_ja,
                'ln' => $request->reason_content_ln,
                'nl' => $request->reason_content_nl,
                'ru' => $request->reason_content_ru,
                'sw' => $request->reason_content_sw,
                'tr' => $request->reason_content_tr,
                'cs' => $request->reason_content_cs
            ],
            'report_count' => $request->report_count,
            'number_of_days' => $request->number_of_days,
            'is_for_post' => $request->is_for_post
        ];
        // Select all reactions reasons and specific reason to check unique constraint
        $reactions_reasons = ReactionReason::all();
        $current_reaction_reason = ReactionReason::find($inputs['id']);

        if ($inputs['reason_content'] != null) {
            foreach ($reactions_reasons as $another_reaction_reason):
                if ($current_reaction_reason->reason_content != $inputs['reason_content']) {
                    if ($another_reaction_reason->reason_content == $inputs['reason_content']) {
                        return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['reason_content'], __('validation.custom.content.exists'), 400);
                    }
                }
            endforeach;

            $reaction_reason->update([
                'reason_content' => $inputs['reason_content'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['report_count'] != null) {
            $reaction_reason->update([
                'report_count' => $inputs['report_count'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['number_of_days'] != null) {
            $reaction_reason->update([
                'number_of_days' => $inputs['number_of_days'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['is_for_post'] != null) {
            $reaction_reason->update([
                'is_for_post' => $inputs['is_for_post'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesReactionReason($reaction_reason), __('notifications.update_reaction_reason_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReactionReason  $reaction_reason
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReactionReason $reaction_reason)
    {
        $reaction_reason->delete();

        $reactions_reasons = ReactionReason::all();

        return $this->handleResponse(ResourcesReactionReason::collection($reactions_reasons), __('notifications.delete_reaction_reason_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * All reasons concerning post or not.
     *
     * @param  int $is_for_post
     * @return \Illuminate\Http\Response
     */
    public function findByForPost($is_for_post)
    {
        $reactions_reasons = ReactionReason::where('is_for_post' . $is_for_post)->get();

        return $this->handleResponse(ResourcesReactionReason::collection($reactions_reasons), __('notifications.find_all_reactions_reasons_success'));
    }
}
