<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function defaultClasses(): array
    {
        $saved = self::get('classes');
        if ($saved) {
            return array_map('trim', explode(',', $saved));
        }
        return [
            'Playgroup',
            'Nursery',
            'Prep / KG',
            'Grade 1',
            'Grade 2',
            'Grade 3',
            'Grade 4',
            'Grade 5',
            'Grade 6',
            'Grade 7',
            'Grade 8',
            'Grade 9 (Matric)',
            'Grade 10 (Matric)',
            'O-Level',
            'A-Level',
        ];
    }

    public static function defaultSources(): array
    {
        $saved = self::get('sources');
        if ($saved) {
            return array_map('trim', explode(',', $saved));
        }
        return [
            'Walk-in',
            'Phone Call',
            'Facebook',
            'Instagram',
            'Website',
            'Referral',
            'Billboard / Banner',
            'Other',
        ];
    }

    public static function defaultStatuses(): array
    {
        return [
            'New',
            'Contacted',
            'Interested',
            'Visit Scheduled',
            'Admitted',
            'Not Interested',
            'Lost',
        ];
    }
}
