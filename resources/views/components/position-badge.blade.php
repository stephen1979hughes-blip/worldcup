@props(['position'])
@php
    $styles = [
        'GK' => 'bg-yellow-100 text-yellow-800',
        'DF' => 'bg-blue-100 text-blue-800',
        'MF' => 'bg-green-100 text-green-800',
        'FW' => 'bg-red-100 text-red-800',
    ];
    $style = $styles[$position] ?? 'bg-gray-100 text-gray-800';
@endphp
<span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $style }}">{{ $position }}</span>
