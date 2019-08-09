<?php

namespace App\Http\Controllers;

use App\shopping_cart;
use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class _FrontUser extends Controller
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
        //
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
            $user = JWTAuth::parseToken()->authenticate();

            $query = array('name', 'status', 'confirmed', 'avatar', 'header', 'referer_code');
            if ($user->id == $id) {
                $query[] = 'email';
                $query[] = 'phone';
                $query[] = 'role';
            }
            $data = User::where('id', $id)
                ->select($query)
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
            $request->remove('_location'); $request->remove('_lat'); $request->remove('_lng');
            $fields = array();
            $user = JWTAuth::parseToken()->authenticate();

            foreach ($request->all() as $key => $value) {
                if ($key !== 'id' && $key !== 'role' && $key !== 'referer_code'
                    && $key !== 'status' && $key !== 'confirmed' &&
                    $key !== 'created_at' && $key !== 'updated_at' &&
                    $key !== 'remember_token') {
                    $fields[$key] = $value;
                };
            }

            User::where('id', $user->id)
                ->update($fields);

            $data = User::find($user->id);


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
        //
    }
}
