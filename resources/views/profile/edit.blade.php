@extends('layouts.app')

@section('title', 'My Profile - EduTrack CRM')
@section('page_title', 'My Profile Settings')
@section('page_subtitle', 'Manage your account information and credentials')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-xl flex items-center justify-center shadow-md shadow-blue-500/20">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500">{{ $user->email }} • <span class="capitalize font-semibold text-blue-600">{{ $user->role }}</span></p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Your Full Name <span class="text-rose-500">*</span>
                </label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Email (readonly) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Email Address
                </label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full px-4 py-2.5 text-sm bg-slate-100 border border-slate-200 text-slate-500 rounded-xl cursor-not-allowed">
                <p class="text-[11px] text-slate-400 mt-1">To change your email address, please contact system administrator.</p>
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Contact Phone Number
                </label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
            </div>

            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4">Change Password</h3>

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Current Password
                        </label>
                        <input id="current_password" name="current_password" type="password"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                        @error('current_password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                New Password
                            </label>
                            <input id="password" name="password" type="password"
                                   class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                            @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Confirm New Password
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                   class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
