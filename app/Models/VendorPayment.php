<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'vendor_payments';
    protected $fillable = [
        'job_order_id',
        'date',
        'amount',
        'description',
        'payment_for',
        'payment_mode',
        'payment_details',
    ];

}
