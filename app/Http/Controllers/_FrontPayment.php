<?php

namespace App\Http\Controllers;

use App\Notifications\_NewPurchaseSmsShop;
use App\Notifications\_NewPurchaseSmsUser;
use App\Notifications\_PayOrder;
use App\platform_payment;
use App\shopping_cart;
use Illuminate\Http\Request;
use App\purchase_order;
use App\purchase_detail;
use App\user_payment;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;


class _FrontPayment extends Controller
{

    private $stripe_pk;
    private $stripe_sk;
    private $stripe_api;

    public function __construct()
    {

        $stripeKeys = platform_payment::where('platform_payments.name', 'stripe')
            ->join('payment_keys', 'platform_payments.id', '=', 'payment_keys.platform_payment')
            ->select('payment_keys.*')
            ->first();

        $this->stripe_sk = ($stripeKeys->status === 'live' ? $stripeKeys->app_secret : $stripeKeys->app_secret_sandbox);
        $this->stripe_ap = ($stripeKeys->status === 'live' ? $stripeKeys->app_key : $stripeKeys->app_key_sandbox);
        $this->stripe_api = ($stripeKeys->status === 'live' ? $stripeKeys->live_end_point : $stripeKeys->sandbox_end_point);
    }


    public function _transfer($uuid = null, $amount = 0, $destination = null, $description = null)
    {

        $currency = (new UseInternalController)->_getSetting('currency');
        $client_secret = $this->stripe_sk;
        $response = Curl::to($this->stripe_api . '/v1/transfers')
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
        $client_secret = $this->stripe_sk;
        $response = Curl::to($this->stripe_api . '/v1/charges')
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
            DB::beginTransaction();
            $user = JWTAuth::parseToken()->authenticate();
            $discount_to_supplier = (new UseInternalController)->_getSetting('discount_to_supplier');
            $marketplace = (new UseInternalController)->_getSetting('marketplace');
            $auto_sms = (new UseInternalController)->_getSetting('auto_sms');
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
                $user_payment = user_payment::where('user_payments.primary', 1)
                    ->join('shops', 'user_payments.user_id', '=', 'shops.users_id')
                    ->join('users', 'user_payments.user_id', '=', 'users.id')
                    ->where('shops.id', $purchase->shop_id)
                    ->select('user_payments.*', 'users.phone as phone_number', 'users.email as shop_email')
                    ->first();

                $data_feed = (new UseInternalController)->_getFeedAmount($purchase->product_amount);

                if ($user_payment && ($user_payment->payment_option === 'stripe')) {
                    $description = "Producto ID: $purchase->product_id, Etiqueta: $purchase->product_label";
                    $description .= "Order: $request->purchase_uuid, Feed: " . $data_feed['application_feed_amount'];
                    $amount_supplier = ($discount_to_supplier == 1) ?
                        $data_feed['amount_without_feed'] : $data_feed['amount_with_feed'];

                    if($marketplace == 1){
                            $r = $this->_transfer($request->purchase_uuid,
                            $amount_supplier, $user_payment->iban, $description);
                        if ($r) {
                            purchase_order::where('uuid', $request->purchase_uuid)
                                ->where('user_id', $user->id)
                                ->update(['status' => 'success']);
                            $user_payment->setAttribute('uuid', $request->purchase_uuid);
                            if ($auto_sms == 1) {
                                $user_payment->notify(new _NewPurchaseSmsShop($user_payment));
                            }
                        }
                    }else{
                        purchase_order::where('uuid', $request->purchase_uuid)
                        ->where('user_id', $user->id)
                        ->update(['status' => 'success']);
                    }
  
                }
            };

            shopping_cart::where('user_id', $user->id)
                ->delete();

            $user->setAttribute('uuid', $request->purchase_uuid);
            if ($auto_sms == 1) {
                $user->notify(new _NewPurchaseSmsUser($user));
            }
            $user->setAttribute('shop_email',$user_payment->shop_email);
            $user->notify(new _PayOrder($user));

            DB::commit();

            /*Proceso de ENVIO*/
            $auto_delivery = (new UseInternalController)->_getSetting('auto_delivery');
            if ($auto_delivery == 1) {
                (new _FrontDelivery)->_internalSend($request->purchase_uuid);
            }
            /*Fin proceso de ENVIO*/
            $response = array(
                'status' => 'success',
                'data' => [
                    'id' => $charge->id,
                    'amount' => $charge->amount,
                    'purchase' => $totalPurchase,
                    'uuid' => $request->purchase_uuid
                ],
                'code' => 0
            );
            return response()->json($response);


        } catch (\Exception $e) {
            DB::rollBack();
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