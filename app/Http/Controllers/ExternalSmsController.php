<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\phone_codes_validate;
use Validator;
use Keygen;

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
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'code') {
                    $fields[$key] = $value;
                };
            }
            $code = Keygen::numeric(6)->generate();
            $fields['code'] = $code;
        
            $validator = Validator::make($request->all(), [
                'phone' => 'required'
            ]);

            if ( $validator->passes() ) {
                $client = $this->OAuth();
 
                $data = phone_codes_validate::insertGetId($fields);
                $data = phone_codes_validate::find($data);
                
                $client->messages->create(
                    $request->phone,
                    [
                        'from' => $from,
                        'body' => 'Apatxee. Tu código es '.$code,
                    ]
                );
    
        

                $response = array(
                    'status' => 'success',
                    'msg' => 'Insertado',
                    'data' => $data,
                    'code' => 0
                );
                return response()->json($response);

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

    public function update(Request $request, $id)
    {
        try{
          
            $data = phone_codes_validate::where('user_id',$request->user_id)
            ->where('code',$id)
            ->where('status','available')
            ->first();
            
            if($data){
                phone_codes_validate::where('id',$data->id)
                ->update(['status'=>'unavailable']);

                $response = array(
                    'status' => 'success',
                    'msg' => 'Validado',
                    'data' => $data,
                    'code' => 0
                );
                return response()->json($response);
            }else{
                throw new \Exception('Codigo no valido');
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
