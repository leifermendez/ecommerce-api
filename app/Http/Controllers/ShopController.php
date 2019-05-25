<?php

namespace App\Http\Controllers;

use Spatie\OpeningHours\OpeningHours;
use App\payment_key;
use App\shop;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $limit = ($request->limit) ? $request->limit : 15;

            $data = shop::orderBy('id', 'DESC')
                ->paginate($limit);

            $response = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);

        } catch (\Exception $e) {

            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );

            return response()->json($response, 500);

        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $fields = array();
        foreach ($request->all() as $key => $value) {
            $fields[$key] = $value;
        }
        try {

            $data = shop::insertGetId($fields);
            $data = shop::find($data);

            $response = array(
                'status' => 'success',
                'msg' => 'Insertado',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => 'fail',
                'code' => 5,
                'error' => $e->getMessage()
            );
            return response()->json($response);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $shedule = array();
            $exceptions = array();
            $data = shop::where('shops.id',$id)
            ->join('hours','shops.id','=','hours.shop_id')
            ->select('shops.*','hours.shedule_hours as hours_shedule_hours',
            'hours.exceptions as hours_exceptions')
            ->first();

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

                    $shedule['forWeek'] = [
                        'monday'     =>  [
                            'isOpen' => $openingHours->isOpenOn('monday'),
                            'hours' => $hours_shedule_hours->tuesday,
                        ],
                        'tuesday'    => [
                            'isOpen' => $openingHours->isOpenOn('tuesday'),
                            'hours' => $hours_shedule_hours->tuesday,
                        ],
                        'wednesday'  =>  [
                            'isOpen' => $openingHours->isOpenOn('wednesday'),
                            'hours' => $hours_shedule_hours->wednesday,
                        ],
                        'thursday'   =>  [
                            'isOpen' => $openingHours->isOpenOn('thursday'),
                            'hours' => $hours_shedule_hours->thursday,
                        ],
                        'friday'     =>  [
                            'isOpen' => $openingHours->isOpenOn('friday'),
                            'hours' => $hours_shedule_hours->friday,
                        ],
                        'saturday'   =>  [
                            'isOpen' => $openingHours->isOpenOn('saturday'),
                            'hours' => $hours_shedule_hours->saturday,
                        ],
                        'sunday'     =>  [
                            'isOpen' => $openingHours->isOpenOn('sunday'),
                            'hours' => $hours_shedule_hours->sunday,
                        ]
                    ];
                   
                    $shedule['todayOpen'] = $openingHours->isOpenAt(Carbon::now());
                    $exceptions = $openingHours->exceptions();
                }

            };
        
            if($data){
                $data->setAttribute('hours_shedule_hours',$shedule);
                $data->setAttribute('hours_exceptions',$exceptions);
            }
            
            $response = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);

        } catch (\Exception $e) {

            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );

            return response()->json($response, 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'id') {
                    $fields[$key] = $value;
                };
            }

            shop::where('id', $id)
                ->update($fields);

            $data = shop::find($id);


            $response = array(
                'status' => 'success',
                'msg' => 'Actualizado',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);


        } catch (\Exception $e) {

            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );

            return response()->json($response, 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {

            shop::where('id', $id)
                ->delete();

            $response = array(
                'status' => 'success',
                'msg' => 'Eliminado',
                'code' => 0
            );
            return response()->json($response);


        } catch (\Exception $e) {

            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );

            return response()->json($response, 500);

        }
    }
}
