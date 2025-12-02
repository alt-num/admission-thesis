@extends('layouts.admission')

@section('title', 'Applicants - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Applicants</h1>
        <p class="mt-2 text-sm text-gray-600">Manage applicant registrations</p>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admission.applicants.index') }}" class="flex items-center space-x-4">
            <div class="flex-1">
                <label for="search" class="sr-only">Search applicants</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by reference number, name, campus, or registration date..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admission.applicants.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App Ref No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applicants as $applicant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $applicant->app_ref_no }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $applicant->first_name }} 
                                {{ $applicant->middle_name ? $applicant->middle_name . ' ' : '' }}
                                {{ $applicant->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $applicant->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $applicant->campus->campus_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusLabels = [
                                        'Pending' => 'Pending',
                                        'Qualified' => 'Qualified',
                                        'NotQualified' => 'Not Qualified',
                                        'Flagged' => 'FLAGGED',
                                    ];
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'Qualified' => 'bg-green-100 text-green-800',
                                        'NotQualified' => 'bg-red-100 text-red-800',
                                        'Flagged' => 'bg-red-200 text-red-900',
                                    ];
                                    // If flagged, show Flagged status and override others
                                    $displayStatus = $applicant->status === 'Flagged' ? 'Flagged' : $applicant->status;
                                    $label = $statusLabels[$displayStatus] ?? $displayStatus;
                                    $color = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ $label }}
                                </span>
                                @if($applicant->applicantUser && strtolower($applicant->applicantUser->account_status) === 'disabled')
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                        DISABLED
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $applicant->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admission.applicants.show', $applicant) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        View
                                    </a>
                                    @if($applicant->status === 'Flagged')
                                        <form method="POST" action="{{ route('admission.applicants.toggle-flagged', $applicant) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                                Unflag
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admission.applicants.toggle-flagged', $applicant) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Flag
                                            </button>
                                        </form>
                                    @endif
                                    @if($applicant->applicantUser)
                                        @if(strtolower($applicant->applicantUser->account_status) === 'active')
                                            <form method="POST" action="{{ route('admission.applicants.toggle-account-status', $applicant) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-gray-600 hover:text-gray-900">
                                                    Disable
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admission.applicants.toggle-account-status', $applicant) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">
                                                    Enable
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                @if(request('search'))
                                    No applicants found matching your search criteria.
                                @else
                                    No applicants found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applicants->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $applicants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

