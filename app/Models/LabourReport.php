<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabourReport extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function labour()
    {
        return $this->belongsTo(Labour::class);
    }

}
