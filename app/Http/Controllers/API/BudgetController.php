<?php

namespace App\Http\Controllers\API;

use App\Models\Budget;
use Illuminate\Http\Request;
use App\Http\Resources\Budget as ResourcesBudget;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class BudgetController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $budgets = Budget::all();

        return $this->handleResponse(ResourcesBudget::collection($budgets), __('notifications.find_all_budgets_success'));
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
            'notifications_count' => $request->notifications_count,
            'reactions_count' => $request->reactions_count,
            'amount' => $request->amount
        ];

        // Validate required fields
        if ($inputs['amount'] == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['amount'], __('validation.required', ['field_name' => __('miscellaneous.admin.miscellaneous.budget.data.amount')]), 400);
        }

        $budget = Budget::create($inputs);

        return $this->handleResponse(new ResourcesBudget($budget), __('notifications.create_budget_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $budget = Budget::find($id);

        if (is_null($budget)) {
            return $this->handleError(__('notifications.find_budget_404'));
        }

        return $this->handleResponse(new ResourcesBudget($budget), __('notifications.find_budget_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Budget $budget)
    {
        // Get inputs
        $inputs = [
            'notifications_count' => $request->notifications_count,
            'reactions_count' => $request->reactions_count,
            'amount' => $request->amount
        ];

        if ($inputs['notifications_count'] != null) {
            $budget->update([
                'notifications_count' => $inputs['notifications_count'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['reactions_count'] != null) {
            $budget->update([
                'reactions_count' => $inputs['reactions_count'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['amount'] != null) {
            $budget->update([
                'amount' => $inputs['amount'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesBudget($budget), __('notifications.update_budget_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function destroy(Budget $budget)
    {
        $budget->delete();

        $budgets = Budget::all();

        return $this->handleResponse(ResourcesBudget::collection($budgets), __('notifications.delete_budget_success'));
    }
}
