@props([
    'label' => 'Yeni',
    'href' => '#'
])

<x-mary-button 
    :label="$label" 
    :link="$href" 
    icon="o-plus"
    class="theme-btn-action" 
/>

