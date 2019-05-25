<?php

namespace App\Http\Controllers;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Request;
use Validator;

define("_api_","https://private-anon-f59e959d29-paack.apiary-mock.com/api");

class ExternalDeliveryController extends Controller
{
    public function store(Request $request){
        try{
            
            $validator = Validator::make($request->all(), [
                'retailer_order_number' => 'required',
                'sale_number' => 'required',
                'description' => 'required',
                'pickup_address_name' => 'required',
                'pickup_address_email' => 'required',
                'pickup_address_phone' => 'required',
                'pickup_address_address' => 'required',
                'pickup_address_postal_code' => 'required',
                'pickup_address_country' => 'required',
                'pickup_address_city' => 'required',
                'pickup_address_instructions' => 'required',
                'delivery_address_name' => 'required',
                'delivery_address_email' => 'required',
                'delivery_address_phone' => 'required',
                'delivery_address_address' => 'required',
                'delivery_address_postal_code' => 'required',
                'delivery_address_country' => 'required',
                'delivery_address_city' => 'required',
                'delivery_address_instructions' => 'required',
                'weight' => 'required',
                'width' => 'required',
                'height' => 'required',
                'length' => 'required',
                'barcode' => 'required',
            ])->validate();
            
            $response = Curl::to(_api_."/public/v2/orders")
            ->withHeaders([
                "Content-Type: application/json", 
                "X-Authentication: 'apikey'"
            ])
            ->withData([
                'order_type' => 'direct',
                'retailer_order_number' => $request->retailer_order_number,
                'pickup_address' => [
                    'name' => $request->pickup_address_name,
                    'email' => $request->pickup_address_email,
                    'phone' => $request->pickup_address_phone,
                    'address' => $request->pickup_address_address,
                    'postal_code' => $request->pickup_address_postal_code,
                    'country' => $request->pickup_address_country,
                    'city' => $request->pickup_address_city,
                    'instructions' => $request->pickup_address_instructions
                ],
                'delivery_address' => [
                    'name' => $request->delivery_address_name,
                    'email' => $request->delivery_address_email,
                    'phone' => $request->delivery_address_phone,
                    'address' => $request->delivery_address_address,
                    'postal_code' => $request->delivery_address_postal_code,
                    'country' => $request->delivery_address_country,
                    'city' => $request->delivery_address_city,
                    'instructions' => $request->delivery_address_instructions
                ],
                'packages' => [
                    'weight' => $request->weight,
                    'width' => $request->width,
                    'height' => $request->height,
                    'length' => $request->length,
                    'barcode' => $request->barcode
                ]
            ])
            ->post();
            //1880805

            $response = json_decode($response);
            dd($response);

            if($response->status!==200){
                throw new \Exception($response->content);
            }

            $data = json_decode($response->content);

            $response = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);

        }catch (\Exception $e) {
            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );

            return response()->json($response, 500);
        }
    }
}
