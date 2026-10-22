<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'seq',
        'amount',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->where('type', 'payment')->sum('amount');
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->paid_amount;
    }

    public function getRefundedAmountAttribute(): float
    {
        return (float) $this->payments()->where('type', 'refund')->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->amount - $this->paid_amount + $this->refunded_amount;
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->balance);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'paid' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Paid'],
            'partial' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Partial'],
            'overdue' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Overdue'],
            default => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'label' => 'Pending'],
        };
    }

    /**
     * Recalculate and update the installment status based on received payments.
     */
    public function updateStatus(): void
    {
        $paid = $this->paid_amount;
        $totalAmount = (float) $this->amount;

        if ($totalAmount > 0 && $paid >= $totalAmount) {
            $newStatus = 'paid';
        } elseif ($paid > 0 && $paid < $totalAmount) {
            $newStatus = 'partial';
        } else {
            $isOverdue = $this->due_date && $this->due_date->endOfDay()->isPast();
            $newStatus = $isOverdue ? 'overdue' : 'pending';
        }

        $this->update(['status' => $newStatus]);
    }
}
