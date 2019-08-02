<?php

namespace App\Http\Controllers;

use App\Notifications\_NewPurchaseSmsShop;
use App\Notifications\_NewPurchaseSmsUser;
use App\Notifications\_UserPurchase;
use App\Notifications\_UserVerified;
use App\purchase_order;
use App\purchase_detail;
use App\shipping_address;
use App\shop;
use App\shopping_cart;
use App\variation_product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

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
            $user = JWTAuth::parseToken()->authenticate();
            $limit = ($request->limit) ? $request->limit : 15;
            $filters = ($request->filters) ? explode("?", $request->filters) : [];

            $sql = purchase_order::orderBy('purchase_orders.id', 'desc')
                ->where(function ($query) use ($filters, $user, $request) {
                    $query->where('purchase_orders.user_id', $user->id);

                    foreach ($filters as $value) {
                        $tmp = explode(",", $value);
                        if (isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])) {
                            $subTmp = explode("|", $tmp[2]);
                            if (count($subTmp) > 1) {
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
                ->join('shops', 'purchase_orders.shop_id', '=', 'shops.id')
                ->select('purchase_orders.*', 'shops.name as shops_name',
                    'shops.phone_mobil as shops_mobil', 'shops.phone_fixed as shops_fixed',
                    DB::raw('(SELECT attacheds.medium FROM attacheds 
                WHERE attacheds.id = shops.image_cover limit 1) as logo_brand')
                )
                ->paginate($limit)
                ->appends(request()->except('page'));

            $data->map(function ($item, $key) {
                $item->list = (new UseInternalController)->_detailPurchase($item->uuid, 3);
                return $item;
            });

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
            DB::beginTransaction();
            $user = JWTAuth::parseToken()->authenticate();

            $shoppingCart = (new UseInternalController)->_shoppingCart($user->id);
            $_shipping_price = (new UseInternalController)->_getSetting('delivery_feed_min');
            $_shipping_tax = (new UseInternalController)->_getSetting('delivery_feed_tax');
            $priceDelivery = floatval(($_shipping_price * $_shipping_tax) + $_shipping_price);
            $uuid = Str::random(40);

            foreach ($shoppingCart['list'] as $value) {
                purchase_detail::insert(
                    [
                        'purchase_uuid' => $uuid,
                        'product_id' => $value['product_id'],
                        'product_qty' => 1,
                        'product_amount' => $value['price_normal'],
                        'shop_id' => $value['shop_id'],
                        'product_label' => $value['name']
                    ]
                );

                $deliver_address = shipping_address::where('user_id', $user->id)
                    ->first();

                if (!$deliver_address) {
                    throw new \Exception('user shopping address empty');
                }

                $total_amount = (isset($lists[$value['shop_id']]['amount'])) ?
                    floatval($lists[$value['shop_id']]['amount'] + $value['price_normal']) :
                    floatval($value['price_normal']);

                $feed_percentage = (new UseInternalController)->_getFeedAmount($total_amount);
                $isFree = variation_product::where('id', $value['variation_product_id'])
                    ->where('delivery', 1)
                    ->exists();

                $lists[$value['shop_id']] = [
                    "shop_id" => $value['shop_id'],
                    "uuid" => $uuid,
                    "user_id" => $user->id,
                    "amount_shipping" => ($isFree) ? 0 : $priceDelivery,
                    "feed" => $feed_percentage['application_feed_amount'],
                    "status" => "wait",
                    "shipping_address_id" => $deliver_address->id,
                    "uuid_shipping" => 'sh_' . Str::random(12),
                    "amount" => $total_amount
                ];


            };
            if (count($lists) < 1) {
                throw new \Exception('shopping cart empty');
            }

            foreach ($lists as $list) {
                purchase_order::insert($list);
            };

//            shopping_cart::where('user_id', $user->id)
//                ->delete();


            $data = purchase_order::where('uuid', $uuid)
                ->get();

            $data_list = purchase_detail::where('purchase_details.purchase_uuid', $uuid)
                ->join('products','purchase_details.product_id','=','products.id')
                ->select('purchase_details.*','products.name as products_name')
                ->get();

            $user->setAttribute(
                'list',$data_list
            );
            $user->setAttribute(
                'uuid',$uuid
            );

            $user->notify(new _UserPurchase($user));

            DB::commit();
            $response = array(
                'status' => 'success',
                'msg' => 'Insertado',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
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
            $data = purchase_order::where('purchase_orders.user_id', $user->id)
                ->join('shops', 'purchase_orders.shop_id', '=', 'shops.id')
                ->select('purchase_orders.*', 'shops.name as shops_name')
                ->where(function ($query) use ($id) {
                    if (strlen($id) > 10) {
                        $query->where('purchase_orders.uuid', $id);
                    } else {
                        $query->where('purchase_orders.id', $id);
                    }
                })
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
