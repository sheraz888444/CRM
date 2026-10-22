@extends('layouts.app')

@section('title', $branch->name . ' - Campus Staff & Performance - EduTrack CRM')
@section('page_title', $branch->name . ' Campus')
@section('page_subtitle', 'Branch overview, assigned staff counselors, inquiry breakdowns, and collection stats')

@section('content')
<div class="space-y-8">

    <!-- Campus Header Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <!-- Left Info -->
            <div class="flex items-start sm:items-center gap-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors shrink-0" 
                   title="Back to Dashboard">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white font-mono font-black flex items-center justify-center text-xl shadow-md shrink-0">
                    {{ $branch->code }}
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $branch->name }}</h2>
                        @if($branch->status === 'active')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active Campus
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Inactive
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 sm:gap-4 text-xs text-slate-500 mt-1.5 flex-wrap font-medium">
                        <span class="font-mono font-bold text-blue-600">Code: {{ $branch->code }}</span>
                        <span>•</span>
                        <span>📍 {{ $branch->address ?: 'No address specified' }}</span>
                        @if($branch->phone)
                            <span>•</span>
                            <span class="font-mono">📞 {{ $branch->phone }}</span>
                        @endif
                        @if($branch->email)
                            <span>•</span>
                            <span>✉️ {{ $branch->email }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2.5 flex-wrap self-start lg:self-center">
                <a href="{{ route('admin.users.create', ['branch_id' => $branch->id]) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Add Staff Member</span>
                </a>
                <a href="{{ route('admin.reports.index', ['branch_id' => $branch->id]) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors">
                    <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Branch Reports</span>
                </a>
                <a href="{{ route('admin.branches.edit', $branch) }}" 
                   class="inline-flex items-center gap-1.5 p-2.5 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors"
                   title="Edit Branch Settings">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
            </div>
        </div>

        <!-- 5 Branch Key Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4 mt-6">
            <!-- Total Staff -->
            <div class="p-4 sm:p-5 rounded-2xl bg-blue-50/70 border border-blue-200/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800 block mb-1">Staff Members</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-blue-700">{{ $staffMembers->count() }}</div>
                <p class="text-[11px] text-blue-600 font-medium mt-1">Assigned to this campus</p>
            </div>

            <!-- Total Inquiries -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Inquiries</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $totalInquiries }}</div>
                <p class="text-[11px] text-slate-500 mt-1">Student leads logged</p>
            </div>

            <!-- Total Admissions -->
            <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block mb-1">Admissions Done</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700">{{ $totalAdmissions }}</div>
                <p class="text-[11px] text-emerald-800 font-medium mt-1">
                    {{ $totalInquiries > 0 ? round(($totalAdmissions / $totalInquiries) * 100, 1) : 0 }}% conversion rate
                </p>
            </div>

            <!-- Fee Recovered -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Fee Collected</span>
                <div class="text-lg sm:text-xl font-black text-emerald-600">PKR {{ number_format($totalFeeCollected) }}</div>
                <p class="text-[11px] text-slate-500 mt-1">Total recoveries</p>
            </div>

            <!-- Pending Balance -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 block mb-1">Pending Due</span>
                <div class="text-lg sm:text-xl font-black text-amber-600">PKR {{ number_format($totalOutstandingDue) }}</div>
                <p class="text-[11px] text-slate-500 mt-1">Outstanding recovery</p>
            </div>
        </div>
    </div>

    <!-- Assigned Staff Members Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Assigned Staff Members & Counselors</h3>
                <p class="text-xs text-slate-500 mt-0.5">Click any staff counselor to inspect all students they handled, payment plans, and print reports</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
                    {{ $staffMembers->count() }} Active Staff Assigned
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[850px]">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Staff Counselor</th>
                        <th class="px-4 py-3.5">Role</th>
                        <th class="px-4 py-3.5">Contact & Email</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center">Inquiries Handled</th>
                        <th class="px-4 py-3.5 text-center">Admissions Done</th>
                        <th class="px-4 py-3.5">Fee Collected</th>
                        <th class="px-4 py-3.5">Remaining Due</th>
                        <th class="px-6 py-3.5 text-right">Inquiry Details & PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($staffStats as $stf)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Staff info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center shrink-0 text-sm shadow-xs">
                                        {{ strtoupper(substr($stf['user']->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.reports.staff', $stf['user']->id) }}" 
                                           class="font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                            {{ $stf['user']->name }}
                                        </a>
                                        <div class="text-xs text-slate-400 font-mono">
                                            {{ $stf['user']->username ? '@' . $stf['user']->username : $stf['user']->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ ucfirst(str_replace('_', ' ', $stf['user']->role)) }}
                                </span>
                            </td>

                            <!-- Contact -->
                            <td class="px-4 py-4 text-xs">
                                <div class="font-medium text-slate-800">{{ $stf['user']->phone_number ?: 'No phone' }}</div>
                                <div class="text-slate-400 font-mono">{{ $stf['user']->email }}</div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if($stf['user']->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- Inquiries -->
                            <td class="px-4 py-4 text-center font-extrabold text-slate-900 text-base">
                                {{ $stf['inquiries_count'] }}
                            </td>

                            <!-- Admissions -->
                            <td class="px-4 py-4 text-center">
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                    {{ $stf['admissions_count'] }}
                                </span>
                            </td>

                            <!-- Fee Collected -->
                            <td class="px-4 py-4 font-bold text-emerald-600 whitespace-nowrap">
                                PKR {{ number_format($stf['fee_collected']) }}
                            </td>

                            <!-- Fee Due -->
                            <td class="px-4 py-4 font-semibold text-amber-600 whitespace-nowrap">
                                PKR {{ number_format($stf['fee_due']) }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    <a href="{{ route('admin.reports.staff', $stf['user']->id) }}" 
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3.5 py-2 rounded-xl shadow-xs transition-colors"
                                       title="View all inquiries handled by this staff">
                                        <span>View Details</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.reports.staff.print', $stf['user']->id) }}" 
                                       target="_blank"
                                       title="Print / Save PDF for {{ $stf['user']->name }}"
                                       class="inline-flex items-center gap-1 p-2 px-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>PDF</span>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $stf['user']->id) }}" 
                                       class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                                       title="Edit Staff Member">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="text-sm font-bold text-slate-700">No staff members assigned to {{ $branch->name }} yet.</p>
                                <p class="text-xs text-slate-500 mt-1">Assign staff counselors to this campus to start tracking inquiries and collections.</p>
                                <div class="mt-4">
                                    <a href="{{ route('admin.users.create', ['branch_id' => $branch->id]) }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                                        <span>Assign First Staff Member &rarr;</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
