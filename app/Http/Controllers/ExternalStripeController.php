<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

define("_api_", "https://api.stripe.com");

class ExternalStripeController extends Controller
{
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $client_secret = env('STRIPE_SECRET', '');

            $request->validate([
                'amount' => 'required',
                'currency' => 'required',
                'source' => 'required',
                'transfer_group' => 'required'
            ]);

            $response = Curl::to(_api_.'/v1/charges')
            ->withContentType('application/x-www-form-urlencoded')
            ->withOption('USERPWD', "$client_secret:")
            ->withHeader('Accept: application/json')
            ->withData( array( 
                'amount' => floatval($request->amount),
                'currency' => $request->currency,
                'source' => $request->source,
                'transfer_group' =>  $request->transfer_group
                 ) )
            ->returnResponseObject()
            ->post();

            if($response->status!==200){
                throw new \Exception($response->content);
            }

            return json_decode($response->content);
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
