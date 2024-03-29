<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderPaymentLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }
}
