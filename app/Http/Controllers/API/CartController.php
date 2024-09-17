<?php

namespace App\Http\Controllers\API;

use stdClass;
use App\Mail\ShortMail;
use App\Models\Cart;
use App\Models\Group;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Status;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\Cart as ResourcesCart;

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
        // FlexPay accessing data
        $gateway_mobile = config('services.flexpay.gateway_mobile');
        $gateway_card = config('services.flexpay.gateway_card_v2');
        // Vonage accessing data
        // $basic  = new \Vonage\Client\Credentials\Basic(config('vonage.api_key'), config('vonage.api_secret'));
        // $client = new \Vonage\Client($basic);
        // Groups
        $transaction_status_group = Group::where('group_name->fr', 'Etat de la transaction')->first();
        $transaction_type_group = Group::where('group_name->fr', 'Type de transaction')->first();
        // Status
        $done_transaction_status = Status::where([['status_name->fr', 'Effectué'], ['group_id', $transaction_status_group->id]])->first();
        // Types
        $mobile_money_type = Type::where([['type_name->fr', 'Mobile money'], ['group_id', $transaction_type_group->id]])->first();
        $bank_card_type = Type::where([['type_name->fr', 'Carte bancaire'], ['group_id', $transaction_type_group->id]])->first();

        if (is_null($mobile_money_type)) {
            return $this->handleError(__('miscellaneous.public.home.posts.boost.transaction_type.mobile_money'), __('notifications.find_type_404'), 404);
        }

        if (is_null($bank_card_type)) {
            return $this->handleError(__('miscellaneous.public.home.posts.boost.transaction_type.bank_card'), __('notifications.find_type_404'), 404);
        }

        // Request
        $cart = Cart::find($id);

        if (is_null($cart)) {
            return $this->handleError(__('notifications.find_cart_404'));
        }

        // Total orders price
        $total_price = Order::where('cart_id', $cart->id)->join('posts', 'orders.post_id', '=', 'posts.id')->sum('posts.price');
        $currency = Order::where('cart_id', $cart->id)->first()->currency;

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
                $data = array(
                    'merchant' => config('services.flexpay.merchant'),
                    'type' => 1,
                    'phone' => $request->other_phone,
                    'reference' => $reference_code,
                    'amount' => $total_price,
                    'currency' => $currency,
                    'callbackUrl' => getApiURL() . '/payment/store'
                );
                $data = json_encode($data);
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $gateway_mobile);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . config('services.flexpay.api_token')
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    return $this->handleError(curl_errno($ch), __('notifications.transaction_request_failed'), 400);

                } else {
                    curl_close($ch); 

                    $jsonRes = json_decode($response, true);
                    $code = $jsonRes['code']; // Push sending status

                    if ($code != '0') {
                        if (!empty($current_user->email)) {
                            Mail::to($current_user->email)->send(new ShortMail(null, 'payment', __('notifications.transaction_push_failed')));
                        }

                        // if (!empty($current_user->phone)) {
                        //     try {
                        //         $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', __('notifications.transaction_push_failed')));

                        //     } catch (\Throwable $th) {
                        //         return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        //     }
                        // }

                        return $this->handleError(__('miscellaneous.error_label'), __('notifications.transaction_push_failed'), 400);

                    } else {
                        $object = new stdClass();

                        $object->result_response = [
                            'message' => $jsonRes['message'],
                            'order_number' => $jsonRes['orderNumber']
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
                        $payment = Payment::where('order_number', $jsonRes['orderNumber'])->first();

                        if (is_null($payment)) {
                            Payment::create([
                                'reference' => $reference_code,
                                'order_number' => $jsonRes['orderNumber'],
                                'amount' => $total_price,
                                'phone' => $request->other_phone,
                                'currency' => 'USD',
                                'type_id' => $request->transaction_type_id,
                                'status_id' => $done_transaction_status->id,
                                'subject_url' => $request->subject_url,
                                'user_id' => $current_user->id
                            ]);
                        }

                        if (!empty($current_user->email)) {
                            Mail::to($current_user->email)->send(new ShortMail(null, 'payment', __('notifications.purchase_complete')));
                        }

                        // if (!empty($current_user->phone)) {
                        //     try {
                        //         $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', __('notifications.purchase_complete')));

                        //     } catch (\Throwable $th) {
                        //         return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        //     }
                        // }

                        return $this->handleResponse($object, __('notifications.purchase_complete'));
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
                $body = json_encode(array(
                    'authorization' => 'Bearer ' . config('services.flexpay.api_token'),
                    'merchant' => config('services.flexpay.merchant'),
                    'reference' => $reference_code,
                    'amount' => $total_price,
                    'currency' => $currency,
                    'description' => __('miscellaneous.bank_transaction_description'),
                    'callback_url' => getApiURL() . '/payment/store',
                    'approve_url' => $request->app_url . '/bought/' . $total_price . '/' . $currency . '/0/' . $current_user->id,
                    'cancel_url' => $request->app_url . '/bought/' . $total_price . '/' . $currency . '/1/' . $current_user->id,
                    'decline_url' => $request->app_url . '/bought/' . $total_price . '/' . $currency . '/2/' . $current_user->id,
                    'home_url' => $request->app_url . '/cart',
                ));

                $curl = curl_init($gateway_card);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $curlResponse = curl_exec($curl);

                $jsonRes = json_decode($curlResponse, true);
                $code = $jsonRes['code'];
                $message = $jsonRes['message'];

                if (!empty($jsonRes['error'])) {
                    return $this->handleError($jsonRes['error'], $message, $jsonRes['status']);

                } else {
                    if ($code != '0') {
                        if (!empty($current_user->email)) {
                            Mail::to($current_user->email)->send(new ShortMail(null, 'payment', $message));
                        }

                        // if (!empty($current_user->phone)) {
                        //     try {
                        //         $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', $message));

                        //     } catch (\Throwable $th) {
                        //         return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        //     }
                        // }

                        return $this->handleError($code, $message, 400);

                    } else {
                        $url = $jsonRes['url'];
                        $orderNumber = $jsonRes['orderNumber'];
                        $object = new stdClass();

                        $object->result_response = [
                            'message' => $message,
                            'order_number' => $orderNumber,
                            'url' => $url
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
                        $payment = Payment::where('order_number', $jsonRes['orderNumber'])->first();

                        if (is_null($payment)) {
                            Payment::create([
                                'reference' => $reference_code,
                                'order_number' => $jsonRes['orderNumber'],
                                'amount' => $total_price,
                                'currency' => 'USD',
                                'type_id' => $request->transaction_type_id,
                                'status_id' => $done_transaction_status->id,
                                'subject_url' => $request->subject_url,
                                'user_id' => $current_user->id
                            ]);
                        }

                        if (!empty($current_user->email)) {
                            Mail::to($current_user->email)->send(new ShortMail(null, 'payment', __('notifications.purchase_complete')));
                        }

                        // if (!empty($current_user->phone)) {
                        //     try {
                        //         $client->sms()->send(new \Vonage\SMS\Message\SMS($current_user->phone, 'Kulisha', __('notifications.purchase_complete')));

                        //     } catch (\Throwable $th) {
                        //         return $this->handleError($th->getMessage(), __('notifications.process_failed'), 500);
                        //     }
                        // }

                        return $this->handleResponse($object, __('notifications.purchase_complete'));
                    }
                }

            } else {
                return $this->handleError(__('notifications.find_user_404'));
            }
        }
    }
}
