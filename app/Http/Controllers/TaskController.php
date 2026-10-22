<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\CallLog;
use App\Models\Installment;
use App\Models\Student;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. Synchronize automated tasks from inquiries and pending installments
        $this->syncAutomatedTasks($user);

        // 2. Query user / branch tasks
        $query = Task::with(['user', 'branch'])->latest('due_date');

        if (!$user->isSuperAdmin() && $user->role !== 'admin') {
            if ($user->branch_id) {
                $query->where(function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id)
                      ->orWhere('user_id', $user->id);
                });
            } else {
                $query->where('user_id', $user->id);
            }
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $filter = $request->get('tab', 'today');

        if ($filter === 'today') {
            $query->where('status', 'pending')->whereDate('due_date', '<=', $today);
        } elseif ($filter === 'overdue') {
            $query->where('status', 'pending')->whereDate('due_date', '<', $today);
        } elseif ($filter === 'upcoming') {
            $query->where('status', 'pending')->whereDate('due_date', '>', $today);
        } elseif ($filter === 'completed') {
            $query->where('status', 'completed');
        }

        $tasks = $query->paginate(20)->withQueryString();

        // Count metrics
        $baseQuery = Task::query();
        if (!$user->isSuperAdmin() && $user->role !== 'admin' && $user->branch_id) {
            $baseQuery->where('branch_id', $user->branch_id);
        }
        $todayCount = (clone $baseQuery)->where('status', 'pending')->whereDate('due_date', '<=', $today)->count();
        $overdueCount = (clone $baseQuery)->where('status', 'pending')->whereDate('due_date', '<', $today)->count();
        $upcomingCount = (clone $baseQuery)->where('status', 'pending')->whereDate('due_date', '>', $today)->count();
        $completedCount = (clone $baseQuery)->where('status', 'completed')->count();

        $branches = Branch::where('status', 'active')->orderBy('name')->get();

        return view('tasks.index', compact(
            'tasks',
            'filter',
            'todayCount',
            'overdueCount',
            'upcomingCount',
            'completedCount',
            'branches'
        ));
    }

    public function complete(Task $task): RedirectResponse
    {
        $task->markCompleted();

        return redirect()->back()->with('success', "Task '{$task->title}' marked as completed.");
    }

    public function reschedule(Request $request, Task $task): RedirectResponse
    {
        $request->validate([
            'due_date' => ['required', 'date'],
        ]);

        $task->reschedule($request->due_date);

        return redirect()->back()->with('success', "Task rescheduled to {$request->due_date}.");
    }

    /**
     * Auto-sync tasks from Students with followups and Installments with upcoming/overdue payments.
     */
    private function syncAutomatedTasks($user): void
    {
        $today = Carbon::today();

        // 1. Sync student followups
        $students = Student::whereNotNull('next_followup_date')
            ->whereNotIn('status', ['Admitted', 'Lost', 'Not Interested'])
            ->whereDate('next_followup_date', '<=', $today->copy()->addDays(7))
            ->get();

        foreach ($students as $stu) {
            $exists = Task::where('type', 'followup')
                ->where('ref_id', $stu->id)
                ->where('ref_type', Student::class)
                ->where('status', 'pending')
                ->exists();

            if (!$exists) {
                Task::create([
                    'user_id' => $stu->created_by ?? $user->id,
                    'branch_id' => $stu->branch_id ?? $user->branch_id,
                    'type' => 'followup',
                    'ref_id' => $stu->id,
                    'ref_type' => Student::class,
                    'title' => "Follow-up with {$stu->student_name} ({$stu->phone_number})",
                    'description' => "Course: " . ($stu->course?->name ?? $stu->class_applying_for ?? 'General') . " - Status: {$stu->status}",
                    'due_date' => $stu->next_followup_date,
                    'status' => 'pending',
                ]);
            }
        }

        // 2. Sync installment alerts
        $installments = Installment::with('admission')
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->whereDate('due_date', '<=', $today->copy()->addDays(7))
            ->get();

        foreach ($installments as $inst) {
            $exists = Task::where('type', 'installment')
                ->where('ref_id', $inst->id)
                ->where('ref_type', Installment::class)
                ->where('status', 'pending')
                ->exists();

            if (!$exists && $inst->admission) {
                $isOverdue = Carbon::parse($inst->due_date)->lt($today);
                $titlePrefix = $isOverdue ? '⚠️ Overdue Installment' : 'Fee Installment Due';

                Task::create([
                    'user_id' => $inst->admission->created_by ?? $user->id,
                    'branch_id' => $inst->admission->branch_id,
                    'type' => 'installment',
                    'ref_id' => $inst->id,
                    'ref_type' => Installment::class,
                    'title' => "{$titlePrefix} #{$inst->seq}: {$inst->admission->name} (Roll: {$inst->admission->roll_no})",
                    'description' => "Amount: PKR " . number_format($inst->amount) . " | Due Date: {$inst->due_date} | Phone: {$inst->admission->phone}",
                    'due_date' => $inst->due_date,
                    'status' => 'pending',
                ]);
            }
        }
    }
}
