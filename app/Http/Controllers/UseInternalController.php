<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\OpeningHours\OpeningHours;
use Carbon\Carbon;
use App\products;
use App\shop;

class UseInternalController extends Controller
{
    public function _isAvailableProduct($id = null){
        try{
            if(!$id){
                throw new \Exception('id null');
            }

            if(!products::where('id',$id)->exists()){
                throw new \Exception('not found');
            }
            
            $product = products::find($id);
            $shedule = array();
            $exceptions = array();
            $data = shop::where('shops.id',$product->shop_id)
            ->join('hours','shops.id','=','hours.shop_id')
            ->select('shops.*','hours.shedule_hours as hours_shedule_hours',
            'hours.exceptions as hours_exceptions')
            ->first();

            if(!$data){
                return [
                    'isAvailable' => false,
                    'nextOpen' => false,
                    'nextClose' => false,
                ];
            }

            if($data){
                $hours_shedule_hours= ($data->hours_shedule_hours && json_decode($data->hours_shedule_hours)) ? 
                json_decode($data->hours_shedule_hours) : null;

                $hours_exceptions= ($data->hours_exceptions && json_decode($data->hours_exceptions)) ? 
                json_decode($data->hours_exceptions) : null;
                
                if($hours_shedule_hours && $hours_exceptions){
                    $openingHours = OpeningHours::create([
                        'monday'     => $hours_shedule_hours->monday,
                        'tuesday'    => $hours_shedule_hours->tuesday,
                        'wednesday'  =>  $hours_shedule_hours->wednesday,
                        'thursday'   =>  $hours_shedule_hours->thursday,
                        'friday'     =>  $hours_shedule_hours->friday,
                        'saturday'   =>  $hours_shedule_hours->saturday,
                        'sunday'     =>  $hours_shedule_hours->sunday,
                        'exceptions' => $hours_exceptions
                    ]);
                    $next_available = $openingHours->nextOpen(Carbon::now());
                    $next_available = Carbon::parse($next_available)->toArray();
                    $next_close = $openingHours->nextClose(Carbon::now());
                    $next_close = Carbon::parse($next_close)->toArray();
                    $shedule = $openingHours->isOpenAt(Carbon::now());
                }
                
                return [
                    'isAvailable' => $shedule,
                    'nextOpen' => $next_available,
                    'nextClose' => $next_close,
                ];

            };     

        }catch(\Execption $e){
            return $e->getMessage();
        }
    }
}
