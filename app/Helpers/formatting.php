<?php
/**
 * @author Xanders
 * @see https://www.xsam-tech.com
 */

use Illuminate\Support\Facades\Session;

if (!function_exists("getRandomNumber")) {
    function getRandomNumber($n)
    {
        $characters = '0123456789';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}

if (!function_exists("formatIntegerNumber")) {
    function formatIntegerNumber($number)
    {
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');

            if ($sessionLocale !== 'fr') {
                return number_format($number, 0, '.', ',');

            } else {
                return number_format($number, 0, ',', ' ');
            }
        } else {
            $appLocale = app()->getLocale();

            if ($appLocale !== 'fr') {
                return number_format($number, 0, '.', ',');

            } else {
                return number_format($number, 0, ',', ' ');
            }
        }
    }
}

if (!function_exists("formatDecimalNumber")) {
    function formatDecimalNumber($number)
    {
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');

            if ($sessionLocale !== 'fr') {
                return number_format($number, 2, '.', ',');

            } else {
                return number_format($number, 2, ',', ' ');
            }
        } else {
            $appLocale = app()->getLocale();

            if ($appLocale !== 'fr') {
                return number_format($number, 2, '.', ',');

            } else {
                return number_format($number, 2, ',', ' ');
            }
        }
    }
}

if (!function_exists("timeAgo")) {
    function timeAgo($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');

            switch ($sessionLocale) {
                case 'en':
                    $string = array(
                        'y' => 'year',
                        'm' => 'month',
                        'w' => 'week',
                        'd' => 'day',
                        'h' => 'hour',
                        'i' => 'minute',
                        's' => 'second',
                    );

                    foreach ($string as $k => &$v) {
                        if ($diff->$k) {
                            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');

                        } else {
                            unset($string[$k]);
                        }
                    }

                    if (!$full) $string = array_slice($string, 0, 1);

                    return $string ? implode(', ', $string) . ' ago' : 'just now';
                    break;

                default:
                    $string = array(
                        'y' => 'an',
                        'm' => 'mois',
                        'w' => 'semaine',
                        'd' => 'jour',
                        'h' => 'heure',
                        'i' => 'minute',
                        's' => 'seconde',
                    );

                    foreach ($string as $k => &$v) {
                        if ($diff->$k) {
                            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 && $v !== 'mois' ? 's' : '');

                        } else {
                            unset($string[$k]);
                        }
                    }

                    if (!$full) $string = array_slice($string, 0, 1);

                    return $string ? 'Il y a ' . implode(', ', $string) : 'en ce moment';
                    break;
            }

        } else {
            $appLocale = app()->getLocale();

            switch ($appLocale) {
                case 'en':
                    $string = array(
                        'y' => 'year',
                        'm' => 'month',
                        'w' => 'week',
                        'd' => 'day',
                        'h' => 'hour',
                        'i' => 'minute',
                        's' => 'second',
                    );
    
                    foreach ($string as $k => &$v) {
                        if ($diff->$k) {
                            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
    
                        } else {
                            unset($string[$k]);
                        }
                    }
    
                    if (!$full) $string = array_slice($string, 0, 1);
    
                    return $string ? implode(', ', $string) . ' ago' : 'just now';
                    break;
                
                default:
                    $string = array(
                        'y' => 'an',
                        'm' => 'mois',
                        'w' => 'semaine',
                        'd' => 'jour',
                        'h' => 'heure',
                        'i' => 'minute',
                        's' => 'seconde',
                    );

                    foreach ($string as $k => &$v) {
                        if ($diff->$k) {
                            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 && $v !== 'mois' ? 's' : '');

                        } else {
                            unset($string[$k]);
                        }
                    }

                    if (!$full) $string = array_slice($string, 0, 1);

                    return $string ? 'Il y a ' . implode(', ', $string) : 'en ce moment';
                    break;
            }
        }
    }
}
