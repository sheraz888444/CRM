<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\CallLog;
use App\Models\Course;
use App\Models\InquiryMode;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Student::with(['creator', 'callLogs', 'branch', 'course', 'inquiryMode'])
            ->withCount('callLogs');

        // Multi-branch scoping
        if (!$user->isSuperAdmin() && $user->role !== 'admin') {
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Apply global search query if present
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Apply course filter
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Apply filters
        $filters = $request->only(['status', 'class', 'source', 'staff_id', 'date_from', 'date_to', 'branch_id', 'course_id']);
        $query->filter($filters);

        $students = $query->latest()->paginate(15)->withQueryString();

        $classes = Setting::defaultClasses();
        $sources = Setting::defaultSources();
        $statuses = Setting::defaultStatuses();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('sort_order')->get();
        $inquiryModes = InquiryMode::all();
        $staffMembers = User::where('role', 'staff')->get();

        return view('students.index', compact(
            'students',
            'classes',
            'sources',
            'statuses',
            'branches',
            'courses',
            'inquiryModes',
            'staffMembers',
            'filters'
        ));
    }

    public function create(): View
    {
        $user = Auth::user();
        $classes = Setting::defaultClasses();
        $sources = Setting::defaultSources();
        $statuses = Setting::defaultStatuses();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('sort_order')->get();
        $inquiryModes = InquiryMode::all();

        return view('students.create', compact('classes', 'sources', 'statuses', 'branches', 'courses', 'inquiryModes', 'user'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['created_by'] = $user->id;

        // Default branch_id if not explicitly provided
        if (empty($data['branch_id'])) {
            $data['branch_id'] = $user->branch_id;
        }

        // If class_applying_for not given but course_id is selected, populate from course name
        if (empty($data['class_applying_for']) && !empty($data['course_id'])) {
            $c = Course::find($data['course_id']);
            $data['class_applying_for'] = $c?->name ?? 'Course Inquiry';
        } elseif (empty($data['class_applying_for'])) {
            $data['class_applying_for'] = 'General Inquiry';
        }

        // If inquiry_source empty but mode_id present, populate from mode
        if (empty($data['inquiry_source']) && !empty($data['mode_id'])) {
            $m = InquiryMode::find($data['mode_id']);
            $data['inquiry_source'] = $m?->name ?? 'Direct';
        }

        $student = Student::create($data);

        // Auto-create initial contact record in CallLog
        $initialType = match ($student->inquiry_source) {
            'Walk-in', 'Visit' => 'Walk-in',
            'WhatsApp' => 'WhatsApp',
            default => 'Incoming',
        };

        CallLog::create([
            'student_id' => $student->id,
            'user_id' => $user->id,
            'call_type' => $initialType,
            'call_date' => Carbon::today()->toDateString(),
            'call_time' => Carbon::now()->format('H:i'),
            'duration' => 'Initial inquiry',
            'remarks' => $student->notes ?: 'Initial inquiry received via ' . ($student->inquiry_source ?: 'phone') . '.',
            'next_follow_up_date' => $student->next_followup_date ?? Carbon::today()->addDays(2)->toDateString(),
            'status_after_call' => $student->status,
        ]);

        // Auto-create daily task if follow-up date is set
        if ($student->next_followup_date) {
            Task::create([
                'user_id' => $user->id,
                'branch_id' => $student->branch_id,
                'type' => 'followup',
                'ref_id' => $student->id,
                'ref_type' => Student::class,
                'title' => "Follow-up with {$student->student_name} ({$student->phone_number})",
                'description' => "Course: " . ($student->course?->name ?? $student->class_applying_for ?? 'Inquiry'),
                'due_date' => $student->next_followup_date,
                'status' => 'pending',
            ]);
        }

        ActivityLog::record(
            'created_student',
            "Added student inquiry: {$student->student_name} ({$student->phone_number})",
            ['student_id' => $student->id, 'branch_id' => $student->branch_id]
        );

        return redirect()->route('students.show', $student)
            ->with('success', "Inquiry for {$student->student_name} saved successfully!");
    }

    public function show(Student $student): View
    {
        $student->load(['creator', 'branch', 'course', 'inquiryMode', 'admission', 'callLogs.user']);

        $totalContacts = $student->callLogs->count();
        $firstContact = $student->callLogs->sortBy('call_date')->first();
        $lastContact = $student->callLogs->sortByDesc('call_date')->first();
        $statuses = Setting::defaultStatuses();

        return view('students.show', compact(
            'student',
            'totalContacts',
            'firstContact',
            'lastContact',
            'statuses'
        ));
    }

    public function edit(Student $student): View
    {
        $classes = Setting::defaultClasses();
        $sources = Setting::defaultSources();
        $statuses = Setting::defaultStatuses();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('status', 'active')->orderBy('sort_order')->get();
        $inquiryModes = InquiryMode::all();

        return view('students.edit', compact('student', 'classes', 'sources', 'statuses', 'branches', 'courses', 'inquiryModes'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['class_applying_for']) && !empty($data['course_id'])) {
            $c = Course::find($data['course_id']);
            $data['class_applying_for'] = $c?->name ?? 'Course Inquiry';
        }

        $student->update($data);

        // Update or create follow-up task if date changed
        if ($student->next_followup_date) {
            Task::updateOrCreate(
                [
                    'type' => 'followup',
                    'ref_id' => $student->id,
                    'ref_type' => Student::class,
                    'status' => 'pending',
                ],
                [
                    'user_id' => Auth::id(),
                    'branch_id' => $student->branch_id,
                    'title' => "Follow-up with {$student->student_name} ({$student->phone_number})",
                    'description' => "Course: " . ($student->course?->name ?? $student->class_applying_for),
                    'due_date' => $student->next_followup_date,
                ]
            );
        }

        ActivityLog::record(
            'updated_student',
            "Updated inquiry details for {$student->student_name}",
            ['student_id' => $student->id]
        );

        return redirect()->route('students.show', $student)
            ->with('success', "Student record for {$student->student_name} updated successfully.");
    }


    /**
     * AJAX endpoint to check if phone number already exists
     */
    public function checkDuplicate(Request $request): JsonResponse
    {
        $phone = $request->query('phone');
        if (!$phone || strlen(trim($phone)) < 6) {
            return response()->json(['exists' => false]);
        }

        $cleanPhone = Student::normalizePhone($phone);
        $excludeId = $request->query('exclude_id');

        $query = Student::with(['creator', 'branch', 'course', 'callLogs' => function ($q) {
            $q->orderBy('call_date', 'asc')->orderBy('call_time', 'asc');
        }]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $existing = $query->where(function ($q) use ($phone, $cleanPhone) {
            $q->where('phone_number', $phone);
            if (!empty($cleanPhone)) {
                $q->orWhere('phone_normalized', $cleanPhone)
                  ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone_number, ' ', ''), '-', ''), '+', ''), '(', '') LIKE ?", ["%{$cleanPhone}%"]);
            }
        })->first();

        if ($existing) {
            $firstCall = $existing->callLogs->first();
            $firstContactDate = $firstCall ? $firstCall->call_date->format('M d, Y') . ' at ' . $firstCall->call_time : $existing->created_at->format('M d, Y h:i A');
            $staffName = $existing->creator ? $existing->creator->name : ($firstCall && $firstCall->user ? $firstCall->user->name : 'Staff');

            return response()->json([
                'exists' => true,
                'student' => [
                    'id' => $existing->id,
                    'student_name' => $existing->student_name,
                    'parent_name' => $existing->parent_name,
                    'phone_number' => $existing->phone_number,
                    'branch_name' => $existing->branch?->name ?? 'Main',
                    'course_name' => $existing->course?->name ?? $existing->class_applying_for,
                    'class_applying_for' => $existing->class_applying_for,
                    'status' => $existing->status,
                    'total_contacts' => $existing->callLogs->count(),
                    'first_contact' => $firstContactDate,
                    'staff_name' => $staffName,
                    'url' => route('students.show', $existing),
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Live search AJAX suggestions for global header search
     */
    public function liveSearch(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $students = Student::search($q)
            ->with(['creator', 'branch', 'course', 'callLogs' => function ($q) {
                $q->latest('call_date')->latest('call_time')->take(1);
            }])
            ->withCount('callLogs')
            ->take(8)
            ->get();

        $results = $students->map(function ($s) {
            $lastCall = $s->callLogs->first();
            return [
                'id' => $s->id,
                'student_name' => $s->student_name,
                'parent_name' => $s->parent_name ?: 'Parent not specified',
                'phone_number' => $s->phone_number,
                'branch_name' => $s->branch?->name ?? 'Main Campus',
                'branch_code' => $s->branch?->code ?? 'B1',
                'class_applying_for' => $s->course?->name ?? $s->class_applying_for,
                'status' => $s->status,
                'badge' => $s->status_badge,
                'total_contacts' => $s->call_logs_count,
                'last_call_notes' => $lastCall ? $lastCall->remarks : null,
                'last_call_date' => $lastCall ? $lastCall->call_date?->format('d M, Y') : null,
                'creator' => $s->creator ? $s->creator->name : 'Staff',
                'url' => route('students.show', $s),
                'admit_url' => route('admissions.create', $s),
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function destroy(Student $student): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->role !== 'admin' && $user->branch_id && $student->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to delete inquiry of another branch.');
        }

        $name = $student->student_name;

        // If linked admission exists, delete it too
        if ($student->admission) {
            $student->admission->delete();
        }

        // Clean up pending tasks
        Task::where('ref_id', $student->id)->where('ref_type', Student::class)->delete();

        $student->delete();

        ActivityLog::record(
            'deleted_student',
            "Deleted student inquiry: {$name}",
            ['student_id' => $student->id]
        );

        return redirect()->route('students.index')
            ->with('success', "Student inquiry for '{$name}' deleted successfully.");
    }
}
