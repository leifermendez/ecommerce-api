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

        try {
            $client_secret = env('STRIPE_SECRET', '');
            $request->validate([
                'code' => 'required',
            ]);

            $response = Curl::to(_api_.'/oauth/token')
                ->withContentType('application/x-www-form-urlencoded')
                ->withHeader('Accept: application/json')
                ->withData( array(
                    'client_secret' => $client_secret,
                    'code' => $request->code,
                    'grant_type' => 'authorization_code'
                ) )
                ->returnResponseObject()
                ->post();
            if($response->status!==200){
                throw new \Exception($response->content);
            }


            $response = array(
                'status' => 'success',
                'data' => json_decode($response->content),
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
