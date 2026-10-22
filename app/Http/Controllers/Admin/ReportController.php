<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Branch;
use App\Models\CallLog;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Student::with(['creator', 'branch', 'course', 'admission.payments', 'admission.installments'])
            ->withCount('callLogs');

        // Scoping
        if (!$user->isSuperAdmin() && $user->role !== 'admin' && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('created_by', $request->staff_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Payment Plan filter: all, lump_sum, installment, inquiry_only
        if ($request->filled('payment_type')) {
            if ($request->payment_type === 'lump_sum') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'lump_sum'));
            } elseif ($request->payment_type === 'installment') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'installment'));
            } elseif ($request->payment_type === 'inquiry_only') {
                $query->doesntHave('admission');
            }
        }

        // Cloned query for dynamic summary metrics across full filtered set
        $metricsQuery = clone $query;
        $filteredStudents = $metricsQuery->get();

        $totalInquiries = $filteredStudents->count();
        $totalCalls = $filteredStudents->sum('call_logs_count');

        $lumpSumCount = 0;
        $installmentCount = 0;
        $totalNetPayable = 0;
        $totalFeeCollected = 0;

        foreach ($filteredStudents as $s) {
            if ($s->admission) {
                if ($s->admission->payment_type === 'lump_sum') {
                    $lumpSumCount++;
                } elseif ($s->admission->payment_type === 'installment') {
                    $installmentCount++;
                }
                $totalNetPayable += (float) $s->admission->net_payable;
                $totalFeeCollected += (float) $s->admission->total_received;
            }
        }

        $totalAdmitted = $lumpSumCount + $installmentCount;
        $inquiryOnlyCount = max(0, $totalInquiries - $totalAdmitted);
        $totalOutstandingDue = max(0, $totalNetPayable - $totalFeeCollected);
        $conversionRate = $totalInquiries > 0 ? round(($totalAdmitted / $totalInquiries) * 100, 1) : 0;

        // Staff-wise Productivity & Payment Plan Breakdown
        $staffQuery = User::whereIn('role', ['staff', 'admin', 'branch_admin'])->orderBy('name');
        if (!$user->isSuperAdmin() && $user->role !== 'admin' && $user->branch_id) {
            $staffQuery->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $staffQuery->where('branch_id', $request->branch_id);
        }
        $allStaff = $staffQuery->get();

        $staffStats = [];
        foreach ($allStaff as $stf) {
            $stfQ = Student::where('created_by', $stf->id)->with(['admission.payments']);

            if ($request->filled('date_from')) {
                $stfQ->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $stfQ->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('branch_id')) {
                $stfQ->where('branch_id', $request->branch_id);
            }
            if ($request->filled('course_id')) {
                $stfQ->where('course_id', $request->course_id);
            }

            $stfStudents = $stfQ->get();
            $stfTotalInquiries = $stfStudents->count();

            $stfLumpSum = 0;
            $stfInstallment = 0;
            $stfCollected = 0;
            $stfPayable = 0;

            foreach ($stfStudents as $s) {
                if ($s->admission) {
                    if ($s->admission->payment_type === 'lump_sum') {
                        $stfLumpSum++;
                    } elseif ($s->admission->payment_type === 'installment') {
                        $stfInstallment++;
                    }
                    $stfPayable += (float) $s->admission->net_payable;
                    $stfCollected += (float) $s->admission->total_received;
                }
            }

            $stfAdmissions = $stfLumpSum + $stfInstallment;

            $staffStats[] = [
                'user' => $stf,
                'inquiries_count' => $stfTotalInquiries,
                'admissions_count' => $stfAdmissions,
                'lump_sum_count' => $stfLumpSum,
                'installment_count' => $stfInstallment,
                'fee_collected' => $stfCollected,
                'fee_due' => max(0, $stfPayable - $stfCollected),
                'conversion_rate' => $stfTotalInquiries > 0 ? round(($stfAdmissions / $stfTotalInquiries) * 100, 1) : 0,
            ];
        }

        // Sort by inquiries count descending
        usort($staffStats, fn($a, $b) => $b['inquiries_count'] <=> $a['inquiries_count']);

        $students = $query->latest('created_at')->paginate(25)->withQueryString();

        $classes = Setting::defaultClasses();
        $sources = Setting::defaultSources();
        $statuses = Setting::defaultStatuses();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('sort_order')->get();
        $staffMembers = User::whereIn('role', ['staff', 'admin', 'branch_admin'])->orderBy('name')->get();
        $filters = $request->only(['date_from', 'date_to', 'staff_id', 'status', 'class', 'source', 'branch_id', 'course_id', 'payment_type']);

        return view('admin.reports.index', compact(
            'students',
            'filters',
            'classes',
            'sources',
            'statuses',
            'branches',
            'courses',
            'staffMembers',
            'totalInquiries',
            'totalAdmitted',
            'lumpSumCount',
            'installmentCount',
            'inquiryOnlyCount',
            'totalCalls',
            'conversionRate',
            'totalNetPayable',
            'totalFeeCollected',
            'totalOutstandingDue',
            'staffStats'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $query = Student::with(['creator', 'branch', 'course', 'admission.payments'])->withCount('callLogs');

        if (!$user->isSuperAdmin() && $user->role !== 'admin' && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('created_by', $request->staff_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('payment_type')) {
            if ($request->payment_type === 'lump_sum') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'lump_sum'));
            } elseif ($request->payment_type === 'installment') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'installment'));
            } elseif ($request->payment_type === 'inquiry_only') {
                $query->doesntHave('admission');
            }
        }

        $students = $query->latest('created_at')->get();

        $filename = 'EduTrack_Detailed_Report_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Inquiry ID',
                'Campus Branch',
                'Student Name',
                'Parent Name',
                'Phone Number',
                'Area / Locality',
                'Course / Program',
                'Inquiry Status',
                'Handled By (Staff)',
                'Payment Mode',
                'Total Fee Package',
                'Amount Received',
                'Remaining Due',
                'Date Created',
                'Exact Time',
            ]);

            foreach ($students as $student) {
                $paymentMode = 'Inquiry Only';
                $package = 0;
                $received = 0;
                $remaining = 0;

                if ($student->admission) {
                    $paymentMode = $student->admission->payment_type === 'installment' ? 'Installment Plan' : 'Lump Sum (Full Paid)';
                    $package = $student->admission->net_payable;
                    $received = $student->admission->total_received;
                    $remaining = $student->admission->remaining;
                }

                fputcsv($handle, [
                    $student->id,
                    $student->branch?->name ?? 'Main',
                    $student->student_name,
                    $student->parent_name ?: 'N/A',
                    $student->phone_number,
                    $student->city ?: 'N/A',
                    $student->course?->name ?? $student->class_applying_for,
                    $student->status,
                    $student->creator ? $student->creator->name : 'N/A',
                    $paymentMode,
                    $package,
                    $received,
                    $remaining,
                    $student->created_at->format('Y-m-d'),
                    $student->created_at->format('h:i:s A'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function print(Request $request): View
    {
        $user = Auth::user();
        $query = Student::with(['creator', 'branch', 'course', 'admission.payments', 'admission.installments'])->withCount('callLogs');

        if (!$user->isSuperAdmin() && $user->role !== 'admin' && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('created_by', $request->staff_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('payment_type')) {
            if ($request->payment_type === 'lump_sum') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'lump_sum'));
            } elseif ($request->payment_type === 'installment') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'installment'));
            } elseif ($request->payment_type === 'inquiry_only') {
                $query->doesntHave('admission');
            }
        }

        $filters = $request->only(['date_from', 'date_to', 'staff_id', 'status', 'class', 'source', 'branch_id', 'course_id', 'payment_type']);
        $students = $query->latest('created_at')->get();

        $schoolName = Setting::get('school_name', config('app.name', 'EduTrack CRM Institute'));

        return view('admin.reports.print', compact('students', 'filters', 'schoolName'));
    }

    public function staffReport(Request $request, User $user): View
    {
        $authUser = Auth::user();
        if (!$authUser->isSuperAdmin() && $authUser->role !== 'admin' && $authUser->branch_id && $user->branch_id !== $authUser->branch_id) {
            abort(403, 'Unauthorized access to other branch staff.');
        }

        $query = Student::where('created_by', $user->id)
            ->with(['creator', 'branch', 'course', 'admission.payments', 'admission.installments'])
            ->withCount('callLogs');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('payment_type')) {
            if ($request->payment_type === 'lump_sum') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'lump_sum'));
            } elseif ($request->payment_type === 'installment') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'installment'));
            } elseif ($request->payment_type === 'inquiry_only') {
                $query->doesntHave('admission');
            }
        }

        $metricsQuery = clone $query;
        $allStaffStudents = $metricsQuery->get();

        $totalInquiries = $allStaffStudents->count();
        $lumpSumCount = 0;
        $installmentCount = 0;
        $totalNetPayable = 0;
        $totalFeeCollected = 0;

        foreach ($allStaffStudents as $s) {
            if ($s->admission) {
                if ($s->admission->payment_type === 'lump_sum') {
                    $lumpSumCount++;
                } elseif ($s->admission->payment_type === 'installment') {
                    $installmentCount++;
                }
                $totalNetPayable += (float) $s->admission->net_payable;
                $totalFeeCollected += (float) $s->admission->total_received;
            }
        }

        $totalAdmitted = $lumpSumCount + $installmentCount;
        $inquiryOnlyCount = max(0, $totalInquiries - $totalAdmitted);
        $totalOutstandingDue = max(0, $totalNetPayable - $totalFeeCollected);
        $conversionRate = $totalInquiries > 0 ? round(($totalAdmitted / $totalInquiries) * 100, 1) : 0;

        $students = $query->latest('created_at')->paginate(25)->withQueryString();
        $courses = Course::where('status', 'active')->orderBy('sort_order')->get();
        $filters = $request->only(['date_from', 'date_to', 'course_id', 'payment_type']);

        return view('admin.reports.staff', compact(
            'user',
            'students',
            'courses',
            'filters',
            'totalInquiries',
            'totalAdmitted',
            'lumpSumCount',
            'installmentCount',
            'inquiryOnlyCount',
            'totalNetPayable',
            'totalFeeCollected',
            'totalOutstandingDue',
            'conversionRate'
        ));
    }

    public function printStaffReport(Request $request, User $user): View
    {
        $authUser = Auth::user();
        if (!$authUser->isSuperAdmin() && $authUser->role !== 'admin' && $authUser->branch_id && $user->branch_id !== $authUser->branch_id) {
            abort(403, 'Unauthorized access to other branch staff.');
        }

        $query = Student::where('created_by', $user->id)
            ->with(['creator', 'branch', 'course', 'admission.payments', 'admission.installments'])
            ->withCount('callLogs');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('payment_type')) {
            if ($request->payment_type === 'lump_sum') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'lump_sum'));
            } elseif ($request->payment_type === 'installment') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'installment'));
            } elseif ($request->payment_type === 'inquiry_only') {
                $query->doesntHave('admission');
            }
        }

        $students = $query->latest('created_at')->get();

        $totalInquiries = $students->count();
        $lumpSumCount = 0;
        $installmentCount = 0;
        $totalNetPayable = 0;
        $totalFeeCollected = 0;

        foreach ($students as $s) {
            if ($s->admission) {
                if ($s->admission->payment_type === 'lump_sum') {
                    $lumpSumCount++;
                } elseif ($s->admission->payment_type === 'installment') {
                    $installmentCount++;
                }
                $totalNetPayable += (float) $s->admission->net_payable;
                $totalFeeCollected += (float) $s->admission->total_received;
            }
        }

        $totalAdmitted = $lumpSumCount + $installmentCount;
        $inquiryOnlyCount = max(0, $totalInquiries - $totalAdmitted);
        $totalOutstandingDue = max(0, $totalNetPayable - $totalFeeCollected);

        $schoolName = Setting::get('school_name', config('app.name', 'EduTrack CRM Institute'));
        $filters = $request->only(['date_from', 'date_to', 'course_id', 'payment_type']);

        return view('admin.reports.staff_print', compact(
            'user',
            'students',
            'schoolName',
            'filters',
            'totalInquiries',
            'totalAdmitted',
            'lumpSumCount',
            'installmentCount',
            'inquiryOnlyCount',
            'totalNetPayable',
            'totalFeeCollected',
            'totalOutstandingDue'
        ));
    }

    public function exportStaffCsv(Request $request, User $user): StreamedResponse
    {
        $authUser = Auth::user();
        if (!$authUser->isSuperAdmin() && $authUser->role !== 'admin' && $authUser->branch_id && $user->branch_id !== $authUser->branch_id) {
            abort(403, 'Unauthorized access to other branch staff.');
        }

        $query = Student::where('created_by', $user->id)
            ->with(['creator', 'branch', 'course', 'admission.payments'])
            ->withCount('callLogs');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('payment_type')) {
            if ($request->payment_type === 'lump_sum') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'lump_sum'));
            } elseif ($request->payment_type === 'installment') {
                $query->whereHas('admission', fn($q) => $q->where('payment_type', 'installment'));
            } elseif ($request->payment_type === 'inquiry_only') {
                $query->doesntHave('admission');
            }
        }

        $students = $query->latest('created_at')->get();
        $safeStaffName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name);
        $filename = "Staff_Report_{$safeStaffName}_" . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students, $user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Inquiry ID',
                'Counselor / Handled By',
                'Campus Branch',
                'Student Name',
                'Parent Name',
                'Phone Number',
                'Area / Locality',
                'Course / Program',
                'Status',
                'Payment Mode',
                'Fee Package (PKR)',
                'Amount Received (PKR)',
                'Remaining Due (PKR)',
                'Date Created',
                'Exact Time',
            ]);

            foreach ($students as $student) {
                $paymentMode = 'Inquiry Only';
                $package = 0;
                $received = 0;
                $remaining = 0;

                if ($student->admission) {
                    $paymentMode = $student->admission->payment_type === 'installment' ? 'Installment Plan' : 'Lump Sum (Full Paid)';
                    $package = $student->admission->net_payable;
                    $received = $student->admission->total_received;
                    $remaining = $student->admission->remaining;
                }

                fputcsv($handle, [
                    $student->id,
                    $user->name,
                    $student->branch?->name ?? 'Main',
                    $student->student_name,
                    $student->parent_name ?: 'N/A',
                    $student->phone_number,
                    $student->city ?: 'N/A',
                    $student->course?->name ?? $student->class_applying_for,
                    $student->status,
                    $paymentMode,
                    $package,
                    $received,
                    $remaining,
                    $student->created_at->format('Y-m-d'),
                    $student->created_at->format('h:i:s A'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
