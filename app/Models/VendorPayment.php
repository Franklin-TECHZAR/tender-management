<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'vendor_balance';
    protected $fillable = [
        'vendor_id',
        'vendor_name',
        'gst_number',
        'out_standing',
    ];

}
