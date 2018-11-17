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