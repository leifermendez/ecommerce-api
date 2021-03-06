<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\resetPassword;
use Carbon\Carbon;
use DB;


class AuthController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = JWTAuth::parseToken()->authenticate();
            $token = JWTAuth::getToken()->get();
            if ($data) {
                $data->setAttribute('token', $token);
            }
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

            return response()->json($response, 401);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
            $credentials = $request->only('email', 'password');

            if ($token = JWTAuth::attempt($credentials)) {

                $data = User::where('email', $request->email)->first();
                $data->setAttribute('token', $token);

                $response = array(
                    'status' => 'success',
                    'data' => $data,
                    'code' => 0
                );
                return response()->json($response);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );
            return response()->json($response, 401);
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
    public function destroy($id = null)
    {

        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            $response = array(
                'status' => 'success',
                'code' => 0
            );
            return response()->json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );
            return response()->json($response, 401);
        }

    }

    public function register(Request $request)
    {
        try {
            $referer_code = Str::random(12);
            $values = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'avatar' => $request->avatar,
                'header' => $request->header,
                'referer_code' => $referer_code
            ];

            $id = User::insertGetId($values);
            $data = User::find($id);

            $data->setAttribute('token', JWTAuth::fromUser($data));

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
            return response()->json($response, 401);
        }
    }

    public function password($email){
        try {
            $user = User::where('email', $email)->first();

            if ($user) {
                $token = Str::random(40);
                DB::table('password_resets')->where('email',$email)->delete();
                DB::table('password_resets')->insert([
                    'email' => $email,
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]);
                $correo['name'] = $user->name;
                $correo['url'] = $token;
                Mail::to($email)->send(new resetPassword($correo));
            }
            $response = [
                'status' => 'success',
                'msg' => 'Email enviado',
                'code' => 0
            ];
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response =[
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            ];
            return response()->json($response, 500);
        }
    }

    public function resetPassword(Request $request){
        try {
            $data = DB::table('password_resets')
            ->select('email')
            ->where('token', $request->token)
            ->first();

            if ($data  == null) {
                $response =[
                'status' => 'fail',
                'msg' => 'Token no existe o ya fue utilizado',
                'code' => 1
                ];
                return response()->json($response, 401);
            }
            $user = User::where('email', $data->email)->first();
            $user->password = bcrypt($request->password);
            $user->save();
            DB::table('password_resets')->where('email',$data->email)->delete();

            $response = [
                'status' => 'success',
                'msg' => 'Password actualizado',
                'code' => 0
            ];
            return response()->json($response, 200);
            
        } catch (Exception $e) {
            $response =[
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            ];
            return response()->json($response, 500);
        }
    }
}
