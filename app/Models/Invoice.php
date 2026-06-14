<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $primaryKey = 'InvoiceID';
    protected $table = 'invoices';

    protected $fillable = [
        'InvoiceNumber',
        'DOID',
        'InvoiceDescription',
        'Subtotal',
        'Tax',
        'Discount',
        'Penalty',
        'TotalAmount',
        'InvoiceStatus',
        'SubmittedDate',
    ];

    protected $casts = [
        'Subtotal'      => 'decimal:2',
        'Tax'           => 'decimal:2',
        'Discount'      => 'decimal:2',
        'Penalty'       => 'decimal:2',
        'TotalAmount'   => 'decimal:2',
        'SubmittedDate' => 'datetime',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'DOID', 'DOID');
    }
}
