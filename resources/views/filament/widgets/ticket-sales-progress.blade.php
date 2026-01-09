@php
    $ticketsSold = $getRecord()->tickets_count ?? 0;
    $totalQuota = $getRecord()->quota ?? 1;
    $percentage = $totalQuota > 0 ? round(($ticketsSold / $totalQuota) * 100, 1) : 0;
    
    $color = match(true) {
        $percentage >= 100 => 'danger',
        $percentage >= 80 => 'warning',
        $percentage >= 50 => 'primary',
        default => 'success'
    };
@endphp

<div class="flex items-center gap-3">
    <div class="flex-1">
        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div 
                class="h-2.5 rounded-full transition-all duration-300 @if($color === 'danger') bg-red-600 @elseif($color === 'warning') bg-yellow-500 @elseif($color === 'primary') bg-blue-600 @else bg-green-600 @endif"
                style="width: {{ min($percentage, 100) }}%"
            ></div>
        </div>
    </div>
    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 min-w-[50px] text-right">
        {{ $percentage }}%
    </div>
</div>