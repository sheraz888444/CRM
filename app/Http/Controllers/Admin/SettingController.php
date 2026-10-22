<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $schoolName = Setting::get('school_name', 'EduTrack International School');
        $schoolPhone = Setting::get('school_phone', '+92 300 1234567');
        $schoolEmail = Setting::get('school_email', 'admissions@edutrack.edu');
        $schoolAddress = Setting::get('school_address', '123 Education Boulevard, Main Campus');
        $classes = implode(', ', Setting::defaultClasses());
        $sources = implode(', ', Setting::defaultSources());

        return view('admin.settings.index', compact(
            'schoolName',
            'schoolPhone',
            'schoolEmail',
            'schoolAddress',
            'classes',
            'sources'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_phone' => ['required', 'string', 'max:100'],
            'school_email' => ['required', 'email', 'max:255'],
            'school_address' => ['nullable', 'string', 'max:500'],
            'classes' => ['required', 'string'],
            'sources' => ['required', 'string'],
        ]);

        Setting::set('school_name', $request->school_name);
        Setting::set('school_phone', $request->school_phone);
        Setting::set('school_email', $request->school_email);
        Setting::set('school_address', $request->school_address);
        Setting::set('classes', $request->classes);
        Setting::set('sources', $request->sources);

        ActivityLog::record('updated_settings', 'Updated system and school settings.');

        return redirect()->route('admin.settings.index')
            ->with('success', 'System settings updated successfully.');
    }
}
