<?php

namespace App\Http\Controllers;

use App\products;
use Illuminate\Http\Request;
use DB;

class _FrontSearch extends Controller
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
            $location = $request->_location;
            $src = $request->src;
            $filters = ($request->filters) ? explode("?", $request->filters) : [];
            $attributes = [];
            $tmp_list = [];
            $sql = [
                '_sql',
                '_sql_category',
                '_sql_attr'
            ];
            foreach ($sql as $value => $key) {
                $sql[$key] = products::orderBy('products.id', 'DESC')
                    ->join('shops', 'products.shop_id', '=', 'shops.id')
                    ->where('shops.zip_code', $location)
                    ->where('products.status', 'available')
                    ->where('products.name', 'LIKE', "%{$src}%")
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
                    });
            };

            $data_products = $sql['_sql']
                ->select('products.*', 'shops.name as shop_name', 'shops.address as shop_address',
                    'shops.slug as shop_slug');
            $data_products = (!$request->pagination) ?
                $data_products->take($limit)->get() : $data_products->paginate($limit);

            $data_products->map(function ($item, $key) use ($request) {
                $isAvailable = (new UseInternalController)->_isAvailableProduct($item->id);
                $getVariations = (new UseInternalController)->_getVariations($item->id);
                $getCoverImageProduct = (new UseInternalController)->_getCoverImageProduct($item->id);
                $gallery = (new UseInternalController)->_getImages($item->id);
                $scoreShop = (new UseInternalController)->_getScoreShop($item->shop_id);
                $item->gallery = $gallery;
                $item->is_available = $isAvailable;
                $item->variations = $getVariations;
                $item->cover_image = $getCoverImageProduct;
                $item->score_shop = $scoreShop;
                return $item;
            });

            $product_attr = $sql['_sql_attr']->disableCache()
                ->join('product_attributes', 'products.id', '=', 'product_attributes.product_id')
                ->join('attributes', 'product_attributes.attributes_id', '=', 'attributes.id')
                ->select('attributes.id as attr_id', 'attributes.name', 'product_attributes.value')
                ->get()
                ->toArray();

            foreach ($product_attr as $key => $value) {
                $tmp_list[$value['name']][] = $value;
                $array = array_map('json_encode', $tmp_list[$value['name']]);
                $array = array_unique($array);
                $array = array_map('json_decode', $array);
                $tmp_list[$value['name']] = $array;
            }

            $categories = $sql['_sql_category']->disableCache()
                ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->select('categories.name', 'categories.id')
                ->groupBy('categories.name', 'categories.id')
                ->get();

            $attributes['product_attr'] = $tmp_list;
            $attributes['categories'] = $categories;

            $data_shops = products::orderBy('products.id', 'DESC')
                ->join('shops', 'products.shop_id', '=', 'shops.id')
                ->where('shops.zip_code', $location)
                ->where('shops.name', 'LIKE', "%{$src}%")
                ->orWhere('shops.meta_key', 'LIKE', "%{$src}%")
                ->select('products.*', 'shops.name as shop_name', 'shops.address as shop_address',
                    'shops.slug as shop_slug')
                ->take($limit)
                ->get();

            $data_shops->map(function ($item, $key) use ($request) {

                $getVariations = (new UseInternalController)->_getVariations($item->id);
                $isAvailable = (new UseInternalController)->_isAvailableProduct($item->id);
                $item->is_available = $isAvailable;
                $item->variations = $getVariations;
                return $item;
            });


            if ($request->all_filters) {
                $response = array(
                    'status' => 'success',
                    'data' => [
                        'list' => $data_products,
                        'filter' => $attributes,
                        'shops' => $data_shops
                    ],
                    'code' => 0
                );
            } else {
                $response = array(
                    'status' => 'success',
                    'data' => [
                        'products' => $data_products,
                        'shops' => $data_shops
                    ],
                    'code' => 0
                );

            }

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
        //
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
    public function destroy($id)
    {
        //
    }
}
