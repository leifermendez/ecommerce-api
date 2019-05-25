<?php

namespace App\Http\Middleware;

use Closure;

class CheckLocation
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
            $location = ($request->header('LOCATION-ZIP')) ? $request->header('LOCATION-ZIP') : $request['_location_zip_code'];    
            if(!$location){
                throw new \Exception('ZIPCODE no valido');
            }
            $request->merge(['_location' => $location]);
            return $next($request);
        }catch(\Exception $e) {

            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );

            return response()->json($response, 403);
        }
  
    }
}
