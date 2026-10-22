<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallLogRequest;
use App\Models\ActivityLog;
use App\Models\CallLog;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CallLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = CallLog::with(['student', 'user'])->latest('call_date')->latest('call_time');

        if ($request->filled('type')) {
            $query->where('call_type', $request->input('type'));
        }

        if ($request->filled('staff_id')) {
            $query->where('user_id', $request->input('staff_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('call_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('call_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->search($search);
            });
        }

        $callLogs = $query->paginate(20)->withQueryString();
        $staffMembers = User::all();

        return view('calls.index', compact('callLogs', 'staffMembers'));
    }

    public function store(StoreCallLogRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $callLog = CallLog::create($data);

        // Update student's status to the new status after call
        $student = Student::findOrFail($data['student_id']);
        if ($student->status !== $data['status_after_call']) {
            $student->update(['status' => $data['status_after_call']]);
        }

        ActivityLog::record(
            'logged_call',
            "Logged {$callLog->call_type} call with {$student->student_name} ({$student->phone_number})",
            ['student_id' => $student->id, 'call_log_id' => $callLog->id]
        );

        return redirect()->route('students.show', $student)
            ->with('success', "New {$callLog->call_type} contact entry logged successfully!");
    }

    public function followUps(Request $request): View
    {
        $today = Carbon::today();
        $filter = $request->query('tab', 'today'); // 'today', 'overdue', 'upcoming', 'all'

        $query = CallLog::with(['student', 'user'])
            ->whereNotNull('next_follow_up_date')
            ->whereHas('student', function ($q) {
                $q->whereNotIn('status', ['Admitted', 'Lost', 'Not Interested']);
            });

        // Filter based on selected tab
        if ($filter === 'today') {
            $query->whereDate('next_follow_up_date', $today);
        } elseif ($filter === 'overdue') {
            $query->whereDate('next_follow_up_date', '<', $today);
        } elseif ($filter === 'upcoming') {
            $query->whereDate('next_follow_up_date', '>', $today)
                  ->whereDate('next_follow_up_date', '<=', $today->copy()->addDays(7));
        }

        // Summary counts for tabs
        $todayCount = CallLog::whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', $today)
            ->whereHas('student', fn($q) => $q->whereNotIn('status', ['Admitted', 'Lost', 'Not Interested']))
            ->count();

        $overdueCount = CallLog::whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '<', $today)
            ->whereHas('student', fn($q) => $q->whereNotIn('status', ['Admitted', 'Lost', 'Not Interested']))
            ->count();

        $upcomingCount = CallLog::whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '>', $today)
            ->whereDate('next_follow_up_date', '<=', $today->copy()->addDays(7))
            ->whereHas('student', fn($q) => $q->whereNotIn('status', ['Admitted', 'Lost', 'Not Interested']))
            ->count();

        $followUps = $query->orderBy('next_follow_up_date', 'asc')->paginate(15)->withQueryString();

        return view('calls.follow-ups', compact(
            'followUps',
            'filter',
            'todayCount',
            'overdueCount',
            'upcomingCount'
        ));
    }

    public function destroy(CallLog $callLog): RedirectResponse
    {
        if (!Auth::user()->isAdmin() && Auth::id() !== $callLog->user_id) {
            return redirect()->back()->with('error', 'You do not have permission to delete this call log.');
        }

        $studentId = $callLog->student_id;
        $callLog->delete();

        ActivityLog::record('deleted_call_log', "Deleted call log entry #{$callLog->id}");

        return redirect()->route('students.show', $studentId)
            ->with('success', 'Call record deleted.');
    }
}
