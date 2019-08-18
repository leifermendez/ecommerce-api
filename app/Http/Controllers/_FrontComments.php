<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\comments;
use App\purchase_detail;
use App\purchase_order;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class _FrontComments extends Controller
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

            $data = comments::orderBy('comments.id', 'DESC')
            ->where(function ($query) use ($filters) {
                foreach ($filters as $value) {
                    $tmp = explode(",", $value);
                    if(isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])){
                        $subTmp = explode("|",$tmp[2]);
                        if(count($subTmp)){
                           foreach ($subTmp as $k) {
                            $query->orWhere($tmp[0],$tmp[1],$k);
                           }
                        }else{
                            $query->where($tmp[0],$tmp[1],$tmp[2]);
                        }
                    }
                }
            })
            ->join('users','comments.user_id','=','users.id')
            ->join('attacheds','comments.attached_id','=','attacheds.id')
            ->select('comments.*','users.name as users_name','users.avatar as users_avatar',
            'attacheds.small as image_comment')
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = array();
        try {
            $user = JWTAuth::parseToken()->authenticate();   
            $validator = Validator::make($request->all(), [
                'product_id' => 'required',
                'purchase_uuid' => 'required',
                'score' => 'required',
                'comment' => 'required'
   
            ])->validate();

            $purchase_data = purchase_detail::where('purchase_details.purchase_uuid',
            $request->purchase_uuid)
            ->join('purchase_orders','purchase_details.purchase_uuid','=','purchase_orders.uuid')
            ->where('purchase_orders.user_id',$user->id)
            ->where('purchase_details.product_id',$request->product_id)
            ->select('purchase_details.*')
            ->first();

            if(!$purchase_data){
                throw new \Exception('purchase not found');
            };

            $comments_data = comments::where('purchase_id',$purchase_data->id)
            ->where('user_id',$user->id)
            ->first();


            if($comments_data){
                throw new \Exception('comment already');
            };


            $fields = [
                'product_id' => $request->product_id,
                'purchase_id' => $purchase_data->id,
                'score' => $request->score,
                'comment' => $request->comment,
                'user_id' => $user->id,
                'shop_id' => $purchase_data->shop_id
            ];

            $data = comments::insertGetId($fields);
            $data = comments::find($data);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $data = comments::where('comments.id',$id)
            ->join('users','comments.user_id','=','users.id')
            ->select('comments.*','users.name as users_name','users.avatar as users_avatar')
            ->first();

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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

            comments::where('id', $id)
                ->update($fields);

            $data = comments::find($id);


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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            comments::where('id', $id)
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
