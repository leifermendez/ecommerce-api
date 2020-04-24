<?php

namespace App\Http\Middleware;

use Closure;
use \Torann\GeoIP\GeoIP;
use App\cookies_red;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CookiesSuggestions
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function _getUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return false;
            } else {
                return $user;
            }
        } catch (TokenExpiredException $e) {
            return false;
        } catch (TokenInvalidException $e) {
            return false;
        } catch (JWTException $e) {
            return false;
        }
    }

    public function handle($request, Closure $next)
    {
        try {
            $_cookies = ($request->header('COOKIES-REF')) ? $request->header('COOKIES-REF') : false;
            $src = ($request->src) ? $request->src : null;
            $ip = geoip()->getClientIP();
            $g = geoip()->getLocation($ip);
            $g = $g->toArray();
            $user = $this->_getUser();

            if ($_cookies) {
                $decrypted = Crypt::decryptString($_cookies);
                if (!cookies_red::where('labels', $decrypted)->where('ip', $ip)->exists()) {
                    $values = [
                        'users_id' => ($user) ? $user->id : null,
                        'labels' => $decrypted,
                        'src' => $src,
                        'ip' => $ip,
                        'browser' => '',
                        'country' => $g['iso_code']
                    ];
                    cookies_red::insert($values);
                }
            };
            return $next($request);
        } catch (\Exception $e) {
            return $next($request);
        }
    }
}
