<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_name',
        'parent_name',
        'phone_number',
        'phone_normalized',
        'alternate_phone',
        'email',
        'address',
        'city',
        'area',
        'class_applying_for',
        'previous_school',
        'inquiry_source',
        'status',
        'notes',
        'remarks',
        'created_by',
        'branch_id',
        'course_id',
        'mode_id',
        'visit_date',
        'next_followup_date',
    ];

    public function getAreaAttribute(): ?string
    {
        return $this->attributes['city'] ?? null;
    }

    public function setAreaAttribute($value): void
    {
        $this->attributes['city'] = $value;
    }

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'next_followup_date' => 'date',
        ];
    }

    // ── Boot: Auto-normalize phone on save ──

    protected static function booted(): void
    {
        static::saving(function (Student $student) {
            $student->phone_normalized = self::normalizePhone($student->phone_number);
        });
    }

    public static function normalizePhone(?string $phone): string
    {
        if (!$phone) {
            return '';
        }
        // Remove non-digit characters
        $digits = preg_replace('/\D+/', '', $phone);
        // Normalize Pakistan / international prefix (e.g. 92300... -> 0300...)
        if (str_starts_with($digits, '92') && strlen($digits) > 10) {
            $digits = '0' . substr($digits, 2);
        }
        return $digits;
    }

    // ── Relationships ──

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function inquiryMode(): BelongsTo
    {
        return $this->belongsTo(InquiryMode::class, 'mode_id');
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class)->orderBy('call_date', 'desc')->orderBy('call_time', 'desc');
    }

    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class, 'inquiry_id');
    }

    // ── Scopes ──

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        $cleanTerm = preg_replace('/\D+/', '', $term);

        return $query->where(function ($q) use ($term, $cleanTerm) {
            $q->where('student_name', 'like', "%{$term}%")
              ->orWhere('parent_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone_number', 'like', "%{$term}%");

            if (!empty($cleanTerm) && strlen($cleanTerm) >= 3) {
                $q->orWhere('phone_normalized', 'like', "%{$cleanTerm}%");
            }
        });
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['class'])) {
            $query->where('class_applying_for', $filters['class']);
        }

        if (!empty($filters['source'])) {
            $query->where('inquiry_source', $filters['source']);
        }

        if (!empty($filters['staff_id'])) {
            $query->where('created_by', $filters['staff_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Scope to filter by branch access (for non-super-admin users).
     */
    public function scopeForBranch($query, ?User $user = null)
    {
        if (!$user) return $query;

        if ($user->isSuperAdmin() || $user->role === 'admin') {
            return $query; // sees all branches
        }

        return $query->where('branch_id', $user->branch_id);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Admitted' => ['bg' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 border-emerald-200', 'dot' => 'bg-emerald-500'],
            'Interested' => ['bg' => 'bg-blue-50 text-blue-700 ring-blue-600/20 border-blue-200', 'dot' => 'bg-blue-500'],
            'Visit Scheduled' => ['bg' => 'bg-purple-50 text-purple-700 ring-purple-600/20 border-purple-200', 'dot' => 'bg-purple-500'],
            'Visited' => ['bg' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 border-indigo-200', 'dot' => 'bg-indigo-500'],
            'Contacted' => ['bg' => 'bg-sky-50 text-sky-700 ring-sky-600/20 border-sky-200', 'dot' => 'bg-sky-500'],
            'New' => ['bg' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 border-indigo-200', 'dot' => 'bg-indigo-500'],
            'Not Interested' => ['bg' => 'bg-amber-50 text-amber-700 ring-amber-600/20 border-amber-200', 'dot' => 'bg-amber-500'],
            'Lost' => ['bg' => 'bg-rose-50 text-rose-700 ring-rose-600/20 border-rose-200', 'dot' => 'bg-rose-500'],
            default => ['bg' => 'bg-slate-50 text-slate-700 ring-slate-600/20 border-slate-200', 'dot' => 'bg-slate-500'],
        };
    }
}
