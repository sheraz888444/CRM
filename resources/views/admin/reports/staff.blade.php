@extends('layouts.app')

@section('title', $user->name . ' - Staff Inquiries & Admissions Report - EduTrack CRM')
@section('page_title', 'Staff Performance & Inquiry Details')
@section('page_subtitle', 'Comprehensive inquiry log, payment modes, area distribution, and fee audits for ' . $user->name)

@section('content')
<div class="space-y-8">

    <!-- Top Navigation & Staff Profile Banner -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <!-- Left: Staff Info -->
            <div class="flex items-start sm:items-center gap-4">
                <a href="{{ route('admin.reports.index') }}" 
                   class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors shrink-0" 
                   title="Back to All Reports">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold flex items-center justify-center text-xl shadow-md shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 sm:gap-4 text-xs text-slate-500 mt-1 flex-wrap font-medium">
                        <span class="flex items-center gap-1 font-mono">
                            {{ $user->username ? '@' . $user->username : $user->email }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-slate-700">
                            🏢 {{ $user->branch?->name ?? 'All Campuses (Universal)' }}
                        </span>
                        @if($user->phone_number)
                            <span>•</span>
                            <span class="font-mono">📞 {{ $user->phone_number }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Export & Print Actions -->
            <div class="flex items-center gap-2.5 self-start lg:self-center">
                <a href="{{ route('admin.reports.staff.csv', array_merge(request()->all(), ['user' => $user->id])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Download CSV</span>
                </a>
                <a href="{{ route('admin.reports.staff.print', array_merge(request()->all(), ['user' => $user->id])) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 shadow-md transition-colors">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print / Save PDF</span>
                </a>
            </div>
        </div>

        <!-- Filter Form for this Staff's inquiries -->
        <form method="GET" action="{{ route('admin.reports.staff', $user) }}" class="mt-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs">
                <!-- Payment Mode -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Fee Payment Mode</label>
                    <select name="payment_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600 outline-none">
                        <option value="">💳 All (Paid & Inquiries)</option>
                        <option value="lump_sum" {{ request('payment_type') === 'lump_sum' ? 'selected' : '' }}>🟢 Lump Sum (Full Paid)</option>
                        <option value="installment" {{ request('payment_type') === 'installment' ? 'selected' : '' }}>🟣 Installment Plans</option>
                        <option value="inquiry_only" {{ request('payment_type') === 'inquiry_only' ? 'selected' : '' }}>⚪ Inquiries (Not Admitted)</option>
                    </select>
                </div>

                <!-- Course / Program -->
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

            <div class="flex items-center justify-between pt-1">
                <div class="text-xs text-slate-500">
                    <span>Showing filtered records created & handled by <strong>{{ $user->name }}</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['date_from', 'date_to', 'course_id', 'payment_type']))
                        <a href="{{ route('admin.reports.staff', $user) }}" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Clear Filters</a>
                    @endif
                    <button type="submit" class="px-6 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                        Apply Filter & Refresh
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Metrics for this Staff -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 sm:gap-4">
        <!-- Total Inquiries -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Inquiries</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $totalInquiries }}</div>
            <p class="text-[11px] text-slate-500 mt-1">{{ $inquiryOnlyCount }} pending inquiry</p>
        </div>

        <!-- Total Admissions -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Total Admissions</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $totalAdmitted }}</div>
            <p class="text-[11px] text-emerald-700 font-semibold mt-1">{{ $conversionRate }}% conversion rate</p>
        </div>

        <!-- Full Paid (Lump-Sum) -->
        <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block mb-1">Full Paid (Lump-Sum)</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700">{{ $lumpSumCount }}</div>
            <p class="text-[11px] text-emerald-800 font-medium mt-1">100% upfront clear</p>
        </div>

        <!-- On Installments -->
        <div class="p-5 rounded-2xl bg-purple-50/70 border border-purple-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-800 block mb-1">On Installments</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-purple-700">{{ $installmentCount }}</div>
            <p class="text-[11px] text-purple-800 font-medium mt-1">Payment plans</p>
        </div>

        <!-- Fee Recovered -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Total Fee Collected</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600">PKR {{ number_format($totalFeeCollected) }}</div>
            <p class="text-[11px] text-slate-500 mt-1">Recovered to date</p>
        </div>

        <!-- Outstanding Dues -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 block mb-1">Pending Due Balance</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600">PKR {{ number_format($totalOutstandingDue) }}</div>
            <p class="text-[11px] text-slate-500 mt-1">Remaining recovery</p>
        </div>
    </div>

    <!-- Comprehensive Inquiries Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Student Inquiries Handled by {{ $user->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Showing all registered student inquiries with exact timestamp, area, and financial status</p>
            </div>
            <div class="text-xs font-semibold text-slate-500">
                Total Found: {{ $students->total() }} records
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[950px]">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Inquiry Ref</th>
                        <th class="px-4 py-3.5">Student & Contact</th>
                        <th class="px-4 py-3.5">Area / Locality</th>
                        <th class="px-4 py-3.5">Course / Program</th>
                        <th class="px-4 py-3.5 text-center">Inquiry Status</th>
                        <th class="px-4 py-3.5 text-center">Payment Plan</th>
                        <th class="px-4 py-3.5">Fee Package</th>
                        <th class="px-4 py-3.5">Paid / Received</th>
                        <th class="px-4 py-3.5">Remaining Due</th>
                        <th class="px-5 py-3.5">Date & Exact Time</th>
                        <th class="px-4 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                        @php
                            $adm = $student->admission;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Ref -->
                            <td class="px-5 py-4 font-mono text-xs font-bold text-slate-600">
                                #{{ $student->id }}
                            </td>

                            <!-- Student & Contact -->
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-900">{{ $student->student_name }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $student->phone_number }}</div>
                                @if($student->parent_name)
                                    <div class="text-[11px] text-slate-400">Father: {{ $student->parent_name }}</div>
                                @endif
                            </td>

                            <!-- Area / Locality -->
                            <td class="px-4 py-4 text-xs font-medium text-slate-700">
                                <span class="inline-flex items-center gap-1">
                                    📍 {{ $student->city ?: 'N/A' }}
                                </span>
                            </td>

                            <!-- Course -->
                            <td class="px-4 py-4 text-xs font-bold text-slate-800">
                                {{ $student->course?->name ?? $student->class_applying_for }}
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4 text-center">
                                @if($adm)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        Admitted
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Payment Plan -->
                            <td class="px-4 py-4 text-center">
                                @if($adm)
                                    @if($adm->payment_type === 'lump_sum')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            🟢 Lump Sum (Full)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            🟣 Installments
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Inquiry Only</span>
                                @endif
                            </td>

                            <!-- Fee Package -->
                            <td class="px-4 py-4 font-bold text-slate-900 text-xs whitespace-nowrap">
                                @if($adm)
                                    PKR {{ number_format($adm->net_payable) }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Amount Received -->
                            <td class="px-4 py-4 font-bold text-emerald-600 text-xs whitespace-nowrap">
                                @if($adm)
                                    PKR {{ number_format($adm->total_received) }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Remaining Due -->
                            <td class="px-4 py-4 font-bold text-amber-600 text-xs whitespace-nowrap">
                                @if($adm)
                                    @if($adm->remaining > 0)
                                        PKR {{ number_format($adm->remaining) }}
                                    @else
                                        <span class="text-emerald-600 font-semibold">Cleared (0)</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Exact Date & Time -->
                            <td class="px-5 py-4 text-xs whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $student->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] font-mono text-slate-500">{{ $student->created_at->format('h:i A') }}</div>
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('students.show', $student) }}" 
                                   class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg border border-blue-200 transition-colors">
                                    <span>View</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-semibold">No student inquiries found for {{ $user->name }} under selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
