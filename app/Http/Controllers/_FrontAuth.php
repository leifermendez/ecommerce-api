<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Hashids\Hashids;
use App\Notifications\_UserWelcome;
use Illuminate\Support\Facades\Mail;
use App\Mail\resetPassword;
use DB;



class _FrontAuth extends Controller
{
    public function validTokenProvider($provider = 'facebook', $token = 'invalid')
    {
        $FB = env('FACEBOOK_ID');
        $urlFacebook = "https://graph.facebook.com/oauth/access_token_info?client_id=" . $FB . "&access_token=$token";
        $urlGoogle = "https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=$token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ($provider === 'google') ? $urlGoogle : $urlFacebook);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        $data = json_decode($data, true);
        curl_close($ch);
        if ($info["http_code"] === 200 && isset($data['expires_in']) && ($data['expires_in'] > 120)) {
            return $data;
        } else {
            return false;
        };
    }

    public function registerNewUser($data = array())
    {

        $fields = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'username' => $data['email'],
            'referer_code' => $data['referer_code'],
            'avatar' => $data['avatar'],
            'role' => $data['role']
        ];

        User::create($fields);

        $dataUser = User::where('email', $data['email'])->first();
        $dataUser->setAttribute('token', JWTAuth::fromUser($dataUser));
        return $dataUser;
    }


    public function create($data = array()) // Registrar
    {
        if (!isset($data['name'])) {
            $response = array(
                'status' => 'fail',
                'msj' => 'Nombre Vacio',
                'code' => 5
            );
            return response()->json($response, 400);
        }
        $random = Str::random(10);
        $random_ref_code = Str::random(8);
        $fields = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => ($data['password']) ? $data['password'] : $random,
            'username' => $data['email'],
            'avatar' => $data['avatar'],
            'role' => $data['role'],
            'referer_code' => $random_ref_code
        ];

        $data = $this->registerNewUser($fields);
        $data->notify(new _UserWelcome($data));

        $response = array(
            'status' => 'success',
            'msj' => 'Registro Exitoso',
            'data' => $data,
            'code' => 0
        );
        return response()->json($response);
    }


    public function index() // -Refrescar Token
    {
        $token = JWTAuth::getToken();
        $exp = env('JWT_TTL', 1440);
        if (!$token) {
            throw new BadRequestHtttpException('Token not provided');
        }
        try {
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $e) {
            throw new AccessDeniedHttpException('The token is invalid');
        }

        $response = array(
            'status' => 'success',
            'data' => [
                'token' => $token,
                'exp' => $exp
            ],
            'code' => 0
        );

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $exp = env('JWT_TTL', 1440);
            $request->validate([
                'email' => 'required'
            ]);

            $email = $request->email;
            $provider = $request->provider;
            $token = $request->token;
            $avatar = $request->avatar;
            $password = $request->password;
            $role = $request->role;
            $random_ref_code = Str::random(8);

            if (!User::where('email', $request->email)->exists()) {
                $response = $this->create(array(
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password,
                    'role' => ($role) ? 'shop' : 'user',
                    'avatar' => ($request->avatar) ? $request->avatar :
                        'https://storage.googleapis.com/media-ecommerce-alterhome/public/upload/products/large_6fCnjzn5xKXY2tWc0s5IAw0fKy7ecaoaaG6.jpg',
                    'referer_code' => $random_ref_code
                ));
                return $response;
            }

            if (strtolower($provider) === 'facebook' || strtolower($provider) === 'google') {
                $valid = $this->validTokenProvider($provider, $token);
                if (!$valid) {
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Token invalid',
                        'code' => 5
                    );
                    return response()->json($response, 400);
                }

                if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                    $new_avatar = (new _FrontAttached)->_internalUpload($avatar);

                    if ($new_avatar['status'] === 'success') {
                        User::where('email', $email)->update([
                            'avatar' => $new_avatar['data']
                        ]);
                    }
                }

                $data = User::where('email', $email)->first();
                $data = JWTAuth::fromUser($data);
            } else {
                if (!$password) {
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Contraseña vacia',
                        'code' => 5
                    );
                    return response()->json($response, 400);
                } else if (strlen($password) < 6) {
                    $response = array(
                        'status' => 'fail',
                        'msj' => 'Contraseña debe tener minimo 8 caracteres',
                        'code' => 5
                    );
                    return response()->json($response);
                }
                $credentials = $request->only('email', 'password');
                $data = JWTAuth::attempt($credentials);
            }

            $codeResponse = ($request->header('origin-client') === 'ext-api-client') ? 200 : 401;
            try {
                if (!$data) {
                    return response()->json(['error' => 'invalid_credentials'], $codeResponse);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], $codeResponse);
            }

//            $hashNew = new Hashids('_ref', 10);
            $data = User::where('email', $email)->first();
            $data->setAttribute('token', JWTAuth::fromUser($data));
            $data->setAttribute('exp', $exp);
//            $data->setAttribute('refcode', $hashNew->encode($data->id));

            $response = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);


        } catch (\Exception $e) {
            $response = array(
                'status' => 'fail',
                'code' => 5,
                'error' => $e->getMessage()
            );
            return response()->json($response);
        }
    }

    public function password($email)
    {
        try {
            $user = User::where('email', $email)->first();

            if ($user) {
                $token = Str::random(40);
                DB::table('password_resets')->where('email', $email)->delete();
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
            $response = [
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            ];
            return response()->json($response, 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $data = DB::table('password_resets')
                ->select('email')
                ->where('token', $request->token)
                ->first();

            if ($data == null) {
                $response = [
                    'status' => 'fail',
                    'msg' => 'Token no existe o ya fue utilizado',
                    'code' => 1
                ];
                return response()->json($response, 401);
            }

            $user = User::where('email', $data->email)->first();
            $user->password = bcrypt($request->password);
            $user->save();
            DB::table('password_resets')->where('email', $data->email)->delete();

            $response = [
                'status' => 'success',
                'msg' => 'Password actualizado',
                'code' => 0
            ];
            return response()->json($response, 200);

        } catch (Exception $e) {
            $response = [
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            ];
            return response()->json($response, 500);
        }
    }


}
