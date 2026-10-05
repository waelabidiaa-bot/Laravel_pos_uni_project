<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role','theme'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function cashSessions(): HasMany
    {
        return $this->hasMany(CashSession::class, 'cashier_id');
    }

    /** The cashier's currently open session, or null. */
    public function openSession(): ?CashSession
    {
        return $this->cashSessions()->where('status', 'OPEN')->latest('opened_at')->first();
    }
}
