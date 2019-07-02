<?php

namespace App\Http\Controllers;

use App\attached;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;

class _FrontAttached extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
                $url_path = Storage::url($name_bulk);

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
                        ->encode($format,100)
                        ->resize(200, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    'medium' => Image::make($file)
                        ->encode($format,100)
                        ->resize(600, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    'large' => Image::make($file)
                        ->encode($format,100)
                        ->resize(1600, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream()->__toString(),
                    /*'original' => Image::make($file)->stream()->__toString()*/
                );

                foreach ($sizes as $key => $value) {
                    $name_bulk = 'public/upload/products/' . $key . '_' . $imageName . '.'.$format;
                    Storage::disk()->put($name_bulk, $value);
                    $responseSize[$key] = Storage::disk()->url($name_bulk);

                }

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

            $imageName = Str::random(35);
            $data = null;

            if ($type_file === 'video') {

                $rules = array(
                    'attached' => 'mimes:mp4,3gb,avi|max:100000'
                );
            } else {

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
                $url_path = Storage::url($name_bulk);

                $data = attached::where('id', $id)
                    ->where('users_id', $user_current->id)
                    ->update(
                        [
                            'name' => $imageName . '.png',
                            'video_url' => $url_path,
                            'media_type' => $type_file
                        ]
                    );

                $data = attached::find($data);
            } else {

                $sizes = array(
                    'small' => Image::make($file)->resize(200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream()->__toString(),
                    'medium' => Image::make($file)->resize(600, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream()->__toString(),
                    'large' => Image::make($file)->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream()->__toString(),
                    /*'original' => Image::make($file)->stream()->__toString()*/
                );


                foreach ($sizes as $key => $value) {
                    $name_bulk = 'public/upload/products/' . $key . '_' . $imageName . '.png';
                    Storage::disk()->put($name_bulk, $value);
                    $responseSize[$key] = Storage::url($name_bulk);

                }

                $data = attached::where('id', $id)
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
                $data = attached::find($data);
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
        //
    }
}
