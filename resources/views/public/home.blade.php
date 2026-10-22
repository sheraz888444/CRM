@extends('layouts.public')

@section('title', 'EduTrack CRM - School Inquiry & Call Management System')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-white via-blue-50/30 to-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100/80 text-blue-700 text-xs font-semibold mb-6 shadow-2xs border border-blue-200">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                <span>EduTrack CRM v2.0 • Purpose-Built for School Admissions</span>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15] mb-6">
                Never Ask a Parent <br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700">"Have you called us before?"</span>
            </h1>

            <!-- Description -->
            <p class="text-lg sm:text-xl text-slate-600 leading-relaxed mb-8 font-normal">
                Instant inquiry lookup by phone number, multi-staff call history timeline, automated duplicate detection, and admission conversion tracking designed specifically for school front desks.
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-xl text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-xl shadow-blue-600/25 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Login to Staff Portal</span>
                </a>
                <a href="#demo-preview" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-base font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 shadow-sm transition-all">
                    <span>See Live Demo Preview</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
            </div>

            <!-- Trust proof badges -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-500 font-medium">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Instant AJAX Duplicate Detection
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Shared Multi-Staff Timeline
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Role-Based Access (Admin / Staff)
                </span>
            </div>
        </div>

        <!-- Interactive UI Showcase Preview -->
        <div id="demo-preview" class="mt-14 max-w-5xl mx-auto rounded-2xl bg-white shadow-2xl border border-slate-200/80 overflow-hidden">
            <!-- Mock browser window bar -->
            <div class="px-5 py-3.5 bg-slate-100/90 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    <span class="ml-2 text-xs font-mono text-slate-500">EduTrack CRM • Student Inquiry & Repeat Caller Resolution</span>
                </div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                    Live Demo Ready
                </span>
            </div>

            <div class="p-6 sm:p-8 bg-slate-50/50">
                <!-- Duplicate warning simulation banner -->
                <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-amber-900">⚠ Duplicate Number Detected: 0300-5551234</p>
                            <p class="text-xs text-amber-700 mt-0.5">
                                This parent already contacted us for <strong>Hamza Tariq (Grade 6)</strong>! First contact was handled by <strong>Sarah Khan</strong> with <strong>3 previous logged contacts</strong>.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 whitespace-nowrap shadow-sm">
                        View Full History & Timeline &rarr;
                    </a>
                </div>

                <!-- Live stats strip -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs">
                        <p class="text-xs font-medium text-slate-500">Total Inquiries</p>
                        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalStudents }}+</p>
                        <p class="text-[11px] text-blue-600 font-medium mt-0.5">Captured in Database</p>
                    </div>
                    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs">
                        <p class="text-xs font-medium text-slate-500">Calls & Contacts</p>
                        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalCalls }}+</p>
                        <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Logged with Timestamps</p>
                    </div>
                    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs">
                        <p class="text-xs font-medium text-slate-500">Admissions Done</p>
                        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalAdmitted }}</p>
                        <p class="text-[11px] text-indigo-600 font-medium mt-0.5">Successful Enrollments</p>
                    </div>
                    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs">
                        <p class="text-xs font-medium text-slate-500">Active Staff</p>
                        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $staffCount }}</p>
                        <p class="text-[11px] text-slate-600 font-medium mt-0.5">Coordinated Team</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid Section -->
<section id="features" class="py-20 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">Comprehensive Features</h2>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">Everything School Staff Need for Inquiries</h3>
            <p class="text-slate-600 mt-3 text-sm">Eliminate paper registers and scattered WhatsApp notes with a single source of truth.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Instant Global Phone Search</h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Search by partial phone number with intelligent normalization (ignoring dashes, spaces, 0 or +92 prefix differences). Results appear instantly.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-amber-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Live Duplicate Detection</h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                    While staff types a phone number in the inquiry form, the system warns instantly if the caller is already in the database and links to their past history.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Vertical Call Timeline</h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Chronological audit timeline for every student inquiry: see which staff member spoke, call type (Incoming, Outgoing, WhatsApp, Walk-in), and remarks.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Follow-up Reminders</h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Never forget to call a prospective parent back. Automatic status badges for Today, Overdue, and Upcoming follow-ups keep the pipeline moving.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-purple-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 rounded-xl bg-purple-600 text-white flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Admin Analytics & Reports</h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Interactive charts for monthly inquiries, conversion rate %, top marketing sources, and staff performance metrics with one-click CSV export.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-rose-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 rounded-xl bg-rose-600 text-white flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Role Security & Audit Logs</h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Granular permissions between Administrators and Front-desk Staff. Full activity audit logs record every login, new inquiry, edit, and call logged.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">Operational Workflow</h2>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">How Front-Desk Staff Use EduTrack</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 relative shadow-sm">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg mb-4">1</div>
                <h4 class="font-bold text-slate-900 mb-2">Incoming Phone Call</h4>
                <p class="text-xs text-slate-600 leading-relaxed">Staff types the caller's phone number into the prominent global search bar at the top of any page.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 relative shadow-sm">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg mb-4">2</div>
                <h4 class="font-bold text-slate-900 mb-2">Instant Identity Check</h4>
                <p class="text-xs text-slate-600 leading-relaxed">System immediately identifies if this parent has contacted before, even if spoken to by another staff colleague.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 relative shadow-sm">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg mb-4">3</div>
                <h4 class="font-bold text-slate-900 mb-2">Log Call & Follow-up</h4>
                <p class="text-xs text-slate-600 leading-relaxed">Staff clicks "Add Call Entry", writes brief notes, schedules a follow-up date, and updates inquiry status.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 relative shadow-sm">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg mb-4">4</div>
                <h4 class="font-bold text-slate-900 mb-2">Convert to Admission</h4>
                <p class="text-xs text-slate-600 leading-relaxed">Admin views conversion rates and staff performance, helping the school achieve higher enrollment numbers.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-16 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-3xl font-extrabold tracking-tight mb-4">Ready to test the live demonstration?</h3>
        <p class="text-blue-100 max-w-xl mx-auto mb-8 text-sm">
            Login as Admin to review charts & exports, or login as Staff to experience rapid inquiry searching and duplicate detection.
        </p>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl font-bold text-blue-700 bg-white hover:bg-blue-50 shadow-xl transition-all transform hover:-translate-y-0.5">
            <span>Proceed to Demo Login</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
</section>
@endsection
