@extends('layouts.app')

@section('title', 'Edit Staff User - EduTrack CRM')
@section('page_title', 'Edit User Account')
@section('page_subtitle', 'Update staff member profile, allocated branch, role, and active status')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Users List</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xl font-bold text-slate-900">Edit User: {{ $user->name }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Modify permissions, allocated campus branch, and contact settings</p>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Full Name <span class="text-rose-500">*</span>
                </label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Username & Email Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Username / Login ID <span class="text-rose-500">*</span>
                    </label>
                    <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required
                           class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Used to log in to the portal</p>
                    @error('username') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Official communication email</p>
                    @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- ASSIGNED CAMPUS BRANCH SELECTOR -->
            <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <label for="branch_id" class="text-xs font-bold uppercase tracking-wider text-blue-950">
                        Allocated Campus Branch
                    </label>
                </div>
                <select id="branch_id" name="branch_id"
                        class="w-full px-4 py-2.5 text-sm font-semibold bg-white border border-blue-300 rounded-xl focus:ring-4 focus:ring-blue-500/15 focus:border-blue-600 outline-none transition-all">
                    <option value="">🌐 None / All Campuses (Global Admin / Unassigned)</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id', $user->branch_id) == $b->id ? 'selected' : '' }}>
                            🏫 {{ $b->name }} (Code: {{ $b->code }}) - {{ $b->city ?? 'Campus' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-blue-700/80 mt-1.5 leading-relaxed">
                    Select which branch campus this staff member operates from. Inquiries, calls, and student admissions registered by them are connected to this branch.
                </p>
                @error('branch_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Role & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        System Role <span class="text-rose-500">*</span>
                    </label>
                    <select id="role" name="role" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:border-blue-500">
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff (Inquiry & Call Desk)</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                    </select>
                    @error('role') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Account Status <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:border-blue-500 font-semibold">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive / Deactivated</option>
                    </select>
                    @error('status') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Optional Password Change -->
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Change Password (Leave blank to keep current)
                </label>
                <input id="password" name="password" type="password" placeholder="Leave empty to keep unchanged"
                       class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit" class="px-7 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
