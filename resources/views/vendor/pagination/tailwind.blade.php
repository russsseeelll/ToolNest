@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center mt-8">
        <ul class="pagination flex space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <span class="px-3 py-2 rounded-md border border-gray-300 text-gray-500 cursor-not-allowed" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-200 transition-colors" aria-label="{{ __('pagination.previous') }}">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled" aria-disabled="true">
                        <span class="px-3 py-2 rounded-md border border-gray-300 text-gray-500">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page">
                                <span class="px-3 py-2 rounded-md bg-[#003865] text-white border border-gray-300">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-200 transition-colors">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-200 transition-colors" aria-label="{{ __('pagination.next') }}">&raquo;</a>
                </li>
            @else
                <li class="disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <span class="px-3 py-2 rounded-md border border-gray-300 text-gray-500 cursor-not-allowed" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
