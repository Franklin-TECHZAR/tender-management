<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'purchase_payment_logs';
    protected $fillable = [
        'purchase_dept_id',
        'date',
        'amount',
        'type',
        'description',
        'payment_for',
        'payment_mode',
        'payment_details',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'purchase_dept_id', 'id');
    }
}
