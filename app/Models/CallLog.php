<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',
        'call_type',
        'call_date',
        'call_time',
        'duration',
        'remarks',
        'next_follow_up_date',
        'status_after_call',
    ];

    protected function casts(): array
    {
        return [
            'call_date' => 'date',
            'next_follow_up_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeBadgeAttribute(): array
    {
        return match ($this->call_type) {
            'Incoming' => ['bg' => 'bg-blue-50 text-blue-700 ring-blue-600/20 border-blue-200', 'icon' => 'phone-incoming'],
            'Outgoing' => ['bg' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 border-emerald-200', 'icon' => 'phone-outgoing'],
            'WhatsApp' => ['bg' => 'bg-green-50 text-green-700 ring-green-600/20 border-green-200', 'icon' => 'chat'],
            'Walk-in' => ['bg' => 'bg-purple-50 text-purple-700 ring-purple-600/20 border-purple-200', 'icon' => 'user-group'],
            default => ['bg' => 'bg-slate-50 text-slate-700 ring-slate-600/20 border-slate-200', 'icon' => 'phone'],
        };
    }
}
