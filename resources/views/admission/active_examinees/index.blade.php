@extends('layouts.admission')

@section('title', 'Active Examinees Monitor - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Active Examinees Monitor</h1>
            <p class="mt-2 text-sm text-gray-600">Real-time view of applicants currently taking exams</p>
        </div>
        <div class="flex items-center space-x-3">
            <button id="refreshButton" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
            <span id="lastUpdate" class="text-sm text-gray-500"></span>
        </div>
    </div>

    <!-- Active Examinees Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div id="examineesTableContainer">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Started</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current State</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suspicious Events</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Change</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code Verified</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody id="examineesTableBody" class="bg-white divide-y divide-gray-200">
                        @include('admission.active_examinees.table_rows', ['examinees' => $activeExaminees])
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(empty($activeExaminees))
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Examinees</h3>
        <p class="text-sm text-gray-500">There are currently no applicants taking exams.</p>
    </div>
    @endif
</div>

<script>
(function() {
    const refreshInterval = {{ config('anticheat.monitor.refresh_interval', 15) }} * 1000; // Convert to milliseconds (static config value)
    let refreshTimer = null;

    function updateLastUpdateTime() {
        const now = new Date();
        document.getElementById('lastUpdate').textContent = 'Last updated: ' + now.toLocaleTimeString();
    }

    function refreshTable() {
        fetch('{{ route('admission.active-examinees.fetch') }}')
            .then(response => response.json())
            .then(data => {
                // Update table body
                const tbody = document.getElementById('examineesTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500">No active examinees</td></tr>';
                } else {
                    // Rebuild table rows
                    let html = '';
                    data.forEach(examinee => {
                        html += buildTableRow(examinee);
                    });
                    tbody.innerHTML = html;
                }
                updateLastUpdateTime();
            })
            .catch(error => {
                console.error('Error refreshing table:', error);
            });
    }

    function buildTableRow(examinee) {
        const statusColors = {
            'Active': 'bg-green-100 text-green-800',
            'Idle': 'bg-yellow-100 text-yellow-800',
            'Finished': 'bg-gray-100 text-gray-800',
        };
        const statusColor = statusColors[examinee.status] || 'bg-gray-100 text-gray-800';

        const startedAt = new Date(examinee.started_at).toLocaleString();
        const lastActivity = examinee.last_activity ? new Date(examinee.last_activity).toLocaleString() : 'N/A';
        const scheduleInfo = examinee.schedule_info ? `${examinee.schedule_info.date} (${examinee.schedule_info.time})` : 'N/A';
        const examCode = examinee.schedule_info?.exam_code || 'N/A';
        const applicantUrlBase = '{{ route("admission.applicants.show", 0) }}';
        const applicantUrl = applicantUrlBase.replace(/\/0$/, '/' + examinee.applicant_id);

        const stateColors = {
            'Focused': 'bg-green-100 text-green-800',
            'Active': 'bg-blue-100 text-blue-800',
            'Hidden/Blurred': 'bg-yellow-100 text-yellow-800',
            'Unknown': 'bg-gray-100 text-gray-800',
        };
        const stateColor = stateColors[examinee.current_state] || 'bg-gray-100 text-gray-800';

        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="${applicantUrl}" 
                       class="text-sm font-medium text-blue-600 hover:text-blue-900">
                        ${examinee.applicant_name}
                    </a>
                    <div class="text-xs text-gray-500">${examinee.app_ref_no}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${examinee.exam_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${scheduleInfo}<br>
                    <span class="text-xs text-gray-400">Code: ${examCode}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${startedAt}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${lastActivity}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${stateColor}">
                        ${examinee.current_state}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                    ${examinee.suspicious_count > 0 
                        ? `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">${examinee.suspicious_count}</span>`
                        : '<span class="text-gray-400">0</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                    ${examinee.had_ip_change 
                        ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Yes</span>'
                        : '<span class="text-gray-400">No</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                    ${examinee.exam_code_verified 
                        ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Yes</span>'
                        : '<span class="text-gray-400">No</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">
                        ${examinee.status}
                    </span>
                </td>
            </tr>
        `;
    }

    // Manual refresh button
    document.getElementById('refreshButton')?.addEventListener('click', function() {
        refreshTable();
    });

    // Auto-refresh
    function startAutoRefresh() {
        refreshTimer = setInterval(refreshTable, refreshInterval);
    }

    function stopAutoRefresh() {
        if (refreshTimer) {
            clearInterval(refreshTimer);
            refreshTimer = null;
        }
    }

    // Start auto-refresh on page load
    updateLastUpdateTime();
    startAutoRefresh();

    // Stop auto-refresh when page is hidden, resume when visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else {
            startAutoRefresh();
            refreshTable(); // Refresh immediately when page becomes visible
        }
    });
})();
</script>
@endsection

