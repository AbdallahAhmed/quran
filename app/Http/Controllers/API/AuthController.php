<?php

namespace App\Http\Controllers\API;

use App\Models\Media;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends APIController
{

    /**
     * POST /auth
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

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
            return $this->errorResponse(["Email or password incorrect."], 400);
        }

        if (fauth()->user()->status == 0) {
            $response['errors'] = ["Please Verification your mail (check your e-mail)."];
            return $this->errorResponse(["Email or password incorrect."], 400);
        }

        $user = fauth()->user();

        $user->last_login = Carbon::now()->getTimestamp();

        $user->save();

        $user->load('photo');
        return $this->response(['user' => $user, 'token' => $user->api_token]);
    }


    /**
     * POST /register
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,[id],id',
            'password' => 'required|min:6',
            'name' => 'required',
        ]);

        $media = null;
        $imageData = null;

        if ($request->filled('image_data')) {
            $media = new Media();
            $imageData = explode('base64,', $request->get('image_data'));

            if (count($imageData) < 2) {
                return $this->errorResponse(['Image not base64 format']);
            }

            $imageData = $imageData[1];

        }

        if ($validator->fails()) {
            return $this->errorResponse(($validator->errors()->all()));
        }

        $user = new User();
        $user->username = $request->get('email');
        $user->email = $request->get('email');
        $user->password = ($request->get('password'));
        $names = explode(' ', $request->get('name'));
        $user->first_name = isset($names[0]) ? $names[0] : '';
        $user->last_name = isset($names[1]) ? $names[1] : '';
        $user->api_token = str_random(60);
        $user->backend = 0;
        $user->status = 1;
        $user->lang = $request->get('lang', 'ar');
        if ($imageData) {
            $media = $media->saveContent($imageData);
            $user->photo_id = $media->id;
        }
        $user->role_id = 2;
        $user->save();
        $user->load('photo');
        return $this->response(['user' => ($user), 'token' => $user->api_token]);
    }


    /**
     * POST /profile/update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        $user = fauth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        $validator->sometimes('email', 'required|email|unique:users,email', function () use ($request, $user) {
            return $request->filled('email') && $request->get('email') != $user->email;
        });


        $validator->sometimes('password', 'required|min:6', function () use ($request, $user) {
            return $request->filled('password');
        });


        $media = null;
        $imageData = null;

        if ($request->filled('image_data')) {
            $media = new Media();
            $imageData = explode('base64,', $request->get('image_data'));

            if (count($imageData) < 2) {
                return $this->errorResponse(['Image not base64 format']);
            }

            $imageData = $imageData[1];

        }

        if ($validator->fails()) {
            return $this->errorResponse(($validator->errors()->all()));
        }

        if ($request->filled('email')) {
            $user->username = $request->get('email');
            $user->email = $request->get('email');
        }


        if ($request->filled('password')) {
            $user->password = ($request->get('password'));
        }


        if ($request->filled('name')) {
            $names = explode(' ', $request->get('name'));
            $user->first_name = isset($names[0]) ? $names[0] : '';
            $user->last_name = isset($names[1]) ? $names[1] : '';
        }


        if ($request->filled('lang')) {
            $user->lang = $request->get('lang', $user->lang);
        }

        if ($imageData) {
            $media = $media->saveContent($imageData);
            $user->photo_id = $media->id;
        }
        $user->save();

        $user->load('photo');
        return $this->response(['user' => ($user), 'token' => $user->api_token]);
    }

    /**
     * POST /profile/token_reset
     * @return \Illuminate\Http\JsonResponse
     */
    public function tokenReset()
    {
        $user = fauth()->user();
        $user->api_token = str_random(60);
        $user->save();
        return $this->response(['token' => $user->api_token]);
    }
}
