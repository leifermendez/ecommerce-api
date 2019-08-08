<?php

namespace App\Http\Middleware;

use App\Http\Controllers\UseInternalController;
use Closure;

class CheckLocation
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $range_closed = (new UseInternalController)->_getSetting('range_closed');
            $location = ($request->header('LOCATION-ZIP')) ?
                $request->header('LOCATION-ZIP') : $request['_location_zip_code'];

            $lat = ($request->header('LAT')) ? $request->header('LAT') : $request['LAT'];
            $lng = ($request->header('LNG')) ? $request->header('LNG') : $request['LNG'];

            if($range_closed == 1){
                if (!$lat || !$lng) {
                    throw new \Exception('LAT LNG no valido');
                }

                $request->merge(['_location' => $location]);
                $request->merge(['_lat' => $lat]);
                $request->merge(['_lng' => $lng]);
                $request->merge(['_range_closed' => true]);
            }

            return $next($request);
        } catch (\Exception $e) {

            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );

            return response()->json($response, 403);
        }

    }
}
