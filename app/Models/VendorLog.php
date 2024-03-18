<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'vendor_payment_logs';
    protected $fillable = [
        'vendor_payment_id ',
        'date',
        'amount',
        'type',
        'description',
        'payment_for',
        'payment_mode',
        'payment_details',
    ];
    public function vendorpayment()
    {
        return $this->belongsTo(VendorPayment::class, 'vendor_payment_id', 'id');
    }
}
