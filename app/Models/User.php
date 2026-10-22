<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'avatar',
        'branch_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Role Checks ──

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isBranchAdmin(): bool
    {
        return $this->role === 'branch_admin';
    }

    /**
     * Backward-compatible: returns true for super_admin OR branch_admin.
     * All existing blade checks like Auth::user()->isAdmin() will still work.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin', 'branch_admin']);
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user can access data from a specific branch.
     */
    public function canAccessBranch(?int $branchId): bool
    {
        if ($this->isSuperAdmin() || $this->role === 'admin') {
            return true; // Super admin sees all branches
        }
        return $this->branch_id === $branchId;
    }

    /**
     * Get display-friendly role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'branch_admin' => 'Branch Admin',
            'admin' => 'Administrator',
            'staff' => 'Staff / Inquiry Officer',
            default => ucfirst($this->role),
        };
    }

    // ── Relationships ──

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'created_by');
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class, 'user_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'received_by');
    }
}
