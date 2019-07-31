<?php

namespace App\Http\Controllers;

use App\categories;
use App\category_attributes;
use Illuminate\Http\Request;

class _FrontAttributesCategories extends Controller
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

            $data = category_attributes::orderBy('category_attributes.id', 'DESC')
                ->join('categories', 'categories.id', '=', 'category_attributes.category_id')
                ->join('attributes', 'category_attributes.attributes_id', '=', 'attributes.id')
                ->select('category_attributes.*', 'categories.name as category_name',
                    'attributes.name as attributes_name', 'attributes.element_type as attributes_element_type')
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
        $fields = array();
        foreach ($request->all() as $key => $value) {
            $fields[$key] = $value;
        }
        try {

            $data = category_attributes::insertGetId($fields);
            $data = category_attributes::find($data);

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
            return response()->json($response, 400);
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

            $data = category_attributes::find($id);
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
                if ($key !== 'id') {
                    $fields[$key] = $value;
                };
            }

            category_attributes::where('id', $id)
                ->update($fields);

            $data = category_attributes::find($id);


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

        try {

            category_attributes::where('id', $id)
                ->delete();

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
