<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'roll_no',
        'inquiry_id',
        'branch_id',
        'created_by',
        'name',
        'father_name',
        'email',
        'phone',
        'phone_normalized',
        'alt_phone',
        'address',
        'qualification',
        'total_package',
        'discount',
        'net_payable',
        'payment_type',
        'status',
        'admission_date',
    ];

    protected function casts(): array
    {
        return [
            'total_package' => 'decimal:2',
            'discount' => 'decimal:2',
            'net_payable' => 'decimal:2',
            'admission_date' => 'date',
        ];
    }

    // ── Relationships ──

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'inquiry_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'admission_courses')->withTimestamps();
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class)->orderBy('seq');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ── Computed Attributes ──

    public function getTotalReceivedAttribute(): float
    {
        return (float) $this->payments()->where('type', 'payment')->sum('amount');
    }

    public function getTotalRefundedAttribute(): float
    {
        return (float) $this->payments()->where('type', 'refund')->sum('amount');
    }

    public function getRemainingAttribute(): float
    {
        return (float) $this->net_payable - $this->total_received + $this->total_refunded;
    }

    public function getFeeStatusAttribute(): string
    {
        $remaining = $this->remaining;
        if ($remaining <= 0) {
            return 'paid';
        } elseif ($this->total_received > 0) {
            return 'partial';
        }
        return 'pending';
    }

    public function getFeeStatusColorAttribute(): string
    {
        return match ($this->fee_status) {
            'paid' => 'green',
            'partial' => 'amber',
            'pending' => 'red',
            default => 'slate',
        };
    }

    // ── Helpers ──

    public static function normalizePhone(?string $phone): string
    {
        return Student::normalizePhone($phone);
    }
}
