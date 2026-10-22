@extends('layouts.app')

@section('title', 'Follow-up Reminders - EduTrack CRM')
@section('page_title', 'Scheduled Follow-up Reminders')
@section('page_subtitle', 'Stay on top of prospective parent callbacks to maximize admission conversion')

@section('content')
<div class="space-y-6">

    <!-- Follow-up Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
        <a href="{{ route('calls.followUps', ['tab' => 'today']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ $filter === 'today' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <span>Due Today</span>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $filter === 'today' ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700' }}">{{ $todayCount }}</span>
        </a>

        <a href="{{ route('calls.followUps', ['tab' => 'overdue']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ $filter === 'overdue' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <span>Overdue Follow-ups</span>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $filter === 'overdue' ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-700' }}">{{ $overdueCount }}</span>
        </a>

        <a href="{{ route('calls.followUps', ['tab' => 'upcoming']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ $filter === 'upcoming' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <span>Upcoming (Next 7 Days)</span>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $filter === 'upcoming' ? 'bg-indigo-500 text-white' : 'bg-indigo-100 text-indigo-700' }}">{{ $upcomingCount }}</span>
        </a>

        <a href="{{ route('calls.followUps', ['tab' => 'all']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ $filter === 'all' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <span>All Pending</span>
        </a>
    </div>

    <!-- Follow-ups Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500">
                Showing {{ $followUps->firstItem() ?? 0 }} to {{ $followUps->lastItem() ?? 0 }} of {{ $followUps->total() }} reminders
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Scheduled Date</th>
                        <th class="px-4 py-3.5">Student & Parent</th>
                        <th class="px-4 py-3.5">Phone Number</th>
                        <th class="px-4 py-3.5">Class</th>
                        <th class="px-4 py-3.5">Current Status</th>
                        <th class="px-4 py-3.5">Previous Call Remarks</th>
                        <th class="px-4 py-3.5">Last Handled By</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($followUps as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->next_follow_up_date->isPast() && !$item->next_follow_up_date->isToday())
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 text-xs font-bold">
                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>{{ $item->next_follow_up_date->format('M d, Y') }}</span>
                                    </div>
                                @elseif($item->next_follow_up_date->isToday())
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span>
                                        <span>Today ({{ $item->next_follow_up_date->format('M d') }})</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 text-xs font-medium">
                                        <span>{{ $item->next_follow_up_date->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('students.show', $item->student) }}" class="font-bold text-slate-900 hover:text-blue-600">
                                    {{ $item->student->student_name }}
                                </a>
                                <div class="text-xs text-slate-400">Parent: {{ $item->student->parent_name ?: 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs font-semibold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md">
                                    {{ $item->student->phone_number }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-medium text-slate-600">
                                {{ $item->student->class_applying_for }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $item->student->status_badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item->student->status_badge['dot'] }}"></span>
                                    {{ $item->student->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 max-w-xs">
                                <p class="text-xs text-slate-600 line-clamp-2 italic">
                                    "{{ $item->remarks }}"
                                </p>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-600">
                                {{ $item->user ? $item->user->name : 'Staff' }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('students.show', $item->student) }}#add-call" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>Call / Log</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No follow-up reminders found in this category.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($followUps->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $followUps->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
