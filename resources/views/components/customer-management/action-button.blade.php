@props([
    'label' => 'Yeni',
    'href' => '#'
])

<x-mary-button 
    :label="$label" 
    :link="$href" 
    icon="o-plus"
    class="btn-primary font-medium px-4 py-2.5 rounded-lg transition-colors text-sm" 
/>

