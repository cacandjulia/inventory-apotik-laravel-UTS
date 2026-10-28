<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];

    // Password otomatis di-hash saat disimpan (kalau belum berupa hash)
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = Hash::info($value)['algoName'] === 'unknown'
            ? Hash::make($value)
            : $value;
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }

    public function sales() { return $this->hasMany(Sale::class); }
    public function purchases() { return $this->hasMany(Purchase::class); }
}