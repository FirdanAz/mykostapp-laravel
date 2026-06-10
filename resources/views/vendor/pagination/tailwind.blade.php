@if ($paginator->hasPages())
<nav aria-label="pagination" class="flex items-center justify-center gap-1 mt-2">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
    <span class="inline-flex items-center justify-center w-8 h-8 text-slate-300 rounded-lg cursor-default">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}"
       class="inline-flex items-center justify-center w-8 h-8 text-slate-500 hover:bg-slate-100 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
        <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-slate-400">...</span>
        @endif

        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold bg-blue-600 text-white rounded-lg">
            {{ $page }}
        </span>
        @else
        <a href="{{ $url }}"
           class="inline-flex items-center justify-center w-8 h-8 text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
            {{ $page }}
        </a>
        @endif
        @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}"
       class="inline-flex items-center justify-center w-8 h-8 text-slate-500 hover:bg-slate-100 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    @else
    <span class="inline-flex items-center justify-center w-8 h-8 text-slate-300 rounded-lg cursor-default">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </span>
    @endif
</nav>
@endif
