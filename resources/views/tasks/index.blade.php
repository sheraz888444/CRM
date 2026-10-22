@extends('layouts.app')

@section('title', 'Daily Task Board - EduTrack CRM')
@section('page_title', 'Daily Task Board')
@section('page_subtitle', 'Unified workflow for inquiry follow-ups and fee installment collections')

@section('content')
<div class="space-y-6" x-data="{ rescheduleModal: false, taskToReschedule: null, newDueDate: '{{ date('Y-m-d') }}', rescheduleActionUrl: '' }">

    <!-- Top Action & Filter Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Front Desk & Counselor Tasks</h3>
            <p class="text-xs text-slate-500">Auto-synchronized reminders for upcoming student follow-ups and fee dues</p>
        </div>

        @if(Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin')
            <form method="GET" action="{{ route('tasks.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="{{ $filter }}">
                <select name="branch_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-blue-600 outline-none">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }} ({{ $b->code }})</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 border-b border-slate-200/80 pb-1">
        <a href="{{ route('tasks.index', ['tab' => 'today', 'branch_id' => request('branch_id')]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ $filter === 'today' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Today's Tasks</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'today' ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $todayCount }}</span>
        </a>

        <a href="{{ route('tasks.index', ['tab' => 'overdue', 'branch_id' => request('branch_id')]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ $filter === 'overdue' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Overdue Alerts</span>
            @if($overdueCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'overdue' ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-800' }}">{{ $overdueCount }}</span>
            @endif
        </a>

        <a href="{{ route('tasks.index', ['tab' => 'upcoming', 'branch_id' => request('branch_id')]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ $filter === 'upcoming' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Upcoming (Next 7 Days)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'upcoming' ? 'bg-indigo-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $upcomingCount }}</span>
        </a>

        <a href="{{ route('tasks.index', ['tab' => 'completed', 'branch_id' => request('branch_id')]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ $filter === 'completed' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Completed</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'completed' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $completedCount }}</span>
        </a>
    </div>

    <!-- Tasks List Container -->
    <div class="space-y-3">
        @forelse($tasks as $task)
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-slate-300 transition-colors">
                
                <!-- Left: Icon & Details -->
                <div class="flex items-start gap-4">
                    @if($task->type === 'installment')
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-200">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                    @endif

                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $task->type === 'installment' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $task->type === 'installment' ? 'Fee Installment Alert' : 'Inquiry Follow-up' }}
                            </span>
                            @if($task->branch)
                                <span class="text-xs font-semibold text-slate-500">• {{ $task->branch->name }}</span>
                            @endif
                        </div>

                        <h4 class="text-sm font-bold text-slate-900 mt-1 {{ $task->status === 'completed' ? 'line-through text-slate-400' : '' }}">
                            {{ $task->title }}
                        </h4>

                        @if($task->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $task->description }}</p>
                        @endif

                        <div class="flex items-center gap-3 text-xs mt-2">
                            <span class="font-medium text-slate-500">
                                Due: <strong class="{{ \Carbon\Carbon::parse($task->due_date)->lt(\Carbon\Carbon::today()) && $task->status === 'pending' ? 'text-rose-600' : 'text-slate-800' }}">
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                </strong>
                            </span>
                            @if($task->user)
                                <span>•</span>
                                <span class="text-slate-400">Assigned: {{ $task->user->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                    @if($task->status === 'pending')
                        <!-- Mark Complete Form -->
                        <form action="{{ route('tasks.complete', $task) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Complete</span>
                            </button>
                        </form>

                        <!-- Reschedule Button -->
                        <button type="button" @click="taskToReschedule = {{ $task->id }}; newDueDate = '{{ $task->due_date }}'; rescheduleActionUrl = '{{ route('tasks.reschedule', $task) }}'; rescheduleModal = true;"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Reschedule</span>
                        </button>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Done on {{ $task->completed_at ? \Carbon\Carbon::parse($task->completed_at)->format('M d') : 'today' }}</span>
                        </span>
                    @endif
                </div>

            </div>
        @empty
            <div class="bg-white p-12 rounded-3xl border border-slate-200/80 shadow-xs text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h4 class="text-base font-bold text-slate-800">No tasks in this view</h4>
                <p class="text-xs text-slate-400 mt-1">All scheduled follow-ups and due alerts for this tab are clear.</p>
            </div>
        @endforelse
    </div>

    @if($tasks->hasPages())
        <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-xs">
            {{ $tasks->links() }}
        </div>
    @endif

    <!-- Reschedule Modal -->
    <div x-show="rescheduleModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="rescheduleModal = false"
             class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 transition-all">

            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h4 class="text-sm font-bold text-slate-900">Reschedule Task Due Date</h4>
                <button @click="rescheduleModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="rescheduleActionUrl" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">New Due Date</label>
                    <input type="date" name="due_date" x-model="newDueDate" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="rescheduleModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25">
                        Update Date
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
