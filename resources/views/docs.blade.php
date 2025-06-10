<x-layout :title="$title">
    <x-navigation/>

    <div class="max-w-screen-xl mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-5 gap-10">
        <!-- Sidebar Navigation - Sticky -->
        <div class="hidden lg:block col-span-1 sticky top-6 self-start h-fit">
            <x-docs.navigation :sections="$sections" :current="$page" :version="$version"/>
        </div>

        <!-- Main Content -->
        <div class="col-span-1 lg:col-span-3 px-3 lg:px-0 prose lg:prose-lg">
            <x-markdown anchors>
                {!! $markdown !!}
            </x-markdown>
        </div>

        <!-- TOC Sidebar - Sticky -->
        <div class="hidden lg:block col-span-1 sticky top-6 self-start h-fit">
            <x-h6>
                On this page
            </x-h6>
            <x-toc class="mt-1 toc">
                {!! $markdown !!}
            </x-toc>
        </div>
    </div>

    <x-footer/>
</x-layout>
