@extends('layouts.app')

@section('title', 'All Students & Inquiries - EduTrack CRM')
@section('page_title', 'All Student Inquiries')
@section('page_subtitle', 'Filter, search, and manage student inquiry records across all staff')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Header -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6" x-data="{ filtersOpen: false }">
        <form method="GET" action="{{ route('students.index') }}" class="space-y-4">
            
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <!-- Search bar -->
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search student name, parent name, or phone number..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="filtersOpen = !filtersOpen"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filters</span>
                        @if(array_filter(request()->only(['status', 'class', 'source', 'staff_id', 'date_from', 'date_to'])))
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        @endif
                    </button>

                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                        Search
                    </button>

                    @if(request()->hasAny(['search', 'status', 'class', 'source', 'staff_id', 'date_from', 'date_to']))
                        <a href="{{ route('students.index') }}" class="px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                            Reset
                        </a>
                    @endif

                    @if(Auth::user()->isStaff())
                        <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all ml-auto">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Student</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Advanced Filter Dropdowns Collapsible -->
            <div x-show="filtersOpen" x-cloak class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                <!-- Status -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Class -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase mb-1">Class</label>
                    <select name="class" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Source -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase mb-1">Source</label>
                    <select name="source" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                        <option value="">All Sources</option>
                        @foreach($sources as $s)
                            <option value="{{ $s }}" {{ request('source') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Staff -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase mb-1">Staff Officer</label>
                    <select name="staff_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                        <option value="">All Staff Members</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase mb-1">Date Created</label>
                    <div class="flex items-center gap-1">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-1/2 px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-1/2 px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Students Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500">
                Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} inquiries
            </span>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.reports.csv') }}?{{ http_build_query(request()->all()) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export Filtered List (CSV)</span>
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[760px]">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Student & Parent</th>
                        <th class="px-4 py-3.5">Phone Number</th>
                        <th class="px-4 py-3.5">Class</th>
                        <th class="px-4 py-3.5">Source</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Call History</th>
                        <th class="px-4 py-3.5">Handled By</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('students.show', $student) }}" class="font-bold text-slate-900 hover:text-blue-600">
                                    {{ $student->student_name }}
                                </a>
                                <div class="text-xs text-slate-400">Parent: {{ $student->parent_name ?: 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs font-semibold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md">
                                    {{ $student->phone_number }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-semibold text-slate-700">
                                {{ $student->class_applying_for }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $student->inquiry_source }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $student->status_badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $student->status_badge['dot'] }}"></span>
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $student->call_logs_count }} contacts
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-600">
                                {{ $student->creator ? $student->creator->name : 'Staff' }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-600 hover:text-white hover:bg-blue-600 transition-colors border border-blue-200 hover:border-transparent">
                                        <span>Timeline</span>
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete inquiry for {{ addslashes($student->student_name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Delete Inquiry">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No student inquiries match your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
