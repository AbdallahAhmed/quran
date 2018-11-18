<?php

use Illuminate\Support\Facades\Auth;


if (!function_exists('fauth')) {
    /**
     * @return mixed
     */
    function fauth()
    {
        return Auth::guard('frontend');
    }
}


if (!function_exists('generateCode')) {

    function generateCode()
    {
        return rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
    }

}

if (!function_exists('juz_name')) {
    function juz_name($number, $lang = "ar")
    {
        if ($lang == "ar") {
            return trim('  الجزء ' . trim(config('juz.' . $number)));
        }

        return 'juz\' ' . $number;
    }
}

/*if(!function_exists('juz_pages')){
    function juz_pages($juz_number){
        $juz = \App\Models\Juz::find($juz_number);
        $pages = array();
        foreach ($juz->pages as $page)
            $pages[] = $page->id;

        return $pages;
    }
}*/

if (!function_exists('get_contest_pages')) {
    function get_contest_pages($juz_from, $juz_to)
    {
        $contest_pages = array();
        $juz_pages = json_decode(file_get_contents(public_path('api/juz_pages.json')));
        foreach ($juz_pages as $key => $value) {
            if ($key >= $juz_from && $key <= $juz_to) {
                $contest_pages = array_merge($contest_pages, $value);
            }
        }
        return array_unique($contest_pages);
    }
}

if (!function_exists('remaining_time_human')) {
    function remaining_time_human($minutes)
    {
        if (app()->getLocale() == "ar") {
            switch ($minutes) {
                case $minutes < 60:
                    return $minutes." دقيقة";
                case $minutes >= 60 && $minutes < 1440:
                    return ((int)($minutes / 60)) . "ساعة ";
                case $minutes >= 1440 && $minutes < 10080:
                    return ((int)($minutes / 1440)) . " يوم";
                case $minutes >= 10080 && $minutes < 43200:
                    return ((int)($minutes / 10080)) . " اسبوع";
                case $minutes >= 43200:
                    return ((int)($minutes / 43200)) . " شهر";
            }
        } else {

            switch ($minutes) {
                case $minutes < 60:
                    return $minutes." minute(s)";
                case $minutes >= 60 && $minutes < 1440:
                    return ((int)($minutes / 60)) . " hour(s)";
                case $minutes >= 1440 && $minutes < 10080:
                    return ((int)($minutes / 1440)) . " day(s)";
                case $minutes >= 10080 && $minutes < 43200:
                    return ((int)($minutes / 10080)) . " week(s)";
                case $minutes >= 43200:
                    return ((int)($minutes / 43200)) . " month(s)";
            }
        }
    }
}