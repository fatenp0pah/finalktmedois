<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $primaryKey = 'DOID';
    protected $table = 'delivery_orders';

    protected $fillable = [
        'DONumber',
        'VendorID',
        'OrderDate',
        'PONumber',
        'ProjectReference',
        'Customer',
        'ShippingAddress',
        'InvoiceAddress',
        'ItemNo',
        'ItemDescription',
        'Quantity',
        'DeliveryDate',
        'DeliveryTime',
        'DOFileLink',
        'ProofFileLink',
        'DOStatus',
        'Remark',
        'SubmittedDate',
    ];

    protected $casts = [
        'SubmittedDate' => 'datetime',
        'DeliveryDate'  => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'VendorID', 'VendorID');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'DOID', 'DOID');
    }

    public function proofs()
    {
        return $this->hasMany(ProofDelivery::class, 'DOID', 'DOID');
    }
}
