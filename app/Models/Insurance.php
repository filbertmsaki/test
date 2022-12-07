<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'customer_id',
        'product',
        'cover_type',
        'usage_type',
        'risk_note_number',
        'debit_note_number',
        'is_vat_exempt',
        'receipt_date',
        'receipt_no',
        'receipt_reference_no',
        'receipt_amount',
        'bank_code',
        'issue_date',
        'cover_note_start_date',
        'cover_note_end_date',
        'payment_mode',
        'sum_issued',
        'premiun_excluding_vat',
        'vat_percentage',
        'vat_amount',
        'premiun_including_vat',
        'status',
        'currency_code',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, "vehicle_id");
    }
}
