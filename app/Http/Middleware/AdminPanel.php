<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

// Get the currently authenticated user...

use Closure;

class AdminPanel
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
        try{
            $user = Auth::user();
            if($user->role === 'admin'){
                return $next($request);
            }else{
                return response()->json(['not_role'], 403);
            }

        }catch (\Exception $e){
            return redirect('login');
        }

    }
}
