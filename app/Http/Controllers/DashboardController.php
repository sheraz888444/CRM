<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Stats for current staff
        $myTotalStudents = Student::where('created_by', $user->id)->count();
        $todayInquiries = Student::whereDate('created_at', $today)->count();
        $todayCalls = CallLog::where('user_id', $user->id)->whereDate('call_date', $today)->count();
        
        $pendingFollowUpsCount = CallLog::whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '<=', $today)
            ->whereHas('student', function ($q) {
                $q->whereNotIn('status', ['Admitted', 'Lost', 'Not Interested']);
            })
            ->count();

        // Recent 10 inquiries
        $recentInquiries = Student::with(['creator', 'callLogs'])
            ->latest()
            ->take(10)
            ->get();

        // Follow-up reminders for today / overdue
        $followUpReminders = CallLog::with(['student', 'user'])
            ->whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '<=', $today)
            ->whereHas('student', function ($q) {
                $q->whereNotIn('status', ['Admitted', 'Lost', 'Not Interested']);
            })
            ->orderBy('next_follow_up_date', 'asc')
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'myTotalStudents',
            'todayInquiries',
            'todayCalls',
            'pendingFollowUpsCount',
            'recentInquiries',
            'followUpReminders'
        ));
    }
}
