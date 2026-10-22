@extends('layouts.app')

@section('title', 'Staff Dashboard - EduTrack CRM')
@section('page_title', 'Admissions & Inquiry Desk')
@section('page_subtitle', 'Welcome back, ' . Auth::user()->name . ' • ' . Carbon\Carbon::today()->format('l, F j, Y'))

@section('content')
<div class="space-y-8">

    <!-- Welcome & Quick Action Banner -->
    <div class="rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white p-6 sm:p-8 shadow-xl shadow-blue-600/15 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold mb-3 border border-white/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Staff Desk Operational</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Good day, {{ Auth::user()->name }}!
                </h2>
                <p class="text-blue-100 text-sm max-w-xl mt-1 leading-relaxed">
                    Ready to take incoming calls? Use the prominent search bar above or press <kbd class="px-2 py-0.5 rounded bg-white/20 text-white font-mono text-xs">/</kbd> to check repeat caller history instantly before picking up.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm bg-white text-blue-700 hover:bg-blue-50 shadow-md transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Record New Inquiry</span>
                </a>
                <a href="{{ route('calls.followUps') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>View Follow-ups</span>
                </a>
            </div>
        </div>

        <!-- Decorative background circles -->
        <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute right-40 -top-20 w-48 h-48 rounded-full bg-indigo-500/10 pointer-events-none"></div>
    </div>

    <!-- PROMINENT CALLER DESK: BIG INSTANT SEARCH & NEW INQUIRY DISPATCH -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border-2 border-blue-500/30 shadow-xl shadow-blue-600/5 relative"
         x-data="{
             deskQuery: '',
             deskResults: [],
             deskLoading: false,
             deskSearched: false,
             deskTimeout: null,
             isPhoneQuery() {
                 return /^[0-9+ -]{3,}$/.test(this.deskQuery.trim());
             },
             performDeskSearch() {
                 clearTimeout(this.deskTimeout);
                 const q = this.deskQuery.trim();
                 if (q.length < 2) {
                     this.deskResults = [];
                     this.deskSearched = false;
                     this.deskLoading = false;
                     return;
                 }
                 this.deskLoading = true;
                 this.deskTimeout = setTimeout(() => {
                     fetch('{{ route('students.liveSearch') }}?q=' + encodeURIComponent(q))
                         .then(res => res.json())
                         .then(data => {
                             this.deskResults = data.results || [];
                             this.deskLoading = false;
                             this.deskSearched = true;
                         })
                         .catch(() => {
                             this.deskLoading = false;
                         });
                 }, 200);
             },
             clearDesk() {
                 this.deskQuery = '';
                 this.deskResults = [];
                 this.deskSearched = false;
                 this.deskLoading = false;
                 $refs.deskInput.focus();
             }
         }"
         @keydown.window.prevent.slash="$refs.deskInput.focus()">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-bold text-xs mb-2 border border-blue-200">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>CALLER DESK & QUICK INQUIRY LOOKUP</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Instant Student & Contact Search</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Type phone number or student name below. If not found in records, you can register them in 1-click.</p>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Direct New Inquiry Form</span>
                </a>
            </div>
        </div>

        <!-- Big Search Bar Input Container -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 sm:pl-5 flex items-center pointer-events-none text-blue-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <input x-ref="deskInput"
                   x-model="deskQuery"
                   @input="performDeskSearch()"
                   @keydown.escape="clearDesk()"
                   type="text"
                   placeholder="Enter Contact Number (e.g. 0300-5551234) or Student / Parent Name..."
                   class="w-full pl-13 sm:pl-14 pr-28 sm:pr-36 py-4 text-base sm:text-lg font-semibold text-slate-900 bg-slate-50 hover:bg-slate-50/80 focus:bg-white border-2 border-slate-200 focus:border-blue-600 rounded-2xl focus:ring-4 focus:ring-blue-500/15 transition-all shadow-inner placeholder:text-slate-400 placeholder:text-sm sm:placeholder:text-base placeholder:font-normal">

            <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center gap-2">
                <div x-show="deskLoading" x-cloak class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>

                <button x-show="deskQuery.length > 0" x-cloak
                        @click="clearDesk()"
                        type="button"
                        class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200/50 transition-colors"
                        title="Clear search">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <kbd class="hidden sm:inline-block text-[11px] font-bold text-slate-400 bg-white border border-slate-200 rounded-lg px-2 py-1 shadow-2xs">Press /</kbd>
            </div>
        </div>

        <!-- SEARCH RESULTS / NOT FOUND STATE -->
        <div class="mt-5" x-show="deskSearched" x-cloak x-transition>
            
            <!-- STATE A: NOT FOUND IN DATABASE -> DUAL OPTIONS: SAVE WITH INQUIRY OR SAVE WITH ADMISSION -->
            <div x-show="deskResults.length === 0 && !deskLoading"
                 class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-amber-50 via-orange-50/60 to-blue-50/40 border-2 border-amber-300 shadow-md">
                <div class="mb-5">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 font-extrabold text-[11px] uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                            <span>Not Found in Records</span>
                        </span>
                        <span class="text-xs text-slate-500">• Choose Registration Action</span>
                    </div>
                    <h4 class="text-base sm:text-lg font-black text-slate-900">
                        No record found for "<span class="text-amber-700 font-mono" x-text="deskQuery"></span>"
                    </h4>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                        Choose whether to save as an initial <strong>Inquiry</strong> or register directly as a confirmed <strong>Admission</strong>:
                    </p>
                </div>

                <!-- DUAL ACTION CARDS: SAVE WITH INQUIRY vs SAVE WITH ADMISSION -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- OPTION 1: SAVE WITH INQUIRY -->
                    <div class="p-5 rounded-2xl bg-white border-2 border-blue-200 hover:border-blue-500 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    Inquiry Form
                                </span>
                            </div>
                            <h5 class="text-base font-bold text-slate-900 group-hover:text-blue-700 transition-colors">
                                Save with Inquiry
                            </h5>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Opens inquiry form only. Record student particulars, residential area, inquiry source, counseling remarks, and schedule next follow-up call.
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <a :href="'{{ route('students.create') }}?' + (isPhoneQuery() ? 'phone=' : 'name=') + encodeURIComponent(deskQuery.trim())"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Proceed to Inquiry Form</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- OPTION 2: SAVE WITH ADMISSION -->
                    <div class="p-5 rounded-2xl bg-white border-2 border-emerald-200 hover:border-emerald-500 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Direct Admission Form
                                </span>
                            </div>
                            <h5 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">
                                Save with Admission
                            </h5>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Directly opens official admission form. Assigns branch Roll Number, enrolls in selected courses, configures lump-sum / installment fee package, and prints receipt.
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <a :href="'{{ route('admissions.create') }}?' + (isPhoneQuery() ? 'phone=' : 'name=') + encodeURIComponent(deskQuery.trim())"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Proceed to Admission Form</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATE B: EXISTING STUDENTS FOUND IN DATABASE -->
            <div x-show="deskResults.length > 0" class="space-y-3">
                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500 px-1">
                    <span class="flex items-center gap-1.5 text-emerald-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Found <span class="font-extrabold text-slate-900 text-sm" x-text="deskResults.length"></span> Matching Student(s) in Records
                    </span>
                    <span class="text-slate-400 font-normal">Click buttons to log call or admit student</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <template x-for="item in deskResults" :key="item.id">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:border-blue-500 hover:shadow-md transition-all flex flex-col justify-between group">
                            
                            <!-- Top Details -->
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-black text-base flex items-center justify-center shrink-0">
                                            <span x-text="item.student_name.charAt(0)"></span>
                                        </div>
                                        <div>
                                            <a :href="item.url" class="font-bold text-slate-900 text-base group-hover:text-blue-600 transition-colors flex items-center gap-1.5">
                                                <span x-text="item.student_name"></span>
                                            </a>
                                            <p class="text-xs text-slate-500 mt-0.5">Parent: <span class="font-medium text-slate-700" x-text="item.parent_name"></span></p>
                                        </div>
                                    </div>

                                    <span :class="item.badge.bg" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ring-1 ring-inset shrink-0" x-text="item.status"></span>
                                </div>

                                <!-- Key Meta Badges -->
                                <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                                    <a :href="'tel:' + item.phone_number" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-800 font-mono font-bold transition-colors">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span x-text="item.phone_number"></span>
                                    </a>

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        <span x-text="item.class_applying_for"></span>
                                    </span>

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span x-text="item.branch_name"></span>
                                    </span>

                                    <span class="text-[11px] text-slate-400" x-text="item.total_contacts + ' total interaction(s)'"></span>
                                </div>

                                <!-- Last Call Note Preview -->
                                <div x-show="item.last_call_notes" class="mt-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 line-clamp-2">
                                    <span class="font-bold text-slate-700" x-text="'Last (' + (item.last_call_date || 'Recent') + '): '"></span>
                                    <span x-text="item.last_call_notes"></span>
                                </div>
                            </div>

                            <!-- Bottom Action Buttons -->
                            <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                <a :href="item.url" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>View History & Calls</span>
                                </a>

                                <div class="flex items-center gap-2">
                                    <a :href="item.admit_url" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold text-xs text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Admit Student</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Stat 1 -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">My Registered Inquiries</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $myTotalStudents }}</span>
                <span class="text-xs text-slate-500 font-medium">students</span>
            </div>
            <p class="text-xs text-slate-400 mt-2">All-time inquiries created by you</p>
        </div>

        <!-- Stat 2 -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Today's New Inquiries</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $todayInquiries }}</span>
                <span class="text-xs text-indigo-600 font-medium">school-wide</span>
            </div>
            <p class="text-xs text-slate-400 mt-2">Inquiries captured today</p>
        </div>

        <!-- Stat 3 -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">My Calls Logged Today</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $todayCalls }}</span>
                <span class="text-xs text-emerald-600 font-medium">calls/chats</span>
            </div>
            <p class="text-xs text-slate-400 mt-2">Contacts handled by you today</p>
        </div>

        <!-- Stat 4 -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pending Follow-ups</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-amber-600">{{ $pendingFollowUpsCount }}</span>
                <span class="text-xs text-amber-700 font-medium">due or overdue</span>
            </div>
            <p class="text-xs text-slate-400 mt-2">Requires phone call / reminder</p>
        </div>
    </div>

    <!-- Dual Column Section: Recent Inquiries & Today's Follow-up Reminders -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Recent Inquiries Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Recent Student Inquiries</h3>
                    <p class="text-xs text-slate-500">Latest 10 inquiries recorded in the school system</p>
                </div>
                <a href="{{ route('students.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <span>View All ({{ \App\Models\Student::count() }})</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[620px]">
                    <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Student & Parent</th>
                            <th class="px-4 py-3.5">Phone Number</th>
                            <th class="px-4 py-3.5">Class</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5">History</th>
                            <th class="px-6 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentInquiries as $student)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $student->student_name }}</div>
                                    <div class="text-xs text-slate-500">Parent: {{ $student->parent_name ?: 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md">
                                        {{ $student->phone_number }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs font-medium text-slate-600">
                                    {{ $student->class_applying_for }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $student->status_badge['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $student->status_badge['dot'] }}"></span>
                                        {{ $student->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $student->callLogs->count() }} contact(s)
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-600 hover:text-white hover:bg-blue-600 transition-colors border border-blue-200 hover:border-transparent">
                                        <span>Timeline</span>
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    No student inquiries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Today's Follow-up Reminders (1 col) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Follow-up Reminders
                    </h3>
                    <p class="text-xs text-slate-500">Calls scheduled for today or overdue</p>
                </div>
                <a href="{{ route('calls.followUps') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    View All
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[480px]">
                @forelse($followUpReminders as $reminder)
                    <div class="p-4 hover:bg-amber-50/40 transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <a href="{{ route('students.show', $reminder->student) }}" class="font-bold text-sm text-slate-900 hover:text-blue-600">
                                {{ $reminder->student->student_name }}
                            </a>
                            @if($reminder->next_follow_up_date->isPast() && !$reminder->next_follow_up_date->isToday())
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">OVERDUE</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">DUE TODAY</span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-500 flex items-center gap-2 mb-2 font-mono">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>{{ $reminder->student->phone_number }}</span>
                            <span>•</span>
                            <span class="font-sans font-medium text-slate-600">{{ $reminder->student->class_applying_for }}</span>
                        </div>

                        <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 line-clamp-2 italic mb-2">
                            "{{ $reminder->remarks }}"
                        </p>

                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span>Last call by: {{ $reminder->user->name }}</span>
                            <a href="{{ route('students.show', $reminder->student) }}#add-call" class="font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                <span>Call Now</span>
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-semibold text-slate-700">No pending follow-ups!</p>
                        <p class="text-xs text-slate-400 mt-1">All scheduled follow-ups are up to date.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
