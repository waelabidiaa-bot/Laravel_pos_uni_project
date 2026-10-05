<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    public $timestamps = false;

    protected $fillable = ['cash_session_id', 'sale_date', 'total', 'status'];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class, 'cash_session_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->cashSession->cashier_id === $user->id;
    }

    public function recalculateTotal(): void
    {
        $this->total = $this->items()
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as t')
            ->value('t');
        $this->save();
    }
}
