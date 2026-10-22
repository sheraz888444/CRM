@extends('layouts.app')

@section('title', 'Call & Contact Logs - EduTrack CRM')
@section('page_title', 'All Call & Contact Logs')
@section('page_subtitle', 'Comprehensive audit trail of phone calls, WhatsApp messages, and walk-in discussions')

@section('content')
<div class="space-y-6">

    <!-- Filters Header -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
        <form method="GET" action="{{ route('calls.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Search Student or Phone</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Type name or phone number..." class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:border-blue-500">
                </div>
            </div>

            <!-- Call Type -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Call Type</label>
                <select name="type" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">All Types</option>
                    <option value="Incoming" {{ request('type') == 'Incoming' ? 'selected' : '' }}>Incoming Call</option>
                    <option value="Outgoing" {{ request('type') == 'Outgoing' ? 'selected' : '' }}>Outgoing Call</option>
                    <option value="WhatsApp" {{ request('type') == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="Walk-in" {{ request('type') == 'Walk-in' ? 'selected' : '' }}>Walk-in Visit</option>
                </select>
            </div>

            <!-- Staff -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Handled By Staff</label>
                <select name="staff_id" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">All Staff</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                    Filter Logs
                </button>
                @if(request()->hasAny(['search', 'type', 'staff_id', 'date_from', 'date_to']))
                    <a href="{{ route('calls.index') }}" class="py-2 px-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-100 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Call Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500">
                Showing {{ $callLogs->firstItem() ?? 0 }} to {{ $callLogs->lastItem() ?? 0 }} of {{ $callLogs->total() }} contact records
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Date & Time</th>
                        <th class="px-4 py-3.5">Student / Parent</th>
                        <th class="px-4 py-3.5">Type</th>
                        <th class="px-4 py-3.5">Handled By</th>
                        <th class="px-4 py-3.5">Remarks / Summary</th>
                        <th class="px-4 py-3.5">Status After</th>
                        <th class="px-4 py-3.5">Follow-up</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($callLogs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 text-xs">{{ $log->call_date->format('M d, Y') }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $log->call_time }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('students.show', $log->student) }}" class="font-bold text-slate-900 hover:text-blue-600 block">
                                    {{ $log->student->student_name }}
                                </a>
                                <span class="text-xs font-mono text-slate-500">{{ $log->student->phone_number }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold ring-1 ring-inset {{ $log->type_badge['bg'] }}">
                                    {{ $log->call_type }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-medium text-slate-700">
                                {{ $log->user ? $log->user->name : 'Staff' }}
                            </td>
                            <td class="px-4 py-4 max-w-xs">
                                <p class="text-xs text-slate-600 line-clamp-2 italic">
                                    "{{ $log->remarks }}"
                                </p>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $log->status_after_call }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-medium">
                                @if($log->next_follow_up_date)
                                    <span class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                        {{ $log->next_follow_up_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('students.show', $log->student) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-600 hover:bg-blue-50 transition-colors">
                                    <span>Timeline</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No call logs found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($callLogs->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $callLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
