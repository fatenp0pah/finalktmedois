<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $primaryKey = 'LogID';
    protected $table = 'audit_logs';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'Action',
        'AffectedRecord',
        'LogDescription',
        'Timestamp',
    ];

    // Static helper — call AuditLog::log() anywhere in the system
    // Satisfies client NFR: "All actions require audit logging"
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'UserID', 'UserID');
    }

    public static function log($userID, $action, $affected = null, $description = null): void
    {
        static::create([
            'UserID'         => $userID,
            'Action'         => $action,
            'AffectedRecord' => $affected,
            'LogDescription' => $description,
            'Timestamp'      => now(),
        ]);
    }
}
