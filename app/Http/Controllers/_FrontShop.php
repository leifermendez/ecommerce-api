<?php

namespace App\Http\Controllers;

use App\shipping_pickup_address;
use App\User;
use Illuminate\Http\Request;
use App\shop;
use DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Artisan;

class _FrontShop extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $limit = ($request->limit) ? $request->limit : 15;
            $filters = ($request->filters) ? explode("?", $request->filters) : [];


            $data = shop::orderBy('shops.id', 'DESC')
                ->where('shops.status', 'available')
                ->select(
                    'shops.*',
                    DB::raw('(SELECT attacheds.small FROM attacheds 
                    WHERE attacheds.id = shops.image_cover limit 1) as image_cover_small'),
                    DB::raw('(SELECT attacheds.small FROM attacheds 
                    WHERE attacheds.id = shops.image_header limit 1) as image_header_small'),
                    DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = shops.image_cover limit 1) as image_cover_medium'),
                    DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = shops.image_header limit 1) as image_header_medium'),
                    DB::raw('(SELECT attacheds.large FROM attacheds 
                    WHERE attacheds.id = shops.image_cover limit 1) as image_cover_large'),
                    DB::raw('(SELECT attacheds.large FROM attacheds 
                    WHERE attacheds.id = shops.image_header limit 1) as image_header_large')
                )
                ->where(function ($query) use ($filters, $request) {
                    if (!$request->outside) {
                        $km = (new UseInternalController)->_getSetting('search_range_km');
                        $measureShop = (new UseInternalController)->_measureShop(
                            $request->header('LAT'),
                            $request->header('LNG'),
                            $km,
                            '<',
                            'distance_in_km,shop_id');

                        $measureShop = array_column($measureShop, 'shop_id');
                        $query->whereIn('shops.id',$measureShop);
                    }
                    foreach ($filters as $value) {
                        $tmp = explode(",", $value);
                        if (isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])) {
                            $subTmp = explode("|", $tmp[2]);
                            if (count($subTmp) > 1) {
                                foreach ($subTmp as $k) {
                                    $query->orWhere($tmp[0], $tmp[1], $k);
                                }
                            } else {
                                $query->where($tmp[0], $tmp[1], $tmp[2]);
                            }
                        }
                    }
                })
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
            DB::beginTransaction();
            $user = JWTAuth::parseToken()->authenticate();
            $request->remove('_location'); $request->remove('_lat'); $request->remove('_lng');
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'id' && $key !== 'users_id') {
                    $fields[$key] = $value;
                };
            }
            $fields['users_id'] = $user->id;
            User::where('id', $user->id)
                ->update(['role' => 'shop']);
            $id = Shop::insertGetId($fields);
            $data = Shop::find($id);
            shipping_pickup_address::insert([
                'shop_id' => $id,
                'country' => '',
                'state' => '',
                'district' => '',
                'address' => '',
                'zip_code' => '',
                'instructions' => ''
            ]);

            Artisan::call("modelCache:clear", ['--model' => 'App\shop']);
            DB::commit();
            $response = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
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

        try {
            $_isLogged = (new UseInternalController)->_isLogged();
            $isMy = false;
            if ($_isLogged) {
                $isMy = (new UseInternalController)->_isMyShop($id);
            }

            $select = ['name',
                'address', 'slug', 'legal_id', 'image_cover', 'image_header', 'meta_key', 'terms_conditions',
                DB::raw('(SELECT attacheds.small FROM attacheds 
                    WHERE attacheds.id = shops.image_cover limit 1) as image_cover_small'),
                DB::raw('(SELECT attacheds.small FROM attacheds 
                    WHERE attacheds.id = shops.image_header limit 1) as image_header_small'),
                DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = shops.image_cover limit 1) as image_cover_medium'),
                DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = shops.image_header limit 1) as image_header_medium'),
                DB::raw('(SELECT attacheds.large FROM attacheds 
                    WHERE attacheds.id = shops.image_cover limit 1) as image_cover_large'),
                DB::raw('(SELECT attacheds.large FROM attacheds 
                    WHERE attacheds.id = shops.image_header limit 1) as image_header_large')];

            if ($isMy) {
                $select[] = 'email_corporate';
                $select[] = 'phone_mobil';
                $select[] = 'phone_fixed';
                $select[] = 'zip_code';
                $data = Shop::where('shops.id', $id)
                    ->select($select)
                    ->first();
            } else {
                $data = Shop::where('shops.id', $id)
                    ->select($select)
                    ->first();
            }

            $data = $data->setAttribute('prevent_check', [
                'bank' => (new UseInternalController)->_checkBank($id, false),
                'schedule' => (new UseInternalController)->_checkSchedule($id, false),
            ]);

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
            DB::beginTransaction();
            $user = JWTAuth::parseToken()->authenticate();
            $request->remove('_location'); $request->remove('_lat'); $request->remove('_lng');
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'id' && $key !== 'users_id') {
                    $fields[$key] = $value;
                };
            }
            $fields['users_id'] = $user->id;
            Shop::where('id', $id)
                ->where('users_id', $user->id)
                ->update($fields);
            User::where('id', $user->id)
                ->update(['role' => 'shop']);

            $data = Shop::find($id);
            Artisan::call("modelCache:clear", ['--model' => 'App\shop']);
            DB::commit();
            $response = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
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
