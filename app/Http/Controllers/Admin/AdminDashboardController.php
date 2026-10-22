<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admission;
use App\Models\Branch;
use App\Models\CallLog;
use App\Models\Course;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Branch scoping
        $selectedBranchId = null;
        if (!$user->isSuperAdmin() && $user->role !== 'admin') {
            $selectedBranchId = $user->branch_id;
        } elseif ($request->filled('branch_id')) {
            $selectedBranchId = $request->branch_id;
        }

        $branches = Branch::where('status', 'active')->orderBy('name')->get();

        // 1. Inquiries query
        $studentQuery = Student::query();
        if ($selectedBranchId) {
            $studentQuery->where('branch_id', $selectedBranchId);
        }

        $totalStudents = (clone $studentQuery)->count();
        $todayInquiries = (clone $studentQuery)->whereDate('created_at', $today)->count();

        // 2. Admissions query
        $admissionQuery = Admission::query();
        if ($selectedBranchId) {
            $admissionQuery->where('branch_id', $selectedBranchId);
        }

        $totalAdmissions = (clone $admissionQuery)->count();
        $totalNetPayable = (clone $admissionQuery)->sum('net_payable');
        $conversionRate = $totalStudents > 0 ? round(($totalAdmissions / $totalStudents) * 100, 1) : 0;

        // 3. Payments / Financials
        $paymentQuery = Payment::where('type', 'payment');
        if ($selectedBranchId) {
            $paymentQuery->whereHas('admission', fn($q) => $q->where('branch_id', $selectedBranchId));
        }

        $totalFeeCollected = (clone $paymentQuery)->sum('amount');
        $cashCollected = (clone $paymentQuery)->where('mode', 'Cash')->sum('amount');
        $bankCollected = (clone $paymentQuery)->whereIn('mode', ['Bank', 'Online', 'Cheque'])->sum('amount');
        $totalOutstandingDue = max(0, $totalNetPayable - $totalFeeCollected);

        // 4. Overdue Installments
        $overdueQuery = Installment::whereIn('status', ['pending', 'partial', 'overdue'])
            ->whereDate('due_date', '<', $today);
        if ($selectedBranchId) {
            $overdueQuery->whereHas('admission', fn($q) => $q->where('branch_id', $selectedBranchId));
        }
        $overdueCount = (clone $overdueQuery)->count();
        $overdueAmount = (clone $overdueQuery)->sum('amount');

        $totalStaff = User::where('role', 'staff')->count();
        $totalCalls = CallLog::count();

        // 5. Monthly trends (last 6 months)
        $months = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthKey = $monthDate->format('M Y');
            $months[] = $monthKey;
            $count = (clone $studentQuery)
                ->whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month)
                ->count();
            $monthlyCounts[] = $count;
        }

        // 6. Branch performance comparison
        $branchComparison = Branch::withCount(['students', 'admissions', 'users'])
            ->with(['admissions' => function ($q) {
                $q->with('payments');
            }])
            ->get()
            ->map(function ($b) {
                $collected = $b->admissions->flatMap->payments->where('type', 'payment')->sum('amount');
                $net = $b->admissions->sum('net_payable');
                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'code' => $b->code,
                    'staff_count' => $b->users_count,
                    'inquiries' => $b->students_count,
                    'admissions' => $b->admissions_count,
                    'collected' => $collected,
                    'net' => $net,
                    'due' => max(0, $net - $collected),
                ];
            });

        // 7. Course Distribution
        $courses = Course::withCount(['students', 'admissions'])->get();

        // 8. Status distribution
        $statusDistribution = (clone $studentQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 9. Inquiry sources
        $sourceDistribution = (clone $studentQuery)
            ->select('inquiry_source', DB::raw('count(*) as count'))
            ->groupBy('inquiry_source')
            ->orderBy('count', 'desc')
            ->pluck('count', 'inquiry_source')
            ->toArray();

        // 10. Staff performance
        $staffPerformance = User::where('role', 'staff')
            ->withCount(['students', 'callLogs'])
            ->get();

        // 11. Recent activity feed
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        // 12. Pending follow-ups
        $pendingFollowUps = CallLog::with(['student', 'user'])
            ->whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '<=', $today)
            ->whereHas('student', fn($q) => $q->whereNotIn('status', ['Admitted', 'Lost', 'Not Interested']))
            ->orderBy('next_follow_up_date', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalAdmissions',
            'totalStaff',
            'totalCalls',
            'todayInquiries',
            'conversionRate',
            'totalFeeCollected',
            'cashCollected',
            'bankCollected',
            'totalOutstandingDue',
            'overdueCount',
            'overdueAmount',
            'months',
            'monthlyCounts',
            'statusDistribution',
            'sourceDistribution',
            'staffPerformance',
            'recentActivities',
            'pendingFollowUps',
            'branches',
            'selectedBranchId',
            'branchComparison',
            'courses'
        ));
    }
}
