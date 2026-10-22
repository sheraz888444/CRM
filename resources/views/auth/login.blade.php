@extends('layouts.public')

@section('title', 'Staff & Admin Login - EduTrack CRM')

@section('content')
<div class="min-h-[85vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 via-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                </svg>
            </div>
        </a>
        <h2 class="mt-4 text-2xl font-extrabold text-slate-900 tracking-tight">EduTrack CRM Portal</h2>
        <p class="mt-1 text-sm text-slate-500">Sign in to your staff or administrator account</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Flash Alert Messages -->
        @if(session('error'))
            <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-rose-900">Notice</p>
                    <p class="mt-0.5 text-xs text-rose-700 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3 shadow-xs animate-pulse">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-rose-900">Authentication Failed</p>
                    <p class="mt-0.5 text-xs text-rose-700 leading-relaxed">{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-emerald-900">Success</p>
                    <p class="mt-0.5 text-xs text-emerald-700 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-200/80">
            
            <!-- Quick Demo 1-Click Login Selector -->
            <div class="mb-6 p-4 rounded-2xl bg-gradient-to-br from-blue-50/80 to-indigo-50/50 border border-blue-100"
                 x-data="{
                     instantLogin(username, pwd) {
                         const userInput = document.getElementById('username');
                         const pwdInput = document.getElementById('password');
                         const form = document.getElementById('loginForm');
                         if (userInput && pwdInput && form) {
                             userInput.value = username;
                             pwdInput.value = pwd;
                             form.submit();
                         }
                     }
                 }">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                        Instant 1-Click Demo Login
                    </span>
                    <span class="text-[11px] text-blue-600 font-semibold">Select Dashboard</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <!-- Admin Portal (Full Multi-branch powers) -->
                    <button type="button" @click="instantLogin('admin', 'password')"
                            class="p-3 rounded-2xl bg-white border-2 border-blue-200 hover:border-blue-600 hover:shadow-lg text-left font-medium text-slate-700 transition-all group">
                        <div class="font-bold text-blue-700 flex items-center justify-between">
                            <span class="text-sm">Admin Portal</span>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold group-hover:bg-blue-600 group-hover:text-white transition-colors">LOGIN ➔</span>
                        </div>
                        <div class="mt-1 text-[11px] text-slate-500 font-mono">
                            user: <span class="text-slate-800 font-semibold">admin</span>
                        </div>
                        <span class="text-[10px] text-blue-600/80 font-medium block mt-1">Multi-Branch & Full Admin</span>
                    </button>

                    <!-- Staff Desk -->
                    <button type="button" @click="instantLogin('sarah', 'password')"
                            class="p-3 rounded-2xl bg-white border-2 border-blue-200 hover:border-blue-600 hover:shadow-lg text-left font-medium text-slate-700 transition-all group">
                        <div class="font-bold text-slate-800 flex items-center justify-between">
                            <span class="text-sm">Staff Desk</span>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold group-hover:bg-blue-600 group-hover:text-white transition-colors">LOGIN ➔</span>
                        </div>
                        <div class="mt-1 text-[11px] text-slate-500 font-mono">
                            user: <span class="text-slate-800 font-semibold">sarah</span>
                        </div>
                        <span class="text-[10px] text-emerald-600 font-medium block mt-1">Inquiries & Admissions</span>
                    </button>
                </div>
            </div>

            <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input id="username" name="login" type="text" value="{{ old('login', 'admin') }}" required autocomplete="username" autofocus
                               class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400 @error('login') border-rose-500 bg-rose-50/30 @enderror @error('email') border-rose-500 bg-rose-50/30 @enderror"
                               placeholder="e.g. admin or sarah">
                    </div>
                    @error('login')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" name="password" type="password" value="password" required autocomplete="current-password"
                               class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400 @error('password') border-rose-500 bg-rose-50/30 @enderror"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" checked class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                        <span class="text-slate-600 font-medium">Keep me signed in</span>
                    </label>

                    <span class="text-slate-400">Password: <code class="font-mono text-slate-600 font-bold">password</code></span>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-600/25 transition-all transform active:scale-[0.98] cursor-pointer">
                        <span>Sign In to System</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            EduTrack CRM • School Inquiry & Call Tracking System
        </p>
    </div>
</div>

<script>
    // Keep session active and refresh CSRF token automatically if tab was in background
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            fetch('{{ route("login") }}')
                .then(res => res.text())
                .then(html => {
                    const match = html.match(/name="_token"\s+value="([^"]+)"/);
                    if (match && match[1]) {
                        document.querySelectorAll('input[name="_token"]').forEach(el => el.value = match[1]);
                    }
                })
                .catch(() => {});
        }
    });
</script>
@endsection
