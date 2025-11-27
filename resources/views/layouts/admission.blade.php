<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ESSU Admission System')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="hidden lg:flex lg:flex-shrink-0 lg:w-64 lg:flex-col">
            <div class="flex flex-col flex-grow bg-gray-900 text-white overflow-y-auto">
                <!-- Logo/Title -->
                <div class="flex items-center flex-shrink-0 px-4 py-6 border-b border-gray-800">
                    <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h1 class="text-xl font-bold">ESSU Admission</h1>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1">
                    <a href="{{ route('admission.dashboard') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Applicants -->
                    <div class="space-y-1">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Applicants</div>
                        <a href="{{ route('admission.applicants.index') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.applicants.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Applicant List
                        </a>
                        <a href="{{ route('admission.applicants.create') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.applicants.create') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Register Applicant
                        </a>
                    </div>

                    <!-- Exams -->
                    <div class="space-y-1">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Exams</div>
                        <a href="{{ route('admission.exams.index') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.exams.*') && !request()->routeIs('admission.exams.create') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exam List
                        </a>
                        <a href="{{ route('admission.exams.create') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.exams.create') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Exam
                        </a>
                    </div>

                    <!-- Master Data -->
                    <div class="space-y-1">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</div>
                        <a href="{{ route('admission.courses.index') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.courses.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Courses
                        </a>
                        <a href="{{ route('admission.campuses.index') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.campuses.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Campuses
                        </a>
                        <a href="{{ route('admission.departments.index') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.departments.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Departments
                        </a>
                        @php
                            $currentUser = Auth::guard('admission')->user();
                            $canViewEmployees = $currentUser && in_array($currentUser->role, ['Admin', 'Staff']);
                        @endphp
                        @if($canViewEmployees)
                            <a href="{{ route('admission.employees.index') }}" 
                               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.employees.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Employees
                            </a>
                        @endif
                    </div>
                    
                    <!-- My Account -->
                    <a href="{{ route('admission.my-account.edit') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.my-account.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        My Account
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('admission.settings.index') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.settings.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                </nav>

                <!-- Logout -->
                <div class="flex-shrink-0 p-4 border-t border-gray-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden transition-opacity duration-300"></div>

        <!-- Mobile sidebar drawer -->
        <aside id="mobile-sidebar" class="fixed top-0 left-0 bottom-0 z-50 w-64 max-w-64 flex-shrink-0 bg-gray-900 text-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden shadow-2xl">
            <div class="flex flex-col h-full bg-gray-900">
                <!-- Logo/Title -->
                <div class="flex items-center justify-between flex-shrink-0 px-4 py-6 border-b border-gray-800">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h1 class="text-xl font-bold">ESSU Admission</h1>
                    </div>
                    <button id="close-sidebar" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <!-- Same navigation items as desktop sidebar -->
                    <a href="{{ route('admission.dashboard') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Applicants</div>
                    <a href="{{ route('admission.applicants.index') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.applicants.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Applicant List
                    </a>
                    <a href="{{ route('admission.applicants.create') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.applicants.create') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Register Applicant
                    </a>
                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Exams</div>
                    <a href="{{ route('admission.exams.index') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.exams.*') && !request()->routeIs('admission.exams.create') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exam List
                    </a>
                    <a href="{{ route('admission.exams.create') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.exams.create') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Exam
                    </a>
                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</div>
                    <a href="{{ route('admission.courses.index') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.courses.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Courses
                    </a>
                    <a href="{{ route('admission.campuses.index') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.campuses.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Campuses
                    </a>
                    <a href="{{ route('admission.departments.index') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.departments.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Departments
                    </a>
                    @php
                        $currentUser = Auth::guard('admission')->user();
                        $canViewEmployees = $currentUser && in_array($currentUser->role, ['Admin', 'Staff']);
                    @endphp
                    @if($canViewEmployees)
                        <a href="{{ route('admission.employees.index') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.employees.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Employees
                        </a>
                    @endif
                    <a href="{{ route('admission.settings.index') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.settings.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                    <a href="{{ route('admission.my-account.edit') }}" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admission.my-account.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        My Account
                    </a>
                </nav>
                <div class="flex-shrink-0 p-4 border-t border-gray-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white">Logout</button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top navigation bar -->
            <header class="flex items-center justify-between flex-shrink-0 px-4 py-4 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <button id="mobile-menu-button" class="lg:hidden text-gray-500 hover:text-gray-700 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800">ESSU Admission System</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, <strong>{{ Auth::guard('admission')->user()->username }}</strong></span>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @include('components.toast')

    <script>
        // Mobile sidebar toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const closeSidebar = document.getElementById('close-sidebar');

        function openSidebar() {
            mobileSidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
            // Prevent body scroll when drawer is open
            document.body.style.overflow = 'hidden';
        }

        function closeSidebarFunc() {
            mobileSidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
            // Restore body scroll when drawer is closed
            document.body.style.overflow = '';
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', openSidebar);
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarFunc);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebarFunc);
        }
    </script>
</body>
</html>

