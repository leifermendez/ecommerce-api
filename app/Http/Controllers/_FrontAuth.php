<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Hashids\Hashids;

define("FACEBOOK_ID", "493119484360306");

class _FrontAuth extends Controller
{
    public function validTokenProvider($provider = 'facebook', $token = 'invalid')
    {
        $urlFacebook = "https://graph.facebook.com/oauth/access_token_info?client_id=" . FACEBOOK_ID . "&access_token=$token";
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
            'avatar' => ($data['avatar']) ? $data['avatar'] : 'https://s3.us-east-2.amazonaws.com/media-mochileros/upload/square_mochilero.png',
        ];

        User::create($fields);

        $dataUser = User::where('email', $data['email'])->first();
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
            return response()->json($response);
        }
        $random = Str::random(10);
        $fields = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => ($data['password']) ? $data['password'] : $random,
            'username' => $data['email'],
            'avatar' => $data['avatar']
        ];

        $data = $this->registerNewUser($fields);

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
            'data' => $token,
            'code' => 0
        );

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required'
            ]);

            $email = $request->email;
            $provider = $request->provider;
            $token = $request->token;
            $avatar = $request->avatar;
            $password = $request->password;

            if (!User::where('email', $request->email)->exists()) {
                $response = $this->create(array(
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password,
                    'avatar' => $request->avatar
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
                    return response()->json($response);
                }

                if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                    $new_avatar = (new _mediaController)->_internalUpload($avatar);

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
                    return response()->json($response);
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


}
