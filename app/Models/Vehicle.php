<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'RegistrationNumber',
        'BodyType',
        'SittingCapacity',
        'MotorCategory',
        'ChassisNumber',
        'Make',
        'Model',
        'ModelNumber',
        'EngineNumber',
        'EngineCapacity',
        'FuelUsed',
        'NumberOfAxles',
        'AxleDistance',
        'YearOfManufacture',
        'TareWeight',
        'GrossWeight',
        'MotorUsage',
        'OwnerName',
        'OwnerCategory',
    ];
}
