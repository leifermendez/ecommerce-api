<?php

namespace App\Http\Middleware;

use Closure;

class CookiesSuggestions
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
        $_cookies = ($request->header('COOKIES-REF')) ? $request->header('COOKIES-REF') : $request['_cookies_ref_products'];  
        return $next($request);
    }
}
