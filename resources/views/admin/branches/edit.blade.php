@extends('layouts.app')

@section('title', 'Edit Branch - EduTrack CRM')
@section('page_title', 'Edit Branch')
@section('page_subtitle', 'Update campus details and status for ' . $branch->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Edit Campus: {{ $branch->name }}</h3>
                <p class="text-xs text-slate-500">Roll number prefix: <strong class="font-mono text-blue-600">{{ $branch->code }}-XXXX</strong></p>
            </div>
            <a href="{{ route('admin.branches.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back</span>
            </a>
        </div>

        <form action="{{ route('admin.branches.update', $branch) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Branch Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Branch / Campus Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all @error('name') border-rose-300 ring-2 ring-rose-100 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch Code -->
                <div>
                    <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Branch Code Prefix <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $branch->code) }}" required
                           maxlength="10"
                           class="w-full uppercase font-mono px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all @error('code') border-rose-300 ring-2 ring-rose-100 @enderror">
                    @error('code')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Status <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all">
                        <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Campus Phone / Helpline</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $branch->phone) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Campus Official Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $branch->email) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all">
                </div>

                <!-- Address -->
                <div class="sm:col-span-2">
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Physical Campus Address</label>
                    <textarea name="address" id="address" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all">{{ old('address', $branch->address) }}</textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.branches.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
