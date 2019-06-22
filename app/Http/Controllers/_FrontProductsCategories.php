<?php

namespace App\Http\Controllers;

use App\banners;
use App\product_categories;
use App\products;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class _FrontProductsCategories extends Controller
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

            $data = product_categories::orderBy('id', 'DESC')
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
        $user = JWTAuth::parseToken()->authenticate();
        $fields = array();
        foreach ($request->all() as $key => $value) {
            $fields[$key] = $value;
        }
        try {
            $data = array();
            $isMy = products::where('products.id', $fields['product_id'])
                ->where('shops.users_id', $user->id)
                ->join('shops', 'products.shop_id', '=', 'shops.id')
                ->exists();
            if (!$isMy) {
                throw new \Exception('not permission for shop ');
            }

            if (gettype($fields['category_id']) === 'array') {
                foreach ($fields['category_id'] as $a) {
                    $b['product_id'] = $fields['product_id'];
                    $b['category_id'] = $a;
                    $data[]=product_categories::insertGetId($b);
                }
            } else {
                $data = product_categories::insertGetId($fields);
                $data = product_categories::find($data);
            }


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

            $data = product_categories::find($id);
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
            $user = JWTAuth::parseToken()->authenticate();
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'id') {
                    $fields[$key] = $value;
                };
            }

            $isMy = products::where('products.id', $fields['product_id'])
                ->where('shops.shops', $user->id)
                ->join('shops', 'products.shop_id', '=', 'shops.id')
                ->exists();
            if (!$isMy) {
                throw new \Exception('not permission for shop ');
            }

            product_categories::where('id', $id)
                ->update($fields);

            $data = product_categories::find($id);


            $response = array(
                'status' => 'success',
                'msg' => 'Actualizado',
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


    }
}
