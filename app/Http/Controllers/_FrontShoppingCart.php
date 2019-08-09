<?php

namespace App\Http\Controllers;

use App\shopping_cart;
use App\products;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;

class _FrontShoppingCart extends Controller
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
            $data = shopping_cart::orderBy('shopping_carts.id', 'DESC')
                ->where('shopping_carts.user_id', $user->id)
                ->join('products', 'shopping_carts.product_id', '=', 'products.id')
                ->join('variation_products', 'variation_products.id', '=', 'shopping_carts.product_variation_id')
                ->join('shops', 'shops.id', '=', 'shopping_carts.shop_id')
                ->select('shopping_carts.id', 'products.name', 'variation_products.label',
                    'variation_products.price_normal',
                    'variation_products.price_regular',
                    'shopping_carts.shop_id',
                    'shops.name as shop_name',
                    'shopping_carts.qty as shopping_carts_qty',
                    'products.id as product_id'
                )
                ->get();

            $_shipping = (new UseInternalController)->_getSetting('delivery_feed_min');
            $_shipping_tax = (new UseInternalController)->_getSetting('delivery_feed_tax');
            $_total_shipping = floatval($_shipping + ($_shipping * $_shipping_tax));

            $data->map(function ($item, $key) use ($request) {
                $getCoverImageProduct = (new UseInternalController)->_getCoverImageProduct($item->product_id);
                $getFeedAmount = (new UseInternalController)->_getFeedAmount($item->price_normal);
                $item->feed_amount = $getFeedAmount;
                $item->cover_image = $getCoverImageProduct;
                return $item;
            });

            $data_total = shopping_cart::orderBy('shopping_carts.id', 'DESC')
                ->where('shopping_carts.user_id', $user->id)
                ->join('products', 'shopping_carts.product_id', '=', 'products.id')
                ->join('variation_products', 'variation_products.id', '=', 'shopping_carts.product_variation_id')
                ->join('shops','shopping_carts.shop_id','=','shops.id')
                ->select(
                    DB::raw('sum(variation_products.price_normal * shopping_carts.qty) as price_normal'),
                    DB::raw('sum(variation_products.price_regular * shopping_carts.qty) as price_regular'),
                    'shopping_carts.shop_id','shops.name as shops_name',
                    'shopping_carts.qty as shopping_carts_qty'
                )
                ->groupBy('shopping_carts.shop_id')
                ->get();


            $data_total->map(function($item) use ($_total_shipping){
                $item->total_shipping =  $_total_shipping;
                return $item;
            });

            $data_shop = shopping_cart::orderBy('shopping_carts.id', 'DESC')
                ->where('shopping_carts.user_id', $user->id)
                ->join('products', 'shopping_carts.product_id', '=', 'products.id')
                ->join('variation_products', 'variation_products.id', '=', 'shopping_carts.product_variation_id')
                ->select(
                    DB::raw('sum(variation_products.price_normal * shopping_carts.qty) as price_normal'),
                    DB::raw('sum(variation_products.price_regular * shopping_carts.qty) as price_regular')
                )
                ->groupBy('shopping_carts.user_id')
                ->first();


            $total_shipping = ($data_total) ? count($data_total) * $_total_shipping : 0;
            //_getSetting('feed_percentage');
            $total_feed = (new UseInternalController)->_sumList($data, 'application_feed_amount');
            $discount_to_supplier = (new UseInternalController)->_getSetting('discount_to_supplier');
            $total_feed = ($discount_to_supplier == 1) ? $total_feed : 0;
            $response = array(
                'status' => 'success',
                'data' => [
                    'list' => $data,
                    'total' => $data_total,
                    'total_shop' => $data_shop,
                    'total_shipping' => $total_shipping,
                    'total_feed' => $total_feed
                ],
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
        $request->request->remove('_location'); $request->request->remove('_lat'); $request->request->remove('_lng'); $request->request->remove('_range_closed');

        foreach ($request->all() as $key => $value) {
            if ($key !== 'user_id') {
                $fields[$key] = $value;
            };
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            (new UseInternalController)->_isAvailableUser($user->id);
            $isMyProduct = (new UseInternalController)->_isMyProduct($fields['product_id']);
            if ($isMyProduct) {
                throw new \Exception('not_allowed_is_my_product');
            }
            $isAvailable = (new UseInternalController)->_isAvailableProduct($fields['product_id']);

            if (!$isAvailable['isAvailable']) {
                throw new \Exception('not_available');
            }

            (new UseInternalController)->_checkBank($fields['shop_id']);

            $fields['user_id'] = $user->id;
            shopping_cart::insertGetId($fields);

            $count_items = shopping_cart::where('user_id', $user->id)
                ->count();

            if ($count_items > 50) {
                throw new \Exception('limit items on shopping cart');
            }

            $data = shopping_cart::where('user_id', $user->id)
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
            return response()->json($response, 400);
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

            shopping_cart::where('id', $id)
                ->where('user_id', $user->id)
                ->update($fields);

            $data = shopping_cart::find($id);


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
            shopping_cart::where('id', $id)
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
