<?php

namespace App\Http\Controllers;

use App\products;
use App\shop;
use Illuminate\Http\Request;
use App\variation_product;
use App\product_attributes;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Artisan;
use DB;

class _FrontProductVariations extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $filters = ($request->filters) ? explode("?", $request->filters) : [];
            $limit = ($request->limit) ? $request->limit : 15;
            DB::statement('SET SESSION group_concat_max_len = 2000000');
            $data = variation_product::orderBy('variation_products.id', 'DESC')
                ->join('product_categories', 'variation_products.product_id', '=', 'product_categories.product_id')
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
                ->select('variation_products.*',
                    'product_categories.category_id as category_id',
                    DB::raw("(SELECT group_concat(
                      JSON_OBJECT(
                        'id', id,
                        'product_id', variation_products.product_id,
                        'attributes_id', attributes_id,
                        'value', value
                      ) SEPARATOR '|'
                    )
                    as _name FROM product_attributes WHERE variation_products_id = variation_products.id
                    AND variation_products.product_id = variation_products.product_id
                    GROUP BY variation_products_id) as attributes_values")
                )
                ->paginate($limit);

            $data->map(function ($i, $k) {
                $i->attributes_values = explode('|',$i->attributes_values);
                return $i;
            });

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

        $fields = array();
        foreach ($request->all() as $key => $value) {
            if ($key !== 'attributes_values_tmp'
                && $key !== 'attributes_values'
                && $key !== 'categories_id' && $key !== 'category_id') {
                $fields[$key] = $value;
            };
        }
        try {
            DB::beginTransaction();
            $isMy = (new UseInternalController)->_isMyProduct($fields['product_id']);
            if (!$isMy) {
                throw new \Exception('not permissions');
            }
            $data = variation_product::insertGetId($fields);
            if (count($request->attributes_values_tmp) > 0) {
                $tmp_attr = [];
                foreach ($request->attributes_values_tmp as $key => $value) {
                    $k = explode("_", $key);
                    $tmp_attr[] = [
                        'product_id' => $fields['product_id'],
                        'attributes_id' => $k[1],
                        'variation_products_id' => $data,
                        'value' => $value,
                    ];
                };
                product_attributes::where('product_id', $fields['product_id'])
                    ->where('variation_products_id', $data)
                    ->delete();
                product_attributes::insert($tmp_attr);
            }


//            $data = variation_product::find($data);
            $data = variation_product::where('id', $data)
                ->select('variation_products.*',
                    DB::raw('(SELECT attacheds.small FROM attacheds
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_large'),
                    DB::raw('(SELECT attacheds.small FROM attacheds
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_medium'),
                    DB::raw('(SELECT attacheds.medium FROM attacheds
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_small')
                )
                ->first();

            Artisan::call("modelCache:clear", ['--model' => 'App\products']);
            DB::commit();
            $response = array(
                'status' => 'success',
                'msg' => 'Insertado',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
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
    public function show($id)
    {
        try {

            $data = variation_product::find($id);
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
            $fields = array();
            foreach ($request->all() as $key => $value) {
                if ($key !== 'id' && $key !== 'attributes_values_tmp'
                    && $key !== 'attributes_values'
                    && $key !== 'categories_id' && $key !== 'category_id') {
                    $fields[$key] = $value;
                };
            }

            $isMy = (new UseInternalController)->_isMyProduct($fields['product_id']);
            if (!$isMy) {
                throw new \Exception('not permissions');
            }
            if (isset($request->attributes_values_tmp) && count($request->attributes_values_tmp) > 0) {
                $tmp_attr = [];
                foreach ($request->attributes_values_tmp as $key => $value) {
                    $k = explode("_", $key);
                    $tmp_attr[] = [
                        'product_id' => $fields['product_id'],
                        'attributes_id' => $k[1],
                        'variation_products_id' => $id,
                        'value' => $value,
                    ];
                };
                product_attributes::where('product_id', $fields['product_id'])
                    ->where('variation_products_id', $id)
                    ->delete();
                product_attributes::insert($tmp_attr);
            }
            variation_product::where('id', $id)
                ->update($fields);

            $data = variation_product::where('id', $id)
                ->select('variation_products.*',
                    DB::raw('(SELECT attacheds.small FROM attacheds
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_large'),
                    DB::raw('(SELECT attacheds.small FROM attacheds
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_medium'),
                    DB::raw('(SELECT attacheds.medium FROM attacheds
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_small')
                )
                ->first();


            Artisan::call("modelCache:clear", ['--model' => 'App\products']);
            DB::commit();
            $response = array(
                'status' => 'success',
                'msg' => 'Actualizado',
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
        try {

            variation_product::where('id', $id)
                ->delete();
            Artisan::call("modelCache:clear", ['--model' => 'App\products']);
            $response = array(
                'status' => 'success',
                'msg' => 'Eliminado',
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
}
