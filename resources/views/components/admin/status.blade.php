@props(['status'])
@php
    $statuses = [
        'pending_demo' => ['label' => 'Chờ xử lý', 'class' => 'pending'],
        'processing_demo' => ['label' => 'Đang xử lý', 'class' => 'processing'],
        'completed_demo' => ['label' => 'Đã xử lý', 'class' => 'completed'],
        'cancelled_demo' => ['label' => 'Đã hủy', 'class' => 'cancelled'],
    ];
    $info = $statuses[$status] ?? ['label' => $status, 'class' => 'unknown'];
@endphp
<span class="status-pill status-{{ $info['class'] }}">{{ $info['label'] }}</span>
