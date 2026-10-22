<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'default_fee',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'default_fee' => 'decimal:2',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('sort_order');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'course_id');
    }

    public function admissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Admission::class, 'admission_courses')->withTimestamps();
    }
}

