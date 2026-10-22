<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'type',
        'ref_id',
        'ref_type',
        'title',
        'description',
        'due_date',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'date',
        ];
    }

    // ── Relationships ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Polymorphic reference to Student (followup) or Installment (installment due).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo('ref');
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')->whereDate('due_date', '<', today());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('status', 'pending')
            ->whereDate('due_date', '>', today())
            ->whereDate('due_date', '<=', today()->addDays($days));
    }

    // ── Helpers ──

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    public function getTypeBadgeAttribute(): array
    {
        return match ($this->type) {
            'followup' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'phone', 'label' => 'Follow-up'],
            'installment' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => 'banknotes', 'label' => 'Installment Due'],
            default => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'icon' => 'clipboard', 'label' => 'Task'],
        };
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => today(),
        ]);
    }

    public function reschedule(\DateTimeInterface $newDate): void
    {
        $this->update([
            'status' => 'rescheduled',
        ]);

        // Create a new task with the new date
        self::create([
            'user_id' => $this->user_id,
            'branch_id' => $this->branch_id,
            'type' => $this->type,
            'ref_id' => $this->ref_id,
            'ref_type' => $this->ref_type,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $newDate,
            'status' => 'pending',
        ]);
    }
}
