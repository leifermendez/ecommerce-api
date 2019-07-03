<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\categories;
use App\products;
use App\shop;
use DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class _FrontProducts extends Controller
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
            $location = $request->_location;
            $categories = [];
            $attributes = [];

            $data = products::orderBy('products.id', 'DESC')
                ->join('shops', 'products.shop_id', '=', 'shops.id')
                ->where('shops.zip_code', $location)
                ->where(function ($query) use ($filters) {
                    foreach ($filters as $value) {
                        $tmp = explode(",", $value);
                        if (isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])) {
                            $subTmp = explode("|", $tmp[2]);
                            if (count($subTmp)) {
                                foreach ($subTmp as $k) {
                                    $query->orWhere($tmp[0], $tmp[1], $k);
                                }
                            } else {
                                $query->where($tmp[0], $tmp[1], $tmp[2]);
                            }
                        }
                    }
                })
                ->select('products.*', 'shops.name as shop_name', 'shops.address as shop_address',
                    'shops.slug as shop_slug')
                ->paginate($limit);

            $data->map(function ($item, $key) use ($request) {

                $getVariations = (new UseInternalController)->_getVariations($item->id, 'ASC', 2);
                $isAvailable = (new UseInternalController)->_isAvailableProduct($item->id);
                $gallery = (new UseInternalController)->_getImages($item->id);
                $scoreShop = (new UseInternalController)->_getScoreShop($item->shop_id);
                $item->is_available = $isAvailable;
                $item->variations = $getVariations;
                $item->gallery = $gallery;
                $item->score_shop = $scoreShop;
                return $item;
            });

//            $categories = products::orderBy('products.id', 'DESC')
//                ->join('shops', 'products.shop_id', '=', 'shops.id')
//                ->where('shops.zip_code', $location)
//                ->where(function ($query) use ($filters) {
//                    foreach ($filters as $value) {
//                        $tmp = explode(",", $value);
//                        if (isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])) {
//                            $subTmp = explode("|", $tmp[2]);
//                            if (count($subTmp)) {
//                                foreach ($subTmp as $k) {
//                                    $query->orWhere($tmp[0], $tmp[1], $k);
//                                }
//                            } else {
//                                $query->where($tmp[0], $tmp[1], $tmp[2]);
//                            }
//                        }
//                    }
//                })
//                ->select('categories.id', 'categories.name',
//                    DB::raw('count(categories.id) as number'))
//                ->groupBy('categories.id')
//                ->get();

//            $attributes = products::orderBy('products.id', 'DESC')
//                ->join('shops', 'products.shop_id', '=', 'shops.id')
//                ->join('categories', 'products.category_id', '=', 'categories.id')
//                ->join('category_attributes', 'categories.id', '=', 'category_attributes.category_id')
//                ->join('attributes', 'attributes.id', '=', 'category_attributes.attributes_id')
//                ->where('shops.zip_code', $location)
//                ->where(function ($query) use ($filters) {
//                    foreach ($filters as $value) {
//                        $tmp = explode(",", $value);
//                        if (isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])) {
//                            $subTmp = explode("|", $tmp[2]);
//                            if (count($subTmp)) {
//                                foreach ($subTmp as $k) {
//                                    $query->orWhere($tmp[0], $tmp[1], $k);
//                                }
//                            } else {
//                                $query->where($tmp[0], $tmp[1], $tmp[2]);
//                            }
//                        }
//                    }
//                })
//                ->select('attributes.id', 'attributes.name',
//                    DB::raw('count(attributes.id) as number'))
//                ->groupBy('attributes.id')
//                ->get();


            $response = array(
                'status' => 'success',
                'data' => [
                    'items' => $data,
//                    'categories' => $categories,
//                    'attributes' => $attributes
                ],
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
        $fields = array();
        foreach ($request->all() as $key => $value) {
            $fields[$key] = $value;
        }
        try {

            $data = products::insertGetId($fields)
                ->disableCache();
            $data = products::find($data);

            $response = array(
                'status' => 'success',
                'msg' => 'Insertado',
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

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $location = $request->_location;

            $data = products::join('shops', 'products.shop_id', '=', 'shops.id')
                ->where('shops.zip_code', $location)
                ->where('products.id', $id)
                ->where(function ($query) use ($request) {
                    if ($request->featured) {
                        $query->where('products.featured', $request->featured);
                    }
                })
                ->select('products.*', 'shops.name as shop_name', 'shops.address as shop_address',
                    'shops.slug as shop_slug',
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
                    WHERE attacheds.id = shops.image_header limit 1) as image_header_large'))
                ->first();

            if ($data) {
                $isAvailable = (new UseInternalController)->_isAvailableProduct($id);
                $getVariations = (new UseInternalController)->_getVariations($id);
                $getCoverImageProduct = (new UseInternalController)->_getCoverImageProduct($id);
                $gallery = (new UseInternalController)->_getImages($id);
                $data->setAttribute('is_available', $isAvailable);
                $data->setAttribute('variations', $getVariations);
                $data->setAttribute('cover_image', $getCoverImageProduct);
                $data->setAttribute('gallery', $gallery);
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
            $fields = array();
            foreach ($request->all() as $key => $value) {
                $fields[$key] = $value;
            }

            $isMy = (new UseInternalController)->_isMyProduct($id);;

            if (!$isMy) {
                throw new \Exception('not permissions');
            }

            products::where('id', $id)
                ->disableCache()
                ->update($fields);
            $data = products::find($id);

            $response = array(
                'status' => 'success',
                'msg' => 'Editado',
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


    }
}
