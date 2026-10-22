<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'installment_id',
        'amount',
        'payment_date',
        'mode',
        'received_by',
        'type',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function isRefund(): bool
    {
        return $this->type === 'refund';
    }

    public function getModeBadgeAttribute(): array
    {
        return match ($this->mode) {
            'Cash' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'banknotes'],
            'Bank' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'building-library'],
            'Online' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'icon' => 'globe-alt'],
            'Cheque' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => 'document-text'],
            default => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'icon' => 'credit-card'],
        };
    }
}
