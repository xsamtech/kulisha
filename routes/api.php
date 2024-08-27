<?php
/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Default API Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'localization'])->group(function () {
    Route::apiResource('group', 'App\Http\Controllers\API\GroupController');
    Route::apiResource('status', 'App\Http\Controllers\API\StatusController')->except(['findByRealName', 'findByAlias', 'findByGroup']);
    Route::apiResource('type', 'App\Http\Controllers\API\TypeController')->except(['findByRealName', 'findByAlias', 'findByGroup']);
    Route::apiResource('category', 'App\Http\Controllers\API\CategoryController')->except(['findByRealName', 'filterByFieldsType']);
    Route::apiResource('order', 'App\Http\Controllers\API\OrderController');
    Route::apiResource('cart', 'App\Http\Controllers\API\CartController');
    Route::apiResource('role', 'App\Http\Controllers\API\RoleController');
    Route::apiResource('visibility', 'App\Http\Controllers\API\VisibilityController')->except(['findByRealName', 'findByAlias', 'findByGroup']);
    Route::apiResource('restriction', 'App\Http\Controllers\API\RestrictionController');
    Route::apiResource('field', 'App\Http\Controllers\API\FieldController')->except(['findByRealName', 'findByAlias']);
    Route::apiResource('coverage_area', 'App\Http\Controllers\API\CoverageAreaController')->except(['index']);
    Route::apiResource('budget', 'App\Http\Controllers\API\BudgetController');
    Route::apiResource('user', 'App\Http\Controllers\API\UserController')->except(['store', 'show', 'search', 'profile', 'login']);
    Route::apiResource('blocked_user', 'App\Http\Controllers\API\BlockedUserController');
    Route::apiResource('website', 'App\Http\Controllers\API\WebsiteController');
    Route::apiResource('password_reset', 'App\Http\Controllers\API\PasswordResetTokenController')->except(['searchByEmailOrPhone', 'searchByEmail', 'searchByPhone', 'checkToken']);
    Route::apiResource('personal_access_token', 'App\Http\Controllers\API\PersonalAccessTokenController')->except(['search']);
    Route::apiResource('notification', 'App\Http\Controllers\API\NotificationController');
    Route::apiResource('history', 'App\Http\Controllers\API\HistoryController');
    Route::apiResource('message', 'App\Http\Controllers\API\MessageController');
    Route::apiResource('file', 'App\Http\Controllers\API\FileController');
    Route::apiResource('subscription', 'App\Http\Controllers\API\SubscriptionController')->except(['index']);
});
/*
|--------------------------------------------------------------------------
| Custom API resource
|--------------------------------------------------------------------------
 */
Route::group(['middleware' => ['api', 'localization']], function () {
    Route::resource('status', 'App\Http\Controllers\API\StatusController');
    Route::resource('type', 'App\Http\Controllers\API\TypeController');
    Route::resource('category', 'App\Http\Controllers\API\CategoryController');
    Route::resource('role', 'App\Http\Controllers\API\RoleController');
    Route::resource('visibility', 'App\Http\Controllers\API\VisibilityController');
    Route::resource('field', 'App\Http\Controllers\API\FieldController');
    Route::resource('coverage_area', 'App\Http\Controllers\API\CoverageAreaController')->except(['index']);
    Route::resource('user', 'App\Http\Controllers\API\UserController');
    Route::resource('password_reset', 'App\Http\Controllers\API\PasswordResetTokenController');
    Route::resource('subscription', 'App\Http\Controllers\API\SubscriptionController');
    Route::resource('payment', 'App\Http\Controllers\API\PaymentController');

    // Status
    Route::get('status/find_by_real_name/{locale}/{data}', 'App\Http\Controllers\API\StatusController@findByRealName')->name('status.api.find_by_real_name');
    Route::get('status/find_by_alias/{alias}', 'App\Http\Controllers\API\StatusController@findByAlias')->name('status.api.find_by_alias');
    Route::get('status/find_by_group/{locale}/{group_name}', 'App\Http\Controllers\API\StatusController@findByGroup')->name('status.api.find_by_group');
    // Type
    Route::get('type/find_by_real_name/{locale}/{data}', 'App\Http\Controllers\API\TypeController@findByRealName')->name('type.api.find_by_real_name');
    Route::get('type/find_by_alias/{alias}', 'App\Http\Controllers\API\TypeController@findByAlias')->name('type.api.find_by_alias');
    Route::get('type/find_by_group/{locale}/{group_name}', 'App\Http\Controllers\API\TypeController@findByGroup')->name('type.api.find_by_group');
    // Category
    Route::get('category/find_by_real_name/{locale}/{data}', 'App\Http\Controllers\API\CategoryController@findByRealName')->name('category.api.find_by_real_name');
    Route::post('category/filter_by_fields_type/{locale}/{type_name}', 'App\Http\Controllers\API\CategoryController@filterByFieldsType')->name('category.api.filter_by_fields_type');
    // Role
    Route::get('role/search/{data}', 'App\Http\Controllers\API\RoleController@search')->name('role.api.search');
    // Visibility
    Route::get('visibility/find_by_real_name/{locale}/{data}', 'App\Http\Controllers\API\VisibilityController@findByRealName')->name('visibility.api.find_by_real_name');
    Route::get('visibility/find_by_alias/{alias}', 'App\Http\Controllers\API\VisibilityController@findByAlias')->name('visibility.api.find_by_alias');
    Route::get('visibility/find_by_group/{locale}/{data}', 'App\Http\Controllers\API\VisibilityController@findByGroup')->name('visibility.api.find_by_group');
    // Field
    Route::get('field/find_by_real_name/{locale}/{data}', 'App\Http\Controllers\API\FieldController@findByRealName')->name('field.api.find_by_real_name');
    Route::get('field/find_by_alias/{alias}', 'App\Http\Controllers\API\FieldController@findByAlias')->name('field.api.find_by_alias');
    // CoverageArea
    Route::post('coverage_area', 'App\Http\Controllers\API\CoverageAreaController@index')->name('coverage_area.api.index');
    // User
    Route::post('user', 'App\Http\Controllers\API\UserController@store')->name('user.api.store');
    Route::get('user/{user_id}', 'App\Http\Controllers\API\UserController@show')->name('user.api.show');
    Route::get('user/search/{data}/{visitor_id}', 'App\Http\Controllers\API\UserController@search')->name('user.api.search');
    Route::get('user/profile/{username}', 'App\Http\Controllers\API\UserController@profile')->name('user.api.profile');
    Route::post('user/login', 'App\Http\Controllers\API\UserController@login')->name('user.api.login');
    // PasswordReset
    Route::get('password_reset/search_by_email_or_phone/{data}', 'App\Http\Controllers\API\PasswordResetController@searchByEmailOrPhone')->name('password_reset.api.search_by_email_or_phone');
    Route::get('password_reset/search_by_email/{data}', 'App\Http\Controllers\API\PasswordResetController@searchByEmail')->name('password_reset.api.search_by_email');
    Route::get('password_reset/search_by_phone/{data}', 'App\Http\Controllers\API\PasswordResetController@searchByPhone')->name('password_reset.api.search_by_phone');
    Route::post('password_reset/check_token', 'App\Http\Controllers\API\PasswordResetController@checkToken')->name('password_reset.api.check_token');
    // Subscription
    Route::get('subscription', 'App\Http\Controllers\API\SubscriptionController@index')->name('subscription.api.index');
    // Payment
    Route::post('payment/store', 'App\Http\Controllers\API\PaymentController@store')->name('payment.api.store');
    Route::get('payment/find_by_phone/{phone_number}', 'App\Http\Controllers\API\PaymentController@findByPhone')->name('payment.api.find_by_phone');
    Route::get('payment/find_by_order_number/{order_number}', 'App\Http\Controllers\API\PaymentController@findByOrderNumber')->name('payment.api.find_by_order_number');
    Route::get('payment/find_by_order_number_user/{order_number}/{user_id}', 'App\Http\Controllers\API\PaymentController@findByOrderNumberUser')->name('payment.api.find_by_order_number_user');
    Route::put('payment/switch_status/{status_id}/{payment_id}', 'App\Http\Controllers\API\PaymentController@switchStatus')->name('payment.api.switch_status');
});
Route::group(['middleware' => ['api', 'auth:sanctum', 'localization']], function () {
    Route::resource('category', 'App\Http\Controllers\API\CategoryController')->except(['findByRealName', 'findByFieldType']);
    Route::resource('cart', 'App\Http\Controllers\API\CartController');
    Route::resource('budget', 'App\Http\Controllers\API\BudgetController');
    Route::resource('user', 'App\Http\Controllers\API\UserController')->except(['store', 'show', 'search', 'profile', 'login']);
    Route::resource('notification', 'App\Http\Controllers\API\NotificationController');
    Route::resource('history', 'App\Http\Controllers\API\HistoryController');
    Route::resource('message', 'App\Http\Controllers\API\MessageController');

    // Category
    Route::put('category/add_fields/{category_id}', 'App\Http\Controllers\API\CategoryController@addFields')->name('category.api.add_fields');
    Route::put('category/withdraw_fields/{category_id}', 'App\Http\Controllers\API\CategoryController@withdrawFields')->name('category.api.withdraw_fields');
    // Cart
    Route::get('cart/is_inside/{work_id}/{user_id}', 'App\Http\Controllers\API\CartController@isInside')->name('cart.api.is_inside');
    Route::put('cart/add_to_cart/{work_id}/{user_id}', 'App\Http\Controllers\API\CartController@addToCart')->name('cart.api.add_to_cart');
    Route::put('cart/remove_from_cart/{work_id}/{cart_id}', 'App\Http\Controllers\API\CartController@removeFromCart')->name('cart.api.remove_from_cart');
    // Budget
    Route::get('budget/find_by_entity/{entity}', 'App\Http\Controllers\API\BudgetController@findByEntity')->name('budget.api.find_by_entity');
    // User
    Route::get('user/find_by_role/{locale}/{role_name}', 'App\Http\Controllers\API\UserController@findByRole')->name('user.api.find_by_role');
    Route::get('user/find_by_not_role/{locale}/{role_name}', 'App\Http\Controllers\API\UserController@findByNotRole')->name('user.api.find_by_not_role');
    Route::get('user/find_by_status/{status_id}', 'App\Http\Controllers\API\UserController@findByStatus')->name('user.api.find_by_status');
    Route::get('user/find_by_visibility/{visibility_id}', 'App\Http\Controllers\API\UserController@findByVisibility')->name('user.api.find_by_visibility');
    Route::get('user/connections_suggestion/{user_id}', 'App\Http\Controllers\API\UserController@connectionsSuggestion')->name('user.api.connections_suggestion');
    Route::put('user/add_connection/{user_id}/{addressee_id}', 'App\Http\Controllers\API\UserController@addConnection')->name('user.api.add_connection');
    Route::put('user/invitation_refusal/{user_id}/{addressee_id}', 'App\Http\Controllers\API\UserController@invitationRefusal')->name('user.api.invitation_refusal');
    Route::post('user/send_external_invitation/{user_id}', 'App\Http\Controllers\API\UserController@sendExternalInvitation')->name('user.api.send_external_invitation');
    Route::post('user/register_post_for_later/{user_id}', 'App\Http\Controllers\API\UserController@registerPostForLater')->name('user.api.register_post_for_later');
    Route::post('user/subscribe_to_group/{user_id}/{addressee_id}', 'App\Http\Controllers\API\UserController@subscribeToGroup')->name('user.api.subscribe_to_group');
    Route::post('user/unsubscribe_to_group/{user_id}/{addressee_id}', 'App\Http\Controllers\API\UserController@unsubscribeToGroup')->name('user.api.unsubscribe_to_group');
    Route::put('user/react_to_invitation/{user_id}', 'App\Http\Controllers\API\UserController@reactToInvitation')->name('user.api.react_to_invitation');
    Route::put('user/accept_subscription/{user_id}', 'App\Http\Controllers\API\UserController@acceptSubscription')->name('user.api.accept_subscription');
    Route::put('user/switch_status/{user_id}/{status_id}', 'App\Http\Controllers\API\UserController@switchStatus')->name('user.api.switch_status');
    Route::put('user/switch_type/{user_id}/{type_id}', 'App\Http\Controllers\API\UserController@switchType')->name('user.api.switch_type');
    Route::put('user/switch_visibility/{user_id}/{visibility_id}', 'App\Http\Controllers\API\UserController@switchVisibility')->name('user.api.switch_visibility');
    Route::put('user/update_role/{user_id}', 'App\Http\Controllers\API\UserController@updateRole')->name('user.api.update_role');
    Route::put('user/update_password/{user_id}', 'App\Http\Controllers\API\UserController@updatePassword')->name('user.api.update_password');
    Route::put('user/add_fields/{user_id}', 'App\Http\Controllers\API\UserController@addFields')->name('user.api.add_fields');
    Route::put('user/withdraw_fields/{user_id}', 'App\Http\Controllers\API\UserController@withdrawFields')->name('user.api.withdraw_fields');
    Route::put('user/update_avatar_picture/{user_id}', 'App\Http\Controllers\API\UserController@updateAvatarPicture')->name('user.api.update_avatar_picture');
    Route::put('user/update_cover/{user_id}', 'App\Http\Controllers\API\UserController@updateCover')->name('user.api.update_cover');
    Route::put('user/upload_file/{user_id}', 'App\Http\Controllers\API\UserController@uploadFile')->name('user.api.upload_file');
    // Notification
    Route::get('notification/select_by_user/{user_id}/{status_alias}', 'App\Http\Controllers\API\NotificationController@selectByUser')->name('notification.api.select_by_user');
    Route::put('notification/switch_status/{notification_id}/{status_alias}', 'App\Http\Controllers\API\NotificationController@switchStatus')->name('notification.api.switch_status');
    Route::put('notification/mark_all_read/{user_id}', 'App\Http\Controllers\API\NotificationController@markAllRead')->name('notification.api.mark_all_read');
    // History
    Route::get('history/select_by_user/{user_id}/{status_alias}', 'App\Http\Controllers\API\HistoryController@selectByUser')->name('history.api.select_by_user');
    Route::put('history/switch_status/{history_id}/{status_alias}', 'App\Http\Controllers\API\HistoryController@switchStatus')->name('history.api.switch_status');
    Route::put('history/mark_all_read/{user_id}', 'App\Http\Controllers\API\HistoryController@markAllRead')->name('history.api.mark_all_read');
    // Message
    Route::get('message/search_in_chat/{locale}/{type_name}/{data}/{sender_id}/{addressee_id}', 'App\Http\Controllers\API\MessageController@searchInChat')->name('message.api.search_in_chat');
    Route::get('message/search_in_group/{entity}/{entity_id}/{member_id}/{data}', 'App\Http\Controllers\API\MessageController@searchInGroup')->name('message.api.search_in_group');
    Route::get('message/chat_with_user/{locale}/{type_name}/{sender_id}/{addressee_user_id}', 'App\Http\Controllers\API\MessageController@chatWithUser')->name('message.api.chat_with_user');
    Route::get('message/chat_with_group/{entity}/{entity_id}', 'App\Http\Controllers\API\MessageController@chatWithGroup')->name('message.api.chat_with_group');
    Route::get('message/members_with_message_status/{status_alias}/{message_id}', 'App\Http\Controllers\API\MessageController@membersWithMessageStatus')->name('message.api.members_with_message_status');
    Route::get('message/delete_for_myself/{user_id}/{message_id}/{entity}', 'App\Http\Controllers\API\MessageController@deleteForMyself')->name('message.api.delete_for_myself');
    Route::get('message/delete_for_everybody/{message_id}', 'App\Http\Controllers\API\MessageController@deleteForEverybody')->name('message.api.delete_for_everybody');
    Route::get('message/mark_all_read_user/{locale}/{type_name}/{sender_id}/{addressee_user_id}', 'App\Http\Controllers\API\MessageController@markAllReadUser')->name('message.api.mark_all_read_user');
    Route::get('message/upload_file/{message_id}', 'App\Http\Controllers\API\MessageController@uploadFile')->name('message.api.upload_file');
});
