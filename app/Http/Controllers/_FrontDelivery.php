<?php

namespace App\Http\Controllers;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Request;
use Validator;
use App\shop;
use App\shipping_address;

define("_api_","https://test.api.paack.co/api");

class _FrontDelivery extends Controller
{

    public function _send($data = array())
    {
        $paack_key = env('PAACK_KEY', '');
        $response = Curl::to(_api_."/public/v2/orders")
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

        if($response->status!=='OK'){
            throw new \Exception(json_encode($response->data));
        }
        $data = $response->data;
        return $data;

    }

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

            $delivery_list = [];

            $data_uuid = (new UseInternalController)
                ->_purchaseStatus($request->retailer_order_number);

            foreach ($data_uuid['purchase'] as $value) {
           
                $data_shop = shop::find($value['shop_id']);
                $data_delivery = shipping_address::where('shipping_addresses.id',$value['shipping_address_id'])
                ->join('users','users.id','=','shipping_addresses.user_id')
                ->select('shipping_addresses.*',
                'users.name as users_name','users.email as users_email','users.phone as users_phone')
                ->first();
              
                //validar si existe direccion y data shop
                if(!$data_delivery){
                    throw new \Exception('error shipping address not found '.$value['uuid_shipping']);
                }

                if(!$data_shop){
                    throw new \Exception('error shop not found '.$value['shop_id']);
                }

                $fields = [
                    'retailer_order_number' => $value['uuid_shipping'],
                    'sale_number' => $request->retailer_order_number,
                    'description' => '',
                    'pickup_address_name' => $data_shop->name,
                    'pickup_address_email' => $data_shop->email_corporate,
                    'pickup_address_phone' => $data_shop->phone_fixed,
                    'pickup_address_address' => $data_shop->address,
                    'pickup_address_postal_code' => $data_shop->zip_code,
                    'pickup_address_country' => 'es',//<------ esto debe ir en la base de datos
                    'pickup_address_city' => 'madrid',//<------ esto debe ir en la base de datos
                    'pickup_address_instructions' => 'preguntar por tony',//<------ esto debe ir en la base de datos
                    'delivery_address_name' => $data_delivery->users_name,
                    'delivery_address_email' => $data_delivery->users_email,
                    'delivery_address_phone' => $data_delivery->users_phone,
                    'delivery_address_address' => $data_delivery->address,
                    'delivery_address_postal_code' => $data_delivery->address,//<---- zip code debe esta en la tabla shipping_addresses
                    'delivery_address_country' => $data_delivery->country,
                    'delivery_address_city' => $data_delivery->district,
                    'delivery_address_instructions' => '',//<---- pensar
                    'weight' => '1234',//<---- pensar
                    'width' => '60',//<---- pensar
                    'height' => '50',//<---- pensar
                    'length' => '40',//<---- pensar
                    'barcode' =>''//<---- pensar
                ];

              

                if($value['status'] ==='success'){
                    $send = $this->_send($fields);
                    //aqui tengo que revisar porque no llega nada
                    $delivery_list[] = $send;
                }
            }//debemos verificar que tenga
      
            
            $response = array(
                'status' => 'success',
                'data' => $delivery_list,
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
