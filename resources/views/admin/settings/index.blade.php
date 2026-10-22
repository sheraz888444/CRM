@extends('layouts.app')

@section('title', 'School & System Settings - EduTrack CRM')
@section('page_title', 'School & System Configuration')
@section('page_subtitle', 'Configure institution identity, admission classes, and marketing inquiry channels')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xl font-bold text-slate-900">Institutional Identity & Classes Setup</h2>
            <p class="text-xs text-slate-500 mt-0.5">These settings customize the CRM for your school's unique academic offerings</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6 sm:p-8 space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: School Identity -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">1</span>
                    School Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="school_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">School Name <span class="text-rose-500">*</span></label>
                        <input id="school_name" name="school_name" type="text" value="{{ old('school_name', $schoolName) }}" required
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all font-semibold text-slate-900">
                    </div>

                    <div>
                        <label for="school_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Official Helpdesk Phone <span class="text-rose-500">*</span></label>
                        <input id="school_phone" name="school_phone" type="text" value="{{ old('school_phone', $schoolPhone) }}" required
                               class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div>
                        <label for="school_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Admissions Email <span class="text-rose-500">*</span></label>
                        <input id="school_email" name="school_email" type="email" value="{{ old('school_email', $schoolEmail) }}" required
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label for="school_address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Campus Physical Address</label>
                        <input id="school_address" name="school_address" type="text" value="{{ old('school_address', $schoolAddress) }}"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Classes List -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">2</span>
                    Available Admission Classes (Comma Separated)
                </h3>

                <div>
                    <label for="classes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Classes & Grades</label>
                    <textarea id="classes" name="classes" rows="3" required
                              class="w-full p-4 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all leading-relaxed">{{ old('classes', $classes) }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Separate classes with a comma (e.g. Playgroup, Nursery, Prep / KG, Grade 1, Grade 2, O-Level, A-Level).</p>
                </div>
            </div>

            <!-- Section 3: Inquiry Sources List -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">3</span>
                    Marketing & Inquiry Sources (Comma Separated)
                </h3>

                <div>
                    <label for="sources" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Inquiry Channels</label>
                    <textarea id="sources" name="sources" rows="3" required
                              class="w-full p-4 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all leading-relaxed">{{ old('sources', $sources) }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Separate channels with a comma (e.g. Walk-in, Phone Call, Facebook, Instagram, Website, Referral, Billboard).</p>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-7 py-3 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
