@extends('layouts.public')

@section('title', 'About EduTrack CRM - School Inquiry System')

@section('content')
<div class="py-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600">About the Solution</span>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mt-2 mb-6">Designed Specifically for School Admissions & Front Desks</h1>
            <p class="text-lg text-slate-600 leading-relaxed">
                EduTrack CRM was conceived to solve the everyday chaos of student inquiries. When a prospective parent calls a school multiple times across different days or shifts, different front-desk officers answer the phone. Without a unified system, staff repeat the same questions, miss critical follow-up promises, and lose prospective enrollments.
            </p>
        </div>
    </div>
</div>

<div class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="p-8 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold mb-4">
                    01
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Our Mission</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Ensure zero lost inquiries, eliminate duplicate records, and provide school front desks with the instant context needed to impress prospective families from the first second of the call.
                </p>
            </div>

            <div class="p-8 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold mb-4">
                    02
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Multi-Staff Harmony</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Colleagues can instantly view previous call notes taken by any staff member. The caller never feels like they are starting from scratch or speaking to a disjointed team.
                </p>
            </div>

            <div class="p-8 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold mb-4">
                    03
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Actionable Intelligence</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Administrators gain transparent insight into conversion bottlenecks, top inquiry channels (Facebook, Referrals, Walk-ins), and individual staff call volumes.
                </p>
            </div>
        </div>

        <!-- School Contact Card -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 text-white p-8 sm:p-12 shadow-xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-400">School Administration</span>
                    <h2 class="text-3xl font-extrabold tracking-tight mt-1 mb-4">{{ $schoolName }}</h2>
                    <p class="text-slate-300 text-sm leading-relaxed mb-6">
                        For any questions regarding system implementation, staff onboarding, or student admission protocols, please contact the admissions helpdesk.
                    </p>
                    <div class="space-y-3 text-sm text-slate-200">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-blue-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <span>Phone: {{ $schoolPhone }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-blue-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <span>Email: {{ $schoolEmail }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10">
                    <h3 class="text-lg font-bold text-white mb-2">Live Demonstration Access</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-6">
                        Test all features including instant search, duplicate warning detection, adding call history, and reviewing administrator charts.
                    </p>
                    <a href="{{ route('login') }}" class="block text-center w-full py-3 px-4 rounded-xl font-bold text-sm bg-blue-600 hover:bg-blue-500 text-white shadow-lg transition-colors">
                        Launch Demo System &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
