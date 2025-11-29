@forelse($examinees as $examinee)
    @php
        $statusColors = [
            'Active' => 'bg-green-100 text-green-800',
            'Idle' => 'bg-yellow-100 text-yellow-800',
            'Finished' => 'bg-gray-100 text-gray-800',
        ];
        $statusColor = $statusColors[$examinee['status']] ?? 'bg-gray-100 text-gray-800';
        
        $stateColors = [
            'Focused' => 'bg-green-100 text-green-800',
            'Active' => 'bg-blue-100 text-blue-800',
            'Hidden/Blurred' => 'bg-yellow-100 text-yellow-800',
            'Unknown' => 'bg-gray-100 text-gray-800',
        ];
        $stateColor = $stateColors[$examinee['current_state'] ?? 'Unknown'] ?? 'bg-gray-100 text-gray-800';
    @endphp
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap">
            <a href="{{ route('admission.applicants.show', $examinee['applicant_id']) }}" 
               class="text-sm font-medium text-blue-600 hover:text-blue-900">
                {{ $examinee['applicant_name'] }}
            </a>
            <div class="text-xs text-gray-500">{{ $examinee['app_ref_no'] }}</div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $examinee['exam_name'] }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            @if($examinee['schedule_info'])
                {{ $examinee['schedule_info']['date'] }}<br>
                <span class="text-xs">{{ $examinee['schedule_info']['time'] }}</span>
                @if(isset($examinee['schedule_info']['exam_code']))
                    <br><span class="text-xs font-mono text-gray-600">Code: {{ $examinee['schedule_info']['exam_code'] }}</span>
                @endif
            @else
                <span class="text-gray-400">N/A</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $examinee['started_at']->format('M d, Y') }}<br>
            <span class="text-xs">{{ $examinee['started_at']->format('g:i A') }}</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            @if($examinee['last_activity'])
                {{ $examinee['last_activity']->format('M d, Y') }}<br>
                <span class="text-xs">{{ $examinee['last_activity']->format('g:i A') }}</span>
            @else
                <span class="text-gray-400">N/A</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stateColor }}">
                {{ $examinee['current_state'] ?? 'Unknown' }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
            @if(($examinee['suspicious_count'] ?? 0) > 0)
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    {{ $examinee['suspicious_count'] }}
                </span>
            @else
                <span class="text-gray-400">0</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
            @if($examinee['had_ip_change'])
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Yes</span>
            @else
                <span class="text-gray-400">No</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
            @if($examinee['exam_code_verified'])
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
            @else
                <span class="text-gray-400">No</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                {{ $examinee['status'] }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500">
            No active examinees
        </td>
    </tr>
@endforelse

