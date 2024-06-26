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
    '400_title' => 'Mauvaise requête',
    '400_description' => 'Vérifiez votre requête s’il vous plait !',
    // 401
    '401_title' => 'Non autorisé',
    '401_description' => 'Vous n’avez pas d’autorisation pour cette requête.',
    // 403
    '403_title' => 'Espace interdit',
    '403_description' => 'Cet espace n’est pas permis.',
    // 404
    '404_title' => 'Page non trouvée',
    '404_description' => 'La page que vous cherchez n’existe pas',
    // 405
    '405_title' => 'Méthode non permise',
    '405_description' => 'Votre requête est envoyée avec une mauvaise méthode.',
    // 419
    '419_title' => 'Page expirée',
    '419_description' => 'La page a mis longtemps sans activité.',
    // 500
    '500_title' => 'Erreur interne',
    '500_description' => 'Notre serveur rencontre un problème. Veuillez réessayez après quelques minutes s’il vous plait !',
    // Others
    'expects_json' => 'La requête actuelle attend probablement une réponse JSON.',

    // ===== ALERTS
    'no_record' => 'Il n’y a aucun enregistrement !',
    'create_error' => 'La création a échoué !',
    'update_error' => 'La modification a échoué !',
    'registered_data' => 'Données enregistrées',
    'required_fields' => 'Veuillez vérifier les champs obligatoires',
    'transaction_waiting' => 'Veuillez valider le message de votre opérateur sur votre téléphone. Ensuite appuyez sur le bouton ci-dessous.',
    'transaction_done' => 'Votre opération est terminée !',
    'transaction_failed' => 'L’envoi de votre paiement a échoué',
    'transaction_type_error' => 'Veuillez choisir le type de transaction',
    'new_partner_message' => 'Vous pouvez maintenant vous connecter en tant que partenaire avec votre n° de téléphone. Mot de passe temportaire :',
    'reaction_sent' => 'Réaction envoyée',
    'invitation_sent' => 'Invitation envoyée',
    // Group
    'find_all_groups_success' => 'Groupes trouvés',
    'find_group_success' => 'Groupe trouvé',
    'find_group_404' => 'Groupe non trouvé',
    'create_group_success' => 'Groupe créé',
    'update_group_success' => 'Groupe modifié',
    'delete_group_success' => 'Groupe supprimé',
    // Status
    'find_all_statuses_success' => 'Etats trouvés',
    'find_status_success' => 'Etat trouvé',
    'find_status_404' => 'Etat non trouvé',
    'create_status_success' => 'Etat créé',
    'update_status_success' => 'Etat modifié',
    'delete_status_success' => 'Etat supprimé',
    // Type
    'find_all_types_success' => 'Types trouvés',
    'find_type_success' => 'Type trouvé',
    'find_type_404' => 'Type non trouvé',
    'create_type_success' => 'Type créé',
    'update_type_success' => 'Type modifié',
    'delete_type_success' => 'Type supprimé',
    // Country
    'find_all_countries_success' => 'Pays trouvés',
    'find_country_success' => 'Pays trouvé',
    'find_country_404' => 'Pays non trouvé',
    'create_country_success' => 'Pays créé',
    'update_country_success' => 'Pays modifié',
    'delete_country_success' => 'Pays supprimé',
    // Order
    'find_all_orders_success' => 'Commandes trouvées',
    'find_order_success' => 'Commande trouvée',
    'find_order_404' => 'Commande non trouvée',
    'create_order_success' => 'Commande créée',
    'update_order_success' => 'Commande modifiée',
    'delete_order_success' => 'Commande supprimée',
    // Cart
    'find_all_carts_success' => 'Paniers ou Watchlists trouvés',
    'find_cart_success' => 'Panier ou Watchlist trouvé',
    'find_cart_404' => 'Panier ou Watchlist non trouvé',
    'create_cart_success' => 'Panier ou Watchlist créé',
    'update_cart_success' => 'Panier ou Watchlist modifié',
    'delete_cart_success' => 'Panier ou Watchlist supprimé',
    // Role
    'find_all_roles_success' => 'Rôles trouvés',
    'find_role_success' => 'Rôle trouvé',
    'find_role_404' => 'Rôle non trouvé',
    'create_role_success' => 'Rôle créé',
    'update_role_success' => 'Rôle modifié',
    'delete_role_success' => 'Rôle supprimé',
    // Visibility
    'find_all_visibilities_success' => 'Visibilités trouvées',
    'find_visibility_success' => 'Visibilité trouvée',
    'find_visibility_404' => 'Visibilité non trouvée',
    'create_visibility_success' => 'Visibilité créée',
    'update_visibility_success' => 'Visibilité modifiée',
    'delete_visibility_success' => 'Visibilité supprimée',
    // User
    'find_all_users_success' => 'Utilisateurs trouvés',
    'find_user_success' => 'Utilisateur trouvé',
    'find_api_token_success' => 'Jeton d’API trouvé',
    'find_user_404' => 'Utilisateur non trouvé',
    'find_concerned_404' => 'Concerné non trouvé',
    'find_visitor_404' => 'Visiteur non trouvé',
    'create_user_success' => 'Utilisateur créé',
    'create_user_SMS_failed' => 'Il y a un problème avec le service des SMS',
    'update_user_success' => 'Utilisateur modifié',
    'update_password_success' => 'Mot de passe modifié',
    'confirm_password_error' => 'Veuillez confirmer votre mot de passe',
    'confirm_new_password' => 'Veuillez confirmer le nouveau mot de passe',
    'delete_user_success' => 'Utilisateur supprimé',
    'login_user_success' => 'Vous êtes connecté(e)',
    'subscribe_user_success' => 'Abonnement envoyé',
    'subscribe_user_accepted' => 'Abonnement accepté',
    'unsubscribe_user_success' => 'Désabonnement effectué',
    // PasswordReset
    'find_all_password_resets_success' => 'Réinitialisations des mots de passe trouvées',
    'find_password_reset_success' => 'Réinitialisation de mot de passe trouvée',
    'find_password_reset_404' => 'Réinitialisation de mot de passe non trouvée',
    'create_password_reset_success' => 'Réinitialisation de mot de passe créée',
    'update_password_reset_success' => 'Réinitialisation de mot de passe modifiée',
    'delete_password_reset_success' => 'Réinitialisation de mot de passe supprimée',
    'unverified_token' => 'Le code OTP n’est pas encore vérifié',
    'bad_token' => 'Le code OTP ne correspond pas',
    'token_label' => 'Votre code OTP :',
    // PersonalAccessToken
    'find_all_personal_access_tokens_success' => 'Jetons personnels trouvés',
    'find_personal_access_token_success' => 'Jeton personnel trouvé',
    'find_personal_access_token_404' => 'Jeton personnel non trouvé',
    'create_personal_access_token_success' => 'Jeton personnel créé',
    'update_personal_access_token_success' => 'Jeton personnel modifié',
    'delete_personal_access_token_success' => 'Jeton personnel supprimé',
    // Notification
    'find_all_notifications_success' => 'Notifications trouvées',
    'find_notification_success' => 'Notification trouvée',
    'find_notification_404' => 'Notification non trouvée',
    'create_notification_success' => 'Notification créée',
    'update_notification_success' => 'Notification modifiée',
    'delete_notification_success' => 'Notification supprimée',
    // Payment
    'find_all_payments_success' => 'Paiements trouvés',
    'find_payment_success' => 'Paiement trouvé',
    'find_payment_404' => 'Paiement non trouvé',
    'processing_succeed' => 'Votre transaction a réussie. Vous pouvez la voir à la liste de vos paiements.',
    'error_while_processing' => 'Une erreur lors du traitement de votre requête',
    'process_failed' => 'Impossible de traiter la demande, veuillez réessayer',
    'process_canceled' => 'Vous avez annulé votre transaction',
    'create_payment_success' => 'Paiement créé',
    'update_payment_success' => 'Paiement modifié',
    'delete_payment_success' => 'Paiement supprimé',
    // Event
    'find_all_events_success' => 'Evénements trouvés',
    'find_event_success' => 'Evénement trouvé',
    'find_event_404' => 'Evénement non trouvé',
    'create_event_success' => 'Evénement créé',
    'update_event_success' => 'Evénement modifié',
    'delete_event_success' => 'Evénement supprimé',
    // Community
    'find_all_communities_success' => 'Communautés trouvées',
    'find_community_success' => 'Communauté trouvée',
    'find_community_404' => 'Communauté non trouvée',
    'create_community_success' => 'Communauté créée',
    'update_community_success' => 'Communauté modifiée',
    'delete_community_success' => 'Communauté supprimée',
    // Subscription
    'find_all_subscriptions_success' => 'Abonnements trouvés',
    'find_subscription_success' => 'Abonnement trouvé',
    'find_subscription_404' => 'Abonnement non trouvé',
    'create_subscription_success' => 'Abonnement créé',
    'update_subscription_success' => 'Abonnement modifié',
    'delete_subscription_success' => 'Abonnement supprimé',

    // ===== USER HISTORIES
    // Subscriptions
    'connection_request' => 'Vous avez envoyé une demande de connexion à <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'accept_connection' => 'Vous avez accepté la demande de connexion de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'reject_connection' => 'Vous avez rejeté la demande de connexion de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'community_subscription' => 'Vous avez souscrit à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'event_subscription' => 'Vous avez souscrit à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    // Invitations
    'community_invitation' => 'Vous avez envoyé à <strong><a href="/users/:to_user_id">:to_user_names</a></strong> une invitation à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'event_invitation' => 'Vous avez envoyé à <strong><a href="/users/:to_user_id">:to_user_names</a></strong> une invitation à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'accept_community_invitation' => 'Vous avez accepté l’invitation de <strong><a href="/users/:to_user_id">:to_user_names</a></strong> à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'decline_community_invitation' => 'Vous avez rejeté l’invitation de <strong><a href="/users/:to_user_id">:to_user_names</a></strong> à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'accept_event_invitation' => 'Vous avez accepté l’invitation de <strong><a href="/users/:to_user_id">:to_user_names</a></strong> à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'maybe_event_invitation' => 'Vous serez peut-être à l’événement <strong><a href="/events/:event_id">:event_title</a></strong> pour lequel vous avez reçu l’invitation de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'decline_event_invitation' => 'Vous avez rejeté l’invitation de <strong><a href="/users/:to_user_id">:to_user_names</a></strong> à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    // Separation
    'community_separation' => 'Vous avez quitté la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'event_separation' => 'Vous avez quitté l’événement <strong><a href="/events/:event_id" >:event_title</a></strong>.',
    // Expulsion
    'community_expulsion' => 'Vous avez retiré <strong><a href="/users/:to_user_id">:to_user_names</a></strong> de la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'event_expulsion' => 'Vous avez retiré <strong><a href="/users/:to_user_id">:to_user_names</a></strong> de l’événement <strong><a href="/events/:event_id" >:event_title</a></strong>.',
    // Reactions
    'post_reaction' => 'Vous avez réagi au :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'message_reaction' => 'Vous avez réagi au message de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    // Posts & Comments
    'post_published' => 'Vous avez publié :new :post_type.',
    'post_published_in_community' => 'Vous avez publié :new :post_type dans la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'post_published_one_mention' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> dans :new :post_type.',
    'post_published_two_mentions' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et une autre personne dans :new :post_type.',
    'post_published_many_mentions' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et :mentions_count autres personnes dans :new :post_type.',
    'post_published_followers_mention' => 'Vous avez mentionné toutes vos connexions dans :new :post_type.',
    'post_shared' => 'Vous avez partagé :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'anonymous_question_sent' => 'Vous avez envoyé une question anonyme à <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent' => 'Vous avez commenté :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_to_event' => 'Vous avez commenté à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'comment_sent_one_mention' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> dans votre commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_two_mentions' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et une autre personne dans votre commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_many_mentions' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et :mentions_count autres personnes dans votre commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_followers_mention' => 'Vous avez mentionné toutes vos connexions dans votre commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_one_mention_self_post' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> dans votre commentaire sur votre :post_type.',
    'comment_sent_two_mentions_self_post' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et une autre personne dans votre commentaire sur votre :post_type.',
    'comment_sent_many_mentions_self_post' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et :mentions_count autres personnes dans votre commentaire sur votre :post_type.',
    'comment_sent_followers_mention_self_post' => 'Vous avez mentionné toutes vos connexions dans votre commentaire sur votre :post_type.',
    'survey_answered' => 'Vous avez répondu au sondage de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    // Payment
    'payment_failed' => 'Vous avez effectué un paiement qui n’a pas abouti.',
    'payment_done' => 'Vous avez effectué votre paiement avec succès.',
    // Account created
    'account_created' => 'Vous avez créé votre compte.',
    // Account disabled
    'account_disabled' => 'Vous avez désactivé votre compte.',
    // Account reactivated
    'account_reactivated' => 'Vous avez réactivé votre compte.',
    // Account deleted
    'account_deleted' => 'Vous avez supprimé votre compte.',
    // Account type changed
    'account_type_changed' => 'Vous avez changé votre type de compte.',

    // ===== PUBLIC NOTIFICATIONS
    // Subscriptions
    'one_direct_connection' => 'Vous êtes maintenant connecté(e) à <strong><a href="/users/:from_user_id">:from_user_names</a></strong>.',
    'two_direct_connections' => 'Vous êtes maintenant connecté(e) à <strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne.',
    'many_direct_connections' => 'Vous êtes maintenant connecté(e) à <strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :requests_count autres personnes.',
    'one_connection_request' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a envoyé une demande de connexion.',
    'two_connection_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne vous ont envoyé une demande de connexion.',
    'many_connection_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :requests_count autres personnes vous ont envoyé une demande de connexion.',
    'one_accept_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a accepté votre demande de connexion.',
    'two_accepts_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont accepté votre demande de connexion.',
    'many_accepts_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :responses_count autres personnes ont accepté votre demande de connexion.',
    'one_reject_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a rejeté votre demande de connexion.',
    'two_rejects_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont rejeté votre demande de connexion.',
    'many_rejects_connection' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :responses_count autres personnes ont rejeté votre demande de connexion.',
    'one_community_subscription_request' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a souscrit à votre communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'two_community_subscription_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont souscrit à votre communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'many_community_subscription_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :requests_count autres personnes ont souscrit à votre communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'one_event_subscription_request' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a souscrit à votre événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'two_event_subscription_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont souscrit à votre événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'many_event_subscription_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :requests_count autres personnes ont souscrit à votre événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    // Invitations
    'one_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :invited à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'two_communities_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne vous ont :invited à des communautés.',
    'many_communities_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :invitations_count autres personnes vous ont :invited à des communautés.',
    'one_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :invited à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'two_events_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne vous ont :invited à des événements.',
    'many_events_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :invitations_count autres personnes vous ont :invited à des événements.',
    'one_accept_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a accepté votre invitation à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'two_accepts_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont accepté votre invitation à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'many_accepts_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :responses_count autres personnes ont accepté votre invitation à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'one_maybe_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> sera peut-être à votre événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'two_maybes_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne seront peut-être à votre événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'many_maybes_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :responses_count autres personnes seront peut-être à votre événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'one_accept_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a accepté votre invitation à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'two_accepts_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont accepté votre invitation à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'many_accepts_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :responses_count autres personnes ont accepté votre invitation à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'one_decline_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a décliné votre invitation à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'two_declines_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont décliné votre invitation à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'many_declines_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :responses_count autres personnes ont décliné votre invitation à l’événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    // Reactions
    'one_person_cheered_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a applaudi votre :post_type.',
    'two_persons_cheered_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont applaudi votre :post_type.',
    'many_persons_cheered_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :reactions_count autres personnes ont applaudi votre :post_type.',
    'one_person_liked_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a aimé votre :post_type.',
    'two_persons_liked_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont aimé votre :post_type.',
    'many_persons_liked_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :reactions_count autres personnes ont aimé votre :post_type.',
    'one_person_supported_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> soutient votre :post_type.',
    'two_persons_supported_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne soutiennent votre :post_type.',
    'many_persons_supported_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :reactions_count autres personnes soutiennent votre :post_type.',
    'one_person_post_interesting' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> trouve votre :post_type :interesting.',
    'two_persons_post_interesting' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne trouvent votre :post_type :interesting.',
    'many_persons_post_interesting' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :reactions_count autres personnes trouvent votre :post_type :interesting.',
    'one_person_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a réagi à votre message.',
    'two_persons_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont réagi à votre message.',
    'many_persons_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :reactions_count autres personnes ont réagi à votre message.',
    // Posts & Comments
    'one_post_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a publié :new :post_type.',
    'two_posts_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont publié :new :posts_type.',
    'many_posts_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :posts_count autres personnes ont publié :new :posts_type.',
    'one_post_published_after_some_times' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a publié :new :post_type après quelques temps d’absence.',
    'two_posts_published_after_some_times' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont publié :new :posts_type après quelques temps d’absence.',
    'many_posts_published_after_some_times' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :posts_count autres personnes ont publié :new :posts_type après quelques temps d’absence.',
    'one_post_published_in_community' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a publié :new :post_type dans la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'two_posts_published_in_community' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont publié :new :posts_type dans la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'many_posts_published_in_community' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :posts_count autres personnes ont publié :new :posts_type dans la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'one_mention_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned dans :new :post_type.',
    'two_mentions_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned et une autre personne dans :new :post_type.',
    'many_mentions_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned et :mentions_count autres personnes dans :new :post_type.',
    'followers_mention_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned, vous et toutes ses connexions dans :new :post_type.',
    'one_share_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a partagé votre :post_type.',
    'two_shares_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont partagé votre :post_type.',
    'many_shares_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :shares_count autres personnes ont partagé votre :post_type.',
    'one_anonymous_question_received' => 'Vous avez reçu une question anonyme.',
    'many_anonymous_question_received' => 'Vous avez reçu des questions anonymes.',
    'one_comment_sent' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a commenté votre :post_type.',
    'two_comments_sent' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont commenté votre :post_type.',
    'many_comments_sent' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :comments_count autres personnes ont commenté votre :post_type.',
    'one_mention_comment' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned dans son commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'two_mentions_comment' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned et une autre personne dans son commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'many_mentions_comment' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned et :mentions_count autres personnes dans son commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'followers_mention_comment' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned, vous et toutes ses connexions dans son commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'one_mention_comment_self_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned dans son commentaire sur :one_s :post_type.',
    'two_mentions_comment_self_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned et une autre personne dans son commentaire sur :one_s :post_type.',
    'many_mentions_comment_self_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned et :mentions_count autres personnes dans son commentaire sur :one_s :post_type.',
    'followers_mention_comment_self_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned, vous et toutes ses connexions dans son commentaire sur :one_s :post_type.',
    // Payment
    'one_payment_done' => 'Votre paiement a été effectué avec le code <strong>:code</strong>. Veuillez cliquer ici pour les détails sur le paiement.',
    // Account created
    'welcome' => 'Bienvenue <strong><a href="/users/:to_user_id">:to_user_names</a></strong>. Cliquez ici pour avoir un aperçu complet de votre réseau.',
    // Account reactivated
    'welcome_back' => 'Bon retour <strong><a href="/users/:to_user_id">:to_user_names</a></strong>. Cliquez ici pour voir les nouveautés de votre réseau.',
    // Account blocked
    'your_account_blocked' => 'Votre a été bloqué le <strong>:date</strong> à <strong>:hour</strong>, parce que vous ne respectiez pas nos conditions.',
    // Account will be blocked
    'will_be_blocked_soon' => 'Nous sommes déjà à la <strong>:nth_personne</strong> qui signale votre <strong>:subject</strong> comme « <strong>:reason</strong> ». Encore <strong>:persons_count</strong> et vous serez bientôt bloqué.',
    // Account deleted
    'before_definitive_delete' => 'Votre a été supprimé depuis le <strong>:date</strong> à <strong>:hour</strong>.<br><br>Vous pouvez encore annuler cette action avant 15 jours, au cas où vous changeriez d’avis.<br><br>Après le <strong>:after_date</strong>, votre compte sera supprimé définitivement.',
    // Member certified
    'member_certified' => 'Votre compte est maintenant certifié. Désormais, vous aussi, vous êtes en mesure de vendre vos produits ou offrir vos services. Cliquez ici pour voir comment ça marche.',
    // Member premium
    'member_premium' => 'Maintenant que vous avez un compte premium, vous êtes en mesure d’utiliser nos services pour davantage faire parler de vous. Cliquez ici pour voir comment ça marche.',
    // Celebration
    'one_celebration' => 'C’est l’anniversaire de <strong><a href="/users/:from_user_id">:from_user_names</a></strong>.',
    'two_celebrations' => 'C’est l’anniversaire de <strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne.',
    'many_celebrations' => 'C’est l’anniversaire de <strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :celebrations_count autres personnes.',
    // Views analytics for profil
    'profil_view_analytics' => 'Votre profil a été consulté :views_count fois :period. Cliquez pour voir les statistiques.',
    // Views analytics for post
    'post_view_analytics' => 'Votre :post_type a été consulté :views_count fois :period. Cliquez pour voir les statistiques.',
];
