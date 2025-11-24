@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="w-full">

        {{-- WRAPPER --}}
        <div class="flex flex-col sm:flex-row gap-4 items-start justify-between w-full">

            {{-- INFO TEXT (SELALU TAMPIL) --}}
            <p class="text-sm text-gray-700 dark:text-gray-300 bg-transparent dark:bg-transparent">
                Menampilkan
                @if ($paginator->firstItem())
                    <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                dari
                <span class="font-semibold">{{ $paginator->total() }}</span>
                data
            </p>

            {{-- PAGINATION BUTTONS (TIDAK DI-HIDDEN) --}}
            <div class="flex items-center gap-1 flex-wrap">

                {{-- PREVIOUS --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 rounded-xl bg-gray-200 text-gray-400 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed">
                        &laquo;
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        &laquo;
                    </a>
                @endif

                {{-- PAGE NUMBERS --}}
                @foreach ($elements as $element)

                    @if (is_string($element))
                        <span class="px-3 py-2 rounded-xl bg-transparent text-gray-500 dark:text-gray-400">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)

                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-2 rounded-xl bg-blue-600 text-white font-semibold shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-blue-500 hover:text-white dark:hover:bg-blue-600 transition shadow-sm">
                                    {{ $page }}
                                </a>
                            @endif

                        @endforeach
                    @endif

                @endforeach

                {{-- NEXT --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        &raquo;
                    </a>
                @else
                    <span class="px-3 py-2 rounded-xl bg-gray-200 text-gray-400 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed">
                        &raquo;
                    </span>
                @endif

            </div>
        </div>

    </nav>
@endif
