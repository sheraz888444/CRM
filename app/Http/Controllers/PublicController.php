<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $totalStudents = Student::count();
        $totalCalls = CallLog::count();
        $totalAdmitted = Student::where('status', 'Admitted')->count();
        $staffCount = User::where('role', 'staff')->where('status', 'active')->count();

        $schoolName = Setting::get('school_name', 'EduTrack International School');
        $schoolPhone = Setting::get('school_phone', '+92 300 1234567');
        $schoolEmail = Setting::get('school_email', 'admissions@edutrack.edu');

        return view('public.home', compact(
            'totalStudents',
            'totalCalls',
            'totalAdmitted',
            'staffCount',
            'schoolName',
            'schoolPhone',
            'schoolEmail'
        ));
    }

    public function about(): View
    {
        $schoolName = Setting::get('school_name', 'EduTrack International School');
        $schoolPhone = Setting::get('school_phone', '+92 300 1234567');
        $schoolEmail = Setting::get('school_email', 'admissions@edutrack.edu');

        return view('public.about', compact(
            'schoolName',
            'schoolPhone',
            'schoolEmail'
        ));
    }
}
