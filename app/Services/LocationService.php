<?php

namespace App\Services;

use ipinfo\ipinfo\IPinfo;
use ipinfo\ipinfo\IPinfoException;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class LocationService
{
    protected $ipinfo;

    public function __construct()
    {
        $this->ipinfo = new IPinfo(config('services.ipinfo.access_token'));
    }

    public function getLocation($ip)
    {
        try {
            $details = $this->ipinfo->getDetails($ip);

            return [
                'latitude' => $details->loc ? explode(',', $details->loc)[0] : null,
                'longitude' => $details->loc ? explode(',', $details->loc)[1] : null,
                'city' => $details->city,
                'region' => $details->region,
                'country' => $details->country
            ];

        } catch (IPinfoException $e) {
            return null;
        }
    }
}
