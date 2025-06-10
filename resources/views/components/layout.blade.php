<x-html class="font-sans border-t-8 border-scarlet-500" title="ADM - Project Report">
    <x-slot name="head">
        @vite('resources/js/app.js')

        @vite('resources/css/app.css')

        {{ $head ?? '' }}

        @livewireStyles
        @bukStyles
    </x-slot>

    {{ $slot }}

    @livewireScripts
    @bukScripts
</x-html>
