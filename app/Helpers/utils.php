<?php

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */

use Carbon\Carbon;

// Get web URL
if (!function_exists('getWebURL')) {
    function getWebURL()
    {
        return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    }
}

// Get APIs URL
if (!function_exists('getApiURL')) {
    function getApiURL()
    {
        return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api';
    }
}

// Friendly username from names
if (!function_exists("friendlyUsername")) {
    function friendlyUsername($str)
    {
        // convert to entities
        $string = htmlentities($str, ENT_QUOTES, 'UTF-8');
        // regex to convert accented chars into their closest a-z ASCII equivelent
        $string = preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $string);
        // convert back from entities
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        // any straggling characters that are not strict alphanumeric are replaced with an underscore
        $string = preg_replace('~[^0-9a-z]+~i', '_', $string);
        // trim / cleanup / all lowercase
        $string = trim($string, '-');
        $string = strtolower($string);

        return $string;
    }
}

// Transform mentions and hashtag to URL
if (!function_exists('transformMentionHashtag')) {
    function transformMentionHashtag($web_url, $subject)
    {
        $pat = array('/#(\w+)/', '/@(\w+)/');
        $rep = array('<strong><a href="' . $web_url . '/hashtag/$1">#$1</a></strong>', '<strong><a href="' . $web_url . '/$1">@$1</a></strong>');

        return preg_replace($pat, $rep, $subject);
    }
}

// Get all hashtags from text
if (!function_exists('getHashtags')) {
    function getHashtags($subject)
    {
        $hashtags = false;

        // preg_match_all("/\#(\w+)/u", $subject, $matches);
        preg_match_all('/#(\w+)/u', $subject, $matches);

        if ($matches) {
            $matches[0] = str_replace('#', '', $matches[0]); //replace #
            $hashtags = implode(' ,', $matches[0]);
        }

        return explode(' ,', $hashtags);
    }
}

// Check if a value exists into an multidimensional array
if (!function_exists('inArrayR')) {
    function inArrayR($needle, $haystack, $key)
    {
        return in_array($needle, collect($haystack)->pluck($key)->toArray());
    }
}

// Month fully readable
if (!function_exists('explicitMonth')) {
    function explicitMonth($month)
    {
        setlocale(LC_ALL, app()->getLocale());

        return utf8_encode(strftime("%B", strtotime(date('F', mktime(0, 0, 0, $month, 10)))));
    }
}

// Day and month fully readable
if (!function_exists('explicitDayMonth')) {
    function explicitDayMonth($date)
    {
        setlocale(LC_ALL, app()->getLocale());

        return utf8_encode(Carbon::parse($date)->formatLocalized('%d %B'));
    }
}

// Date fully readable
if (!function_exists('explicitDate')) {
    function explicitDate($date)
    {
        setlocale(LC_ALL, app()->getLocale());

        return utf8_encode(Carbon::parse($date)->formatLocalized('%A %d %B %Y'));
    }
}

// Delete item from exploded array
if (!function_exists('deleteExplodedArrayItem')) {
    function deleteExplodedArrayItem($separator, $subject, $item)
    {
        $explodes = explode($separator, $subject);
        $clean_inventory = array();

        foreach ($explodes as $explode) {
            if (!isset($clean_inventory[$explode])) {
                $clean_inventory[$explode] = 0;
            }

            $clean_inventory[$explode]++;
        }

        // Item can be deleted
        unset($clean_inventory[$item]);

        $saved = array();

        foreach ($clean_inventory as $key => $quantity) {
            $saved = array_merge($saved, array_fill(0, $quantity, $key));
        }

        return implode($separator, $saved);
    }
}

// Add an item to exploded array
if (!function_exists('addItemsToExplodedArray')) {
    function addItemsToExplodedArray($separator, $subject, $items)
    {
        $explodes = explode($separator, $subject);
        $saved = array_merge($explodes, $items);

        return implode($separator, $saved);
    }
}
