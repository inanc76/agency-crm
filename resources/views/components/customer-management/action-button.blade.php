@props([
    'label' => 'Yeni',
    'href' => '#'
])

<x-mary-button 
    :label="$label" 
    :link="$href" 
    icon="o-plus"
    class="btn-success text-white font-medium px-4 py-2.5 rounded-lg transition-colors text-sm !bg-[#10B981] !border-[#10B981]" 
/>

