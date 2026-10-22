<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'status',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Generate next roll number for this branch.
     * Format: B1-0001, B1-0002, etc.
     */
    public function nextRollNo(): string
    {
        $lastAdmission = $this->admissions()->orderByDesc('id')->first();

        if ($lastAdmission) {
            // Extract the numeric part from roll_no like "B1-0042"
            $parts = explode('-', $lastAdmission->roll_no);
            $nextNum = isset($parts[1]) ? intval($parts[1]) + 1 : 1;
        } else {
            $nextNum = 1;
        }

        return $this->code . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    public function generateNextRollNo(): string
    {
        return $this->nextRollNo();
    }
}
