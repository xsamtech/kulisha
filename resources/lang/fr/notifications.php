<?php
/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
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
    '400_description' => 'Vérifiez votre requête s\'il vous plait !',
    // 401
    '401_title' => 'Non autorisé',
    '401_description' => 'Vous n\'avez pas d\'autorisation pour cette requête.',
    // 403
    '403_title' => 'Espace interdit',
    '403_description' => 'Cet espace n\'est pas permis.',
    // 404
    '404_title' => 'Page non trouvée',
    '404_description' => 'La page que vous cherchez n\'existe pas',
    // 405
    '405_title' => 'Méthode non permise',
    '405_description' => 'Votre requête est envoyée avec une mauvaise méthode.',
    // 419
    '419_title' => 'Page expirée',
    '419_description' => 'La page a mis longtemps sans activité.',
    // 500
    '500_title' => 'Erreur interne',
    '500_description' => 'Notre serveur rencontre un problème. Veuillez réessayez après quelques minutes s\'il vous plait !',
    // Others
    'expects_json' => 'La requête actuelle attend probablement une réponse JSON.',

    // ===== ALERTS
    'no_record' => 'Il n\'y a aucun enregistrement !',
    'create_error' => 'La création a échoué !',
    'update_error' => 'La modification a échoué !',
    'registered_data' => 'Données enregistrées',
    'required_fields' => 'Veuillez vérifier les champs obligatoires',
    'transaction_waiting' => 'Veuillez valider le message de votre opérateur sur votre téléphone. Ensuite appuyez sur le bouton ci-dessous.',
    'transaction_done' => 'Votre opération est terminée !',
    'transaction_failed' => 'L\'envoi de votre paiement a échoué',
    'transaction_type_error' => 'Veuillez choisir le type de transaction',
    'new_partner_message' => 'Vous pouvez maintenant vous connecter en tant que partenaire avec votre n° de téléphone. Mot de passe temportaire :',
    // LegalInfoSubject
    'find_all_legal_info_subjects_success' => 'Sujets trouvés',
    'find_legal_info_subject_success' => 'Sujet trouvé',
    'find_legal_info_subject_404' => 'Sujet non trouvé',
    'create_legal_info_subject_success' => 'Sujet créé',
    'update_legal_info_subject_success' => 'Sujet modifié',
    'delete_legal_info_subject_success' => 'Sujet supprimé',
    // LegalInfoTitle
    'find_all_legal_info_titles_success' => 'Titres trouvés',
    'find_legal_info_title_success' => 'Titre trouvé',
    'find_legal_info_title_404' => 'Titre non trouvé',
    'create_legal_info_title_success' => 'Titre créé',
    'update_legal_info_title_success' => 'Titre modifié',
    'delete_legal_info_title_success' => 'Titre supprimé',
    // LegalInfoContent
    'find_all_legal_info_contents_success' => 'Contenus trouvés',
    'find_legal_info_content_success' => 'Contenu trouvé',
    'find_legal_info_content_404' => 'Contenu non trouvé',
    'create_legal_info_content_success' => 'Contenu créé',
    'update_legal_info_content_success' => 'Contenu modifié',
    'delete_legal_info_content_success' => 'Contenu supprimé',
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
    // Book
    'find_all_books_success' => 'Livres trouvés',
    'find_book_success' => 'Livre trouvé',
    'find_book_404' => 'Livre non trouvé',
    'create_book_success' => 'Livre créé',
    'update_book_success' => 'Livre modifié',
    'delete_book_success' => 'Livre supprimé',
    // Media
    'find_all_medias_success' => 'Médias trouvés',
    'find_media_success' => 'Média trouvé',
    'find_media_404' => 'Média non trouvé',
    'create_media_success' => 'Média créé',
    'update_media_success' => 'Média modifié',
    'delete_media_success' => 'Média supprimé',
    // Part
    'find_all_parts_success' => 'Parties des médias trouvées',
    'find_part_success' => 'Partie du média trouvée',
    'find_part_404' => 'Partie du média non trouvée',
    'create_part_success' => 'Partie du média créée',
    'update_part_success' => 'Partie du média modifiée',
    'delete_part_success' => 'Partie du média supprimée',
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
    // Pricing
    'find_all_pricings_success' => 'Tarifications trouvées',
    'find_pricing_success' => 'Tarification trouvée',
    'find_pricing_404' => 'Tarification non trouvée',
    'create_pricing_success' => 'Tarification créée',
    'update_pricing_success' => 'Tarification modifiée',
    'delete_pricing_success' => 'Tarification supprimée',
    // Role
    'find_all_roles_success' => 'Rôles trouvés',
    'find_role_success' => 'Rôle trouvé',
    'find_role_404' => 'Rôle non trouvé',
    'create_role_success' => 'Rôle créé',
    'update_role_success' => 'Rôle modifié',
    'delete_role_success' => 'Rôle supprimé',
    // User
    'find_all_users_success' => 'Utilisateurs trouvés',
    'find_user_success' => 'Utilisateur trouvé',
    'find_api_token_success' => 'Jeton d\'API trouvé',
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
    // PasswordReset
    'find_all_password_resets_success' => 'Réinitialisations des mots de passe trouvées',
    'find_password_reset_success' => 'Réinitialisation de mot de passe trouvée',
    'find_password_reset_404' => 'Réinitialisation de mot de passe non trouvée',
    'create_password_reset_success' => 'Réinitialisation de mot de passe créée',
    'update_password_reset_success' => 'Réinitialisation de mot de passe modifiée',
    'delete_password_reset_success' => 'Réinitialisation de mot de passe supprimée',
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
    // Donation
    'find_all_donations_success' => 'Donations trouvées',
    'find_donation_success' => 'Donation trouvée',
    'find_donation_404' => 'Donation non trouvée',
    'create_donation_success' => 'Donation créée',
    'update_donation_success' => 'Donation modifiée',
    'delete_donation_success' => 'Donation supprimée',

    // ===== USER HISTORIES
    // Subscriptions
    'connection_request' => 'Vous avez envoyé une demande de connexion à <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    // Invitations
    'community_invitation' => 'Vous avez envoyé à <strong><a href="/users/:to_user_id">:to_user_names</a></strong> une invitation à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'event_invitation' => 'Vous avez envoyé à <strong><a href="/users/:to_user_id">:to_user_names</a></strong> une invitation à l\'événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    // Reactions
    'post_reaction' => 'Vous avez réagi au :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'message_reaction' => 'Vous avez réagi au message de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    // Posts & Comments
    'post_published' => 'Vous avez publié :a_new :post_type.',
    'post_published_one_mention' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> dans :a_new :post_type.',
    'post_published_two_mentions' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et une autre personne dans :a_new :post_type.',
    'post_published_many_mentions' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et :mentions_count autres personnes dans :a_new :post_type.',
    'post_published_followers_mention' => 'Vous avez mentionné toutes vos connexions dans :a_new :post_type.',
    'anonymous_question_sent' => 'Vous avez envoyé une question anonyme à <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent' => 'Vous avez commenté :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_one_mention' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> dans votre commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_two_mentions' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et une autre personne dans votre commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_many_mentions' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et :mentions_count autres personnes dans votre commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_followers_mention' => 'Vous avez mentionné toutes vos connexions dans votre commentaire sur :the :post_type de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',
    'comment_sent_one_mention_self_post' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> dans votre commentaire sur votre :post_type.',
    'comment_sent_two_mentions_self_post' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et une autre personne dans votre commentaire sur votre :post_type.',
    'comment_sent_many_mentions_self_post' => 'Vous avez mentionné <strong><a href="/users/:to_user_id">:to_user_names</a></strong> et :mentions_count autres personnes dans votre commentaire sur votre :post_type.',
    'comment_sent_followers_mention_self_post' => 'Vous avez mentionné toutes vos connexions dans votre commentaire sur votre :post_type.',
    'survey_answered' => 'Vous avez répondu au sondage de <strong><a href="/users/:to_user_id">:to_user_names</a></strong>.',

    // ===== PUBLIC NOTIFICATIONS
    // Subscriptions
    'one_connection_request' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a envoyé une demande de connexion.',
    'two_connection_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne vous ont envoyé une demande de connexion.',
    'many_connection_requests' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :requests_count autres personnes vous ont envoyé une demande de connexion.',
    // Invitations
    'one_community_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :invited à la communauté <strong><a href="/communities/:community_id">:community_name</a></strong>.',
    'two_communities_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne vous ont :invited à des communautés.',
    'many_communities_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :invitations_count autres personnes vous ont :invited à des communautés.',
    'one_event_invitation' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :invited à l\'événement <strong><a href="/events/:event_id">:event_title</a></strong>.',
    'two_events_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne vous ont :invited à des événements.',
    'many_events_invitations' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :invitations_count autres personnes vous ont :invited à des événements.',
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
    'one_person_post_instructive' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> trouve votre :post_type :instructive.',
    'two_persons_post_instructive' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne trouvent votre :post_type :instructive.',
    'many_persons_post_instructive' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :reactions_count autres personnes trouvent votre :post_type :instructive.',
    'one_person_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a réagi à votre message.',
    'two_persons_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont réagi à votre message.',
    'many_persons_reacted_message' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :reactions_count autres personnes ont réagi à votre message.',
    // Posts & Comments
    'one_post_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> a publié :a_new :post_type.',
    'two_posts_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et une autre personne ont publié des :new_posts_type.',
    'many_posts_published' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> et :posts_count autres personnes ont publié des :new_posts_type.',
    'one_mention_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned dans :a_new :post_type.',
    'two_mentions_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned et une autre personne dans :a_new :post_type.',
    'many_mentions_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned et :mentions_count autres personnes dans :a_new :post_type.',
    'followers_mention_post' => '<strong><a href="/users/:from_user_id">:from_user_names</a></strong> vous a :mentioned, vous et toutes ses connexions dans :a_new :post_type.',
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
];
