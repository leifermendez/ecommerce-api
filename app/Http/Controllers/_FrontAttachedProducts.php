<?php

namespace App\Http\Controllers;

use App\product_attached;
use App\products;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Artisan;

class _FrontAttachedProducts extends Controller
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

            $data = product_attached::orderBy('id', 'DESC')
                ->where(function ($query) use ($filters) {
                    foreach ($filters as $value) {
                        $tmp = explode(",", $value);
                        if (isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])) {
                            $subTmp = explode("|", $tmp[2]);
                            if (count($subTmp)>1) {
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
            $request->validate([
                'attached_id' => 'required',
                'product_id' => 'required'
            ]);

            $fields = array();
            foreach ($request->all() as $key => $value) {
                $fields[$key] = $value;
            }

            $isMy = (new UseInternalController)->_isMyProduct($fields['product_id']);

            if (!$isMy) {
                throw new \Exception('not permissions');
            }

            $data = product_attached::insertGetId($fields);
            $data = product_attached::find($data);
            Artisan::call("modelCache:clear", ['--model' => 'App\products']);

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
            $request->validate([
                'attached_id' => 'required',
                'product_id' => 'required'
            ]);

            $fields = array();
            foreach ($request->all() as $key => $value) {
                $fields[$key] = $value;
            }
            $isMy = (new UseInternalController)->_isMyProduct($fields['product_id']);

            if (!$isMy) {
                throw new \Exception('not permissions');
            }

            $data = product_attached::where('id', $id)
                ->update($fields);
            $data = product_attached::find($data);
            Artisan::call("modelCache:clear", ['--model' => 'App\products']);
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

        try {

            $data = product_attached::find($id);
            $isMy = (new UseInternalController)->_isMyProduct($data->product_id);

            if (!$isMy) {
                throw new \Exception('not permissions');
            }

            $data = product_attached::where('id', $id)
                ->delete();

            $response = array(
                'status' => 'success',
                'msg' => 'Eliminado',
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
}
