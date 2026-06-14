<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorApiLog extends Model
{
    protected $primaryKey = 'APILogID';
    protected $table = 'vendor_api_logs';
    public $timestamps = false;

    protected $fillable = [
        'VendorID',
        'APIAction',
        'APIStatus',
        'LogDate',
    ];

    protected $casts = [
        'LogDate' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'VendorID', 'VendorID');
    }
}
