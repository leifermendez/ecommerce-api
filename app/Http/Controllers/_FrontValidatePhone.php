<?php

namespace App\Http\Controllers;

use App\phone_codes_validate;
use App\User;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Keygen;

class _FrontValidatePhone extends Controller
{
    function OAuth()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $client = new Client($sid, $token);
        return $client;
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
            $from = env('TWILIO_FROM');
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'code' && $key !== 'user_id') {
                    $fields[$key] = $value;
                };
            }
            $code = Keygen::numeric(6)->generate();
            $fields['code'] = $code;
            $fields['user_id'] = $user->id;

            $validator = Validator::make($request->all(), [
                'phone' => 'required'
            ]);

            if ($validator->passes()) {
                $client = $this->OAuth();

                $data = phone_codes_validate::insertGetId($fields);
                $data = phone_codes_validate::find($data);

                $client->messages->create(
                    $request->phone,
                    [
                        'from' => $from,
                        'body' => 'Apatxee. Tu código es ' . $code,
                    ]
                );


                $response = array(
                    'status' => 'success',
                    'msg' => 'Insertado',
                    'data' => $data,
                    'code' => 0
                );
                return response()->json($response);

            } else {
                throw new \Exception("Numero no valido");
            }

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
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $data = phone_codes_validate::where('user_id', $user->id)
                ->where('code', $id)
                ->where('status', 'available')
                ->first();

            if ($data) {
                phone_codes_validate::where('id', $data->id)
                    ->update(['status' => 'unavailable']);

                User::where('id', $user->id)
                    ->update(['confirmed' => 1]);

                $response = array(
                    'status' => 'success',
                    'msg' => 'Validado',
                    'data' => $data,
                    'code' => 0
                );
                return response()->json($response);
            } else {
                throw new \Exception('Codigo no valido');
            }

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
