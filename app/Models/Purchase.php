<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "purchase";

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
