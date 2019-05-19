<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Validator;

class ExternalSmsController extends Controller
{

    function OAuth(){
        $sid    = env( 'TWILIO_SID' );
        $token  = env( 'TWILIO_TOKEN' );
        $client = new Client( $sid, $token );
        return $client;
    }

    public function store(Request $request){
        try{
            $from = env( 'TWILIO_FROM' );
            $validator = Validator::make($request->all(), [
                'number' => 'required'
            ]);

            if ( $validator->passes() ) {
                $client = $this->OAuth();
 
                $client->messages->create(
                    $request->number,
                    [
                        'from' => $from,
                        'body' => 'Hola probando',
                    ]
                );
            }else{
                throw new \Exception("Numero no valido");
            }

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
