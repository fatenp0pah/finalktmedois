<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProofDelivery extends Model
{
    protected $primaryKey = 'ProofID';
    protected $table = 'proof_of_deliveries';
    public $timestamps = false;

    protected $fillable = [
        'DOID',
        'FileName',
        'FileType',
        'FileLink',
        'UploadedDate',
    ];

    protected $casts = [
        'UploadedDate' => 'datetime',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'DOID', 'DOID');
    }
}
