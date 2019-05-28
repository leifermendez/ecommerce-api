<?php

namespace App\Http\Controllers;

use App\attributes_globals;
use Illuminate\Http\Request;
use App\category_attributes;
use App\attributes;


class AttributesController extends Controller
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

            $data = attributes::orderBy('id', 'DESC')
                ->paginate($limit);

            $data->map(function ($item, $key) use ($request) {
                $item->values = attributes_globals::where('attributes_id', $item->id)
                    ->get();
                return $item;
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
            if ($key !== 'values') {
                $fields[$key] = $value;
            };
        }
        try {

            $data = attributes::insertGetId($fields);
            foreach ($request->values as $value) {
                attributes_globals::insert([
                    'attributes_id' => $data,
                    'value' => $value
                ]);
            }
            $data = attributes::find($data);

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
        try {

            $data = attributes::find($id);
            if ($data) {
                $data->values = attributes_globals::where('attributes_id', $id)
                    ->get();
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
                if ($key !== 'id' && $key !== 'values') {
                    $fields[$key] = $value;
                };
            }

            attributes::where('id', $id)
                ->update($fields);

            foreach ($request->values as $key => $value) {
                $getActionID = (new UseInternalController)->actionID($key);

                switch ($getActionID['action']) {
                    case 'insert':
                        attributes_globals::insert([
                            'attributes_id' => $id,
                            'value' => $value
                        ]);
                        break;
                    case 'delete':
                        attributes_globals::where('id', $getActionID['id'])
                            ->where('attributes_id', $id)
                            ->delete();
                        break;
                    case 'update':

                        if (attributes_globals::where('id', $getActionID['id'])
                            ->where('attributes_id', $id)->exists()) {
                            attributes_globals::where('id', $getActionID['id'])
                                ->where('attributes_id', $id)
                                ->update([
                                    'value' => $value
                                ]);
                        }
                        break;
                }

            }

            $data = attributes::find($id);


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
            attributes_globals::where('attributes_id', $id)
                ->delete();

            attributes::where('id', $id)
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
