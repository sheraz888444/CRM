@extends('layouts.app')

@section('title', 'Edit Admission #' . $admission->roll_no . ' - EduTrack CRM')
@section('page_title', 'Edit Admission')
@section('page_subtitle', 'Update student particulars, enrolled programs, or fee package for ' . $admission->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Edit Admission File</h3>
                <p class="text-xs text-slate-500">Roll No: <strong class="font-mono text-blue-600">{{ $admission->roll_no }}</strong> • Campus: {{ $admission->branch->name ?? 'Main' }}</p>
            </div>

            <a href="{{ route('admissions.show', $admission) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back</span>
            </a>
        </div>

        <form action="{{ route('admissions.update', $admission) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Student Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $admission->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                </div>

                <!-- Father Name -->
                <div>
                    <label for="father_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Father / Guardian Name</label>
                    <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $admission->father_name) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Primary Phone <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $admission->phone) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none font-mono">
                </div>

                <!-- Alt Phone -->
                <div>
                    <label for="alt_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Alternate Phone</label>
                    <input type="text" name="alt_phone" id="alt_phone" value="{{ old('alt_phone', $admission->alt_phone) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none font-mono">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $admission->email) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Admission Status <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                        <option value="active" {{ old('status', $admission->status) === 'active' ? 'selected' : '' }}>Active Enrollment</option>
                        <option value="completed" {{ old('status', $admission->status) === 'completed' ? 'selected' : '' }}>Completed / Graduated</option>
                        <option value="cancelled" {{ old('status', $admission->status) === 'cancelled' ? 'selected' : '' }}>Cancelled / Left</option>
                    </select>
                </div>

                <!-- Courses Checkboxes -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Enrolled Courses <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php
                            $enrolledIds = $admission->courses->pluck('id')->toArray();
                        @endphp
                        @foreach($courses as $c)
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                                <input type="checkbox" name="courses[]" value="{{ $c->id }}"
                                       {{ in_array($c->id, old('courses', $enrolledIds)) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded">
                                <span class="text-xs font-bold text-slate-800">{{ $c->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Total Package & Discount -->
                <div>
                    <label for="total_package" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Total Package Fee (PKR) <span class="text-rose-500">*</span></label>
                    <input type="number" name="total_package" id="total_package" value="{{ old('total_package', $admission->total_package) }}" required min="0" step="any"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold focus:ring-2 focus:ring-blue-600 outline-none">
                </div>

                <div>
                    <label for="discount" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Discount (PKR)</label>
                    <input type="number" name="discount" id="discount" value="{{ old('discount', $admission->discount) }}" min="0" step="any"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold focus:ring-2 focus:ring-blue-600 outline-none">
                </div>

                <!-- Address -->
                <div class="sm:col-span-2">
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Residential Address</label>
                    <textarea name="address" id="address" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">{{ old('address', $admission->address) }}</textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admissions.show', $admission) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25">
                    Update Admission File
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
