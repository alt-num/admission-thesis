@extends('layouts.admission')

@section('title', 'Settings - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
        <p class="mt-2 text-sm text-gray-600">System configuration and preferences</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <!-- Active Exam -->
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Active Exam</h2>
            @if($activeExam)
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Exam Title</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $activeExam->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-gray-500">No active exam is currently set.</p>
            @endif
        </div>

        <!-- Coming Soon Notice -->
        <div class="pt-6 border-t border-gray-200">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <strong>Note:</strong> Settings management functionality is coming soon. This page is currently read-only.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

