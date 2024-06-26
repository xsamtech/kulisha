<?php
/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Notifications Language Lines
    |--------------------------------------------------------------------------
    |
    */

    // ===== ERROR PAGES
    // 400
    '400_title' => 'Bad request',
    '400_description' => 'Verify your request please!',
    // 401
    '401_title' => 'Unauthorized',
    '401_description' => 'You have not authorization for this request',
    // 403
    '403_title' => 'Forbidden space',
    '403_description' => 'This space is not allowed',
    // 404
    '404_title' => 'Page not found',
    '404_description' => 'The page you are looking for does not exist',
    // 405
    '405_title' => 'Method not allowed',
    '405_description' => 'Your request is sent with a bad method',
    // 419
    '419_title' => 'Page expired',
    '419_description' => 'The page maked long time without activity',
    // 500
    '500_title' => 'Internal error',
    '500_description' => 'Our server meets a problem. Please retry after some minutes!',
    // Others
    'expects_json' => 'The current request probably expects a JSON response.',

    // ===== ALERTS
    'no_record' => 'There is no recording!',
    'create_error' => 'The creation failed!',
    'update_error' => 'The change failed!',
    'registered_data' => 'Data registered',
    'required_fields' => 'Please check the required fields',
    'transaction_waiting' => 'Please confirm the message from your operator on your phone. Then press the button below.',
    'transaction_done' => 'Your transaction is complete!',
    'transaction_failed' => 'Failed to send your payment.',
    'transaction_type_error' => 'Please select transaction type',
    'new_partner_message' => 'You can now log in as a partner with your phone number. Temporary password:',
    'reaction_sent' => 'Reaction sent',
    'invitation_sent' => 'Invitation sent',
    // Group
    'find_all_groups_success' => 'Groups found',
    'find_group_success' => 'Group found',
    'find_group_404' => 'Group not found',
    'create_group_success' => 'Group created',
    'update_group_success' => 'Group updated',
    'delete_group_success' => 'Group deleted',
    // Status
    'find_all_statuses_success' => 'Statuses found',
    'find_status_success' => 'Status found',
    'find_status_404' => 'Status not found',
    'create_status_success' => 'Status created',
    'update_status_success' => 'Status updated',
    'delete_status_success' => 'Status deleted',
    // Type
    'find_all_types_success' => 'Types found',
    'find_type_success' => 'Type found',
    'find_type_404' => 'Type not found',
    'create_type_success' => 'Type created',
    'update_type_success' => 'Type updated',
    'delete_type_success' => 'Type deleted',
    // Country
    'find_all_countries_success' => 'Countries found',
    'find_country_success' => 'Country found',
    'find_country_404' => 'Country not found',
    'create_country_success' => 'Country created',
    'update_country_success' => 'Country updated',
    'delete_country_success' => 'Country deleted',
    // Order
    'find_all_orders_success' => 'Orders found',
    'find_order_success' => 'Order found',
    'find_order_404' => 'Order not found',
    'create_order_success' => 'Order created',
    'update_order_success' => 'Order updated',
    'delete_order_success' => 'Order deleted',
    // Cart
    'find_all_carts_success' => 'Carts or Watchlists found',
    'find_cart_success' => 'Cart or Watchlist found',
    'find_cart_404' => 'Cart or Watchlist not found',
    'create_cart_success' => 'Cart or Watchlist created',
    'update_cart_success' => 'Cart or Watchlist updated',
    'delete_cart_success' => 'Cart or Watchlist deleted',
    // Role
    'find_all_roles_success' => 'Roles found',
    'find_role_success' => 'Role found',
    'find_role_404' => 'Role not found',
    'create_role_success' => 'Role created',
    'update_role_success' => 'Role updated',
    'delete_role_success' => 'Role deleted',
    // Visibility
    'find_all_visibilities_success' => 'Visibilities found',
    'find_visibility_success' => 'Visibility found',
    'find_visibility_404' => 'Visibility not found',
    'create_visibility_success' => 'Visibility created',
    'update_visibility_success' => 'Visibility updated',
    'delete_visibility_success' => 'Visibility deleted',
    // User
    'find_all_users_success' => 'Users found',
    'find_user_success' => 'User found',
    'find_api_token_success' => 'API token found',
    'find_user_404' => 'User not found',
    'find_concerned_404' => 'Concerned not found',
    'find_visitor_404' => 'Visitor not found',
    'create_user_success' => 'User created',
    'create_user_SMS_failed' => 'There is a problem with the SMS service',
    'update_user_success' => 'User updated',
    'update_password_success' => 'Password updated',
    'confirm_password_error' => 'Please confirm your password',
    'confirm_new_password' => 'Please confirm the new password',
    'delete_user_success' => 'User deleted',
    'login_user_success' => 'You are connected',
    'subscribe_user_success' => 'Subscription sent',
    'subscribe_user_accepted' => 'Subscription accepted',
    'unsubscribe_user_success' => 'Unsubscription done',
    // PasswordReset
    'find_all_password_resets_success' => 'Passwords resets found',
    'find_password_reset_success' => 'Password reset found',
    'find_password_reset_404' => 'Password reset not found',
    'create_password_reset_success' => 'Password reset created',
    'update_password_reset_success' => 'Password reset updated',
    'delete_password_reset_success' => 'Password reset deleted',
    'unverified_token' => 'The OTP code is not yet verified',
    'bad_token' => 'The OTP code does not match',
    'token_label' => 'Your OTP code:',
    // PersonalAccessToken
    'find_all_personal_access_tokens_success' => 'Personal tokens found',
    'find_personal_access_token_success' => 'Personal token found',
    'find_personal_access_token_404' => 'Personal token not found',
    'create_personal_access_token_success' => 'Personal token created',
    'update_personal_access_token_success' => 'Personal token updated',
    'delete_personal_access_token_success' => 'Personal token deleted',
    // Notification
    'find_all_notifications_success' => 'Notifications found',
    'find_notification_success' => 'Notification found',
    'find_notification_404' => 'Notification not found',
    'create_notification_success' => 'Notification created',
    'update_notification_success' => 'Notification updated',
    'delete_notification_success' => 'Notification deleted',
    // Payment
    'find_all_payments_success' => 'Payments found',
    'find_payment_success' => 'Payment found',
    'find_payment_404' => 'Payment not found',
    'processing_succeed' => 'Your transaction succeeded. You can see it at your payments list.',
    'error_while_processing' => 'An error while processing your request',
    'process_failed' => 'Unable to process the request, please try again',
    'process_canceled' => 'You canceled your transaction',
    'create_payment_success' => 'Payment created',
    'update_payment_success' => 'Payment updated',
    'delete_payment_success' => 'Payment deleted',
    // Event
    'find_all_events_success' => 'Events found',
    'find_event_success' => 'Event found',
    'find_event_404' => 'Event not found',
    'create_event_success' => 'Event created',
    'update_event_success' => 'Event updated',
    'delete_event_success' => 'Event deleted',
    // Community
    'find_all_communities_success' => 'Communities found',
    'find_community_success' => 'Community found',
    'find_community_404' => 'Community not found',
    'create_community_success' => 'Community created',
    'update_community_success' => 'Community updated',
    'delete_community_success' => 'Community deleted',
    // Subscription
    'find_all_subscriptions_success' => 'Subscriptions found',
    'find_subscription_success' => 'Subscription found',
    'find_subscription_404' => 'Subscription not found',
    'create_subscription_success' => 'Subscription created',
    'update_subscription_success' => 'Subscription updated',
    'delete_subscription_success' => 'Subscription deleted',

    // ===== USER HISTORIES
    // Subscriptions
    'connection_request' => 'You have sent a connection request to <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'accept_connection' => 'You have accepted <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’s connection request.',
    'reject_connection' => 'You have rejected <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’s connection request.',
    'community_subscription' => 'You subscribed to the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'event_subscription' => 'You subscribed to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    // Invitations
    'community_invitation' => 'You have sent <strong><a href="/users/:to_user_id">:to_user_names</a></strong> an invitation to the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'event_invitation' => 'You have sent <strong><a href="/users/:to_user_id">:to_user_names</a></strong> an invitation to the <strong><a href="/events/:event_id" >:event_title</a></strong> event.',
    'accept_community_invitation' => 'You have accepted <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’s invitation to the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'decline_community_invitation' => 'You rejected <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’s invitation to the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'accept_event_invitation' => 'You accepted <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’s invitation to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'maybe_event_invitation' => 'You may be at the <strong><a href="/events/:event_id">:event_title</a></strong> event for which received <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’s invitation.',
    'decline_event_invitation' => 'You have rejected <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ invitation to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    // Separation
    'community_separation' => 'You have left the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'event_separation' => 'You have left the <strong><a href="/events/:event_id" >:event_title</a></strong> event.',
    // Expulsion
    'community_expulsion' => 'You removed <strong><a href="/users/:to_user_id">:to_user_names</a></strong> from the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'event_expulsion' => 'You removed <strong><a href="/users/:to_user_id">:to_user_names</a></strong> from the <strong><a href="/events/:event_id" >:event_title</a></strong> event.',
    // Reactions
    'post_reaction' => 'You reacted to <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :post_type.',
    'message_reaction' => 'You reacted to <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ message.',
    // Posts & Comments
    'post_published' => 'You have published :new :post_type.',
    'post_published_in_community' => 'You have published :new :post_type in the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'post_published_one_mention' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> in :new :post_type.',
    'post_published_two_mentions' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> and another person in :new :post_type.',
    'post_published_many_mentions' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> and :mentions_count others in :new :post_type.',
    'post_published_followers_mention' => 'You have mentioned all your connections in :new :post_type.',
    'post_shared' => 'You have shared :the <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :post_type.',
    'anonymous_question_sent' => 'You sent an anonymous question to <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent' => 'You commented on <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :the :post_type.',
    'comment_sent_to_event' => 'You commented on the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'comment_sent_one_mention' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> in your comment on <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :the :post_type.',
    'comment_sent_two_mentions' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> and another person in your comment on <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :the :post_type.',
    'comment_sent_many_mentions' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> and :mentions_count others in your comment on <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :the :post_type.',
    'comment_sent_followers_mention' => 'You have mentioned all your connections in your comment on <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :the :post_type.',
    'comment_sent_one_mention' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> in your comment on your :post_type.',
    'comment_sent_two_mentions' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> and another person in your comment on your :post_type.',
    'comment_sent_many_mentions' => 'You have mentioned <strong><a href="/users/:to_user_id">:to_user_names</a></strong> and :mentions_count others in your comment on your :post_type.',
    'comment_sent_followers_mention' => 'You have mentioned all your connections in your comment on your :post_type.',
    'survey_answered' => 'You responded to the <strong><a href="/users/:to_user_id">:to_user_names</a></strong> survey.',
    // Payment
    'payment_failed' => 'You made a payment that was unsuccessful.',
    'payment_done' => 'You have successfully made your payment.',
    // Account created
    'account_created' => 'You have created your account.',
    // Account disabled
    'account_disabled' => 'You have disabled your account.',
    // Account reactivated
    'account_reactivated' => 'You have reactivated your account.',
    // Account deleted
    'account_deleted' => 'You have deleted your account.',
    // Account type changed
    'account_type_changed' => 'You have changed your account type.',

    // ===== PUBLIC NOTIFICATIONS
    // Subscriptions
    'one_direct_connection' => 'You are now connected to <strong><a href="/users/:from_user_id">:from_user_names</a></strong>.',
    'two_direct_connections' => 'You are now connected to <strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person.',
    'many_direct_connections' => 'You are now connected to <strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :requests_count others.',
    'one_connection_request' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has sent you a connection request.',
    'two_connection_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person sent you a connection request.',
    'many_connection_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :requests_count others sent you a connection request.',
    'one_accept_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> accepted your connection request.',
    'two_accepts_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person accepted your connection request.',
    'many_accepts_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :responses_count others accepted your connection request.',
    'one_reject_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> rejected your connection request.',
    'two_rejects_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person rejected your connection request.',
    'many_rejects_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :responses_count others rejected your connection request.',
    'one_community_subscription_request' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has subscribed to your <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'two_community_subscription_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person have subscribed to your <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'many_community_subscription_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :requests_count others have subscribed to your <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'one_event_subscription_request' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has subscribed to your <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'two_event_subscription_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person have subscribed to your <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'many_event_subscription_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :requests_count others have subscribed to your <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    // Invitations
    'one_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :invited you to the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'two_communities_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person :invited you to communities.',
    'many_communities_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :invitations_count others :invited you to communities.',
    'one_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :invited you to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'two_events_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person :invited you to events.',
    'many_events_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :invitations_count others :invited you to events.',
    'one_accept_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has accepted your invitation to the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'two_accepts_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person have accepted your invitation to the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'many_accepts_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :responses_count others have accepted your invitation to the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'one_maybe_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> may be at your <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'two_maybes_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person may be at your <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'many_maybes_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :responses_count others may be at your <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'one_accept_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has accepted your invitation to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'two_accepts_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person accepted your invitation to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'many_accepts_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :responses_count others accepted your invitation to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'one_decline_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has declined your invitation to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'two_declines_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person declined your invitation to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    'many_declines_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :responses_count others declined your invitation to the <strong><a href="/events/:event_id">:event_title</a></strong> event.',
    // Reactions
    'one_person_cheered_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> cheered your :post_type.',
    'two_persons_cheered_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person cheered your :post_type.',
    'many_persons_cheered_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :reactions_count others cheered your post.',
    'one_person_liked_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> liked your :post_type.',
    'two_persons_liked_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person liked your :post_type.',
    'many_persons_liked_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :reactions_count others liked your :post_type.',
    'one_person_supported_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> supports :post_type.',
    'two_persons_supported_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person support your :post_type.',
    'many_persons_supported_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :reactions_count others support your :post_type.',
    'one_person_post_interesting' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> considers your :post_type :interesting.',
    'two_persons_post_interesting' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person consider your :post_type :interesting.',
    'many_persons_post_interesting' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :reactions_count others consider your :post_type :interesting.',
    'one_person_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> reacted to your message.',
    'two_persons_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person reacted to your message.',
    'many_persons_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :reactions_count others reacted to your message.',
    // Posts & Comments
    'one_post_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has published :new :post_type.',
    'two_posts_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person have published :new :posts_type.',
    'many_posts_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :posts_count others have published :new :posts_type.',
    'one_post_published_after_some_times' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> published :new :post_type after some time of absence.',
    'two_posts_published_after_some_times' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person published :new :posts_type after some time of absence.',
    'many_posts_published_after_some_times' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :posts_count others published :new :posts_type after some time of absence.',
    'one_post_published_in_community' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has published :new :post_type in the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'two_posts_published_in_community' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person have published :new :posts_type in the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'many_posts_published_in_community' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :posts_count others have published :new :posts_type in the <strong><a href="/communities/:community_id">:community_name</a></strong> community.',
    'one_mention_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you in :new :post_type.',
    'two_mentions_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and another person in :new :post_type.',
    'many_mentions_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and :mentions_count others in :new :post_type.',
    'followers_mention_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and all his connections in :new :post_type.',
    'one_share_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> has shared your :post_type.',
    'two_shares_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person have shared your :post_type.',
    'many_shares_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :shares_count others have shared your :post_type.',
    'one_anonymous_question_received' => 'You received an anonymous question.',
    'many_anonymous_question_received' => 'You received anonymous questions.',
    'one_comment_sent' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> commented on your :post_type.',
    'two_comments_sent' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person commented on your :post_type.',
    'many_comments_sent' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :comments_count others commented on your :post_type.',
    'one_mention_comment' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you in his comment on :the <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :post_type.',
    'two_mentions_comment' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and another person in his comment on :the <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :post_type.',
    'many_mentions_comment' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and :mentions_count others in his comment on :the <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :post_type.',
    'followers_mention_comment' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and all his connections in his comment on :the <strong><a href="/users/:to_user_id">:to_user_names</a></strong>’ :post_type.',
    'one_mention_comment_self_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you in his comment on :one_s :post_type.',
    'two_mentions_comment_self_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and another person in his comment on :one_s :post_type.',
    'many_mentions_comment_self_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and :mentions_count others in his comment on :one_s :post_type.',
    'followers_mention_comment_self_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> :mentioned you and all his connections in his comment on :one_s :post_type.',
    // Payment
    'one_payment_done' => 'Your payment was done with code <strong>:code</strong>. Please click here for payment details.',
    // Account created
    'welcome' => 'Welcome <strong><a href="/users/:to_user_id">:to_user_names</a></strong>. Click here to get a complete overview of your network.',
    // Account reactivated
    'welcome_back' => 'Welcome back <strong><a href="/users/:to_user_id">:to_user_names</a></strong>. Click here to see what’s new in your network.',
    // Account blocked
    'your_account_blocked' => 'Your account was blocked on <strong>:date</strong> at <strong>:hour</strong>, because you did not respect our conditions.',
    // Account will be blocked
    'will_be_blocked_soon' => 'We are already on the <strong>:nth_personne</strong> person who has reported your <strong>:subject</strong> as « <strong>:reason</strong> ». <strong>:persons_count</strong> more and you’ll soon be blocked.',
    // Account deleted
    'before_definitive_delete' => 'Your account has been removed since <strong>:date</strong> at <strong>:hour</strong>.<br><br>You can still cancel this action before 15 days, in case you change the mind.<br><br>After <strong>:after_date</strong>, your account will be deleted definitively.',
    // Member certified
    'member_certified' => 'Your account is now certified. From now on, you too are able to sell our products or offer your services. Click here to see how it works.',
    // Member premium
    'member_premium' => 'Now that you have a premium account, you are able to use our services to make you talk about you. Click here to see how it works.',
    // Celebration
    'one_celebration' => 'It’s <strong><a href="/users/:from_user_id">:from_user_names</a></strong>’s birthday.',
    'two_celebrations' => 'It’s <strong><a href="/users/:from_user_id">:from_user_names</a></strong> and another person’s birthday.',
    'many_celebrations' => 'It’s <strong><a href="/users/:from_user_id">:from_user_names</a></strong> and :celebrations_count other people’s birthday.',
    // Views analytics for profil
    'profil_view_analytics' => 'Your profile has been viewed :views_count times :period. Click to see the statistics.',
    // Views analytics for post
    'post_view_analytics' => 'Your :post_type has been viewed :views_count times :period. Click to see the statistics.',
];
