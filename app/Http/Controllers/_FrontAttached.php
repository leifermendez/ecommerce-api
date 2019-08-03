<?php

namespace App\Http\Controllers;

use App\attached;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;

define("_api_google_vision","https://vision.googleapis.com/v1");

class _FrontAttached extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function _getLabel($image = null)
    {
        try{
            
            $google_key = env('GOOGLE_API_VISION_KEY', '');
            $response = Curl::to(_api_google_vision."/images:annotate?key=".$google_key)
            ->withData([
                'requests' => [
                    'image' => [
                        'source' => [
                            'imageUri' => 'https://ae01.alicdn.com/kf/HTB1TbxmPFXXXXXSXVXXq6xXFXXX6/5XL-6XL-talla-grande-2018-Primavera-Verano-vestido-talla-grande-blanco-negro-rayas-Vestidos-talla-grande.jpg'
                        ]
                    ],
                    'features' => [
                        'type' => 'LABEL_DETECTION',
                        'maxResults' => 10
                    ]
                ]
            ])
            ->post();
            //1880805


            $response = json_decode($response);
            return $response;
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



        }catch (\Exception $e) {

            return false;

        }
    }

    public function _internalUpload($url = null)
    {


        $random = Str::random(40);
        $name_bulk = 'upload/profile_' . $random . '.png';

        $value = Image::make(file_get_contents($url))->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        })->stream()->__toString();
        Storage::disk()->put($name_bulk, $value, 'public');
//        Storage::disk('s3')->put($name_bulk, $value, 'public');
//        $public_url = Storage::disk('s3')->url($name_bulk);
        $public_url = Storage::url($name_bulk);

        return [
            'status' => 'success',
            'data' => $public_url
        ];

    }


    public function index(Request $request)
    {
        try {
            $user_current = JWTAuth::parseToken()->authenticate();
            $limit = ($request->limit) ? $request->limit : 15;

            $data = attached::orderBy('id', 'DESC')
                ->where('users_id', $user_current->id)
                ->paginate($limit);

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

            return response()->json($response, 500);

        }
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
            $user_current = JWTAuth::parseToken()->authenticate();
            $file = $request->file('attached');
            $type_file = $request->type_file;
            $format = 'jpg';
            $id = $request->id;
            $imageName = Str::random(35);
            $data = null;

            if ($type_file === 'video') {
                $file_validate = array('video' => $file);
                $rules = array(
                    'attached' => 'mimes:mp4,3gb,avi|max:100000'
                );
            } else {
                $file_validate = array('image' => $file);
                $rules = array(
                    'attached' => 'mimes:jpeg,bmp,png|max:20000'
                );
            }

            $validator = Validator::make($request->all(), $rules);
//
            if ($validator->fails()) {
                $status = array(
                    'status' => 'fail',
                    'msg' => 'Tipo de archivo no permitido'
                );
                return response()->json($status);
            }

            $responseSize = array();
            if ($type_file === 'video') {
                $name_bulk = 'public/upload/products/video_' . $imageName . '.' . $file->getClientOriginalExtension();
                Storage::disk()->put($name_bulk, $file);
                $a = Storage::disk()->files($name_bulk);
                if (!(count($a))) {
                    throw new \Exception('not files array');
                }

                $file_inside = explode('/', $a[0]);
                $file_inside = end($file_inside);
                $url_path = Storage::url($name_bulk);
                $url_path .= '/' . $file_inside;

                $data = attached::insertGetId(
                    [
                        'name' => $imageName . '.png',
                        'users_id' => $user_current->id,
                        'video_url' => $url_path,
                        'media_type' => $type_file
                    ]
                );

                $data = attached::find($data);
            } else {

                $sizes = array(
                    'small' => Image::make($file)
                        ->encode($format, 100)
                        ->resize(200, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    'medium' => Image::make($file)
                        ->encode($format, 100)
                        ->resize(600, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    'large' => Image::make($file)
                        ->encode($format, 100)
                        ->resize(1600, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    /*'original' => Image::make($file)->stream()->__toString()*/
                );

                foreach ($sizes as $key => $value) {
                    $name_bulk = 'public/upload/products/' . $key . '_' . $imageName . '.' . $format;
                    Storage::disk()->put($name_bulk, $value);
                    $responseSize[$key] = Storage::disk()->url($name_bulk);

                }

                if ($id) {
                    attached::where('id', $id)
                        ->update(
                            [
                                'name' => $imageName . '.png',
                                'users_id' => $user_current->id,
                                'large' => $responseSize['large'],
                                'medium' => $responseSize['medium'],
                                'small' => $responseSize['small'],
                                'media_type' => $type_file
                            ]
                        );

                    $data = attached::find($id);
                } else {
                    $data = attached::insertGetId(
                        [
                            'name' => $imageName . '.png',
                            'users_id' => $user_current->id,
                            'large' => $responseSize['large'],
                            'medium' => $responseSize['medium'],
                            'small' => $responseSize['small'],
                            'media_type' => $type_file
                        ]
                    );
                    $data = attached::find($data);
                }


            }

            $a=$this->_getLabel($data);
            dd($a);

            $status = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($status);


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
            $user_current = JWTAuth::parseToken()->authenticate();
            $file = $request->file('attached');
            $type_file = $request->type_file;
            $format = 'jpg';
            $imageName = Str::random(35);
            $data = null;

            if ($type_file === 'video') {
                $file_validate = array('video' => $file);
                $rules = array(
                    'attached' => 'mimes:mp4,3gb,avi|max:100000'
                );
            } else {
                $file_validate = array('image' => $file);
                $rules = array(
                    'attached' => 'mimes:jpeg,bmp,png|max:20000'
                );
            }

            $validator = Validator::make($request->all(), $rules);
//
            if ($validator->fails()) {
                $status = array(
                    'status' => 'fail',
                    'msg' => 'Tipo de archivo no permitido'
                );
                return response()->json($status);
            }

            $responseSize = array();
            if ($type_file === 'video') {
                $name_bulk = 'public/upload/products/video_' . $imageName . '.' . $file->getClientOriginalExtension();
                Storage::disk()->put($name_bulk, $file);
                $a = Storage::disk()->files($name_bulk);
                if (!(count($a))) {
                    throw new \Exception('not files array');
                }

                $file_inside = explode('/', $a[0]);
                $file_inside = end($file_inside);
                $url_path = Storage::url($name_bulk);
                $url_path .= '/' . $file_inside;

                $data = attached::insertGetId(
                    [
                        'name' => $imageName . '.png',
                        'users_id' => $user_current->id,
                        'video_url' => $url_path,
                        'media_type' => $type_file
                    ]
                );

                $data = attached::find($data);
            } else {

                $sizes = array(
                    'small' => Image::make($file)
                        ->encode($format, 100)
                        ->resize(200, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    'medium' => Image::make($file)
                        ->encode($format, 100)
                        ->resize(600, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    'large' => Image::make($file)
                        ->encode($format, 100)
                        ->resize(1600, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    /*'original' => Image::make($file)->stream()->__toString()*/
                );

                foreach ($sizes as $key => $value) {
                    $name_bulk = 'public/upload/products/' . $key . '_' . $imageName . '.' . $format;
                    Storage::disk()->put($name_bulk, $value);
                    $responseSize[$key] = Storage::disk()->url($name_bulk);

                }

                attached::where('id', $id)
                    ->update(
                        [
                            'name' => $imageName . '.png',
                            'users_id' => $user_current->id,
                            'large' => $responseSize['large'],
                            'medium' => $responseSize['medium'],
                            'small' => $responseSize['small'],
                            'media_type' => $type_file
                        ]
                    );

                $data = attached::find($id);
            }


            $status = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($status);


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
        try {
            $user_current = JWTAuth::parseToken()->authenticate();
            attached::where('id', $id)
                ->where('users_id', $user_current->id)
                ->delete();

            $status = array(
                'status' => 'success',
                'code' => 0
            );
            return response()->json($status);


        } catch (\Exception $e) {
            $response = array(
                'status' => 'fail',
                'msg' => $e->getMessage(),
                'code' => 1
            );

            return response()->json($response, 500);

        }
    }
}
