<?php

namespace App\Http\Controllers\API;

use App\Mail\PasswordChangedMail;
use App\Mail\ResetPasswordMail;
use App\Mail\VerificationMail;
use App\Mail\WelcomeMail;
use App\Models\Khatema;
use App\Models\Media;
use App\Models\Token;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
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
      /*  $device_token = $request->get('device_token');
        $token = Token::where('device_token', $device_token)->get();
        $token_id = "";
        if (count($token) > 0) {
            $token_id = $token->id;
        } else {
            $token = new Token();
            $token->device_token = $device_token;
            $token->save();
            $token_id = $token->id;
        }
        $user->devices()->syncWithoutDetaching($token_id);*/

        $user->load('photo');

        $user['current_khatema'] = $user->PendingKhatema()->first();
        $user['last_khatema'] = $user->CompletedKhatemas()->orderBy('created_at', 'DESC')->first();

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

        app()->setLocale($request->get('lang', "ar"));


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
        $names = preg_split('/\s+/', $request->get('name'), -1, PREG_SPLIT_NO_EMPTY);
        $user->first_name = isset($names[0]) ? $names[0] : '';
        $user->last_name = isset($names[1]) ? $names[1] : '';
        $user->api_token = str_random(60);
        $user->backend = 0;
        $user->status = 1;
        $user->code = generateCode();
        $user->lang = $request->get('lang', 'ar');
        if ($imageData) {
            $media = $media->saveContent($imageData);
            $user->photo_id = $media->id;
        }
        $user->role_id = 2;
        $user->save();

       /* $device_token = $request->get('device_token');
        $token = new Token();
        $token->device_token = $device_token;
        $token->save();
        $token_id = $token->id;
        $user->devices()->sync($token_id);*/

        $user->load('photo');

        $khatema = Khatema::create([
            'pages' => '[]',
            'user_id' => $user->id,
            'created_at' => Carbon::now()
        ]);

        $user['current_khatema'] = $user->PendingKhatema()->first();


        Mail::to($user->email)->send(new WelcomeMail($user));

        return $this->response(['user' => ($user), 'token' => $user->api_token]);
    }


    /**
     * GET /auth/resendCode
     */
    public function resendCode()
    {
        if (fauth()->user()) {
            $user = fauth()->user();
            if (!$user->code) {
                $user->code = generateCode();
                $user->save();
            }
            Mail::to($user->email)->send(new VerificationMail($user));
            return $this->response('email sent');
        } else {
            return $this->errorResponse(['Api token  missing']);
        }
    }


    /**
     * GET /auth/verify
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);


        if ($validator->fails()) {
            return $this->errorResponse(($validator->errors()->all()));
        }

        if (fauth()->user()) {
            $user = fauth()->user();
            $user->code = null;
            $user->status = 1;
            $user->save();
            Mail::to($user->email)->send(new WelcomeMail($user));
            return $this->response('Verification Completed');
        } else {
            return $this->errorResponse(['Api token  missing']);
        }
    }


    /**
     * POST /profile/update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        app()->setLocale($request->get('lang', "ar"));

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
            $names = preg_split('/\s+/', $request->get('name'), -1, PREG_SPLIT_NO_EMPTY);
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

        $user['current_khatema'] = $user->PendingKhatema()->first();

        $user->load('photo');
        return $this->response(($user));
    }

    /**
     * POST /profile/token_reset
     * @return \Illuminate\Http\JsonResponse
     */
    public function tokenReset(Request $request)
    {

        app()->setLocale($request->get('lang', "ar"));

        $user = fauth()->user();
        $user->api_token = str_random(60);
        $user->save();
        return $this->response($user->api_token);
    }

    /**
     * POST /auth/forget-password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword(Request $request)
    {

        app()->setLocale($request->get('lang', "ar"));

        $user = User::where(['backend' => 0, 'email' => $request->get('email')])->first();
        if (!$user) {
            return $this->errorResponse(['You email not exists']);
        }
        $user->code = generateCode();
        $user->save();
        Mail::to($user->email)->send(new ResetPasswordMail($user));
        return $this->response('Check your email');
    }


    /**
     * POST /auth/reset-password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {

        app()->setLocale($request->get('lang', "ar"));

        $user = User::where(['backend' => 0, 'email' => $request->get('email')])->first();
        if (!$user) {
            return $this->errorResponse(['You email not exists']);
        }
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(($validator->errors()->all()));
        }

        if ($user->code != $request->get('code')) {
            return $this->errorResponse(['This code invalid']);
        }

        $user->code = null;
        $user->password = $request->get('password');
        $user->save();
        $user['current_khatema'] = $user->PendingKhatema()->first();
        Mail::to($user->email)->send(new PasswordChangedMail($user));
        return $this->response(['user' => ($user), 'token' => $user->api_token]);
    }
}
