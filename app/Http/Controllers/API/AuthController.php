<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends APIController
{

    /**
     * POST /auth
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $response = ['data' => [], 'errors' => []];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $response['errors'] = ($validator->errors()->all());
            return response()->json($response, '400');
        }

        $isAuthed = fauth()->once([
            'username' => $request->get('email'),
            'password' => $request->get('password'),
            'backend' => 0
        ]);

        if (!$isAuthed) {
            $response['errors'] = ["Email or password incorrect."];
            return response()->json($response, '400');
        }

        if (fauth()->user()->status == 0) {
            $response['errors'] = ["Please Verification your mail (check your e-mail)."];
            return response()->json($response, '400');
        }

        if (fauth()->user()->suspended == 1) {
            $response['errors'] = ["Your account suspended for ever "];
            return response()->json($response, '400');
        }

        if (fauth()->user()->suspended_to && fauth()->user()->suspended_to->getTimestamp() >= Carbon::now()->getTimestamp()) {
            $response['errors'] = ["Your account suspended for " . fauth()->user()->suspended_to->format('l jS \\of F Y h:i:s A')];
            return response()->json($response, '400');
        }
        $user = fauth()->user();

        $response['data'] = ($user);
        $response['data']->last_login = isset($user->last_login) && !empty($user->last_login) ? $user->last_login->timestamp : null;
        $response['data']->about = $user->about;
        $response['data']->email_verify = $user->email_verify;
        $response['token'] = $user->api_token;
        $user->last_login = Carbon::now();
        $user->save();
        return response()->json($response);
    }
    //
}
