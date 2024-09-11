<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\Order as ResourcesOrder;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::orderByDesc('created_at')->paginate(30);
        $count_all = Order::count();

        return $this->handleResponse(ResourcesOrder::collection($orders), __('notifications.find_all_orders_success'), $orders->lastPage(), $count_all);
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
            'cart_id' => $request->cart_id,
            'post_id' => $request->post_id,
            'quantity' => $request->quantity
        ];

        // Validate required fields
        if (!is_numeric($inputs['cart_id']) OR trim($inputs['cart_id']) == null) {
            return $this->handleError($inputs['cart_id'], __('validation.required', ['field_name' => __('miscellaneous.menu.public.orders.cart.title')]), 400);
        }

        if (!is_numeric($inputs['post_id']) OR trim($inputs['post_id']) == null) {
            return $this->handleError($inputs['post_id'], __('validation.required', ['field_name' => __('miscellaneous.menu.public.orders.cart.title')]), 400);
        }

        $post = Post::find($inputs['post_id']);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        // Set the current unit price of the product/service
        $inputs['current_unit_price'] = $post->price;
        $inputs['currency'] = $post->currency;
        $order = Order::create($inputs);

        return $this->handleResponse(new ResourcesOrder($order), __('notifications.create_order_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);

        if (is_null($order)) {
            return $this->handleError(__('notifications.find_order_404'));
        }

        return $this->handleResponse(new ResourcesOrder($order), __('notifications.find_order_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        // Get inputs
        $inputs = [
            'cart_id' => $request->cart_id,
            'post_id' => $request->post_id,
            'quantity' => $request->quantity,
            'current_unit_price' => $request->current_unit_price,
            'currency' => $request->currency
        ];

        if ($inputs['cart_id'] != null) {
            $order->update([
                'cart_id' => $inputs['cart_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['post_id'] != null) {
            $order->update([
                'post_id' => $inputs['post_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['quantity'] != null) {
            $order->update([
                'quantity' => $inputs['quantity'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['current_unit_price'] != null) {
            $order->update([
                'current_unit_price' => $inputs['current_unit_price'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['currency'] != null) {
            $order->update([
                'currency' => $inputs['currency'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesOrder($order), __('notifications.update_order_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();

        $orders = Order::all();

        return $this->handleResponse(ResourcesOrder::collection($orders), __('notifications.delete_order_success'));
    }
}
