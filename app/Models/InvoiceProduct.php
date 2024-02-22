<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceProduct extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'invoice_products';
    protected $fillable = [
        'invoice_purchase_id',
        'material_id',
        'quantity',
        'unit',
        'amount',
        'gst',
        'total',
    ];
    public function invoicePurchase()
    {
        return $this->belongsTo(InvoicePurchase::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
