<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePurchase extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoice_purchases';
    protected $fillable = [
        'job_order_id',
        'invoice_no',
        'vendor_id',
        'type',
        'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function purchaseType()
    {
        return $this->belongsTo(PurchaseType::class, 'type');
    }

    public function invoiceProduct()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_purchase_id', 'id');
    }
}
