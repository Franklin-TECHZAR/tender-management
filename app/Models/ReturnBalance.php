<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnBalance extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'return_balance';
    protected $fillable = [
        'job_order_id ',
        'date',
        'amount',
        'description',
        'payment_mode',
        'payment_details',
    ];
}
