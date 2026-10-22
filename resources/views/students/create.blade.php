@extends('layouts.app')

@section('title', 'Add New Student Inquiry - EduTrack CRM')
@section('page_title', 'Register New Student Inquiry')
@section('page_subtitle', 'Record parent contact details and initial inquiry notes')

@section('content')
<div class="max-w-4xl mx-auto"
     x-data="{
         phone: '{{ old('phone_number', request('phone', '')) }}',
         checkingPhone: false,
         duplicateFound: false,
         existingStudent: null,
         debounceTimer: null,
         dismissWarning: false,

         checkDuplicate() {
             this.dismissWarning = false;
             clearTimeout(this.debounceTimer);
             if (this.phone.trim().length < 6) {
                 this.duplicateFound = false;
                 this.existingStudent = null;
                 return;
             }

             this.checkingPhone = true;
             this.debounceTimer = setTimeout(() => {
                 fetch('{{ route('students.checkDuplicate') }}?phone=' + encodeURIComponent(this.phone))
                     .then(res => res.json())
                     .then(data => {
                         this.checkingPhone = false;
                         if (data.exists) {
                             this.duplicateFound = true;
                             this.existingStudent = data.student;
                         } else {
                             this.duplicateFound = false;
                             this.existingStudent = null;
                         }
                     })
                     .catch(() => {
                         this.checkingPhone = false;
                     });
             }, 350);
         }
     }"
     x-init="if (phone.length >= 6) checkDuplicate()">

    <!-- Back Navigation -->
    <div class="mb-6">
        <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to All Students</span>
        </a>
    </div>

    <!-- PROMINENT DUPLICATE NUMBER WARNING BANNER -->
    <div x-show="duplicateFound && !dismissWarning" x-cloak
         class="mb-8 p-6 rounded-3xl bg-amber-500/10 border-2 border-amber-500/40 shadow-xl shadow-amber-500/5 transition-all">
        <div class="flex flex-col sm:flex-row items-start justify-between gap-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-500/20">
                    <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-200 text-amber-900">Repeat Caller Warning</span>
                        <span class="text-xs text-amber-800 font-semibold" x-text="'Phone: ' + (existingStudent ? existingStudent.phone_number : '')"></span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mt-1">
                        ⚠ This number has already contacted our school!
                    </h3>
                    <p class="text-sm text-slate-700 mt-1 leading-relaxed">
                        Registered student: <strong class="text-slate-900 font-bold" x-text="existingStudent ? existingStudent.student_name : ''"></strong> 
                        (<span x-text="existingStudent ? existingStudent.class_applying_for : ''"></span>) • 
                        Status: <span class="font-semibold text-slate-900" x-text="existingStudent ? existingStudent.status : ''"></span>.
                    </p>
                    <p class="text-xs text-slate-600 mt-1">
                        First contact was on <strong x-text="existingStudent ? existingStudent.first_contact : ''"></strong> 
                        by staff officer <strong x-text="existingStudent ? existingStudent.staff_name : ''"></strong>. 
                        Total previous contacts: <span class="font-bold text-amber-900" x-text="existingStudent ? existingStudent.total_contacts : '0'"></span>.
                    </p>
                </div>
            </div>

            <!-- Action buttons: View History or Dismiss -->
            <div class="flex sm:flex-col items-center gap-2 w-full sm:w-auto shrink-0">
                <a :href="existingStudent ? existingStudent.url : '#'"
                   class="w-full text-center px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-600/25 transition-all">
                    View Full Call History &rarr;
                </a>
                <button type="button" @click="dismissWarning = true"
                        class="w-full px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white/80 hover:bg-white border border-slate-200 transition-colors">
                    Continue Creating Anyway
                </button>
            </div>
        </div>
    </div>

    <!-- Student Registration Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xl font-bold text-slate-900">Student & Inquiry Information</h2>
            <p class="text-xs text-slate-500 mt-0.5">Please fill out all the inquiry details accurately. Duplicate phone check runs automatically.</p>
        </div>

        <form method="POST" action="{{ route('students.store') }}" class="p-6 sm:p-8 space-y-8">
            @csrf

            <!-- Section 1: Contact & Phone (Top Priority) -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">1</span>
                    Primary Contact & Duplicate Check
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone Number (with live AJAX duplicate detection) -->
                    <div class="md:col-span-1">
                        <label for="phone_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input id="phone_number"
                                   name="phone_number"
                                   type="text"
                                   x-model="phone"
                                   @input="checkDuplicate()"
                                   required
                                   placeholder="e.g. 0300-5551234 or +923005551234"
                                   class="w-full pl-10 pr-10 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all @error('phone_number') border-rose-500 bg-rose-50/20 @enderror">

                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <div x-show="checkingPhone" x-cloak class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                <svg x-show="duplicateFound && !checkingPhone" x-cloak class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Live search automatically runs as you type.</p>
                        @error('phone_number')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alternate Phone -->
                    <div>
                        <label for="alternate_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Alternate Phone / WhatsApp (Optional)
                        </label>
                        <input id="alternate_phone"
                               name="alternate_phone"
                               type="text"
                               value="{{ old('alternate_phone') }}"
                               placeholder="e.g. 0321-9876543"
                               class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Student & Parent Details -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">2</span>
                    Student & Family Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Name -->
                    <div>
                        <label for="student_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Student Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input id="student_name"
                               name="student_name"
                               type="text"
                               value="{{ old('student_name', request('name', '')) }}"
                               required
                               placeholder="e.g. Rayyan Faisal"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all @error('student_name') border-rose-500 @enderror">
                        @error('student_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Parent / Guardian Name -->
                    <div>
                        <label for="parent_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Parent / Guardian Name
                        </label>
                        <input id="parent_name"
                               name="parent_name"
                               type="text"
                               value="{{ old('parent_name') }}"
                               placeholder="e.g. Faisal Javed"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Email Address (Optional)
                        </label>
                        <input id="email"
                               name="email"
                               type="email"
                               value="{{ old('email') }}"
                               placeholder="parent@example.com"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <!-- Area / Locality (Mandatory) -->
                    <div>
                        <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Area / Locality <span class="text-rose-500">*</span>
                        </label>
                        <input id="city"
                               name="city"
                               type="text"
                               required
                               value="{{ old('city') }}"
                               placeholder="e.g. Gulberg, Model Town, F-10, Saddar"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Student residential area/locality (Mandatory)</p>
                        @error('city') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Residential Address (Optional)
                        </label>
                        <input id="address"
                               name="address"
                               type="text"
                               value="{{ old('address') }}"
                               placeholder="e.g. House #12, Street #4, Sector F-10/2"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 3: Course & Campus Details -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">3</span>
                    Course & Campus Inquiry Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Branch Campus -->
                    <div>
                        <label for="branch_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Branch Campus <span class="text-rose-500">*</span>
                        </label>
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin')
                            <select id="branch_id" name="branch_id" required
                                    class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id', Auth::user()->branch_id) == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }} ({{ $b->code }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
                            <div class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-semibold text-slate-700">
                                {{ Auth::user()->branch?->name ?? 'Default Branch' }}
                            </div>
                        @endif
                    </div>

                    <!-- Target Course -->
                    <div>
                        <label for="course_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Target Course / Program
                        </label>
                        <select id="course_id" name="course_id"
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                            <option value="">-- Select Course --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (PKR {{ number_format($c->default_fee) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Inquiry Mode -->
                    <div>
                        <label for="mode_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Inquiry Mode <span class="text-rose-500">*</span>
                        </label>
                        <select id="mode_id" name="mode_id" required
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                            @foreach($inquiryModes as $m)
                                <option value="{{ $m->id }}" {{ old('mode_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Initial Status -->
                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Inquiry Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="status"
                                name="status"
                                required
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all font-semibold">
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ old('status', 'New') == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Next Follow-up Date -->
                    <div>
                        <label for="next_followup_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Schedule Follow-up Date
                        </label>
                        <input id="next_followup_date" name="next_followup_date" type="date"
                               value="{{ old('next_followup_date', date('Y-m-d', strtotime('+2 days'))) }}"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <p class="text-[10px] text-blue-600 mt-1">Auto-syncs with Daily Task Board</p>
                    </div>

                    <!-- Campus Visit Date -->
                    <div>
                        <label for="visit_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Campus Visit Date (Optional)
                        </label>
                        <input id="visit_date" name="visit_date" type="date"
                               value="{{ old('visit_date') }}"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 4: Initial Call Remarks -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">4</span>
                    Initial Inquiry Notes & Remarks
                </h3>

                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Inquiry Notes / Caller Remarks
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="3"
                              placeholder="Describe what the parent inquired about (fee structure, transport, campus visit request, etc.). This automatically forms the first contact log in the timeline."
                              class="w-full p-4 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('students.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-7 py-3 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Save Student Inquiry & Start Timeline</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
