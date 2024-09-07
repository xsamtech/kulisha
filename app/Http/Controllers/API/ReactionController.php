<?php

namespace App\Http\Controllers\API;

use App\Models\Group;
use App\Models\Reaction;
use Illuminate\Http\Request;
use App\Http\Resources\Reaction as ResourcesReaction;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class ReactionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reactions = Reaction::all();

        return $this->handleResponse(ResourcesReaction::collection($reactions), __('notifications.find_all_reactions_success'));
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
            'reaction_name' => [
                'af' => $request->reaction_name_af,
                'de' => $request->reaction_name_de,
                'ar' => $request->reaction_name_ar,
                'zh' => $request->reaction_name_zh,
                'en' => $request->reaction_name_en,
                'es' => $request->reaction_name_es,
                'fr' => $request->reaction_name_fr,
                'it' => $request->reaction_name_it,
                'ja' => $request->reaction_name_ja,
                'ln' => $request->reaction_name_ln,
                'nl' => $request->reaction_name_nl,
                'ru' => $request->reaction_name_ru,
                'sw' => $request->reaction_name_sw,
                'tr' => $request->reaction_name_tr,
                'cs' => $request->reaction_name_cs
            ],
            'reaction_description' => [
                'af' => $request->reaction_description_af,
                'de' => $request->reaction_description_de,
                'ar' => $request->reaction_description_ar,
                'zh' => $request->reaction_description_zh,
                'en' => $request->reaction_description_en,
                'es' => $request->reaction_description_es,
                'fr' => $request->reaction_description_fr,
                'it' => $request->reaction_description_it,
                'ja' => $request->reaction_description_ja,
                'ln' => $request->reaction_description_ln,
                'nl' => $request->reaction_description_nl,
                'ru' => $request->reaction_description_ru,
                'sw' => $request->reaction_description_sw,
                'tr' => $request->reaction_description_tr,
                'cs' => $request->reaction_description_cs
            ],
            'alias' => $request->alias,
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'group_id' => $request->group_id
        ];
        // Select all group reactions to check unique constraint
        $reactions = Reaction::where('group_id', $inputs['group_id'])->get();

        // Validate required fields
        if ($inputs['reaction_name'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['reaction_name'], __('validation.required', ['field_name' => __('miscellaneous.admin.group.reaction.data.reaction_name')]), 400);
        }

        if (!is_numeric($inputs['group_id']) OR trim($inputs['group_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['group_id'], __('miscellaneous.admin.group.choose_group'), 400);
        }

        // Check if reaction name already exists
        foreach ($reactions as $another_reaction):
            if ($another_reaction->reaction_name == $inputs['reaction_name']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['reaction_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $reaction = Reaction::create($inputs);

        return $this->handleResponse(new ResourcesReaction($reaction), __('notifications.create_reaction_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reaction = Reaction::find($id);

        if (is_null($reaction)) {
            return $this->handleError(__('notifications.find_reaction_404'));
        }

        return $this->handleResponse(new ResourcesReaction($reaction), __('notifications.find_reaction_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reaction  $reaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reaction $reaction)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'reaction_name' => [
                'af' => $request->reaction_name_af,
                'de' => $request->reaction_name_de,
                'ar' => $request->reaction_name_ar,
                'zh' => $request->reaction_name_zh,
                'en' => $request->reaction_name_en,
                'es' => $request->reaction_name_es,
                'fr' => $request->reaction_name_fr,
                'it' => $request->reaction_name_it,
                'ja' => $request->reaction_name_ja,
                'ln' => $request->reaction_name_ln,
                'nl' => $request->reaction_name_nl,
                'ru' => $request->reaction_name_ru,
                'sw' => $request->reaction_name_sw,
                'tr' => $request->reaction_name_tr,
                'cs' => $request->reaction_name_cs
            ],
            'reaction_description' => [
                'af' => $request->reaction_description_af,
                'de' => $request->reaction_description_de,
                'ar' => $request->reaction_description_ar,
                'zh' => $request->reaction_description_zh,
                'en' => $request->reaction_description_en,
                'es' => $request->reaction_description_es,
                'fr' => $request->reaction_description_fr,
                'it' => $request->reaction_description_it,
                'ja' => $request->reaction_description_ja,
                'ln' => $request->reaction_description_ln,
                'nl' => $request->reaction_description_nl,
                'ru' => $request->reaction_description_ru,
                'sw' => $request->reaction_description_sw,
                'tr' => $request->reaction_description_tr,
                'cs' => $request->reaction_description_cs
            ],
            'alias' => $request->alias,
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'group_id' => $request->group_id
        ];
        // Select all reactions and specific reaction to check unique constraint
        $reactions = Reaction::where('group_id', $inputs['group_id'])->get();
        $current_reaction = Reaction::find($inputs['id']);

        if ($inputs['reaction_name'] != null) {
            foreach ($reactions as $another_reaction):
                if ($current_reaction->reaction_name != $inputs['reaction_name']) {
                    if ($another_reaction->reaction_name == $inputs['reaction_name']) {
                        return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['reaction_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $reaction->update([
                'reaction_name' => $inputs['reaction_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['reaction_description'] != null) {
            $reaction->update([
                'reaction_description' => $inputs['reaction_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['alias'] != null) {
            $reaction->update([
                'alias' => $inputs['alias'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['color'] != null) {
            $reaction->update([
                'color' => $inputs['color'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $reaction->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $reaction->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['image_url'] != null) {
            $reaction->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['group_id'] != null) {
            $reaction->update([
                'group_id' => $inputs['group_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesReaction($reaction), __('notifications.update_reaction_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reaction  $reaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reaction $reaction)
    {
        $reaction->delete();

        $reactions = Reaction::all();

        return $this->handleResponse(ResourcesReaction::collection($reactions), __('notifications.delete_reaction_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Find a reaction by its name.
     *
     * @param  string $locale
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function findByRealName($locale, $data)
    {
        $reaction = Reaction::where('reaction_name->' . $locale, $data)->first();

        if (is_null($reaction)) {
            return $this->handleError(__('notifications.find_reaction_404'));
        }

        return $this->handleResponse(new ResourcesReaction($reaction), __('notifications.find_reaction_success'));
    }

    /**
     * Find a reaction by its alias.
     *
     * @param  string $alias
     * @return \Illuminate\Http\Response
     */
    public function findByAlias($alias)
    {
        $reaction = Reaction::where('alias', $alias)->first();

        if (is_null($reaction)) {
            return $this->handleError(__('notifications.find_reaction_404'));
        }

        return $this->handleResponse(new ResourcesReaction($reaction), __('notifications.find_reaction_success'));
    }

    /**
     * Find all reactions by group.
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

        $reactions = Reaction::where('group_id', $group->id)->get();

        return $this->handleResponse(ResourcesReaction::collection($reactions), __('notifications.find_all_reactions_success'));
    }
}
