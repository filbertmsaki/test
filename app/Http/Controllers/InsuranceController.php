<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InsuranceController extends Controller
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
    public function clear($string)
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim($string)));
    }

    public function checkStatus(Request $request)
    {
        $request->merge([
            'vehicle_no' => $this->clear($request->vehicle_no)
        ]);
        $validator = Validator::make($request->all(), [
            'vehicle_no' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Fail to validate',
                'data' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $data = Insurance::whereHas("vehicle", function ($query) use ($request) {
            $query->whereVehicleNo($request->vehicle_no);
        })->first();
        if ($data->status == 'active') {

            return response()->json([
                'status' => Response::$statusTexts[Response::HTTP_FOUND],
                'message' => trans('responses.active_cover', ['number' => $request->vehicle_no]),
                'data' => []
            ], Response::HTTP_FOUND);
        } else {
            $instructions = trans('responses.vehicle_no') . ' : ' . $data->vehicle->vehicle_no . PHP_EOL
                . trans('responses.chassis_number') . ' : ' . $data->vehicle->chassis . PHP_EOL
                . trans('responses.vehicle_type') . ' : ' . $data->vehicle->body . PHP_EOL
                . trans('responses.body_color') . ' : ' . $data->vehicle->color . PHP_EOL
                . trans('responses.owner_name') . ' : ' . $data->vehicle->owner . PHP_EOL;
            return response()->json([
                'status' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
                'message' => trans('responses.inactive_cover', ['number' => $request->vehicle_no]),
                'data' => $instructions
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return 'hellow';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
