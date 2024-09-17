<?php
/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'flexpay' => [
        'merchant' => env('FLEXPAY_MERCHANT'),
        'gateway_mobile' => env('FLEXPAY_GATEWAY_MOBILE'),
        'gateway_card_v1' => env('FLEXPAY_GATEWAY_CARD_V1'),
        'gateway_card_v2' => env('FLEXPAY_GATEWAY_CARD_V2'),
        'gateway_check' => env('FLEXPAY_GATEWAY_CHECK'),
        'api_token' => env('FLEXPAY_API_TOKEN'),
    ],

    'ipinfo' => [
        'access_token' => env('IPINFO_SECRET'),
    ],

];
