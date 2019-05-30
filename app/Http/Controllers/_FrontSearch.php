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

            $data_products = products::orderBy('products.id', 'DESC')
                ->join('shops', 'products.shop_id', '=', 'shops.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('shops.zip_code', $location)
                ->where('products.name', 'LIKE', "%{$src}%")
                ->select('products.*', 'shops.name as shop_name', 'shops.address as shop_address',
                    'shops.slug as shop_slug', 'categories.name as category_name')
                ->take($limit)
                ->get();

            $data_products->map(function ($item, $key) use ($request) {

                $getVariations = (new UseInternalController)->_getVariations($item->id);
                $isAvailable = (new UseInternalController)->_isAvailableProduct($item->id);
                $item->is_available = $isAvailable;
                $item->variations = $getVariations;
                return $item;
            });

            $data_shops = products::orderBy('products.id', 'DESC')
                ->join('shops', 'products.shop_id', '=', 'shops.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('shops.zip_code', $location)
                ->where('shops.name', 'LIKE', "%{$src}%")
                ->orWhere('shops.meta_key', 'LIKE', "%{$src}%")
                ->select('products.*', 'shops.name as shop_name', 'shops.address as shop_address',
                    'shops.slug as shop_slug', 'categories.name as category_name')
                ->take($limit)
                ->get();

            $data_shops->map(function ($item, $key) use ($request) {

                $getVariations = (new UseInternalController)->_getVariations($item->id);
                $isAvailable = (new UseInternalController)->_isAvailableProduct($item->id);
                $item->is_available = $isAvailable;
                $item->variations = $getVariations;
                return $item;
            });


            $response = array(
                'status' => 'success',
                'data' => [
                    'products' => $data_products,
                    'shops' => $data_shops
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
