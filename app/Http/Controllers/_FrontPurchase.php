<?php

namespace App\Http\Controllers;

use App\purchase_order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class _FrontPurchase extends Controller
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
            $filters = ($request->filters) ? explode("?", $request->filters) : [];
            $user = JWTAuth::parseToken()->authenticate();
            $sql = purchase_order::orderBy('purchase_orders.id', 'desc')
                ->where('purchase_orders.user_id', $user->id)
                ->where(function ($query) use ($filters) {
                    foreach ($filters as $value) {
                        $tmp = explode(",", $value);
                        if (isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])) {
                            $subTmp = explode("|", $tmp[2]);
                            if (count($subTmp)) {
                                foreach ($subTmp as $k) {
                                    $query->orWhere($tmp[0], $tmp[1], $k);
                                }
                            } else {
                                $query->where($tmp[0], $tmp[1], $tmp[2]);
                            }

                        }
                    }
                });

            $data = $sql
                ->paginate($limit)
                ->appends(request()->except('page'));


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
        $fields = array();
        $lists = [];
        $request->request->remove('_location');

        try {
            $user = JWTAuth::parseToken()->authenticate();

            $shoppingCart = (new UseInternalController)->_shoppingCart($user->id);

            $uuid = Str::random(40);

            foreach ($shoppingCart['list'] as $value) {

                $lists[$value['shop_id']] = [
                    "shop_id" => $value['shop_id'],
                    "uuid" => $uuid,
                    "user_id" => $user->id,
                    "uuid_shipping" => 'sh_' . Str::random(12),
                    "amount" => (isset($lists[$value['shop_id']]['amount'])) ?
                        floatval($lists[$value['shop_id']]['amount'] + $value['price_normal']) :
                        floatval($value['price_normal'])
                ];

            };

            foreach ($lists as $list) {
                purchase_order::insert($list);
            };

            $data = purchase_order::where('uuid', $uuid)
                ->get();

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
    public function show(Request $request, $id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $data = purchase_order::where('user_id', $user->id)
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
        //
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
            purchase_order::where('id', $id)
                ->where('user_id', $user->id)
                ->update(['status' => 'cancel']);

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
