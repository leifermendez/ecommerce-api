<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\purchase_order;
use App\purchase_detail;
use App\user_payment;
use App\shop;
use Ixudra\Curl\Facades\Curl;
use Tymon\JWTAuth\Facades\JWTAuth;

define("_api_", "https://api.stripe.com");

class _FrontPayment extends Controller
{

    public function _transfer($uuid = null, $amount = 0, $destination = null, $description = null)
    {

        $currency = (new UseInternalController)->_getSetting('currency');
        $client_secret = env('STRIPE_SECRET', '');
        $response = Curl::to(_api_ . '/v1/transfers')
            ->withContentType('application/x-www-form-urlencoded')
            ->withOption('USERPWD', "$client_secret:")
            ->withHeader('Accept: application/json')
            ->withData(array(
                'amount' => number_format($amount, 2, '', ''),
                'currency' => $currency,
                'destination' => $destination,
                'transfer_group' => $uuid,
                'description' => $description
            ))
            ->returnResponseObject()
            ->post();

        if ($response->status !== 200) {
            throw new \Exception($response->content);
        }

        $data = json_decode($response->content);
        return $data;
    }

    public function _charge($uuid = null, $amount = 0, $source = null)
    {
        $currency = (new UseInternalController)->_getSetting('currency');
        $client_secret = env('STRIPE_SECRET', '');
        $response = Curl::to(_api_ . '/v1/charges')
            ->withContentType('application/x-www-form-urlencoded')
            ->withOption('USERPWD', "$client_secret:")
            ->withHeader('Accept: application/json')
            ->withData(array(
                'amount' => number_format($amount, 2, '', ''),
                'currency' => $currency,
                'source' => $source,
                'transfer_group' => $uuid
            ))
            ->returnResponseObject()
            ->post();

        if ($response->status !== 200) {
            throw new \Exception($response->content);
        }

        $data = json_decode($response->content);
        return $data;

    }

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
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $feed_percentage = (new UseInternalController)->_getSetting('feed_percentage');
            $feed_amount = (new UseInternalController)->_getSetting('feed_amount');
            $feed_limit_price = (new UseInternalController)->_getSetting('feed_limit_price');

            $request->validate([
                'source' => 'required',
                'purchase_uuid' => 'required'
            ]);

            if (!purchase_order::where('uuid', $request->purchase_uuid)
                ->where('user_id', $user->id)
                ->where('status', 'wait')->exists()) {
                throw new \Exception('uuid already payment');
            }

            $totalPurchase = (new UseInternalController)->_totalPurchase($request->purchase_uuid);

            $charge = $this->_charge($request->purchase_uuid, $totalPurchase['total'], $request->source);
            $detail_purchase = purchase_detail::where('purchase_uuid', $request->purchase_uuid)->get();

            foreach ($detail_purchase as $purchase) {
                $user_payment = shop::where('shops.id', $purchase->shop_id)
                    ->join('user_payments', 'user_payments.user_id', '=', 'shops.users_id')
                    ->orderBy('user_payments.updated_at', 'DESC')
                    ->where('user_payments.primary', 1)
                    ->select('user_payments.*')
                    ->first();

                if ($user_payment && ($user_payment->payment_option === 'stripe')) {
                    $amount_detail = ($purchase->product_amount);

                    if ($amount_detail >= $feed_limit_price) {
                        $percentage_feed = $amount_detail * $feed_percentage;
                        $amount_detail = ($amount_detail - $percentage_feed);
                    } else {
                        $amount_detail = ($amount_detail - $feed_amount);
                    }

                    $description = "Producto ID: $purchase->product_id, Etiqueta: $purchase->product_label";
                    $this->_transfer($request->purchase_uuid,
                        $amount_detail, $user_payment->iban, $description);

                }
            };


//            $data_purchase = purchase_order::where('uuid', $request->purchase_uuid)
//                ->where('user_id', $user->id)
//                ->update(['status' => 'success']);


            $response = array(
                'status' => 'success',
                'data' => [
                    'id' => $charge->id,
                    'amount' => $charge->amount,
                    'purchase' => $totalPurchase
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
