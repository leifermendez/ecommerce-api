<?php

namespace App\Http\Controllers;

use App\shipping_address;
use App\shopping_cart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class _FrontShipping extends Controller
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
            $user = JWTAuth::parseToken()->authenticate();
            $data = shipping_address::orderBy('id', 'DESC')
                ->where('user_id', $user->id)
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->request->remove('_location'); $request->request->remove('_lat'); $request->request->remove('_lng'); $request->request->remove('_range_closed');
        $fields = array();
        foreach ($request->all() as $key => $value) {
            if ($key !== 'uuid' && $key !== 'user_id') {
                $fields[$key] = $value;
            };
        }
        try {
            $request->validate([
                'country' => 'required',
                'state' => 'required',
                'district' => 'required',
                'address' => 'required',
            ]);

            $user = JWTAuth::parseToken()->authenticate();

            $fields['user_id'] = $user->id;

            $data = shipping_address::insertGetId($fields);
            $data = shipping_address::find($data);

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
            return response()->json($response,400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $data = shipping_address::orderBy('id', 'DESC')
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->first();


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
            $request->request->remove('_location'); $request->request->remove('_lat'); $request->request->remove('_lng'); $request->request->remove('_range_closed');
            $fields = array();
            $user = JWTAuth::parseToken()->authenticate();

            foreach ($request->all() as $key => $value) {
                if ($key !== 'id') {
                    $fields[$key] = $value;
                };
            }

            shipping_address::where('id', $id)
                ->where('user_id', $user->id)
                ->update($fields);

            $data = shipping_address::find($id);


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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            shipping_address::where('id', $id)
                ->where('user_id', $user->id)
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
