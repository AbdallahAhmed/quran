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
            return trim(' الجزء' . trim(config('juz.' . $number)));
        }

        return 'juz\' ' . $number;
    }
}