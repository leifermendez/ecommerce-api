<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\shop;
use DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class _FrontShop extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $limit = ($request->limit) ? $request->limit : 15;

            $data = shop::orderBy('id', 'DESC')
                ->where('users_id', $user->id)
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
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $request->request->remove('_location');
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'id' && $key !== 'users_id') {
                    $fields[$key] = $value;
                };
            }
            $fields['users_id'] = $user->id;
            $id = Shop::insertGetId($fields);
            $data = Shop::find($id);

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
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        try {
            $isLogged = (new UseInternalController)->_isLogged();

            $data = Shop::where('shops.id', $id);
            if ($isLogged) {
                $data->select('name',
                    'address', 'slug', 'legal_id', 'image_cover', 'image_header', 'meta_key', 'terms_conditions',
                    'email_corporate', 'phone_mobil', 'phone_fixed',
                    DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = image_cover limit 1) as image_cover'),
                    DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = image_header limit 1) as image_header')
                )->where('shops.users_id', $isLogged->id)
                    ->first();
            } else {
                $data->select('name',
                    'address', 'slug', 'legal_id', 'image_cover', 'image_header', 'meta_key', 'terms_conditions',
                    DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = image_cover limit 1) as image_cover'),
                    DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = image_header limit 1) as image_header')
                )->first();
            };

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
            $user = JWTAuth::parseToken()->authenticate();
            $request->request->remove('_location');
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'id' && $key !== 'users_id') {
                    $fields[$key] = $value;
                };
            }
            $fields['users_id'] = $user->id;
            Shop::where('id', $id)
                ->where('users_id',$user->id)
                ->update($fields);

            $data = Shop::find($id);

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
