<?php

namespace App\Http\Controllers\API;

use stdClass;
use App\Http\Controllers\ApiClientManager;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\Cart as ResourcesCart;
use App\Models\Status;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class CartController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carts = Cart::all();

        return $this->handleResponse(ResourcesCart::collection($carts), __('notifications.find_all_carts_success'));
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
            'number' => $request->number,
            'type_id' => $request->type_id,
            'status_id' => $request->status_id,
            'user_id' => $request->user_id
        ];
        // Select all carts to check unique constraint
        $carts = Cart::all();

        // Validate required fields
        if (trim($inputs['user_id']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['user_id'], __('validation.required', ['field_name' => __('miscellaneous.choose_user')]), 400);
        }

        // Check if cart payment code already exists
        foreach ($carts as $another_cart):
            if ($another_cart->number == $inputs['number']) {
                return $this->handleError($inputs['number'], __('validation.custom.code.exists'), 400);
            }
        endforeach;

        $cart = Cart::create($inputs);

        return $this->handleResponse(new ResourcesCart($cart), __('notifications.create_cart_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cart = Cart::find($id);

        if (is_null($cart)) {
            return $this->handleError(__('notifications.find_cart_404'));
        }

        return $this->handleResponse(new ResourcesCart($cart), __('notifications.find_cart_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'number' => $request->number,
            'type_id' => $request->type_id,
            'status_id' => $request->status_id,
            'user_id' => $request->user_id
        ];
        // Select all carts to check unique constraint
        $carts = Cart::all();
        $current_cart = Cart::find($inputs['id']);

        if ($inputs['number'] != null) {
            foreach ($carts as $another_cart):
                if ($current_cart->number != $inputs['number']) {
                    if ($another_cart->number == $inputs['number']) {
                        return $this->handleError($inputs['number'], __('validation.custom.code.exists'), 400);
                    }
                }
            endforeach;

            $cart->update([
                'number' => $inputs['number'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['type_id'] != null) {
            $cart->update([
                'type_id' => $inputs['type_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['status_id'] != null) {
            $cart->update([
                'status_id' => $inputs['status_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $cart->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesCart($cart), __('notifications.update_cart_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        $carts = Cart::all();

        return $this->handleResponse(ResourcesCart::collection($carts), __('notifications.delete_cart_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Get user cart by type.
     *
     * @param  int $user_id
     * @param  int $type_id
     * @return \Illuminate\Http\Response
     */
    public function findByType($user_id, $type_id)
    {
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $type = Type::find($type_id);

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        if ($type->getTranslation('type_name', 'fr') == 'Wishlist') {
            $cart = Cart::where([['type_id', $type->id], ['user_id', $user_id]])->first();

            if (is_null($cart)) {
                $cart = Cart::create([
                    'type_id' => $type->id,
                    'user_id' => $user->id
                ]);
            }

            return $this->handleResponse(new ResourcesCart($cart), __('notifications.find_cart_success'));

        } else {
            $status = Status::where('alias', 'on_order')->first();

            if (is_null($status)) {
                return $this->handleError(__('notifications.find_status_404'));
            }

            $cart = Cart::where([['type_id', $type->id], ['status_id', $status->id], ['user_id', $user_id]])->first();
            $carts = Cart::where([['type_id', $type->id], ['user_id', $user_id]])->get();

            $object = new stdClass();

            $object->current_cart = new ResourcesCart($cart);
            $object->archives = ResourcesCart::collection($carts);

            return $this->handleResponse($object, __('notifications.find_all_carts_success'));
        }
    }

    /**
     * Check if product/service is in cart or wishlist.
     *
     * @param  int $post_id
     * @param  int $user_id
     * @param  int $type_id
     * @return \Illuminate\Http\Response
     */
    public function isInside($post_id, $user_id, $type_id)
    {
        $post = Post::find($post_id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $type = Type::find($type_id);

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        $cart = Cart::where([['type_id', $type->id], ['user_id', $user_id]])->first();

        if (inArrayR($post->id, $cart->orders, 'post_id')) {
            return $this->handleResponse(true, __('notifications.find_post_success'), null);

        } else {
            return $this->handleResponse(false, __('notifications.find_post_404'), null);
        }
    }

    /**
     * Add product/service to cart or wishlist.
     *
     * @param  string $locale
     * @param  string $type_name
     * @param  int $post_id
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function addToCart($locale, $type_name, $post_id, $user_id)
    {
        $post = Post::find($post_id);

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        $type = Type::where('type_name->' . $locale, $type_name)->first();

        if (is_null($type)) {
            return $this->handleError(__('notifications.find_type_404'));
        }

        $status = Status::where('alias', 'on_order')->first();

        if (is_null($status)) {
            return $this->handleError(__('notifications.find_status_404'));
        }

        $cart = $type->getTranslation('type_name', 'fr') == 'Wishlist' 
                    ? Cart::where([['type_id', $type->id], ['user_id', $user_id]])->first() 
                    : Cart::where([['type_id', $type->id], ['status_id', $status->id], ['user_id', $user_id]])->first();

        if ($cart != null) {
            Order::create([
                'post_id' => $post->id,
                'cart_id' => $cart->id
            ]);

            $cart->update([
                'updated_at' => now()
            ]);

            return $this->handleResponse(new ResourcesCart($cart), __('notifications.find_cart_success'));

        } else {
            $cart = Cart::create([
                'type_id' => $type->id,
                'user_id' => $user->id
            ]);

            Order::create([
                'post_id' => $post->id,
                'cart_id' => $cart->id
            ]);

            return $this->handleResponse(new ResourcesCart($cart), __('notifications.find_cart_success'));
        }
    }

    /**
     * Remove product/service from cart or wishlist.
     *
     * @param  int $cart_id
     * @param  int $post_id
     * @return \Illuminate\Http\Response
     */
    public function removeFromCart($cart_id, $post_id)
    {
        $cart = Cart::find($cart_id);
        $post = Post::find($post_id);

        if (is_null($cart)) {
            return $this->handleError(__('notifications.find_cart_404'));
        }

        if (is_null($post)) {
            return $this->handleError(__('notifications.find_post_404'));
        }

        $order = Order::where([['post_id', $post->id], ['cart_id', $cart->id]])->first();

        $order->delete();

        return $this->handleResponse(new ResourcesCart($cart), __('notifications.delete_post_success'));
    }

    /**
     * Purchase ordered product/service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function purchase(Request $request, $id)
    {
        // Manage API Client
        $api_manager = new ApiClientManager();
        // FlexPay accessing data
        $gateway_mobile = config('services.flexpay.gateway_mobile');
        $gateway_card = config('services.flexpay.gateway_card_v2');
        // Vonage accessing data
        // $basic  = new \Vonage\Client\Credentials\Basic(config('vonage.api_key'), config('vonage.api_secret'));
        // $client = new \Vonage\Client($basic);
        // Requests
        $cart = Cart::find($id);

        if (is_null($cart)) {
            return $this->handleError(__('notifications.find_cart_404'));
        }

        // Total orders price
        $total_price = Order::where('cart_id', $cart->id)->join('posts', 'orders.post_id', '=', 'posts.id')->sum('posts.price');

        // Mobile money type
        $mobile_money_type = Type::where('type_name->fr', 'Mobile money')->first();

        if (is_null($mobile_money_type)) {
            return $this->handleError(__('miscellaneous.public.home.posts.boost.transaction_type.mobile_money'), __('notifications.find_type_404'), 404);
        }

        // Bank card
        $bank_card_type = Type::where('type_name->fr', 'Carte bancaire')->first();

        if (is_null($bank_card_type)) {
            return $this->handleError(__('miscellaneous.public.home.posts.boost.transaction_type.bank_card'), __('notifications.find_type_404'), 404);
        }

        // Validations
        if ($request->transaction_type_id == null OR !is_numeric($request->transaction_type_id)) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $request->transaction_type_id, __('validation.required', ['field_name' => __('miscellaneous.public.home.posts.boost.transaction_type.title')]), 400);
        }

        // If the transaction is via mobile money
        if ($request->transaction_type_id == $mobile_money_type->id) {
            $current_user = User::find($cart->user_id);

            if ($current_user != null) {
                $reference_code = 'REF-' . ((string) random_int(10000000, 99999999)) . '-' . $current_user->id;

                // Create response by sending request to FlexPay
                $jsonRes = $api_manager::call('POST', $gateway_mobile, config('services.flexpay.api_token'), [
                    'merchant' => 'KULISHA',
                    'type' => $request->transaction_type_id,
                    'phone' => $request->other_phone,
                    'reference' => $reference_code,
                    'amount' => $total_price,
                    'currency' => 'USD',
                    'callbackUrl' => getApiURL() . '/payment/store'
                ], null, null, true);

                if (!empty($jsonRes->error)) {
                    return $this->handleError($jsonRes->error, $jsonRes->message, $jsonRes->status);

                } else {
                    $code = $jsonRes->code;

                    if ($code != '0') {
                        // try {
                        //     $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', __('notifications.process_failed')));

                        // } catch (\Throwable $th) {
                        //     return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        // }

                        return $this->handleError($jsonRes->code, $jsonRes->message, 400);

                    } else {
                        $object = new stdClass();

                        $object->result_response = [
                            'message' => $jsonRes->message,
                            'order_number' => $jsonRes->orderNumber
                        ];

                        // The cart is updated only if the processing succeed
                        $random_string = (string) random_int(1000000, 9999999);
                        $generated_number = 'KLS-' . $random_string . '-' . date('Y.m.d');

                        $cart->update([
                            'number' => $generated_number,
                            'updated_at' => now()
                        ]);

                        $object->cart = new ResourcesCart($cart);

                        // Register payment, even if FlexPay will
                        $payment = Payment::where('order_number', $jsonRes->orderNumber)->first();

                        if (is_null($payment)) {
                            Payment::create([
                                'reference' => $reference_code,
                                'order_number' => $jsonRes->orderNumber,
                                'amount' => $total_price,
                                'phone' => $request->other_phone,
                                'currency' => 'USD',
                                'type_id' => $request->transaction_type_id,
                                'status_id' => $code,
                                'subject_url' => $request->subject_url,
                                'user_id' => $current_user->id
                            ]);
                        }

                        return $this->handleResponse($object, __('notifications.boost_post_success'));
                    }
                }

            } else {
                return $this->handleError(__('notifications.find_user_404'));
            }
        }

        // If the transaction is via bank card
        if ($request->transaction_type_id == $bank_card_type->id) {
            $current_user = User::find($cart->user_id);

            if ($current_user != null) {
                $reference_code = 'REF-' . ((string) random_int(10000000, 99999999)) . '-' . $current_user->id;

                // Create response by sending request to FlexPay
                $jsonRes = $api_manager::call('POST', $gateway_card, config('services.flexpay.api_token'), [
                    'merchant' => 'KULISHA',
                    'reference' => $reference_code,
                    'amount' => $total_price,
                    'description' => __('miscellaneous.bank_transaction_description'),
                    'currency' => 'USD',
                    'callbackUrl' => getApiURL() . '/payment/store',
                    'approve_url' => $request->app_url . '/boosted/' . $total_price . '/USD/0/' . $current_user->id,
                    'cancel_url' => $request->app_url . '/boosted/' . $total_price . '/USD/1/' . $current_user->id,
                    'decline_url' => $request->app_url . '/boosted/' . $total_price . '/USD/2/' . $current_user->id,
                    'language' => app()->getLocale(),
                ], null, null, true);

                if (!empty($jsonRes->error)) {
                    return $this->handleError($jsonRes->error, $jsonRes->message, $jsonRes->status);

                } else {
                    if ($jsonRes->code != '0') {
                        // try {
                        //     $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', __('notifications.process_failed')));

                        // } catch (\Throwable $th) {
                        //     return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        // }

                        return $this->handleError($jsonRes->code, $jsonRes->message, 400);

                    } else {
                        $object = new stdClass();

                        $object->result_response = [
                            'message' => $jsonRes->message,
                            'order_number' => $jsonRes->orderNumber,
                            'url' => $jsonRes->url
                        ];

                        // The cart is updated only if the processing succeed
                        $random_string = (string) random_int(1000000, 9999999);
                        $generated_number = 'KLS-' . $random_string . '-' . date('Y.m.d');

                        $cart->update([
                            'number' => $generated_number,
                            'updated_at' => now()
                        ]);

                        $object->cart = new ResourcesCart($cart);

                        // Register payment, even if FlexPay will
                        $payment = Payment::where('order_number', $jsonRes->orderNumber)->first();

                        if (is_null($payment)) {
                            Payment::create([
                                'reference' => $reference_code,
                                'order_number' => $jsonRes->orderNumber,
                                'amount' => $total_price,
                                'currency' => 'USD',
                                'type_id' => $request->transaction_type_id,
                                'status_id' => $jsonRes->code,
                                'subject_url' => $request->subject_url,
                                'user_id' => $current_user->id
                            ]);
                        }

                        return $this->handleResponse($object, __('notifications.boost_post_success'));
                    }
                }

            } else {
                return $this->handleError(__('notifications.find_user_404'));
            }
        }
    }
}
