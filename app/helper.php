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
