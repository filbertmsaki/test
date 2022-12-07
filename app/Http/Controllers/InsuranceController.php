<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
        $response = [
            "headers" => [
                "ResponseId" => "202212061612578262281",
                "RequestId" => "TXj7fcsBSB",
                "ResponseStatusCode" => "TIRA001",
                "ResponseStatusDesc" => "Successful"
            ],
            "data" => [
                "RegistrationNumber" => "T665CSF",
                "BodyType" => "Saloon (closed top)",
                "SittingCapacity" => 5,
                "MotorCategory" => 1,
                "ChassisNumber" => "NZE1213144335",
                "Make" => "Toyota",
                "Model" => "SPACIO",
                "ModelNumber" => "TA-NZE121N",
                "Color" => "White",
                "EngineNumber" => "1NZ127659",
                "EngineCapacity" => "1490",
                "FuelUsed" => "Petrol",
                "NumberOfAxles" => 2,
                "AxleDistance" => 0,
                "YearOfManufacture" => 2002,
                "TareWeight" => 1180,
                "GrossWeight" => 1280,
                "MotorUsage" => "Private or Normal",
                "OwnerName" => "PHILIP MARSEL SAKAYA",
                "OwnerCategory" => "Sole Proprietor"
            ]
        ];
        return  $response;
    }

    public function vehicleDetails()
    {
        $response = [
            "headers" => [
                "ResponseId" => "202212061612578262281",
                "RequestId" => "TXj7fcsBSB",
                "ResponseStatusCode" => "TIRA001",
                "ResponseStatusDesc" => "Successful"
            ],
            "data" => [
                "RegistrationNumber" => "T665CSF",
                "BodyType" => "Saloon (closed top)",
                "SittingCapacity" => 5,
                "MotorCategory" => 1,
                "ChassisNumber" => "NZE1213144335",
                "Make" => "Toyota",
                "Model" => "SPACIO",
                "ModelNumber" => "TA-NZE121N",
                "Color" => "White",
                "EngineNumber" => "1NZ127659",
                "EngineCapacity" => "1490",
                "FuelUsed" => "Petrol",
                "NumberOfAxles" => 2,
                "AxleDistance" => 0,
                "YearOfManufacture" => 2002,
                "TareWeight" => 1180,
                "GrossWeight" => 1280,
                "MotorUsage" => "Private or Normal",
                "OwnerName" => "PHILIP MARSEL SAKAYA",
                "OwnerCategory" => "Sole Proprietor"
            ]
        ];
        return  $response;
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

        $premiun_excluding_vat = 100000;
        $vat_percentage = 0.18;
        $vat_amount = $vat_percentage * $premiun_excluding_vat;
        $premiun_including_vat =  $premiun_excluding_vat + $vat_amount;
        $product = 'MOTOR PRIVATE';
        $cover_type = 'THIRD PARTY ONLY';
        $usage_type = 'PRIVATE';
        $receipt_date = date('Y-m-d H:i:s');
        $receipt_no = random_int(10000000000, 99999999999);
        $receipt_reference_no = Str::random(4) . random_int(10000000000, 99999999999);
        $receipt_amount = $premiun_including_vat;
        $bank_code = 'VODACOM';
        $issue_date = Carbon::now();
        $cover_note_start_date = Carbon::now();
        $cover_note_end_date = Carbon::now()->addYear()->subDay(1);
        $period = trans('responses.one_year');
        $payment_mode = 'BANK';
        $currency_code = 'TZS';
        $data = Insurance::where(function ($query) use ($request) {
            $query->whereHas("vehicle", function ($query) use ($request) {
                $query->where('RegistrationNumber', $request->vehicle_no);
            });
        })->first();


        if ($data) {
            if ($data->status == 'active') {
                return response()->json([
                    'status' => Response::$statusTexts[Response::HTTP_FOUND],
                    'message' => trans('responses.active_cover', ['number' => $request->vehicle_no]),
                    'data' => []
                ], Response::HTTP_FOUND);
            } else if ($data->status == 'inactive') {

                $chassis = str_replace(substr($data->vehicle->ChassisNumber, 3, -3), "******", $data->vehicle->ChassisNumber);
                $owner = str_replace(substr($data->vehicle->OwnerName, 3, -3), "******", $data->vehicle->OwnerName);
                $instructions =
                    trans('responses.registration_number') . ' : ' . $data->vehicle->RegistrationNumber . PHP_EOL .
                    trans('responses.chassis_number') . ' : ' . $chassis  . PHP_EOL .
                    trans('responses.body_type') . ' : ' . $data->vehicle->Model . ' ' . $data->vehicle->BodyType . PHP_EOL .
                    trans('responses.body_color') . ' : ' . $data->vehicle->Color . PHP_EOL .
                    trans('responses.motor_usage') . ' : ' . $data->vehicle->MotorUsage . PHP_EOL .
                    trans('responses.owner_name') . ' : ' .  $owner . PHP_EOL . PHP_EOL .
                    trans('responses.cover_amount') . PHP_EOL .
                    trans('responses.price') . ' : ' .  $premiun_excluding_vat  . PHP_EOL .
                    trans('responses.vat') . '( ' . $vat_percentage . ' )' . ' : ' .  $vat_amount . PHP_EOL .
                    trans('responses.amount') . ' : ' .  $premiun_including_vat  . PHP_EOL;
                return response()->json([
                    'status' => Response::$statusTexts[Response::HTTP_ACCEPTED],
                    'message' => trans('responses.inactive_cover', ['number' => $request->vehicle_no]),
                    'data' => $instructions
                ], Response::HTTP_ACCEPTED);
            }
        } else {
            return response()->json([
                'status' => Response::$statusTexts[Response::HTTP_BAD_REQUEST],
                'message' => trans('responses.inactive_cover', ['number' => $request->vehicle_no]),
                'data' => []
            ], Response::HTTP_BAD_REQUEST);
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
        $validator = Validator::make($request->all(), [
            'plate_number' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Fail to validate',
                'data' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->plate_number) {
            $folderPath = "uploads/";
            $path = public_path($folderPath);
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $base64Image = explode(";base64,", $request->plate_number);
            $explodeImage = explode("image/", $base64Image[0]);
            $imageType = $explodeImage[1];
            $image_base64 = base64_decode($base64Image[1]);
            $file = $folderPath . uniqid() . '.' . $imageType;
            file_put_contents($file, $image_base64);
        }
        return response()->json([
            'status' => Response::$statusTexts[Response::HTTP_OK],
            'message' => 'Image uploaded',
            'data' => []
        ], Response::HTTP_OK);
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
