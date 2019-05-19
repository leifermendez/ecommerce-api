<?php

namespace App\Http\Controllers;
use Ixudra\Curl\Facades\Curl;

use Illuminate\Http\Request;

define("_api_","https://developers.einforma.com/api/v1");

class ExternalCifController extends Controller
{
    function OAuth(){
        try {
            $client_id = env('ELINFORMAR_CLIENT_ID', '');
            $client_secret = env('ELINFORMAR_CLIENT_SECRET', '');
    
            $response = Curl::to(_api_.'/oauth/token')
            ->withContentType('application/x-www-form-urlencoded')
            ->withHeader('Accept: application/json')
            ->withData( array( 
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'grant_type' => 'client_credentials',
                'scope' => 'buscar:consultar:empresas'
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


    public function show($id=null){

        try{
            if(!$id){
                throw new \Exception("ID not valido");
            }
            $auth = $this->OAuth();
            if($auth){
                $response = Curl::to(_api_."/companies/$id/test")
                ->withHeader('Accept: application/json')
                ->withHeader('Authorization: Bearer '.$auth->access_token )
                ->returnResponseObject()
                ->get();

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
