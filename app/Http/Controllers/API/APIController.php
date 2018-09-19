<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    //


    /**
     * Return standard success response
     * @param $data
     * @param bool $status
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    function response($data, $status = true, $code = 200)
    {
        return response()->json(['data' => $data, 'status' => $status], $code);
    }


    /**
     * Return standard errors response
     * @param $data
     * @param bool $status
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    function errorResponse($data, $code = 400)
    {
        return response()->json(['errors' => $data, 'status' => false], $code);
    }


}
