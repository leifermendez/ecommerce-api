<?php

namespace App\Http\Controllers;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use App\shop;
use App\shipping_address;
use App\delivery_order;
use Carbon\Carbon;

define("_api_delivery_", "https://api.paack.co/api");

class _FrontDelivery extends Controller
{

    public function _send($data = array())
    {
        $paack_key = env('PAACK_KEY', '');
        $pickup_window = [
            'start_time' => Carbon::now()->addMinutes(10),
            'end_time' => Carbon::now()->addMinutes(60)
        ];
        $response = Curl::to(_api_delivery_ . "/public/v2/orders")
            ->withHeaders([
                "X-Authentication: $paack_key"
            ])
            ->withData([
                'order_type' => 'direct',
                'retailer_order_number' => $data['retailer_order_number'],
                'pickup_address' => [
                    'name' => $data['pickup_address_name'],
                    'email' => $data['pickup_address_email'],
                    'phone' => $data['pickup_address_phone'],
                    'address' => $data['pickup_address_address'],
                    'postal_code' => $data['pickup_address_postal_code'],
                    'country' => $data['pickup_address_country'],
                    'city' => $data['pickup_address_city'],
                    'instructions' => $data['pickup_address_instructions']
                ],
                'delivery_address' => [
                    'name' => $data['delivery_address_name'],
                    'email' => $data['delivery_address_email'],
                    'phone' => $data['delivery_address_phone'],
                    'address' => $data['delivery_address_address'],
                    'postal_code' => $data['delivery_address_postal_code'],
                    'country' => $data['delivery_address_country'],
                    'city' => $data['delivery_address_city'],
                    'instructions' => $data['delivery_address_instructions']
                ],
                'pickup_window' => $pickup_window
                /*'packages' => [
                    'weight' => $request->weight,
                    'width' => $request->width,
                    'height' => $request->height,
                    'length' => $request->length,
                    'barcode' => $request->barcode
                ]*/
            ])
            ->post();
        //1880805

        $response = json_decode($response);

        if ($response->status !== 'OK') {
            throw new \Exception(json_encode($response->data));
        }
        $data = $response->data;
        return $data;

    }

    public function _internalSend($uuid = null)
    {
        try {

            if (!$uuid) {
                throw new \Exception('uuid null');
            }

            $user = JWTAuth::parseToken()->authenticate();

            $delivery_list = [];
            $delivery_errors = [];

            $data_uuid = (new UseInternalController)
                ->_purchaseStatus($uuid);

            foreach ($data_uuid['purchase'] as $value) {

                $data_pickup = shop::where('shops.id', $value['shop_id'])
                    ->disableCache()
                    ->join('shipping_pickup_addresses', 'shops.id', '=', 'shipping_pickup_addresses.shop_id')
                    ->select('shops.*', 'shipping_pickup_addresses.country as pickup_country',
                        'shipping_pickup_addresses.district as pickup_city',
                        'shipping_pickup_addresses.instructions as pickup_instructions')
                    ->first();

                $data_delivery = shipping_address::where('shipping_addresses.id', $value['shipping_address_id'])
                    ->join('users', 'users.id', '=', 'shipping_addresses.user_id')
                    ->select('shipping_addresses.*',
                        'users.name as users_name', 'users.email as users_email', 'users.phone as users_phone')
                    ->first();

                if (!$data_delivery) {
                    throw new \Exception('error delivery address not found ' . $value['uuid_shipping']);
                }

                if (!$data_pickup) {
                    throw new \Exception('error shop pickup address not found ' . $value['shop_id']);
                }

                $fields = [
                    'retailer_order_number' => $value['uuid_shipping'],
                    'sale_number' => $uuid,
                    'description' => '',
                    'pickup_address_name' => $data_pickup->name,
                    'pickup_address_email' => $data_pickup->email_corporate,
                    'pickup_address_phone' => $data_pickup->phone_fixed,
                    'pickup_address_address' => $data_pickup->address,
                    'pickup_address_postal_code' => $data_pickup->zip_code,
                    'pickup_address_country' => $data_pickup->pickup_country,
                    'pickup_address_city' => $data_pickup->pickup_city,
                    'pickup_address_instructions' => $data_pickup->pickup_instructions,
                    'delivery_address_name' => $data_delivery->users_name,
                    'delivery_address_email' => $data_delivery->users_email,
                    'delivery_address_phone' => $data_delivery->users_phone,
                    'delivery_address_address' => $data_delivery->address,
                    'delivery_address_postal_code' => $data_delivery->zip_code,
                    'delivery_address_country' => $data_delivery->country,
                    'delivery_address_city' => $data_delivery->district,
                    'delivery_address_instructions' => $data_delivery->instructions,
                    'weight' => '10',//<---- pensar
                    'width' => '10',//<---- pensar
                    'height' => '10',//<---- pensar
                    'length' => '10',//<---- pensar
                    'barcode' => ''//<---- pensar
                ];

                if ($value['status'] === 'success') {
                    $send = $this->_send($fields);
                    $delivery_value = [
                        'deliver_uuid' => $value['uuid_shipping'],
                        'purchase_uuid' => $value['uuid'],
                        'retailer_order_number' => $send->paack_order_number,
                        'tracking_url' => $send->tracking_url,
                        'user_id' => $user->id
                    ];
                    delivery_order::insertGetId($delivery_value);
                    $delivery_list[] = $send;
                } else {
                    $value['error_msg'] = 'status ' . $value['status'];
                    $delivery_errors[] = $value;
                }
            }


            $response = array(
                'status' => 'success',
                'data' => [
                    'delivery' => $delivery_list,
                    'errors' => $delivery_errors
                ],
                'code' => 0
            );
            return $response;

        } catch (\Exception $e) {
            return null;
        }
    }

    public function index(Request $request)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $limit = ($request->limit) ? $request->limit : 15;

            $data = shop::orderBy('id', 'DESC')
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

    public function show(Request $request, $id)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $data = delivery_order::where('user_id', $user->id)
                ->where(function ($query) use ($request, $id) {
                    if ($request->for) {
                        $query->where('deliver_uuid', $id);
                    } else {
                        $query->where('id', $id);
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

    public function store(Request $request)
    {
        try {

            Validator::make($request->all(), [
                'retailer_order_number' => 'required'

            ])->validate();

            $user = JWTAuth::parseToken()->authenticate();

            $delivery_list = [];
            $delivery_errors = [];

            $data_uuid = (new UseInternalController)
                ->_purchaseStatus($request->retailer_order_number);

            foreach ($data_uuid['purchase'] as $value) {

                $data_pickup = shop::where('shops.id', $value['shop_id'])
                    ->disableCache()
                    ->join('shipping_pickup_addresses', 'shops.id', '=', 'shipping_pickup_addresses.shop_id')
                    ->select('shops.*', 'shipping_pickup_addresses.country as pickup_country',
                        'shipping_pickup_addresses.address as pickup_address',
                        'shipping_pickup_addresses.state as pickup_state',
                        'shipping_pickup_addresses.district as pickup_city',
                        'shipping_pickup_addresses.country as pickup_country',
                        'shipping_pickup_addresses.zip_code as pickup_zip_code',
                        'shipping_pickup_addresses.instructions as pickup_instructions')
                    ->first();


                $data_delivery = shipping_address::where('shipping_addresses.id', $value['shipping_address_id'])
                    ->join('users', 'users.id', '=', 'shipping_addresses.user_id')
                    ->select('shipping_addresses.*',
                        'users.name as users_name', 'users.email as users_email', 'users.phone as users_phone')
                    ->first();

                if (!$data_delivery) {
                    throw new \Exception('error delivery address not found ' . $value['uuid_shipping']);
                }

                if (!$data_pickup) {
                    throw new \Exception('error shop pickup address not found ' . $value['shop_id']);
                }

                $fields = [
                    'retailer_order_number' => $value['uuid_shipping'],
                    'sale_number' => $request->retailer_order_number,
                    'description' => '',
                    'pickup_address_name' => $data_pickup->name,
                    'pickup_address_email' => $data_pickup->email_corporate,
                    'pickup_address_phone' => $data_pickup->phone_fixed,
                    'pickup_address_address' => $data_pickup->pickup_address,
                    'pickup_address_postal_code' => $data_pickup->pickup_zip_code,
                    'pickup_address_country' => $data_pickup->pickup_country,
                    'pickup_address_city' => $data_pickup->pickup_city,
                    'pickup_address_instructions' => $data_pickup->pickup_instructions,
                    'delivery_address_name' => $data_delivery->users_name,
                    'delivery_address_email' => $data_delivery->users_email,
                    'delivery_address_phone' => $data_delivery->users_phone,
                    'delivery_address_address' => $data_delivery->address,
                    'delivery_address_postal_code' => $data_delivery->zip_code,
                    'delivery_address_country' => $data_delivery->country,
                    'delivery_address_city' => $data_delivery->district,
                    'delivery_address_instructions' => $data_delivery->instructions,
                    'weight' => '',//<---- pensar
                    'width' => '',//<---- pensar
                    'height' => '',//<---- pensar
                    'length' => '',//<---- pensar
                    'barcode' => ''//<---- pensar
                ];

                if ($value['status'] === 'success') {
                    $send = $this->_send($fields);
                    $delivery_value = [
                        'deliver_uuid' => $value['uuid_shipping'],
                        'purchase_uuid' => $value['uuid'],
                        'retailer_order_number' => $send->paack_order_number,
                        'tracking_url' => $send->tracking_url,
                        'user_id' => $user->id
                    ];
                    delivery_order::insertGetId($delivery_value);
                    $delivery_list[] = $send;
                } else {
                    $value['error_msg'] = 'status ' . $value['status'];
                    $delivery_errors[] = $value;
                }
            }


            $response = array(
                'status' => 'success',
                'data' => [
                    'delivery' => $delivery_list,
                    'errors' => $delivery_errors
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
}
