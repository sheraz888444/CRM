<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryMode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'status',
        'sort_order',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('sort_order');
    }
}
