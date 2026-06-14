<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $primaryKey = 'UserID';

    protected $fillable = [
        'Username',
        'UserPassword',
        'UserEmail',
        'UserRole',
        'UserStatus',
        'LastLogin',
    ];

    protected $hidden = ['UserPassword', 'remember_token'];

    // Laravel Auth uses 'password' field — map it to UserPassword
    public function getAuthPassword()
    {
        return $this->UserPassword;
    }

    // Relationships
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'UserID', 'UserID');
    }
    public function staff()
    {
        return $this->hasOne(Staff::class,  'UserID', 'UserID');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'UserID', 'UserID');
    }

    // Role helpers
    public function isVendor(): bool
    {
        return $this->UserRole === 'Vendor';
    }
    public function isOfficer(): bool
    {
        return $this->UserRole === 'Officer';
    }
    public function isFinance(): bool
    {
        return $this->UserRole === 'Finance';
    }
    public function isAdmin(): bool
    {
        return $this->UserRole === 'Admin';
    }
}
