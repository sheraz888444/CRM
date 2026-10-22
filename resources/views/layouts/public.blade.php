<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduTrack CRM - School Inquiry & Call Management System')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans antialiased text-slate-800 selection:bg-blue-600 selection:text-white" x-data="{ mobileMenu: false }">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-blue-700 via-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">EduTrack</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">CRM</span>
                        </div>
                        <p class="text-xs text-slate-500 hidden sm:block">School Inquiry & Call Management</p>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-blue-600 font-semibold' : 'text-slate-600 hover:text-slate-900' }} transition-colors">Home</a>
                    <a href="{{ route('about') }}" class="text-sm font-medium {{ request()->routeIs('about') ? 'text-blue-600 font-semibold' : 'text-slate-600 hover:text-slate-900' }} transition-colors">About System</a>
                    <a href="{{ route('home') }}#features" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Key Features</a>
                    <a href="{{ route('home') }}#how-it-works" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">How It Works</a>
                </nav>

                <!-- Auth Action Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all">
                                <span>Admin Panel</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all">
                                <span>Staff Dashboard</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-600/25 transition-all transform active:scale-95">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Staff Portal Login</span>
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenu = !mobileMenu" type="button" class="md:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenu" x-cloak class="md:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Home</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">About System</a>
            <a href="{{ route('home') }}#features" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Key Features</a>
            <a href="{{ route('home') }}#how-it-works" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">How It Works</a>
            <div class="pt-2 border-t border-slate-100">
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600">Login to CRM</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold">
                            E
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">EduTrack CRM</span>
                    </div>
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed mb-4">
                        A modern, professional inquiry and call management system tailored for school admissions, front-desk staff, and administration. Never lose an inquiry or repeat caller again.
                    </p>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-950 text-emerald-400 border border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            System Live & Operational
                        </span>
                        <span>Laravel 11 • Tailwind CSS</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Home Overview</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About the Platform</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Staff Login</a></li>
                        <li><a href="{{ route('home') }}#features" class="hover:text-white transition-colors">Feature Highlights</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-4">Admissions Helpdesk</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>+92 300 1234567</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>admissions@edutrack.edu.pk</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Plot 42, Education Blvd, Sector H-8, Islamabad</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                <p>&copy; {{ date('Y') }} EduTrack CRM. Built for School Admissions & Management.</p>
                <div class="flex items-center gap-6">
                    <span class="text-slate-500">Fast • Reliable • Multi-staff repeat caller tracking</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
