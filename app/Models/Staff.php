<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $primaryKey = 'StaffID';
    protected $table = 'staff';

    protected $fillable = [
        'UserID',
        'StaffName',
        'StaffEmail',
        'StaffPhoneNum',
        'StaffRole',
        'Department',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
