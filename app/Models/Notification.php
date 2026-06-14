<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $primaryKey = 'NotificationID';
    protected $table = 'notifications';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'NotificationMessage',
        'NotificationStatus',
        'CreatedDate',
    ];

    protected $casts = [
        'CreatedDate' => 'datetime',
    ];

    // Static helper — call Notification::send() to notify a user
    public static function send($userID, $message): void
    {
        static::create([
            'UserID'              => $userID,
            'NotificationMessage' => $message,
            'NotificationStatus'  => 'Unread',
            'CreatedDate'         => now(),
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
