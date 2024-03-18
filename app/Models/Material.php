<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function invoicePurchase()
    {
        return $this->hasMany(InvoicePurchase::class);
    }

    public function invoiceProduct()
    {
        return $this->hasMany(InvoiceProduct::class);
    }


}
