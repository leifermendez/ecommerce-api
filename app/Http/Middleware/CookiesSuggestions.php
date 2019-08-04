<?php

namespace App\Http\Middleware;

use Closure;
use \Torann\GeoIP\GeoIP;
use App\cookies_red;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CookiesSuggestions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function _getUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return false;
            }else{
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
        $_cookies = ($request->header('_check_session_label')) ? $request->header('_check_session_label') : $request['_cookies_ref_products'];  
        $src = ($request->src) ? $request->src : null;
        $ip = geoip()->getClientIP();
        $g = geoip()->getLocation($ip);
        $g=$g->toArray();
        $user = $this->_getUser();
        dd($_cookies);
        
        if($_cookies){
            $values = [
                'users_id' => ($user) ? $user->id : null,
                'labels' => $_cookies,
                'src' => $src,
                'ip' => $ip,
                'browser' => '',
                'country' => $g['iso_code']
             ];
            cookies_red::insert($values);
        };
        return $next($request);
    }
}
