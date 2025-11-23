@extends('layouts.admission')

@section('title', $exam->title . ' - Exam Editor - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $exam->title }} – Exam Editor</h1>
        <p class="mt-2 text-sm text-gray-600">Coming Soon</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Exam Editor Coming Soon</h3>
            <p class="mt-2 text-sm text-gray-500">
                The exam editor for <strong>{{ $exam->title }}</strong> will be available soon.
            </p>
            <p class="mt-1 text-sm text-gray-500">
                This page will allow you to manage sections, subsections, and questions for this exam.
            </p>
            <div class="mt-6">
                <a href="{{ route('admission.exams.show', $exam) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Back to Exam Details
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

