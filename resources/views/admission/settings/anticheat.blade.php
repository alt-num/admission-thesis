@extends('layouts.admission')

@section('title', 'Anti-Cheat Settings - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Anti-Cheat Settings</h1>
            <p class="mt-2 text-sm text-gray-600">Configure anti-cheat system behavior for applicant exams</p>
        </div>
        <a href="{{ route('admission.settings.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Settings
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-sm text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('admission.settings.anticheat.update') }}">
        @csrf
        @method('PUT')

        <!-- Master Toggle -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Anti-Cheat System</h2>
                    <p class="mt-1 text-sm text-gray-600">Master toggle for all anti-cheat functionality</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" 
                           {{ $settings->enabled ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">
                        {{ $settings->enabled ? 'ON' : 'OFF' }}
                    </span>
                </label>
            </div>
        </div>

        <!-- Feature Toggles -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Feature Controls</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Tab/Window Switch Detection</label>
                        <p class="text-xs text-gray-500">Detect when applicants switch tabs or windows</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="tab_switch_detection" value="1" 
                               {{ $settings->tab_switch_detection ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Focus-Loss Event Logging</label>
                        <p class="text-xs text-gray-500">Track and log focus loss events (logging only, no punishment)</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="focus_loss_violations" value="1" 
                               {{ $settings->focus_loss_violations ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Copy/Paste Blocking</label>
                        <p class="text-xs text-gray-500">Block copy, cut, and paste operations</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="copy_paste_blocking" value="1" 
                               {{ $settings->copy_paste_blocking ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Right-Click Blocking</label>
                        <p class="text-xs text-gray-500">Block right-click context menu</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="right_click_blocking" value="1" 
                               {{ $settings->right_click_blocking ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div>
                        <label class="text-sm font-medium text-gray-900">DevTools Hotkey Blocking</label>
                        <p class="text-xs text-gray-500">Block F12, Ctrl+Shift+I, and other dev tools shortcuts</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="devtools_hotkey_blocking" value="1" 
                               {{ $settings->devtools_hotkey_blocking ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div>
                        <label class="text-sm font-medium text-gray-900">IP Change Logging</label>
                        <p class="text-xs text-gray-500">Log when applicant's IP address changes during exam</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="ip_change_logging" value="1" 
                               {{ $settings->ip_change_logging ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Exam Code Requirement</label>
                        <p class="text-xs text-gray-500">Require exam code before starting exam</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="exam_code_required" value="1" 
                               {{ $settings->exam_code_required ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-3">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Refresh Detection</label>
                        <p class="text-xs text-gray-500">Detect and log page refreshes/reloads</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="refresh_detection" value="1" 
                               {{ $settings->refresh_detection ?? true ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Monitoring Controls -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Monitoring Controls</h2>
            <div class="space-y-4">
                <div>
                    <label for="idle_timeout_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Idle Timeout (Minutes)
                    </label>
                    <input type="number" 
                           id="idle_timeout_minutes" 
                           name="idle_timeout_minutes" 
                           value="{{ $settings->idle_timeout_minutes ?? 10 }}"
                           min="1" 
                           max="60" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Mark examinee as "Idle" after this many minutes of inactivity (default: 10, informational only)</p>
                </div>

                <div class="flex items-center justify-between py-3 border-t border-gray-200 pt-4">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Show Monitoring Banner</label>
                        <p class="text-xs text-gray-500">Display "Your behavior is monitored" banner to examinees</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="monitoring_banner_enabled" value="1" 
                               {{ $settings->monitoring_banner_enabled ?? false ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div>
                    <label for="ip_check_strictness" class="block text-sm font-medium text-gray-700 mb-2">
                        IP Check Strictness
                    </label>
                    <select id="ip_check_strictness" 
                            name="ip_check_strictness" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="log_only" {{ $settings->ip_check_strictness === 'log_only' ? 'selected' : '' }}>Log Only (default)</option>
                        <option value="warn" {{ $settings->ip_check_strictness === 'warn' ? 'selected' : '' }}>Warn (not implemented yet)</option>
                        <option value="block" {{ $settings->ip_check_strictness === 'block' ? 'selected' : '' }}>Block (not implemented yet)</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">How to handle IP address changes during exam</p>
                </div>
            </div>
        </div>

        <!-- Developer Bypass -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Developer Bypass Mode</h2>
                    <p class="mt-1 text-sm text-gray-600">Disable anti-cheat automatically in local development environment</p>
                    <p class="mt-1 text-xs text-gray-500">This only affects local environments. Production servers are not affected.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="developer_bypass_enabled" value="1" 
                           {{ $settings->developer_bypass_enabled ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">
                        {{ $settings->developer_bypass_enabled ? 'ON' : 'OFF' }}
                    </span>
                </label>
            </div>
        </div>

        <!-- Configuration Summary -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-blue-900 mb-4">Current Configuration Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-blue-800">Anti-Cheat:</span>
                    <span class="ml-2 text-blue-700">{{ $settings->enabled ? 'Enabled' : 'Disabled' }}</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Tab Switching:</span>
                    <span class="ml-2 text-blue-700">{{ $settings->tab_switch_detection ? 'Enabled' : 'Disabled' }}</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Idle Timeout:</span>
                    <span class="ml-2 text-blue-700">{{ $settings->idle_timeout_minutes ?? 10 }} minutes</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Exam Code Required:</span>
                    <span class="ml-2 text-blue-700">{{ $settings->exam_code_required ? 'Yes' : 'No' }}</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Developer Mode:</span>
                    <span class="ml-2 text-blue-700">{{ $settings->developer_bypass_enabled ? 'On' : 'Off' }}</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">IP Check Strictness:</span>
                    <span class="ml-2 text-blue-700">{{ ucfirst(str_replace('_', ' ', $settings->ip_check_strictness)) }}</span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

