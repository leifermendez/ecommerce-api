<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class _FrontAuth extends Controller
{
    function validTokenProvider($provider = 'facebook', $token = 'invalid')
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




}
