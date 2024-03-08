<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'id','job_order', 'labour_id', 'date', 'amount', 'description', 'payment_mode', 'payment_details'
    ];

    public function labour()
    {
        return $this->belongsTo(Labour::class);
    }
}
