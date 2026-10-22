@extends('layouts.app')

@section('title', $student->student_name . ' - Inquiry & Call Timeline')
@section('page_title', $student->student_name)
@section('page_subtitle', 'Inquiry History, Contact Details & Multi-Staff Call Timeline')

@section('content')
<div class="space-y-8" x-data="{ addCallModal: false }">

    <!-- Top Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to All Students</span>
        </a>

        <div class="flex items-center gap-2.5">
            @if($student->admission)
                <a href="{{ route('admissions.show', $student->admission) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/25 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Enrolled (Roll: {{ $student->admission->roll_no }})</span>
                </a>
            @else
                <a href="{{ route('admissions.create', ['student' => $student->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/25 transition-all transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Convert to Admission</span>
                </a>
            @endif

            <button @click="addCallModal = true" type="button"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all transform active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Call Entry</span>
            </button>

            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-2xs transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit</span>
            </a>

            @if(Auth::user()->isAdmin() || Auth::id() === $student->created_by)
                <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Are you sure you want to delete this inquiry record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2.5 rounded-xl text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition-colors" title="Delete Inquiry">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- STUDENT CONTACT BADGE SUMMARY BAR -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-lg border border-blue-500/30 shrink-0">
                {{ $totalContacts }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-white tracking-wide">
                        Contacted {{ $totalContacts }} {{ Str::plural('time', $totalContacts) }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $student->status_badge['bg'] }} font-semibold">
                        {{ $student->status }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 mt-0.5">
                    First Contact: <span class="text-white font-medium">{{ $firstContact ? $firstContact->call_date->format('M d, Y') : $student->created_at->format('M d, Y') }}</span>
                    @if($lastContact)
                        • Last Contact: <span class="text-white font-medium">{{ $lastContact->call_date->format('M d, Y') }} ({{ $lastContact->call_type }})</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 text-xs">
            <a href="tel:{{ $student->phone_number }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-slate-900 bg-white hover:bg-slate-100 shadow-sm transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 4V3z"/></svg>
                <span>Call {{ $student->phone_number }}</span>
            </a>
            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $student->phone_number) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm transition-colors">
                <span>WhatsApp</span>
            </a>
        </div>
    </div>

    <!-- Main Grid: Profile Card (Left) + Contact History Timeline (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Student Profile Card (1 Col) -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Inquiry Profile</h3>
                    <span class="text-xs font-mono font-semibold text-slate-400">#{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="p-6 space-y-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Student Name</span>
                        <span class="text-base font-bold text-slate-900">{{ $student->student_name }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Class Applying For</span>
                        <span class="inline-block mt-0.5 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs">
                            {{ $student->class_applying_for }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Parent / Guardian</span>
                        <span class="font-semibold text-slate-800">{{ $student->parent_name ?: 'Not Provided' }}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block font-medium">Primary Phone Number</span>
                        <span class="font-mono text-slate-900 font-bold flex items-center gap-1.5 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $student->phone_number }}
                        </span>
                    </div>

                    @if($student->alternate_phone)
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Alternate Phone / WhatsApp</span>
                            <span class="font-mono text-slate-700 font-semibold mt-0.5 block">{{ $student->alternate_phone }}</span>
                        </div>
                    @endif

                    @if($student->email)
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Email Address</span>
                            <span class="text-slate-700 font-medium truncate block">{{ $student->email }}</span>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block font-medium">Area / Locality</span>
                        <span class="text-slate-700 font-semibold block">
                            {{ $student->city ?: 'Area not specified' }}
                            @if($student->address)
                                <span class="text-xs text-slate-500 block mt-0.5 font-normal">{{ $student->address }}</span>
                            @endif
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Inquiry Source</span>
                        <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                            {{ $student->inquiry_source }}
                        </span>
                    </div>

                    @if($student->previous_school)
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Previous School</span>
                            <span class="text-slate-700 text-xs font-medium">{{ $student->previous_school }}</span>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-100 text-xs text-slate-400 space-y-1">
                        <div>Created by: <span class="font-semibold text-slate-700">{{ $student->creator ? $student->creator->name : 'Staff' }}</span></div>
                        <div>Date Registered: {{ $student->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Notes preview card if any -->
            @if($student->notes)
                <div class="bg-amber-50/60 rounded-3xl border border-amber-200/80 p-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-900 block mb-2">Original Inquiry Notes</span>
                    <p class="text-xs text-amber-900 leading-relaxed italic">
                        "{{ $student->notes }}"
                    </p>
                </div>
            @endif
        </div>

        <!-- Right: VERTICAL TIMELINE OF ALL CONTACTS (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
                
                <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>Contact & Call History Timeline</span>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                {{ $totalContacts }} Entries
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Chronological record of every phone call, visit, WhatsApp chat, and follow-up</p>
                    </div>

                    <button @click="addCallModal = true" type="button" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Call</span>
                    </button>
                </div>

                <!-- Timeline Stream -->
                <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-3 sm:before:left-4 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200">
                    
                    @forelse($student->callLogs as $call)
                        <div class="relative group">
                            <!-- Timeline node dot -->
                            <div class="absolute -left-6 sm:-left-8 top-1 w-6 sm:w-8 h-6 sm:h-8 rounded-full bg-white border-2 border-blue-600 flex items-center justify-center text-blue-600 shadow-xs group-hover:scale-110 transition-transform">
                                @if($call->call_type === 'Incoming')
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                @elseif($call->call_type === 'Outgoing')
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                @elseif($call->call_type === 'WhatsApp')
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @endif
                            </div>

                            <!-- Timeline Card -->
                            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 hover:border-blue-200 hover:bg-white hover:shadow-md transition-all">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold ring-1 ring-inset {{ $call->type_badge['bg'] }}">
                                            {{ $call->call_type }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-800">
                                            Handled by: {{ $call->user ? $call->user->name : 'Staff' }}
                                        </span>
                                        @if($call->duration)
                                            <span class="text-[11px] text-slate-400 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                                ⏱ {{ $call->duration }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-xs text-slate-500 font-mono flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>{{ $call->call_date->format('M d, Y') }} at {{ $call->call_time }}</span>
                                    </div>
                                </div>

                                <!-- Remarks -->
                                <p class="text-sm text-slate-700 leading-relaxed bg-white p-3.5 rounded-xl border border-slate-100 shadow-2xs font-normal">
                                    {{ $call->remarks }}
                                </p>

                                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-400">Status set to:</span>
                                        <span class="font-bold text-slate-800 px-2 py-0.5 rounded bg-slate-200/70">
                                            {{ $call->status_after_call }}
                                        </span>
                                    </div>

                                    @if($call->next_follow_up_date)
                                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 border border-amber-200 font-medium">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Follow-up: <strong>{{ $call->next_follow_up_date->format('M d, Y') }}</strong></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            No contact records logged yet. Click "Add Call Entry" to begin the timeline.
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>

    <!-- ADD NEW CALL ENTRY MODAL -->
    <div x-show="addCallModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <!-- Backdrop -->
            <div x-show="addCallModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="addCallModal = false"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

            <!-- Modal Panel -->
            <div x-show="addCallModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200">
                
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Log Call / Contact Entry</h3>
                        <p class="text-xs text-slate-500">Student: <strong>{{ $student->student_name }}</strong> ({{ $student->phone_number }})</p>
                    </div>
                    <button @click="addCallModal = false" type="button" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('calls.store') }}" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Call Type -->
                        <div>
                            <label for="call_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Call / Contact Type <span class="text-rose-500">*</span></label>
                            <select id="call_type" name="call_type" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500">
                                <option value="Incoming">Incoming Call</option>
                                <option value="Outgoing">Outgoing Call</option>
                                <option value="WhatsApp">WhatsApp Chat / Call</option>
                                <option value="Walk-in">Campus Walk-in Visit</option>
                            </select>
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Duration (Optional)</label>
                            <input id="duration" name="duration" type="text" placeholder="e.g. 5 mins" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500">
                        </div>

                        <!-- Call Date -->
                        <div>
                            <label for="call_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Date <span class="text-rose-500">*</span></label>
                            <input id="call_date" name="call_date" type="date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500">
                        </div>

                        <!-- Call Time -->
                        <div>
                            <label for="call_time" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Time <span class="text-rose-500">*</span></label>
                            <input id="call_time" name="call_time" type="text" value="{{ date('H:i') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Remarks / Notes -->
                    <div>
                        <label for="remarks" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Remarks / Notes <span class="text-rose-500">*</span></label>
                        <textarea id="remarks" name="remarks" rows="3" required placeholder="What was discussed with the parent? E.g. parent confirmed admission test date, inquired about fee concession, etc." class="w-full p-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Next Follow-up Date -->
                        <div>
                            <label for="next_follow_up_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Next Follow-up Date</label>
                            <input id="next_follow_up_date" name="next_follow_up_date" type="date" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500">
                        </div>

                        <!-- Status After Call -->
                        <div>
                            <label for="status_after_call" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Status After Call <span class="text-rose-500">*</span></label>
                            <select id="status_after_call" name="status_after_call" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-semibold">
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}" {{ $student->status == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button @click="addCallModal = false" type="button" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25">Save Contact Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
