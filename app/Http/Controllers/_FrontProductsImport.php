<?php

namespace App\Http\Controllers;

use App\Imports\VariationProductImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class _FrontProductsImport extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        try {
            if ($request->file('file')) {
                $file = $request->file('file');

                Excel::import(new VariationProductImport, $file);

                $status = array(
                    'status' => 'success',
                    'data' => 'Productos Creados',
                    'code' => 0
                );
                return response()->json($status);
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errors = [];
            $failures = $e->failures();
            foreach ($failures as $failure) {
                array_push($errors, ['liena' => $failure->row(), 'campo' => $failure->attribute(), 'error' => $failure->errors()]);
            }
            return response()->json($errors, 500);
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
