@extends('layouts.admission')

@section('title', $employee->first_name . ' ' . $employee->last_name . ' - Employee Details - ESSU Admission System')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg whitespace-pre-line">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Employee Details</h1>
            <p class="mt-2 text-sm text-gray-600">{{ $employee->first_name }} {{ $employee->middle_name ? $employee->middle_name . ' ' : '' }}{{ $employee->last_name }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admission.employees.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <!-- Employee Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h2>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $employee->first_name }} 
                    {{ $employee->middle_name ? $employee->middle_name . ' ' : '' }}
                    {{ $employee->last_name }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Department</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $employee->department->department_name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    @php
                        $statusColor = strtolower($employee->status) === 'active' 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <!-- Login Account Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Login Account</h2>
        
        @php
            $currentUser = Auth::guard('admission')->user();
            $isAdmin = $currentUser && $currentUser->role === 'Admin';
        @endphp

        @if($employee->admissionUser)
            <!-- Account Exists -->
            <div class="space-y-4">
                <div class="p-4 {{ strtolower($employee->status) === 'active' ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} border rounded-lg">
                    <p class="text-sm {{ strtolower($employee->status) === 'active' ? 'text-green-800' : 'text-gray-600' }} font-medium mb-3">
                        Login account exists
                        @if(strtolower($employee->status) !== 'active')
                            <span class="ml-2 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Employee Inactive
                            </span>
                        @endif
                    </p>
                    <dl class="space-y-2">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-700">Username:</dt>
                            <dd class="text-sm text-gray-900 font-mono">{{ $employee->admissionUser->username }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-700">Role:</dt>
                            <dd class="text-sm text-gray-900">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $employee->admissionUser->role === 'Admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $employee->admissionUser->role }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-700">Account Status:</dt>
                            <dd class="text-sm text-gray-900">
                                @php
                                    $accountStatus = $employee->admissionUser->account_status ?? 'active';
                                    $accountStatusColor = $accountStatus === 'active' 
                                        ? 'bg-green-100 text-green-800' 
                                        : 'bg-red-100 text-red-800';
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $accountStatusColor }}">
                                    {{ ucfirst($accountStatus) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                @if($isAdmin)
                    <div class="flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('admission.employees.reset-username', $employee) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to reset the username?')"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Username
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admission.employees.reset-password', $employee) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to reset the password?')"
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Reset Password
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admission.employees.toggle-account-status', $employee) }}" class="inline">
                            @csrf
                            @php
                                $accountStatus = $employee->admissionUser->account_status ?? 'active';
                                $isDisabled = $accountStatus === 'disabled';
                            @endphp
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to {{ $isDisabled ? 'enable' : 'disable' }} this account?')"
                                    class="inline-flex items-center px-4 py-2 {{ $isDisabled ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($isDisabled)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @endif
                                </svg>
                                {{ $isDisabled ? 'Enable Account' : 'Disable Account' }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">Only Admin can manage login accounts.</p>
                    </div>
                @endif
            </div>
        @else
            <!-- No Account -->
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-sm text-gray-600 mb-4">This employee does not have a login account.</p>
                
                @if($isAdmin)
                    @if(strtolower($employee->status) === 'active')
                        <form method="POST" action="{{ route('admission.employees.create-account', $employee) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Create login account for this employee?')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Login Account
                            </button>
                        </form>
                    @else
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">Cannot create account for an inactive employee.</p>
                        </div>
                    @endif
                @else
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">Only Admin can create login accounts.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

