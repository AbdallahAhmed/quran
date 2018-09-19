<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Auth;

class PublicApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$token = $this->getAuthorizationToken($request)) {
            return $next($request);
        }
        // Authenticate
        if (!$this->authenticate($token)) {
            return $next($request);
        }
        return $next($request);
    }


    /**
     * Make User Authenticate
     * @param $token
     * @return bool
     */
    private function authenticate($token)
    {
        $user = User::where('api_token', $token)->where('backend', '<>', 1)->first(['id']);
        if (!isset($user)) {
            return false;
        }
        Auth::guard('frontend')->onceUsingId($user->id);
        return true;
    }

    /**
     * Get api token form request
     * @param $request
     * @return null
     */
    private function getAuthorizationToken($request)
    {
        $header = $request->header('authorization');
        if (empty($header) || strlen($header) <= 8 || !str_contains($header, 'Bearer ')) {
            return null;
        }
        // 7 is number of char of 'Bearer '
        return (substr($header, 7));
    }
}
