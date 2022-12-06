<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'insurance_cover',
        'insurance_period_start',
        'insurance_period_end',
        'amount',
        'payment_method',
        'payment_number',
        'status'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, "vehicle_id");
    }
}
