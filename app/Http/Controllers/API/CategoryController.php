<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Models\Type;
use Illuminate\Http\Request;
use App\Http\Resources\Category as ResourcesCategory;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();

        return $this->handleResponse(ResourcesCategory::collection($categories), __('notifications.find_all_categories_success'));
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
            'category_name' => [
                'af' => $request->category_name_af,
                'de' => $request->category_name_de,
                'ar' => $request->category_name_ar,
                'zh' => $request->category_name_zh,
                'en' => $request->category_name_en,
                'es' => $request->category_name_es,
                'fr' => $request->category_name_fr,
                'it' => $request->category_name_it,
                'ja' => $request->category_name_ja,
                'ru' => $request->category_name_ru,
                'sw' => $request->category_name_sw,
                'tr' => $request->category_name_tr,
                'cs' => $request->category_name_cs,
                'eo' => $request->category_name_eo
            ],
            'category_description' => [
                'af' => $request->category_description_af,
                'de' => $request->category_description_de,
                'ar' => $request->category_description_ar,
                'zh' => $request->category_description_zh,
                'en' => $request->category_description_en,
                'es' => $request->category_description_es,
                'fr' => $request->category_description_fr,
                'it' => $request->category_description_it,
                'ja' => $request->category_description_ja,
                'ru' => $request->category_description_ru,
                'sw' => $request->category_description_sw,
                'tr' => $request->category_description_tr,
                'cs' => $request->category_description_cs,
                'eo' => $request->category_description_eo
            ],
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'type_id' => $request->type_id
        ];
        // Select all categories to check unique constraint
        $categories = Category::all();

        // Validate required fields
        if ($inputs['category_name'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['category_name'], __('validation.required', ['field_name' => __('miscellaneous.admin.field.category.data.category_name')]), 400);
        }

        if ($inputs['type_id'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['type_id'], __('validation.required', ['field_name' => __('miscellaneous.admin.field.category.data.choose_type')]), 400);
        }

        // Check if category name already exists
        foreach ($categories as $another_category):
            if ($another_category->category_name == $inputs['category_name']) {
                return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['category_name'], __('validation.custom.name.exists'), 400);
            }
        endforeach;

        $category = Category::create($inputs);

        if ($request->fields_ids != null) {
            $category->fields()->sync($request->fields_ids);
        }

        return $this->handleResponse(new ResourcesCategory($category), __('notifications.create_category_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (is_null($category)) {
            return $this->handleError(__('notifications.find_category_404'));
        }

        return $this->handleResponse(new ResourcesCategory($category), __('notifications.find_category_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'category_name' => [
                'af' => $request->category_name_af,
                'de' => $request->category_name_de,
                'ar' => $request->category_name_ar,
                'zh' => $request->category_name_zh,
                'en' => $request->category_name_en,
                'es' => $request->category_name_es,
                'fr' => $request->category_name_fr,
                'it' => $request->category_name_it,
                'ja' => $request->category_name_ja,
                'ru' => $request->category_name_ru,
                'sw' => $request->category_name_sw,
                'tr' => $request->category_name_tr,
                'cs' => $request->category_name_cs,
                'eo' => $request->category_name_eo
            ],
            'category_description' => [
                'af' => $request->category_description_af,
                'de' => $request->category_description_de,
                'ar' => $request->category_description_ar,
                'zh' => $request->category_description_zh,
                'en' => $request->category_description_en,
                'es' => $request->category_description_es,
                'fr' => $request->category_description_fr,
                'it' => $request->category_description_it,
                'ja' => $request->category_description_ja,
                'ru' => $request->category_description_ru,
                'sw' => $request->category_description_sw,
                'tr' => $request->category_description_tr,
                'cs' => $request->category_description_cs,
                'eo' => $request->category_description_eo
            ],
            'color' => $request->color,
            'icon_font' => $request->icon_font,
            'icon_svg' => $request->icon_svg,
            'image_url' => $request->image_url,
            'type_id' => $request->type_id
        ];
        // Select all categories and specific category to check unique constraint
        $categories = Category::all();
        $current_category = Category::find($inputs['id']);

        if ($inputs['category_name'] != null) {
            foreach ($categories as $another_category):
                if ($current_category->category_name != $inputs['category_name']) {
                    if ($another_category->category_name == $inputs['category_name']) {
                        return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['category_name'], __('validation.custom.name.exists'), 400);
                    }
                }
            endforeach;

            $category->update([
                'category_name' => $inputs['category_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['category_description'] != null) {
            $category->update([
                'category_description' => $inputs['category_description'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['color'] != null) {
            $category->update([
                'color' => $inputs['color'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_font'] != null) {
            $category->update([
                'icon_font' => $inputs['icon_font'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['icon_svg'] != null) {
            $category->update([
                'icon_svg' => $inputs['icon_svg'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['image_url'] != null) {
            $category->update([
                'image_url' => $inputs['image_url'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['type_id'] != null) {
            $category->update([
                'type_id' => $inputs['type_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesCategory($category), __('notifications.update_category_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        $categories = Category::all();

        return $this->handleResponse(ResourcesCategory::collection($categories), __('notifications.delete_category_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a category by its name.
     *
     * @param  string $locale
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($locale, $data)
    {
        $category = Category::where('category_name->' . $locale, $data)->first();

        if (is_null($category)) {
            return $this->handleError(__('notifications.find_category_404'));
        }

        return $this->handleResponse(new ResourcesCategory($category), __('notifications.find_category_success'));
    }

    /**
     * Find all categories by type.
     *
     * @param  string $locale
     * @param  string $type_name
     * @return \Illuminate\Http\Response
     */
    public function findByType($locale, $type_name)
    {
        $type = Type::where('type_name->' . $locale, $type_name)->first();

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        $categories = Category::where('type_id', $type->id)->get();

        return $this->handleResponse(ResourcesCategory::collection($categories), __('notifications.find_all_categories_success'));
    }

    /**
     * Add fields to category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function addFields(Request $request, $id)
    {
        $category = Category::find($id);

        if (is_null($category)) {
            return $this->handleError(__('notifications.find_category_404'));
        }

        if (isset($request->field_id)) {
            $category->fields()->syncWithoutDetaching([$request->field_id]);
        }

        if (isset($request->fields_ids)) {
            $category->fields()->syncWithoutDetaching($request->fields_ids);
        }

        return $this->handleResponse(new ResourcesCategory($category), __('notifications.update_category_success'));
    }

    /**
     * Withdraw fields from category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function withdrawFields(Request $request, $id)
    {
        $category = Category::find($id);

        if (is_null($category)) {
            return $this->handleError(__('notifications.find_category_404'));
        }

        if (isset($request->field_id)) {
            $category->fields()->detach([$request->field_id]);
        }

        if (isset($request->fields_ids)) {
            $category->fields()->detach($request->fields_ids);
        }

        return $this->handleResponse(new ResourcesCategory($category), __('notifications.update_category_success'));
    }
}
