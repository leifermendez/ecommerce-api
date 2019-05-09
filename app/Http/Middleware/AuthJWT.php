<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJWT
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
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 403);
            }else if($user->status !== 'available'){
                return response()->json(['user_unavailable'], 403);
            }
        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], 403);
        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], 403);
        } catch (JWTException $e) {

            return response()->json(['token_absent'], 403);
        }

        return $next($request);
    }
}
