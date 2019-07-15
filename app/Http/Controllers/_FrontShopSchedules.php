<?php

namespace App\Http\Controllers;

use App\hours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Tymon\JWTAuth\Facades\JWTAuth;

class _FrontShopSchedules extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'shop_id' => 'required',
                'shedule_hours' => 'required'
            ]);

            $isMy = (new UseInternalController)->_isMyShop($request->shop_id);

            if (!$isMy) {
                throw new \Exception('not owner shop');
            }

            $fields = array(
                'shop_id' => $request->shop_id,
                'shedule_hours' => json_encode($request->shedule_hours),
                'exceptions' => json_encode($request->exceptions)
            );


            $data = hours::insertGetId($fields);
            $data = hours::find($data);
            Artisan::call("modelCache:clear", ['--model' => 'App\products']);

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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $data = hours::where('shop_id', $id)->first();

            if ($data) {
                $data->shedule_hours = json_decode($data->shedule_hours);
                $data->exceptions = json_decode($data->exceptions);
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'shop_id' => 'required',
                'shedule_hours' => 'required'
            ]);

            $isMy = (new UseInternalController)->_isMyShop($request->shop_id);

            if (!$isMy) {
                throw new \Exception('not owner shop');
            }

            $fields = array(
                'shedule_hours' => json_encode($request->shedule_hours),
                'exceptions' => json_encode($request->exceptions)
            );


            hours::where('shop_id', $id)
                ->update($fields);
            $data = hours::where('shop_id', $id)->first();
            Artisan::call("modelCache:clear", ['--model' => 'App\products']);

            $response = array(
                'status' => 'success',
                'msg' => 'Editado',
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
