<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduTrack CRM')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 selection:bg-blue-600 selection:text-white"
      x-data="{ sidebarOpen: false }"
      @keydown.window.slash.prevent="$refs.globalSearchInput.focus()">

    <!-- Toast Notifications -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-xl shadow-2xl border border-slate-700 transition-all transform ease-out duration-300">
            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm font-medium">{{ session('success') }}</p>
            <button @click="show = false" class="text-slate-400 hover:text-white ml-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             class="fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-rose-900 text-white px-5 py-3.5 rounded-xl shadow-2xl border border-rose-700 transition-all transform ease-out duration-300">
            <div class="w-7 h-7 rounded-lg bg-rose-500/20 text-rose-300 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-sm font-medium">{{ session('error') }}</p>
            <button @click="show = false" class="text-rose-300 hover:text-white ml-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-cloak
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden transition-opacity"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/90 flex flex-col transition-transform duration-300 ease-in-out lg:static shrink-0">
            
            <!-- Sidebar Header / Logo -->
            <div class="h-18 px-6 flex items-center justify-between border-b border-slate-100">
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/25">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-lg font-bold text-slate-900 tracking-tight">EduTrack</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">CRM</span>
                        </div>
                        <p class="text-[11px] text-slate-500 truncate max-w-[140px]">{{ \App\Models\Setting::get('school_name', 'International School') }}</p>
                    </div>
                </a>

                <button @click="sidebarOpen = false" class="lg:hidden p-1.5 text-slate-400 hover:text-slate-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Sidebar Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
                @if(Auth::user()->isAdmin())
                    <!-- Admin Primary Navigation -->
                    <div>
                        <div class="flex items-center justify-between px-3 mb-2">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Administration</p>
                            <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded bg-blue-100 text-blue-700">ADMIN</span>
                        </div>
                        <nav class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Admin Dashboard</span>
                            </a>

                            <a href="{{ route('admin.branches.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.branches.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Branch Campuses</span>
                                <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('admin.branches.*') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">{{ \App\Models\Branch::count() }}</span>
                            </a>

                            <a href="{{ route('admin.courses.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.courses.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Course Masters</span>
                            </a>

                            <a href="{{ route('admin.users.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span>Manage Staff Accounts</span>
                            </a>

                            <a href="{{ route('admin.reports.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Reports & Analytics</span>
                            </a>

                            <a href="{{ route('admin.activityLogs.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.activityLogs.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Activity Audit Logs</span>
                            </a>

                            <a href="{{ route('admin.settings.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>System Settings</span>
                            </a>
                        </nav>
                    </div>

                    <!-- Admissions & Inquiries (Operations Access) -->
                    <div class="pt-4 border-t border-slate-100">
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Admissions & Desks</p>
                        <nav class="space-y-1">
                            <a href="{{ route('admissions.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admissions.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Admissions & Fees</span>
                                <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('admissions.*') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">{{ \App\Models\Admission::count() }}</span>
                            </a>

                            <a href="{{ route('tasks.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('tasks.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <span>Daily Task Board</span>
                            </a>

                            <a href="{{ route('students.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('students.index') || request()->routeIs('students.show') || request()->routeIs('students.edit') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>All Inquiries</span>
                                <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('students.index') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">{{ \App\Models\Student::count() }}</span>
                            </a>

                            <a href="{{ route('calls.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('calls.index') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Call Log History</span>
                            </a>
                        </nav>
                    </div>

                @else
                    <!-- Staff Primary Navigation -->
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Inquiry & Call Desk</p>
                        <nav class="space-y-1">
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Staff Dashboard</span>
                            </a>

                            <a href="{{ route('tasks.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('tasks.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <span>Daily Task Board</span>
                            </a>

                            <a href="{{ route('admissions.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admissions.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Admissions & Fees</span>
                                <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('admissions.*') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">{{ Auth::user()->branch_id ? \App\Models\Admission::where('branch_id', Auth::user()->branch_id)->count() : \App\Models\Admission::count() }}</span>
                            </a>

                            <a href="{{ route('students.create') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('students.create') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                <span>Add New Inquiry</span>
                                <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ request()->routeIs('students.create') ? 'bg-blue-500 text-white' : 'bg-emerald-100 text-emerald-700' }}">NEW</span>
                            </a>

                            <a href="{{ route('students.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('students.index') || request()->routeIs('students.show') || request()->routeIs('students.edit') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>All Inquiries</span>
                                <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('students.index') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">{{ Auth::user()->branch_id ? \App\Models\Student::where('branch_id', Auth::user()->branch_id)->count() : \App\Models\Student::count() }}</span>
                            </a>

                            <a href="{{ route('calls.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('calls.index') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Call Log History</span>
                            </a>
                        </nav>
                    </div>
                @endif
            </div>

            <!-- Sidebar User Card Footer -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ Auth::user()->isAdmin() ? 'Administrator' : 'Admissions Staff' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Sign Out" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="h-16 lg:h-18 bg-white border-b border-slate-200/90 px-3 sm:px-6 lg:px-8 flex items-center justify-between gap-3 z-30 shrink-0 min-w-0">
                
                <!-- Left: Mobile Sidebar Toggle & Page Title / Breadcrumb -->
                <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                    <button @click="sidebarOpen = true" type="button" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl shrink-0" aria-label="Open sidebar">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">@yield('page_title', 'EduTrack CRM')</h1>
                        <p class="text-[11px] sm:text-xs text-slate-500 truncate hidden md:block">@yield('page_subtitle', Carbon\Carbon::today()->format('l, F j, Y'))</p>
                    </div>
                </div>

                <!-- Center: PROMINENT GLOBAL SEARCH BAR with Alpine.js Live Search (Visible on xl+ to avoid crowding on 1366px laptops) -->
                <div class="hidden xl:flex flex-1 max-w-xs xl:max-w-md relative mx-3"
                     x-data="{
                         query: '',
                         results: [],
                         loading: false,
                         open: false,
                         timeout: null,
                         performSearch() {
                             clearTimeout(this.timeout);
                             if (this.query.trim().length < 2) {
                                 this.results = [];
                                 this.open = false;
                                 return;
                             }
                             this.loading = true;
                             this.timeout = setTimeout(() => {
                                 fetch('{{ route('students.liveSearch') }}?q=' + encodeURIComponent(this.query))
                                     .then(res => res.json())
                                     .then(data => {
                                         this.results = data.results || [];
                                         this.loading = false;
                                         this.open = true;
                                     })
                                     .catch(() => {
                                         this.loading = false;
                                     });
                             }, 250);
                         }
                     }"
                     @click.away="open = false">
                     
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input x-ref="globalSearchInput"
                               x-model="query"
                               @input="performSearch()"
                               @focus="if(results.length > 0 || query.trim().length >= 2) open = true"
                               @keydown.escape="open = false"
                               type="text"
                               placeholder="Search phone or student name..."
                               class="w-full pl-9 sm:pl-10 pr-12 sm:pr-20 py-2 sm:py-2.5 text-xs sm:text-sm bg-slate-100 hover:bg-slate-100/80 focus:bg-white border border-transparent focus:border-blue-500 rounded-xl focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-slate-400">
                        
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5 pointer-events-none">
                            <div x-show="loading" x-cloak class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                            <kbd class="hidden sm:inline-block text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded px-1.5 py-0.5 shadow-2xs">/</kbd>
                        </div>
                    </div>

                    <!-- Live Instant Results Dropdown Box -->
                    <div x-show="open" x-cloak
                         class="absolute left-1/2 -translate-x-1/2 sm:left-auto sm:right-0 sm:translate-x-0 w-[400px] sm:w-[480px] max-w-[92vw] mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 transition-all max-h-96 overflow-y-auto">
                        
                        <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-medium">Found <span class="text-blue-600 font-bold" x-text="results.length"></span> matching record(s)</span>
                            <span>Click to view inquiry</span>
                        </div>

                        <template x-if="results.length === 0 && !loading">
                            <div class="p-6 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-800">No student inquiry found</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">This phone or student is not registered in the system yet.</p>
                                <div class="mt-4 flex flex-col sm:flex-row items-center justify-center gap-2">
                                    <a :href="'{{ route('students.create') }}?' + (/^[0-9+ -]+$/.test(query.trim()) ? 'phone=' : 'name=') + encodeURIComponent(query.trim())" 
                                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3.5 py-2 rounded-xl shadow-sm transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        <span>Save with Inquiry</span>
                                    </a>
                                    <a :href="'{{ route('admissions.create') }}?' + (/^[0-9+ -]+$/.test(query.trim()) ? 'phone=' : 'name=') + encodeURIComponent(query.trim())" 
                                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-3.5 py-2 rounded-xl shadow-sm transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Save with Admission</span>
                                    </a>
                                </div>
                            </div>
                        </template>

                        <ul class="divide-y divide-slate-100">
                            <template x-for="item in results" :key="item.id">
                                <li>
                                    <a :href="item.url" class="p-3.5 flex items-center justify-between hover:bg-blue-50/50 transition-colors group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0 group-hover:scale-105 transition-transform">
                                                <span x-text="item.student_name.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-semibold text-slate-900 group-hover:text-blue-600" x-text="item.student_name"></span>
                                                    <span class="text-xs text-slate-400">•</span>
                                                    <span class="text-xs font-medium text-slate-600" x-text="item.class_applying_for"></span>
                                                </div>
                                                <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                                                    <span class="font-mono text-slate-700 font-medium flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                        <span x-text="item.phone_number"></span>
                                                    </span>
                                                    <span>Parent: <span x-text="item.parent_name"></span></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 text-right shrink-0">
                                            <div>
                                                <span :class="item.badge.bg" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset" x-text="item.status"></span>
                                                <p class="text-[11px] text-slate-400 mt-0.5" x-text="item.total_contacts + ' contact(s)'"></p>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Right Actions: Branch Indicator, Quick Add Actions & User Menu -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Branch Badge -->
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-xl text-[11px] sm:text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200 shrink-0">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>All Campuses</span>
                        </span>
                    @elseif(Auth::user()->branch)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-xl text-[11px] sm:text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shrink-0 max-w-[150px] sm:max-w-[200px]">
                            <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                            <span class="truncate">{{ Auth::user()->branch->name }}</span>
                            <span class="text-[10px] font-mono bg-blue-200 text-blue-900 px-1 py-0.2 rounded shrink-0">{{ Auth::user()->branch->code }}</span>
                        </span>
                    @endif

                    @if(Auth::user()->isStaff())
                        <a href="{{ route('admissions.create') }}" class="hidden lg:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>New Admission</span>
                        </a>
                    @endif

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ userMenu: false }">
                        <button @click="userMenu = !userMenu" type="button" class="flex items-center gap-2 p-1 rounded-xl hover:bg-slate-100 transition-colors">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="userMenu" x-cloak
                             @click.away="userMenu = false"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
                            
                            <div class="px-4 py-2.5 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ Auth::user()->role }}
                                </span>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>My Profile</span>
                            </a>

                            @if(Auth::user()->isAdmin())
                                @if(request()->routeIs('admin.*'))
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-medium">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        <span>Switch to Staff Desk</span>
                                    </a>
                                @else
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-medium">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        <span>Switch to Admin Panel</span>
                                    </a>
                                @endif
                            @endif

                            <div class="border-t border-slate-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 font-medium">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
