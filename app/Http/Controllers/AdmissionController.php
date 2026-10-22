<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Admission;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Admission::with(['branch', 'courses', 'payments', 'installments'])->latest();

        // Branch scoping
        if (!$user->isSuperAdmin() && $user->role !== 'admin') {
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search by Roll No, Name, Phone
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('roll_no', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('father_name', 'like', "%{$s}%");
            });
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('courses', function ($q) use ($request) {
                $q->where('courses.id', $request->course_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $admissions = $query->paginate(15)->withQueryString();

        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('sort_order')->get();

        return view('admissions.index', compact('admissions', 'branches', 'courses'));
    }

    public function create(Request $request, ?Student $student = null): View
    {
        $user = Auth::user();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('sort_order')->get();

        // If student is passed via query param or route
        $prefillStudent = $student;
        if (!$prefillStudent && $request->filled('student_id')) {
            $prefillStudent = Student::find($request->student_id);
        }
        if (!$prefillStudent && $request->filled('inquiry_id')) {
            $prefillStudent = Student::find($request->inquiry_id);
        }

        return view('admissions.create', compact('branches', 'courses', 'prefillStudent', 'user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'alt_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'courses' => ['required', 'array', 'min:1'],
            'courses.*' => ['exists:courses,id'],
            'total_package' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_type' => ['required', 'in:lump_sum,installment'],
            'admission_date' => ['required', 'date'],
            'inquiry_id' => ['nullable', 'exists:students,id'],
            'initial_payment' => ['nullable', 'numeric', 'min:0'],
            'payment_mode' => ['nullable', 'string', 'in:Cash,Bank,Online,Cheque'],
            // Installments array if installment plan
            'installments' => ['nullable', 'array'],
            'installments.*.amount' => ['nullable', 'numeric', 'min:0'],
            'installments.*.due_date' => ['nullable', 'date'],
        ]);

        return DB::transaction(function () use ($request) {
            $branch = Branch::findOrFail($request->branch_id);
            $rollNo = $branch->generateNextRollNo();

            $totalPackage = (float) $request->total_package;
            $discount = (float) ($request->discount ?? 0);
            $netPayable = max(0, $totalPackage - $discount);

            $admission = Admission::create([
                'roll_no' => $rollNo,
                'inquiry_id' => $request->inquiry_id,
                'branch_id' => $branch->id,
                'created_by' => Auth::id(),
                'name' => $request->name,
                'father_name' => $request->father_name,
                'phone' => $request->phone,
                'phone_normalized' => Admission::normalizePhone($request->phone),
                'alt_phone' => $request->alt_phone,
                'email' => $request->email,
                'address' => $request->address,
                'qualification' => $request->qualification,
                'total_package' => $totalPackage,
                'discount' => $discount,
                'net_payable' => $netPayable,
                'payment_type' => $request->payment_type,
                'status' => 'active',
                'admission_date' => $request->admission_date,
            ]);

            // Sync courses
            $admission->courses()->sync($request->courses);

            // Create installments if installment mode
            if ($request->payment_type === 'installment' && !empty($request->installments)) {
                $seq = 1;
                foreach ($request->installments as $inst) {
                    if (!empty($inst['amount']) && !empty($inst['due_date'])) {
                        Installment::create([
                            'admission_id' => $admission->id,
                            'seq' => $seq++,
                            'amount' => (float) $inst['amount'],
                            'due_date' => $inst['due_date'],
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            // Handle initial payment if provided
            $initialPayment = (float) ($request->initial_payment ?? 0);
            if ($initialPayment > 0) {
                // If installments exist, link to first installment
                $firstInst = $admission->installments()->orderBy('seq')->first();

                Payment::create([
                    'admission_id' => $admission->id,
                    'installment_id' => $firstInst?->id,
                    'amount' => $initialPayment,
                    'payment_date' => $request->admission_date ?? Carbon::today()->toDateString(),
                    'mode' => $request->payment_mode ?? 'Cash',
                    'received_by' => Auth::id(),
                    'type' => 'payment',
                    'remarks' => 'Initial down-payment at admission registration',
                ]);

                // Update installment status if linked
                if ($firstInst) {
                    $firstInst->updateStatus();
                }
            }

            // Update linked inquiry status to Admitted
            if ($request->inquiry_id) {
                $inquiry = Student::find($request->inquiry_id);
                if ($inquiry) {
                    $inquiry->update(['status' => 'Admitted']);
                }
            }

            ActivityLog::record(
                'created_admission',
                "Registered new admission: {$admission->name} (Roll: {$admission->roll_no}) at {$branch->name}",
                ['admission_id' => $admission->id, 'roll_no' => $admission->roll_no]
            );

            return redirect()->route('admissions.show', $admission)
                ->with('success', "Admission successfully created! Assigned Roll No: {$admission->roll_no}");
        });
    }

    public function show(Admission $admission): View
    {
        $admission->load(['branch', 'courses', 'payments.receiver', 'installments.payments', 'inquiry', 'creator']);

        return view('admissions.show', compact('admission'));
    }

    public function edit(Admission $admission): View
    {
        $admission->load(['branch', 'courses']);
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('sort_order')->get();

        return view('admissions.edit', compact('admission', 'branches', 'courses'));
    }

    public function update(Request $request, Admission $admission): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'alt_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'courses' => ['required', 'array', 'min:1'],
            'courses.*' => ['exists:courses,id'],
            'total_package' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,completed,cancelled'],
        ]);

        $totalPackage = (float) $request->total_package;
        $discount = (float) ($request->discount ?? 0);
        $netPayable = max(0, $totalPackage - $discount);

        $admission->update([
            'name' => $request->name,
            'father_name' => $request->father_name,
            'phone' => $request->phone,
            'phone_normalized' => Admission::normalizePhone($request->phone),
            'alt_phone' => $request->alt_phone,
            'email' => $request->email,
            'address' => $request->address,
            'qualification' => $request->qualification,
            'total_package' => $totalPackage,
            'discount' => $discount,
            'net_payable' => $netPayable,
            'status' => $request->status,
        ]);

        $admission->courses()->sync($request->courses);

        ActivityLog::record(
            'updated_admission',
            "Updated admission details for Roll No: {$admission->roll_no}",
            ['admission_id' => $admission->id]
        );

        return redirect()->route('admissions.show', $admission)
            ->with('success', "Admission {$admission->roll_no} updated successfully.");
    }

    public function destroy(Admission $admission): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->role !== 'admin' && $user->branch_id && $admission->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to delete admission of another branch.');
        }

        $name = $admission->name;
        $rollNo = $admission->roll_no;

        // If linked inquiry exists, update status
        if ($admission->inquiry) {
            $admission->inquiry->update(['status' => 'Lost']);
        }

        // Clean up pending tasks
        Task::where('ref_id', $admission->id)->where('ref_type', Admission::class)->delete();

        $admission->delete();

        ActivityLog::record(
            'deleted_admission',
            "Deleted admission record for: {$name} (Roll No: {$rollNo})",
            ['admission_id' => $admission->id, 'roll_no' => $rollNo]
        );

        return redirect()->route('admissions.index')
            ->with('success', "Admission record for '{$name}' (Roll No: {$rollNo}) deleted successfully.");
    }
}
