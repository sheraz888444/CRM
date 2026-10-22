<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PhoneSearchController extends Controller
{
    /**
     * Main phone search page (§2).
     */
    public function index(): View
    {
        return view('search.index');
    }

    /**
     * AJAX: Search by phone number across all branches.
     */
    public function searchByPhone(Request $request): JsonResponse
    {
        $phone = trim($request->query('phone', ''));

        if (strlen($phone) < 4) {
            return response()->json(['found' => false, 'results' => []]);
        }

        $normalized = Student::normalizePhone($phone);

        // Search inquiries by normalized phone
        $inquiries = Student::with(['creator', 'branch', 'course', 'inquiryMode', 'callLogs.user', 'admission.courses', 'admission.payments'])
            ->where(function ($q) use ($phone, $normalized) {
                $q->where('phone_normalized', $normalized);
                if (!empty($phone)) {
                    $q->orWhere('phone_number', 'like', "%{$phone}%");
                }
            })
            ->get();

        // Also search admissions by normalized phone
        $admissions = Admission::with(['branch', 'courses', 'installments', 'payments', 'creator'])
            ->where(function ($q) use ($phone, $normalized) {
                $q->where('phone_normalized', $normalized);
                if (!empty($phone)) {
                    $q->orWhere('phone', 'like', "%{$phone}%");
                }
            })
            ->get();

        if ($inquiries->isEmpty() && $admissions->isEmpty()) {
            return response()->json([
                'found' => false,
                'phone' => $phone,
                'normalized' => $normalized,
                'results' => [],
            ]);
        }

        $results = [];

        foreach ($inquiries as $inquiry) {
            $admission = $inquiry->admission;
            $results[] = [
                'type' => $admission ? 'admitted' : 'inquiry',
                'inquiry_id' => $inquiry->id,
                'admission_id' => $admission?->id,
                'name' => $inquiry->student_name,
                'phone' => $inquiry->phone_number,
                'status' => $inquiry->status,
                'status_badge' => $inquiry->status_badge,
                'course' => $inquiry->course?->name ?? $inquiry->class_applying_for,
                'branch' => $inquiry->branch?->name ?? 'N/A',
                'branch_code' => $inquiry->branch?->code ?? '',
                'officer' => $inquiry->creator?->name ?? 'Staff',
                'created_at' => $inquiry->created_at->format('M d, Y'),
                'total_calls' => $inquiry->callLogs->count(),
                'call_history' => $inquiry->callLogs->take(5)->map(fn($c) => [
                    'date' => $c->call_date->format('M d'),
                    'type' => $c->call_type,
                    'remarks' => $c->remarks,
                    'user' => $c->user?->name,
                ]),
                'inquiry_url' => route('students.show', $inquiry),
                'admission_url' => $admission ? route('admissions.show', $admission) : null,
                'roll_no' => $admission?->roll_no,
                'total_package' => $admission?->net_payable,
                'total_received' => $admission?->total_received,
                'remaining' => $admission?->remaining,
                'fee_status' => $admission?->fee_status,
            ];
        }

        return response()->json([
            'found' => true,
            'phone' => $phone,
            'normalized' => $normalized,
            'results' => $results,
        ]);
    }
}
