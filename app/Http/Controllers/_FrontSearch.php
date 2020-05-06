<?php

namespace App\Http\Controllers;

use App\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use DB;

class _FrontSearch extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $limit = ($request->limit) ? $request->limit : 15;
            $filters = ($request->filters) ? explode("?", $request->filters) : [];
            $attributes_filter = ($request->attributes_filter) ? explode("?", $request->attributes_filter) : [];
            $location = $request->_location;
            $measureShop = [];
            if ($request->_range_closed) {
                $km = (new UseInternalController)->_getSetting('search_range_km');
                $measureShop = (new UseInternalController)->_measureShop(
                    $request->_lat,
                    $request->_lng,
                    $km,
                    '<',
                    'distance_in_km,shop_id');
            }
            $measureShop = array_column($measureShop, 'shop_id');

            $src = $request->src;
            $data_attributes = [];
            $tmp_list = [];
            $sql = [
                '_sql',
                '_sql_category',
                '_sql_attr'
            ];
            DB::statement('SET SESSION group_concat_max_len = 2000000');
            foreach ($sql as $value => $key) {
                $sql[$key] = products::where('products.status', 'available')
                    ->join('shops', 'products.shop_id', '=', 'shops.id')
                    ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                    ->join('hours', 'shops.id', '=', 'hours.shop_id')
                    ->where(function ($query) use ($filters, $request, $src, $measureShop) {
                        if($request->_range_closed){
                            $query->whereIn('shops.id', $measureShop);
                        };
                        if($request->input('_check_session_label')
                        && ($request->input('_check_session_label_exists') === 'true')){
                            $_label =$request->input('_check_session_label');
                            $decrypted = Crypt::decryptString($_label);
                            $decrypted = str_replace(",", "%' OR products.label LIKE '%", $decrypted);
                            $query
                            ->whereRaw("(products.label LIKE '%$decrypted%')");

                        }else{
                            $query->where('products.name', 'LIKE', "%{$src}%");
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
                    });


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
                        })->orderBy('products.id', 'DESC');
                }
            };

            $data_products = $sql['_sql']
                ->select('products.*', 'product_categories.category_id as category', 'shops.name as shop_name',
                    'shops.address as shop_address',
                    'hours.shedule_hours as hours_shedule_hours',
                    'hours.exceptions as hours_exceptions',
                    'shops.slug as shop_slug',
                    'shops.name as shop_name',
                    'shops.id as shop_id',
                    DB::raw('(
                    SELECT SUM(score) AS TotalItemsOrdered FROM comments WHERE shop_id = shops.id
                    ) as score_shop'),
                    DB::raw('(
                    SELECT COUNT(*) AS TotalItemsOrdered FROM comments WHERE shop_id = shops.id) as score_count'),
                    DB::raw('(
                        SELECT attacheds.medium
                        FROM product_attacheds
                        INNER JOIN attacheds ON attacheds.id = product_attacheds.attached_id
                        WHERE product_id = products.id ORDER BY product_attacheds.id ASC LIMIT 1
                    ) as product_image'
                    ),
                    DB::raw("(
                      SELECT
                        group_concat(
                          JSON_OBJECT(
                            'price_normal', price_normal,
                            'price_regular', price_regular,
                            'quantity', quantity,
                            'label', label,
                            'id', id,
                            'attached_id', attached_id,
                            'observation', observation,
                            'feed_percentage', (SELECT value FROM settings WHERE meta = 'feed_percentage' LIMIT 1),
                            'feed_amount', (SELECT value FROM settings WHERE meta = 'feed_amount' LIMIT 1),
                            'feed_base', (SELECT value FROM settings WHERE meta = 'feed_limit_price' LIMIT 1),
                            'delivery', delivery,
                            'status', status
                          ) SEPARATOR '|'
                        ) as _variations
                         FROM variation_products WHERE product_id = products.id
                       ) as variations"),
                    DB::raw("(
                     SELECT
                        group_concat(
                          JSON_OBJECT(
                            'medium', attacheds.medium,
                            'attached_id', attached_id
                          ) SEPARATOR '|'
                        ) as gallery
                        FROM product_attacheds
                        INNER JOIN attacheds ON
                        attacheds.id = product_attacheds.attached_id
                        WHERE product_id = products.id  AND  variation_product_id is null) as gallery")
                );

            $data_products = ($request->order != 'rand') ?
            $data_products->orderBy('products.id', 'DESC') :
            $data_products->inRandomOrder();

            $data_products = (!$request->pagination) ?
                $data_products->take($limit)->get() : $data_products->paginate($limit);


            $data_products->map(function ($item, $key) use ($request) {
                $isAvailable = (new UseInternalController)
                    ->_v2_isAvailableProduct($item->hours_shedule_hours, $item->hours_exceptions);
                $item->is_available = $isAvailable;
                $item->variations = (new UseInternalController)
                    ->_parseVariation($item->variations);
                $item->gallery = (new UseInternalController)
                    ->_parseVariation($item->gallery);
                return $item;
            });

            $product_attr = $sql['_sql_attr']
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

            $categories = $sql['_sql_category']
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->select('categories.name', 'categories.id')
                ->groupBy('categories.name', 'categories.id')
                ->get();

            $data_attributes['product_attr'] = $tmp_list;
            $data_attributes['categories'] = $categories;

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
                        'filter' => $data_attributes,
//                        'shops' => $data_shops
                    ],
                    'code' => 0
                );
            } else {
                $response = array(
                    'status' => 'success',
                    'data' => [
                        'products' => $data_products,
//                        'shops' => $data_shops
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
