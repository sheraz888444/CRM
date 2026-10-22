@extends('layouts.app')

@section('title', 'Executive Analytics - EduTrack CRM')
@section('page_title', 'Institute Executive Dashboard')
@section('page_subtitle', 'Multi-campus admissions overview, inquiry volume, fee collections, and staff productivity')

@section('content')
<div class="space-y-8">

    <!-- Top Branch Filter & Executive Actions Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Multi-Campus Performance Overview</h3>
            <p class="text-xs text-slate-500">
                @if($selectedBranchId)
                    Currently viewing: <strong class="text-blue-600 font-bold">{{ $branches->find($selectedBranchId)?->name }}</strong>
                @else
                    Aggregated overview across all {{ $branches->count() }} active campus branches
                @endif
            </p>
        </div>

        @if(Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin')
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:inline">Filter Branch:</label>
                <select name="branch_id" onchange="this.form.submit()"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all">
                    <option value="">🌐 All Institute Campuses</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>
                            {{ $b->name }} ({{ $b->code }})
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <!-- Core Stat Cards (5 Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
        <!-- Card 1 -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Total Inquiries</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $totalStudents }}</div>
            <p class="text-[11px] text-blue-600 font-semibold mt-1">{{ $todayInquiries }} added today</p>
        </div>

        <!-- Card 2 -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Confirmed Admissions</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $totalAdmissions }}</div>
            <p class="text-[11px] text-emerald-700 font-semibold mt-1">Enrolled students</p>
        </div>

        <!-- Card 3 -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Calls & Follow-ups</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-indigo-600">{{ $totalCalls }}</div>
            <p class="text-[11px] text-slate-500 font-medium mt-1">Logged touchpoints</p>
        </div>

        <!-- Card 4 -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 block mb-2">Total Fee Collected</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600">PKR {{ number_format($totalFeeCollected) }}</div>
            <p class="text-[11px] text-slate-500 font-medium mt-1">Cash: PKR {{ number_format($cashCollected) }}</p>
        </div>

        <!-- Card 5 -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Outstanding Dues</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600">PKR {{ number_format($totalOutstandingDue) }}</div>
            @if($overdueCount > 0)
                <p class="text-[11px] text-rose-600 font-bold mt-1">⚠️ {{ $overdueCount }} overdue part(s)</p>
            @else
                <p class="text-[11px] text-slate-400 font-medium mt-1">Scheduled dues</p>
            @endif
        </div>
    </div>

    <!-- Multi-Branch Comparison Table (Shown for Super Admin or Multi-branch view) -->
    @if(empty($selectedBranchId) && count($branchComparison) > 0)
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Campus-wise Revenue & Admissions Comparison</h3>
                    <p class="text-xs text-slate-500">Live breakdown of student inquiries, admissions, and financial recoveries per branch</p>
                </div>
                <a href="{{ route('admin.branches.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    Manage Campuses &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[640px]">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Campus Branch</th>
                            <th class="px-4 py-3.5 text-center">Staff Members</th>
                            <th class="px-4 py-3.5 text-center">Inquiries</th>
                            <th class="px-4 py-3.5 text-center">Admissions</th>
                            <th class="px-4 py-3.5">Fee Collected</th>
                            <th class="px-4 py-3.5">Outstanding Balance</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($branchComparison as $bc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 font-mono font-bold flex items-center justify-center text-xs">
                                            {{ $bc['code'] }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.branches.show', $bc['id']) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                                {{ $bc['name'] }}
                                            </a>
                                            <div class="text-[11px] text-slate-400 font-mono">Campus Code: {{ $bc['code'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <a href="{{ route('admin.branches.show', $bc['id']) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-all hover:scale-105" 
                                       title="Click to view staff details of this branch">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>{{ $bc['staff_count'] }} Staff</span>
                                        <span class="text-blue-500 font-bold">&rarr;</span>
                                    </a>
                                </td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-700">
                                    {{ $bc['inquiries'] }}
                                </td>
                                <td class="px-4 py-4 text-center font-bold text-emerald-600">
                                    {{ $bc['admissions'] }}
                                </td>
                                <td class="px-4 py-4 font-bold text-emerald-600">
                                    PKR {{ number_format($bc['collected']) }}
                                </td>
                                <td class="px-4 py-4 font-semibold text-amber-600">
                                    PKR {{ number_format($bc['due']) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <a href="{{ route('admin.branches.show', $bc['id']) }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg border border-slate-200 transition-colors" title="View Staff Details">
                                            Staff Details &rarr;
                                        </a>
                                        <a href="{{ route('admin.dashboard', ['branch_id' => $bc['id']]) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg border border-blue-200 transition-colors" title="Filter Dashboard">
                                            Filter
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif



    <!-- Dual Column: Recent Activity Feed & Pending Follow-ups -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Recent Activity Feed -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Recent System Activity</h3>
                    <p class="text-xs text-slate-500">Audit trail of inquiries, admissions, and payments</p>
                </div>
                <a href="{{ route('admin.activityLogs.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    All Logs &rarr;
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recentActivities as $act)
                    <div class="flex items-start gap-3 text-xs">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold shrink-0 mt-0.5">
                            {{ $act->user ? strtoupper(substr($act->user->name, 0, 1)) : 'S' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-800">
                                <span class="font-bold text-slate-900">{{ $act->user ? $act->user->name : 'System' }}</span>
                                <span class="text-slate-600">{{ $act->description }}</span>
                            </p>
                            <span class="text-[11px] text-slate-400 mt-0.5 block font-mono">
                                {{ $act->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">No recent activity.</p>
                @endforelse
            </div>
        </div>

        <!-- Cross-Staff Follow-ups Due -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Follow-ups & Dues Due Today</h3>
                    <p class="text-xs text-slate-500">Pending callbacks and scheduled counselor alerts</p>
                </div>
                <a href="{{ route('tasks.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    Task Board &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($pendingFollowUps as $item)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3 text-xs">
                        <div>
                            <a href="{{ route('students.show', $item->student) }}" class="font-bold text-slate-900 hover:text-blue-600">
                                {{ $item->student->student_name }}
                            </a>
                            <div class="text-[11px] text-slate-500 mt-0.5 font-mono">
                                {{ $item->student->phone_number }} • Officer: {{ $item->user ? $item->user->name : 'Staff' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                {{ $item->next_follow_up_date->format('M d') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">All follow-ups up to date! Good work.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

@endsection

