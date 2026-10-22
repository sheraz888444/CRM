@extends('layouts.app')

@section('title', 'Admissions & Fee Management - EduTrack CRM')
@section('page_title', 'Admissions & Enrollments')
@section('page_subtitle', 'Confirmed student admissions, course enrollments, fee packages, and installment schedules')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Student Admissions Register</h3>
            <p class="text-xs text-slate-500">Track student roll numbers, enrolled courses, lump-sum / installment fee progress</p>
        </div>

        @if(Auth::user()->isStaff())
            <a href="{{ route('admissions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Admission Registration</span>
            </a>
        @else
            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span>Admin View (Read-Only Records)</span>
            </div>
        @endif
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admissions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Roll No, Name, Phone..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Branch Filter -->
            @if(Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin')
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Branch Campus</label>
                <select name="branch_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }} ({{ $b->code }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Course Filter -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Course</label>
                <select name="course_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl text-sm font-bold text-white bg-slate-800 hover:bg-slate-900 transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'branch_id', 'course_id', 'status']))
                    <a href="{{ route('admissions.index') }}" class="py-2 px-3 rounded-xl text-sm font-medium text-slate-500 hover:bg-slate-100 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Admissions Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-3 bg-slate-50/90 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Enrolled Students & Fee Ledger</span>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-100/70 px-2.5 py-1 rounded-full border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Green Row = Fully Paid
                </span>
                <span class="inline-flex items-center gap-1.5 font-bold text-rose-700 bg-rose-100/70 px-2.5 py-1 rounded-full border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                    Red Row = Unpaid / Partially Paid
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[760px]">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Roll No & Student</th>
                        <th class="px-4 py-3.5">Branch Campus</th>
                        <th class="px-4 py-3.5">Enrolled Course(s)</th>
                        <th class="px-4 py-3.5">Fee Package</th>
                        <th class="px-4 py-3.5">Paid / Remaining</th>
                        <th class="px-4 py-3.5">Fee Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($admissions as $adm)
                        @php
                            $isPaid = ($adm->fee_status === 'paid' || $adm->remaining <= 0);
                        @endphp
                        <tr class="{{ $isPaid ? 'bg-emerald-50/50 hover:bg-emerald-100/60 border-l-4 border-l-emerald-500' : 'bg-rose-50/60 hover:bg-rose-100/70 border-l-4 border-l-rose-500' }} transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $isPaid ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-rose-100 text-rose-800 border-rose-300' }} font-bold flex items-center justify-center shrink-0 border text-xs">
                                        {{ $adm->branch->code ?? 'B' }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admissions.show', $adm) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                            {{ $adm->name }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-xs font-bold {{ $isPaid ? 'text-emerald-700 bg-emerald-100/80' : 'text-rose-700 bg-rose-100/80' }} px-1.5 py-0.5 rounded">{{ $adm->roll_no }}</span>
                                            <span class="text-xs text-slate-500">{{ $adm->phone }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-medium text-slate-700">
                                {{ $adm->branch->name ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-xs">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($adm->courses as $c)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-white/90 text-indigo-700 border border-indigo-200 text-[11px] shadow-2xs">
                                            {{ $c->name }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs">
                                <div class="font-bold text-slate-900">PKR {{ number_format($adm->net_payable) }}</div>
                                @if($adm->discount > 0)
                                    <div class="text-[11px] text-slate-400 line-through">PKR {{ number_format($adm->total_package) }}</div>
                                @endif
                                <div class="text-[10px] text-slate-500 capitalize">{{ str_replace('_', ' ', $adm->payment_type) }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs">
                                <div class="font-bold text-emerald-700">Paid: PKR {{ number_format($adm->total_received) }}</div>
                                @if($isPaid)
                                    <div class="font-bold text-emerald-600 text-[11px]">Due: PKR 0 (Cleared)</div>
                                @else
                                    <div class="font-extrabold text-rose-600 text-[11px]">Due: PKR {{ number_format($adm->remaining) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($isPaid)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Fully Paid
                                    </span>
                                @elseif($adm->fee_status === 'overdue')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                        Overdue Dues
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                        Partially Paid
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admissions.show', $adm) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-white hover:bg-blue-50 border border-blue-200 shadow-2xs transition-colors">
                                        <span>View</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    <a href="{{ route('admissions.edit', $adm) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Edit Admission">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admissions.destroy', $adm) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete the admission record for {{ addslashes($adm->name) }} (Roll No: {{ $adm->roll_no }})?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Delete Admission">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No admissions found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admissions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $admissions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
