<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Stripe\Stripe;
use Ixudra\Curl\Facades\Curl;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;


define("_api_", "https://connect.stripe.com");

class ExternalStripeAuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

        //$stripe = Stripe::make('your-stripe-api-key', 'your-stripe-api-version');

    public function index()
    {
        try {
            //$priceDelivery = (new UseInternalController)->_getSetting('delivery_feed_min');
            $api = 'https://connect.stripe.com/oauth/authorize';
            $platform_id = env('STRIPE_PLATFORM_ID');
            $data = $api . '?response_type=code&client_id=';
            $data .= $platform_id . '&scope=read_write';
            $data .= '&redirect_uri=http://localhost:4200';

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

        //  curl -X POST https://connect.stripe.com/oauth/token \
        //-d client_secret=YOUR_SECRET_KEY \
        //-d code=ac_F79cwmtschAcSAkKzJcDh11wRX25qk0N \
        //-d grant_type=authorization_code
        try {
            $stripe = new Stripe();
//            $user_current = JWTAuth::parseToken()->authenticate();
//            $stripe_sk = env('STRIPE_SECRET');
//
//            Validator::make($request->all(), [
//                'code' => 'required'
//            ])->validate();
//
//            $response = Curl::to(_api_ . "/oauth/token")
//                ->withData([
//                    'client_secret' => $stripe_sk,
//                    'code' => $request->code,
//                    'grant_type' => 'authorization_code'
//                ])
//                ->returnResponseObject()
//                ->post();
//
//            if ($response->status !== 200) {
//                throw new \Exception($response->content);
//            }
//
//            $data = json_decode($response->content);

            /**
             *
             *
             */
            $stripe = $stripe->account()->details();
            dd($stripe);

            $values = [
                'user_id' => $user_current->id,
                'payment_option' => 'stripe',
                'payment_email' => ''
            ];

            user_payment::insertGetId()
                ->insert();

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
        //
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
        //
    }
}
