<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\categories;
use App\products;
use App\shop;
use Illuminate\Support\Facades\DB;

class _FrontSeller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

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
    public function show(Request $request, $id)
    {
        try {

            $limit = ($request->limit) ? $request->limit : 15;
            $filters = ($request->filters) ? explode("?", $request->filters) : [];
            $attributes_filter = ($request->attributes_filter) ? explode("?", $request->attributes_filter) : [];
            $data_attributes = [];

            $tmp_list = [];
            $sql = [
                '_sql',
                '_sql_category',
                '_sql_attr'
            ];

            foreach ($sql as $value => $key) {
                $sql[$key] = products::where('products.shop_id', $id)
                    ->join('shops', 'products.shop_id', '=', 'shops.id')
                    ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                    ->where(function ($query) use ($filters) {
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
                    ->orderBy('products.id', 'DESC')
                    ->orderBy('products.featured', 'ASC');

                if ($request->attributes_filter) {
                    $sql[$key] = $sql[$key]
                        ->join('product_attributes as att', 'products.id', '=', 'att.product_id')
                        ->where(function ($query) use ($attributes_filter) {
                            foreach ($attributes_filter as $value) {
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
                        });
                }
            }

            $data = $sql['_sql']
                ->select('products.*', 'product_categories.category_id as category', 'shops.name as shop_name',
                    'shops.address as shop_address',
                    'shops.slug as shop_slug')
                ->paginate($limit);

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
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->select('categories.name', 'categories.id')
                ->groupBy('categories.name', 'categories.id')
                ->get();

            $data_attributes['product_attr'] = $tmp_list;
            $data_attributes['categories'] = $categories;

            $data->map(function ($item, $key) use ($request) {

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

            if ($request->all_filters) {
                $response = array(
                    'status' => 'success',
                    'data' => [
                        'list' => $data,
                        'filter' => $data_attributes
                    ],
                    'code' => 0
                );
            } else {
                $response = array(
                    'status' => 'success',
                    'data' => $data,
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
