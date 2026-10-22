@extends('layouts.app')

@section('title', 'Edit ' . $student->student_name . ' - EduTrack CRM')
@section('page_title', 'Edit Student Inquiry')
@section('page_subtitle', 'Update contact information and admission status')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <!-- Back Navigation -->
    <div class="mb-6">
        <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Student Detail</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Edit Inquiry: {{ $student->student_name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Modify inquiry attributes and save changes</p>
            </div>
            <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg">
                {{ $student->phone_number }}
            </span>
        </div>

        <form method="POST" action="{{ route('students.update', $student) }}" class="p-6 sm:p-8 space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Contact Info -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">1</span>
                    Contact Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number', $student->phone_number) }}" required
                               class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                        @error('phone_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="alternate_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Alternate Phone / WhatsApp
                        </label>
                        <input id="alternate_phone" name="alternate_phone" type="text" value="{{ old('alternate_phone', $student->alternate_phone) }}"
                               class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Student Details -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">2</span>
                    Student & Family Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="student_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Student Name <span class="text-rose-500">*</span>
                        </label>
                        <input id="student_name" name="student_name" type="text" value="{{ old('student_name', $student->student_name) }}" required
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div>
                        <label for="parent_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Parent / Guardian Name
                        </label>
                        <input id="parent_name" name="parent_name" type="text" value="{{ old('parent_name', $student->parent_name) }}"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Email Address
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email', $student->email) }}"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Area / Locality <span class="text-rose-500">*</span>
                        </label>
                        <input id="city" name="city" type="text" required value="{{ old('city', $student->city) }}"
                               placeholder="e.g. Gulberg, Model Town, F-10, Saddar"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Student residential area/locality (Mandatory)</p>
                        @error('city') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Address
                        </label>
                        <input id="address" name="address" type="text" value="{{ old('address', $student->address) }}"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 3: Academic & Status -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs">3</span>
                    Academic & Admission Status
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="class_applying_for" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Class Applying For <span class="text-rose-500">*</span>
                        </label>
                        <select id="class_applying_for" name="class_applying_for" required
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                            @foreach($classes as $c)
                                <option value="{{ $c }}" {{ old('class_applying_for', $student->class_applying_for) == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="inquiry_source" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Inquiry Source <span class="text-rose-500">*</span>
                        </label>
                        <select id="inquiry_source" name="inquiry_source" required
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                            @foreach($sources as $s)
                                <option value="{{ $s }}" {{ old('inquiry_source', $student->inquiry_source) == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Inquiry Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all font-semibold">
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ old('status', $student->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label for="previous_school" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Previous School Attended
                        </label>
                        <input id="previous_school" name="previous_school" type="text" value="{{ old('previous_school', $student->previous_school) }}"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 4: Notes -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-4">Inquiry Notes</h3>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full p-4 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all">{{ old('notes', $student->notes) }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('students.show', $student) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-7 py-3 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
