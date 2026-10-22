<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $query = Branch::withCount(['users', 'students', 'admissions'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('address', 'like', "%{$s}%");
            });
        }

        $branches = $query->paginate(15)->withQueryString();

        return view('admin.branches.index', compact('branches'));
    }

    public function show(Branch $branch): View
    {
        $staffMembers = $branch->users()
            ->withCount(['students', 'callLogs'])
            ->orderBy('name')
            ->get();

        $staffStats = [];
        foreach ($staffMembers as $staff) {
            $students = \App\Models\Student::where('created_by', $staff->id)
                ->where('branch_id', $branch->id)
                ->with(['admission.payments', 'course'])
                ->get();

            $inquiriesCount = $students->count();
            $admissionsCount = $students->filter(fn($s) => $s->admission !== null)->count();
            $lumpSumCount = $students->filter(fn($s) => $s->admission && $s->admission->payment_type === 'lump_sum')->count();
            $installmentCount = $students->filter(fn($s) => $s->admission && $s->admission->payment_type === 'installment')->count();
            $feeCollected = $students->sum(fn($s) => $s->admission ? (float)$s->admission->total_received : 0);
            $feeDue = $students->sum(fn($s) => $s->admission ? (float)$s->admission->remaining : 0);

            $staffStats[] = [
                'user' => $staff,
                'inquiries_count' => $inquiriesCount,
                'admissions_count' => $admissionsCount,
                'lump_sum_count' => $lumpSumCount,
                'installment_count' => $installmentCount,
                'fee_collected' => $feeCollected,
                'fee_due' => $feeDue,
                'conversion_rate' => $inquiriesCount > 0 ? round(($admissionsCount / $inquiriesCount) * 100, 1) : 0,
            ];
        }

        $totalInquiries = $branch->students()->count();
        $totalAdmissions = $branch->admissions()->count();
        $totalFeeCollected = $branch->admissions()->with('payments')->get()->flatMap->payments->where('type', 'payment')->sum('amount');
        $totalNetPayable = $branch->admissions()->sum('net_payable');
        $totalOutstandingDue = max(0, $totalNetPayable - $totalFeeCollected);

        return view('admin.branches.show', compact(
            'branch',
            'staffMembers',
            'staffStats',
            'totalInquiries',
            'totalAdmissions',
            'totalFeeCollected',
            'totalOutstandingDue'
        ));
    }

    public function create(): View
    {
        return view('admin.branches.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:branches,code', 'alpha_num'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $branch = Branch::create($request->only('name', 'code', 'address', 'phone', 'email', 'status'));

        ActivityLog::record(
            'created_branch',
            "Created new branch: {$branch->name} ({$branch->code})",
            ['branch_id' => $branch->id]
        );

        return redirect()->route('admin.branches.index')
            ->with('success', "Branch '{$branch->name}' created successfully.");
    }

    public function edit(Branch $branch): View
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', Rule::unique('branches')->ignore($branch->id), 'alpha_num'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $branch->update($request->only('name', 'code', 'address', 'phone', 'email', 'status'));

        ActivityLog::record(
            'updated_branch',
            "Updated branch: {$branch->name}",
            ['branch_id' => $branch->id]
        );

        return redirect()->route('admin.branches.index')
            ->with('success', "Branch '{$branch->name}' updated successfully.");
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        if ($branch->users()->count() > 0 || $branch->students()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete branch with existing users or inquiries. Deactivate it instead.');
        }

        $name = $branch->name;
        $branch->delete();

        ActivityLog::record('deleted_branch', "Deleted branch: {$name}");

        return redirect()->route('admin.branches.index')
            ->with('success', "Branch '{$name}' deleted successfully.");
    }
}
