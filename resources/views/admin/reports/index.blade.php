@extends('layouts.app')

@section('title', 'Staff Performance & Financial Reports - EduTrack CRM')
@section('page_title', 'Staff Productivity & Admissions Reporting')
@section('page_subtitle', 'Live inquiry breakdowns by counselor, full-paid vs installment admissions, area distribution, and collection audits')

@section('content')
<div class="space-y-8">

    <!-- Report Filter Header -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Dynamic Report Parameters & Filters</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Filter by staff counselor, fee payment mode, branch, or date range</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.csv', request()->all()) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Download CSV</span>
                    </a>
                    <a href="{{ route('admin.reports.print', request()->all()) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print / Save PDF</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3.5 text-xs">
                <!-- Payment Plan Mode Filter -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Fee Payment Mode</label>
                    <select name="payment_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600 outline-none">
                        <option value="">💳 All (Paid & Inquiries)</option>
                        <option value="lump_sum" {{ request('payment_type') === 'lump_sum' ? 'selected' : '' }}>🟢 Lump Sum (Full Paid)</option>
                        <option value="installment" {{ request('payment_type') === 'installment' ? 'selected' : '' }}>🟣 Installment Plans</option>
                        <option value="inquiry_only" {{ request('payment_type') === 'inquiry_only' ? 'selected' : '' }}>⚪ Inquiries (Not Admitted)</option>
                    </select>
                </div>

                <!-- Branch Filter -->
                @if(Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin')
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Campus Branch</label>
                        <select name="branch_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600 outline-none">
                            <option value="">🌐 All Campus Branches</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }} ({{ $b->code }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Course Filter -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Course / Program</label>
                    <select name="course_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600 outline-none">
                        <option value="">📚 All Courses</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <div class="text-xs text-slate-500">
                    @if(request()->filled('payment_type'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold">
                            <span>Mode: {{ ucfirst(str_replace('_', ' ', request('payment_type'))) }}</span>
                            <a href="{{ route('admin.reports.index', request()->except('payment_type')) }}" class="hover:text-indigo-900">&times;</a>
                        </span>
                    @endif
                    @if(request()->filled('branch_id'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold ml-1">
                            <span>Branch: {{ $branches->find(request('branch_id'))?->name }}</span>
                            <a href="{{ route('admin.reports.index', request()->except('branch_id')) }}" class="hover:text-blue-900">&times;</a>
                        </span>
                    @endif
                    @if(request()->filled('course_id'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold ml-1">
                            <span>Course: {{ $courses->find(request('course_id'))?->name }}</span>
                            <a href="{{ route('admin.reports.index', request()->except('course_id')) }}" class="hover:text-emerald-900">&times;</a>
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['date_from', 'date_to', 'status', 'class', 'source', 'branch_id', 'course_id', 'payment_type']))
                        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Clear All</a>
                    @endif
                    <button type="submit" class="px-6 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                        Apply Filter & Refresh
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Metrics Grid (6 Dynamic Stat Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 sm:gap-4">
        <!-- Card 1: Inquiries -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Inquiries</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $totalInquiries }}</div>
            <p class="text-[11px] text-slate-500 mt-1">{{ $inquiryOnlyCount }} pending inquiries</p>
        </div>

        <!-- Card 2: Confirmed Admissions -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Total Admissions</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $totalAdmitted }}</div>
            <p class="text-[11px] text-emerald-700 font-semibold mt-1">{{ $conversionRate }}% conversion rate</p>
        </div>

        <!-- Card 3: Paid in Full (Lump-Sum) -->
        <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block mb-1">Paid in Full (Lump-Sum)</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700">{{ $lumpSumCount }}</div>
            <p class="text-[11px] text-emerald-800 font-medium mt-1">100% upfront clear</p>
        </div>

        <!-- Card 4: On Installments -->
        <div class="p-5 rounded-2xl bg-purple-50/70 border border-purple-200 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-800 block mb-1">On Installments</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-purple-700">{{ $installmentCount }}</div>
            <p class="text-[11px] text-purple-800 font-medium mt-1">Scheduled payment plan</p>
        </div>

        <!-- Card 5: Total Fee Collected -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Total Fee Collected</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600">PKR {{ number_format($totalFeeCollected) }}</div>
            <p class="text-[11px] text-slate-500 mt-1">Recovered amount</p>
        </div>

        <!-- Card 6: Outstanding Dues -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 block mb-1">Outstanding Dues</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600">PKR {{ number_format($totalOutstandingDue) }}</div>
            <p class="text-[11px] text-slate-500 mt-1">Remaining balance</p>
        </div>
    </div>

    <!-- STAFF PRODUCTIVITY & PAYMENT METHOD BREAKDOWN TABLE -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Staff Inquiry Performance & Payment Breakdown</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">How many inquiries each staff member handled, how many closed on lump-sum, and how many on installments</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                {{ count($staffStats) }} Active Staff Accounts
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[850px]">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Staff Counselor</th>
                        <th class="px-4 py-3.5">Campus Branch</th>
                        <th class="px-4 py-3.5 text-center">Total Inquiries</th>
                        <th class="px-4 py-3.5 text-center">Admissions Done</th>
                        <th class="px-4 py-3.5 text-center">Full Paid (Lump-Sum)</th>
                        <th class="px-4 py-3.5 text-center">On Installments</th>
                        <th class="px-4 py-3.5">Total Recovered</th>
                        <th class="px-4 py-3.5">Remaining Due</th>
                        <th class="px-6 py-3.5 text-right">Inquiry Details & PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($staffStats as $stf)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center shrink-0 text-sm shadow-xs">
                                        {{ strtoupper(substr($stf['user']->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">
                                            {{ $stf['user']->name }}
                                        </div>
                                        <div class="text-xs text-slate-400 font-mono">
                                            {{ $stf['user']->username ? '@' . $stf['user']->username : $stf['user']->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-medium text-slate-700">
                                @if($stf['user']->branch)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $stf['user']->branch->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">🌐 All Campuses</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center font-extrabold text-slate-900 text-base">
                                {{ $stf['inquiries_count'] }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                    {{ $stf['admissions_count'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center font-bold text-emerald-600">
                                {{ $stf['lump_sum_count'] }}
                            </td>
                            <td class="px-4 py-4 text-center font-bold text-purple-700">
                                {{ $stf['installment_count'] }}
                            </td>
                            <td class="px-4 py-4 font-bold text-emerald-600 whitespace-nowrap">
                                PKR {{ number_format($stf['fee_collected']) }}
                            </td>
                            <td class="px-4 py-4 font-semibold text-amber-600 whitespace-nowrap">
                                PKR {{ number_format($stf['fee_due']) }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    <a href="{{ route('admin.reports.staff', array_merge(request()->only(['date_from', 'date_to', 'course_id', 'branch_id', 'payment_type']), ['user' => $stf['user']->id])) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3.5 py-2 rounded-xl shadow-xs transition-colors">
                                        <span>View Details</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.reports.staff.print', array_merge(request()->only(['date_from', 'date_to', 'course_id', 'branch_id', 'payment_type']), ['user' => $stf['user']->id])) }}"
                                       target="_blank"
                                       title="Print / Save PDF for {{ $stf['user']->name }}"
                                       class="inline-flex items-center gap-1 p-2 px-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>PDF</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                                No staff productivity records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DETAILED INQUIRY & ADMISSION RECORDS TABLE WITH EXACT DATE & TIME -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">
                    Detailed Student Inquiries & Admission Ledger
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} records • Includes area, counselor, fee plan, and exact time
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Lump-Sum Paid: {{ $lumpSumCount }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span>Installments: {{ $installmentCount }}</span>
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[950px]">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Student & Area</th>
                        <th class="px-4 py-3.5">Campus Branch</th>
                        <th class="px-4 py-3.5">Course / Class</th>
                        <th class="px-4 py-3.5">Counselor (Staff)</th>
                        <th class="px-4 py-3.5">Fee & Payment Mode</th>
                        <th class="px-4 py-3.5">Paid / Remaining</th>
                        <th class="px-4 py-3.5">Inquiry Date & Time</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $st)
                        @php
                            $adm = $st->admission;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Student & Area -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">
                                    <a href="{{ route('students.show', $st) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $st->student_name }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Parent: {{ $st->parent_name ?: 'N/A' }} • <span class="font-mono text-slate-700 font-semibold">{{ $st->phone_number }}</span>
                                </div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Area: {{ $st->city ?: 'Area not specified' }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Branch Campus -->
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-semibold text-slate-700">
                                {{ $st->branch->name ?? 'Main' }}
                            </td>

                            <!-- Target Course -->
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-bold text-indigo-700">
                                {{ $st->course?->name ?? $st->class_applying_for }}
                            </td>

                            <!-- Handled By Staff -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ $st->creator ? strtoupper(substr($st->creator->name, 0, 1)) : 'S' }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 block leading-tight">
                                            {{ $st->creator ? $st->creator->name : 'Staff' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            {{ $st->creator?->username ? '@' . $st->creator->username : '' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Payment Mode: Full Paid Lump-Sum vs Installments -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($adm)
                                    @if($adm->payment_type === 'lump_sum')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>Full Paid (Lump-Sum)</span>
                                        </span>
                                        <div class="text-[11px] text-emerald-700 font-mono font-bold mt-1">
                                            Roll: {{ $adm->roll_no }}
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-purple-50 text-purple-800 border border-purple-300">
                                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                            <span>Installment Plan</span>
                                        </span>
                                        <div class="text-[11px] text-purple-700 font-mono font-bold mt-1">
                                            {{ $adm->installments->count() }} Installment(s)
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset {{ $st->status_badge['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $st->status_badge['dot'] }}"></span>
                                        <span>{{ $st->status }} (Inquiry Phase)</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Paid / Remaining -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($adm)
                                    <div class="text-xs font-bold text-emerald-600 font-mono">
                                        Paid: PKR {{ number_format($adm->total_received) }}
                                    </div>
                                    <div class="text-[11px] {{ $adm->remaining > 0 ? 'text-amber-600 font-bold' : 'text-slate-400' }} font-mono mt-0.5">
                                        Due: PKR {{ number_format($adm->remaining) }}
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">No admission fee</span>
                                @endif
                            </td>

                            <!-- Inquiry Date & Exact Time -->
                            <td class="px-4 py-4 whitespace-nowrap text-xs">
                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $st->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="text-[11px] text-blue-600 font-mono font-bold mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>{{ $st->created_at->format('h:i:s A') }}</span>
                                    <span class="text-slate-400 font-normal">({{ $st->created_at->diffForHumans() }})</span>
                                </div>
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('students.show', $st) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="View Student Profile">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($adm)
                                        <a href="{{ route('admissions.show', $adm) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors" title="View Fee Ledger">
                                            <span>Fee Ledger</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No records match the selected parameters. Try clearing or expanding your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
