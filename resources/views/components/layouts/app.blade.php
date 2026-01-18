<!DOCTYPE html>
<html lang="tr" data-theme="light">

@php
    // Use Shared Theme Settings (Cached in Provider)
    // Fallback to empty object/null handled by optional chaining
    $theme = $themeSettings ?? null;
@endphp

@include('components.layouts.partials._head', ['theme' => $theme])

<body class="font-sans antialiased text-gray-900" style="background-color: var(--page-bg);">
    {{-- Header - Dynamic colors from theme settings --}}
    @include('components.layouts.partials._header', ['theme' => $theme])

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <x-mary-toast />
    @livewireScripts
    @stack('scripts')
</body>

</html>