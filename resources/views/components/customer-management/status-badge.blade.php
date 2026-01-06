@props([
    'status' => 'draft',
    'label' => null
])
@php
    $classMap = [
        'sent' => 'badge-info',
        'draft' => 'badge-ghost',
        'downloaded' => 'badge-success',
        'active' => 'badge-success',
        'passive' => 'badge-ghost',
        'pending' => 'badge-warning',
    ];

    $labelMap = [
        'sent' => 'Gönderildi',
        'draft' => 'Taslak',
        'downloaded' => 'İndirildi',
        'active' => 'Aktif',
        'passive' => 'Pasif',
        'pending' => 'Beklemede',
    ];

    $badgeClass = $classMap[$status] ?? 'badge-ghost';
    $displayLabel = $label ?? ($labelMap[$status] ?? $status);
@endphp

<x-mary-badge :value="$displayLabel" class="{{ $badgeClass }} badge-md" />

