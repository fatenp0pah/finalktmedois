<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $primaryKey = 'VendorID';

    protected $fillable = [
        'UserID',
        'VendorNumber',
        'CompanyName',
        'RefNumber',
        'VendorEmail',
        'VendorContactNum',
        'ContactPerson',
        'ExpiredDate',
        'VendorStatus',
        'LastSyncDate',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'VendorID', 'VendorID');
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'VendorID', 'VendorID');
    }
    public function apiLogs()
    {
        return $this->hasMany(VendorApiLog::class, 'VendorID', 'VendorID');
    }

    // Status helpers
    public function isActive(): bool
    {
        return $this->VendorStatus === 'Active';
    }
    public function isInactive(): bool
    {
        return $this->VendorStatus === 'Inactive';
    }
    public function isDeactivated(): bool
    {
        return $this->VendorStatus === 'Deactivated';
    }

    // API Sync methods — simulates KTMB Oracle master DB integration
    public function simulateApiSync(): void
    {
        $this->LastSyncDate = now();
        $this->save();
        VendorApiLog::create([
            'VendorID'  => $this->VendorID,
            'APIAction' => 'SyncVendor',
            'APIStatus' => 'Success',
            'LogDate'   => now(),
        ]);
    }

    public function autoSyncOnLogin(): void
    {
        $this->LastSyncDate = now();
        $this->save();
        VendorApiLog::create([
            'VendorID'  => $this->VendorID,
            'APIAction' => 'RetrieveVendor',
            'APIStatus' => 'Success',
            'LogDate'   => now(),
        ]);
    }

    public function checkStatusFromMaster(): string
    {
        VendorApiLog::create([
            'VendorID'  => $this->VendorID,
            'APIAction' => 'CheckVendorStatus',
            'APIStatus' => 'Success',
            'LogDate'   => now(),
        ]);
        return $this->VendorStatus;
    }
}
