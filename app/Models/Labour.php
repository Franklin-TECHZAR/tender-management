<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Labour extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function labourReports()
    {
        return $this->hasMany(LabourReport::class, 'labour_id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }
}
