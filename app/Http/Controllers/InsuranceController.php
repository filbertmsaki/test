<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Insurance;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function phone_number_format($code, $digits)
    {

        $characters = preg_replace('/[^0-9]/', '', trim($digits));
        $trimedmobile = substr($characters, -9);
        $phonenumber = $code . $trimedmobile;
        return $phonenumber;
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

        $issue_date = Carbon::now();
        $cover_note_start_date = Carbon::now();
        $cover_note_end_date = Carbon::now()->addYear()->subDay(1)->endOfDay();
        $vehicle = Vehicle::where('RegistrationNumber', $request->vehicle_no)->first();
        if ($vehicle) {
            $valid_insurance = Insurance::whereVehicleId($vehicle->id)->latest()->first();
            if ($valid_insurance) {
                if ($valid_insurance->status == 'active' && $valid_insurance->cover_note_end_date > $cover_note_start_date) {
                    return response()->json([
                        'status' => Response::$statusTexts[Response::HTTP_FOUND],
                        'message' => trans('responses.active_cover', ['number' => $request->vehicle_no]),
                        'data' => []
                    ], Response::HTTP_FOUND);
                } else if ($valid_insurance->cover_note_end_date < $cover_note_start_date) {
                    $chassis = str_replace(substr($valid_insurance->vehicle->ChassisNumber, 3, -3), "******", $valid_insurance->vehicle->ChassisNumber);
                    $owner = str_replace(substr($valid_insurance->vehicle->OwnerName, 3, -3), "******", $valid_insurance->vehicle->OwnerName);
                    $instructions =
                        trans('responses.registration_number') . ' : ' . $valid_insurance->vehicle->RegistrationNumber . PHP_EOL .
                        trans('responses.chassis_number') . ' : ' . $chassis  . PHP_EOL .
                        trans('responses.body_type') . ' : ' . $valid_insurance->vehicle->Model . ' ' . $valid_insurance->vehicle->BodyType . PHP_EOL .
                        trans('responses.body_color') . ' : ' . $valid_insurance->vehicle->Color . PHP_EOL .
                        trans('responses.motor_usage') . ' : ' . $valid_insurance->vehicle->MotorUsage . PHP_EOL .
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
                    'status' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    'message' => trans('responses.not_valid_vehicle', ['number' => $request->vehicle_no]),
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }
        } else {
            return response()->json([
                'status' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
                'message' => trans('responses.not_valid_vehicle', ['number' => $request->vehicle_no]),
                'data' => []
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function sendPaymentInfo(Request $request)
    {
        $request->merge([
            'vehicle_no' => $this->clear($request->vehicle_no),
            'phone_number' => $this->phone_number_format('255', $request->phone_number)
        ]);
        $validator = Validator::make($request->all(), [
            'vehicle_no' => 'required',
            'phone_number' => 'required|min:9',
            'paymeny_method' => 'required|min:3'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Fail to validate',
                'data' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        if (strtolower($request->paymeny_method) == 'vodacom') {
            $payment_method = strtoupper(strtolower('M-Pesa'));
        } elseif (strtolower($request->paymeny_method) == 'tigo') {
            $payment_method = strtoupper(strtolower('Tigo-pesa'));
        } elseif (strtolower($request->paymeny_method) == 'airtel') {
            $payment_method = strtoupper(strtolower('Airtel moneny'));
        } else {
            $payment_method = strtoupper(strtolower('bank'));
        }
        $is_vat_exempt = 'N';
        $premiun_excluding_vat = 100000;
        $vat_percentage = 0.18;
        $vat_amount = $vat_percentage * $premiun_excluding_vat;
        $premiun_including_vat =  $premiun_excluding_vat + $vat_amount;
        $product = 'MOTOR PRIVATE';
        $cover_type = 'THIRD PARTY ONLY';
        $usage_type = 'PRIVATE';
        $receipt_date = date('Y-m-d H:i:s');
        $receipt_no = random_int(10000000000, 99999999999);
        $receipt_reference_no = strtoupper(Str::random(4) . random_int(10000000000, 99999999999));
        $receipt_amount = $premiun_including_vat;
        $bank_code = $payment_method;
        $issue_date = Carbon::now();
        $cover_note_start_date = Carbon::now();
        $cover_note_end_date = Carbon::now()->addYear()->subDay(1)->endOfDay();
        $period = trans('responses.one_year');
        $payment_mode = $payment_method;
        $currency_code = 'TZS';
        $risk_note_number = 'RN' . random_int(1000, 9999);
        $debit_note_number = 'DN' . random_int(1000, 9999);
        $vehicle = Vehicle::where('RegistrationNumber', $request->vehicle_no)->first();
        if ($vehicle) {
            $customer = Customer::where('customer_name', $vehicle->OwnerName)->first();
            if ($customer) {
                $last_insurance = Insurance::whereVehicleId($vehicle->id)
                    ->where('cover_note_end_date', '>', $cover_note_start_date)
                    ->latest()->first();
                if ($last_insurance) {
                    return response()->json([
                        'status' => Response::$statusTexts[Response::HTTP_FOUND],
                        'message' => trans('responses.active_cover', ['number' => $request->vehicle_no]),
                        'data' => []
                    ], Response::HTTP_FOUND);
                }
                DB::beginTransaction();
                $insurance = Insurance::create([
                    'vehicle_id' => $vehicle->id,
                    'customer_id' => $customer->id,
                    'product' => $product,
                    'cover_type' => $cover_type,
                    'usage_type' => $usage_type,
                    'risk_note_number' => $risk_note_number,
                    'debit_note_number' => $debit_note_number,
                    'is_vat_exempt' => $is_vat_exempt,
                    'receipt_date' => $receipt_date,
                    'receipt_no' => $receipt_no,
                    'receipt_reference_no' => $receipt_reference_no,
                    'receipt_amount' => $receipt_amount,
                    'bank_code' => $bank_code,
                    'issue_date' => $issue_date,
                    'cover_note_start_date' => $cover_note_start_date,
                    'cover_note_end_date' => $cover_note_end_date,
                    'payment_mode' => $payment_mode,
                    'sum_issued' => 0,
                    'premiun_excluding_vat' => $premiun_excluding_vat,
                    'vat_percentage' => $vat_percentage,
                    'vat_amount' => $vat_amount,
                    'premiun_including_vat' => $premiun_including_vat,
                    'status' => 'active'
                ]);
                DB::commit();
                if ($insurance) {
                    $chassis = str_replace(substr($insurance->vehicle->ChassisNumber, 3, -3), "******", $insurance->vehicle->ChassisNumber);
                    $owner = str_replace(substr($insurance->vehicle->OwnerName, 3, -3), "******", $insurance->vehicle->OwnerName);
                    $cover_note =
                        trans('responses.cover_details') . PHP_EOL . PHP_EOL .
                        trans('responses.status') . ' : ' . 'INACTIVE' . PHP_EOL .
                        trans('responses.sticker_no') . ' : ' . '' . PHP_EOL .
                        trans('responses.cover_note_ref') . ' : ' . '' . PHP_EOL .
                        trans('responses.insure') . ' : ' . 'Jubilee Allianz General Insurance Tanzania' . PHP_EOL .
                        trans('responses.class_of_insurance') . ' : ' . $insurance->product . PHP_EOL .
                        trans('responses.transacting_company') . ' : ' . 'Jubilee Allianz General Insurance Tanzania' . PHP_EOL .
                        trans('responses.transacting_company_type') . ' : ' . 'Insurance Company' . PHP_EOL .
                        trans('responses.registration_number') . ' : ' . $insurance->vehicle->RegistrationNumber . PHP_EOL .
                        trans('responses.chassis_number') . ' : ' . $insurance->vehicle->ChassisNumber  . PHP_EOL .
                        trans('responses.cover_type') . ' : ' . $insurance->cover_type . PHP_EOL .
                        trans('responses.date_issued') . ' : ' . $insurance->issue_date . PHP_EOL .
                        trans('responses.start_date') . ' : ' . $insurance->cover_note_start_date . PHP_EOL .
                        trans('responses.end_date') . ' : ' . $insurance->cover_note_end_date . PHP_EOL;

                    $payment_note = '1. ' . $payment_method . ' will send you a  prompt to enter your ' . $payment_method . ' pin to approve ' . $premiun_including_vat . ' Tsh to be deducted in your account. Enter your PIN to pay.' . PHP_EOL . '2. You will receive an SMS confirmation of your transaction from Jubilee Allianz General Insurance Tanzania.';

                    return response()->json([
                        'status' => Response::$statusTexts[Response::HTTP_CREATED],
                        'message' => trans('responses.new_cover', ['number' => $request->vehicle_no]),
                        'data' => [
                            'cover_note' => $cover_note,
                            'payment_note' => $payment_note
                        ]
                    ], Response::HTTP_CREATED);
                }
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Data not found',
            'data' => $validator->errors()
        ], Response::HTTP_BAD_REQUEST);
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
