@props([
    'sections' => [],
    'current' => '',
    'version' => 'main',
])

<nav class="sticky top-0">
    @foreach ($sections as $section)
        <div {!! $loop->first ? '' : 'class="mt-8"' !!} >
            @isset($section['title'])
                <x-h6>
                    {{ $section['title'] }}
                </x-h6>
            @endisset

            @foreach ($section['pages'] as $page)
                @php($slug = $page['file'] ?? $page)

                <a
                    href="{{ $page['link'] ?? route('docs', [$version, $slug]) }}"
                    @isset($page['link']) @endisset
                    class="mt-1 group flex items-center px-1 py-2 text-sm leading-5 font-medium focus:outline-none transition ease-in-out duration-150 {{ strtolower($current) === ($slug['link'] ?? $slug) ? 'text-gray-900 bg-gray-100 hover:bg-gray-100 focus:bg-gray-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 focus:bg-gray-100' }}"
                    aria-current="page"
                >

                    <span class="truncate">
                        {{ $loop->iteration }}. {{ $page['name'] ?? str_replace('-', ' ', Illuminate\Support\Str::title($slug)) }}
                    </span>
                </a>
            @endforeach
        </div>
    @endforeach
</nav>
