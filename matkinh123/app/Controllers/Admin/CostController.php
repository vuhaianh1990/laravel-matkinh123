<?php

namespace Matkinh123\Controllers\Admin;

use Illuminate\Http\Request;
use Matkinh123\Controllers\Controller;
use Validator;
use Matkinh123\Models\Cost;
use DB;

class CostController extends Controller
{

    /**
     *
     */
    public function index()
    {
        return admin_view('cost');
    }

    public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_start' => 'nullable|date_format:Y-m-d H:i:s',
            'date_end'   => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'msg'    => 'Validation Error'
            ]);
        }

        $date_start = strtotime($request->date_start);
        $date_end   = strtotime($request->date_end);
        if (($date_end - $date_start) < 0) {
            return response()->json([
                'status' => 401,
                'msg'    => 'Validation Error'
            ]);
        }

        $cost = Cost::select(['id', 'type', 'name', 'note', 'money', 'prepay', 'status', 'monthlyfee', 'created_at', 'updated_at'])
                    ->orderBy('created_at', 'desc');
        if ($request->has(['date_start', 'date_end'])) {
            $cost->whereBetween('created_at', [$request->date_start, $request->date_end]);
        }
        $cost = $cost->get();

        if ($cost->count() == 0) {
            return response()->json([
                'status' => 404,
                'msg'    => 'Not found'
            ]);
        }

        return response()->json([
            'status' => 200,
            'data'   => $cost
        ]);
    }

    public function delRow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|unique:costs,id,'. $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'msg'    => 'Validation Error',
                'valid'  => $validator
            ]);
        }

        $success = Cost::destroy($request->id);

        if ($success) {
            return response()->json([
                'status' => 200,
                'msg'    => 'Xoá dữ liệu thành công',
            ]);
        }

        return response()->json([
            'status' => 404,
            'msg'    => 'Not found'
        ]);
    }

    public function addCost(Request $request)
    {
        $validator = Validator::make($request->data, [
            'type'       => 'required|string',
            'name'       => 'required|string',
            'money'      => 'required|integer|min:1000',
            'prepay'     => 'required|integer|min:0',
            'status'     => 'required|integer',
            'created_at' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'msg'    => 'Validation Error',
                'valid'  => $validator->errors()
            ]);
        }

        $money_shortage = $request->money - $request->prepay;
        if ($money_shortage < 0) {
            return response()->json([
                'status' => 400,
                'msg'    => 'Validation Error',
            ]);
        }

        $success = Cost::create([
            'type'           => $request->data['type'],
            'name'           => $request->data['name'],
            'money'          => $request->data['money'],
            'prepay'         => $request->data['prepay'],
            'monthlyfee'     => $request->data['monthlyfee'],
            'note'           => $request->data['note'],
            'status'         => $request->data['status'],
            'created_at'     => $request->data['created_at']
        ]);


        if ($success) {
            return response()->json([
                'status' => 200,
                'msg'    => 'Thêm thành công'
            ]);
        }

        return response()->json([
            'status' => 401,
            'msg'    => 'Insert Fail',
        ]);
    }

    public function editCost($id, Request $request)
    {
        $validator = Validator::make($request->data, [
            'c_id'       => 'required|integer|min:1',
            'money'      => 'integer|min:1000',
            'prepay'     => 'integer|min:0',
            'status'     => 'integer',
            'created_at' => 'date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'msg'    => 'Validation Error',
                'valid'  => $validator->errors()
            ]);
        }

        $success = Cost::where('id', $request->data['c_id'])->update([
            'type'           => $request->data['type'],
            'name'           => $request->data['name'],
            'money'          => $request->data['money'],
            'prepay'         => $request->data['prepay'],
            'monthlyfee'     => $request->data['monthlyfee'],
            'note'           => $request->data['note'],
            'status'         => $request->data['status'],
            'created_at'     => $request->data['created_at']
        ]);

        return response()->json([
            'status' => 200,
            'msg' => 'Cập nhật thành công!',
        ]);
    }
}
