@if ($paginator->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Mostrando {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }}
            de {{ $paginator->total() }} registros
        </div>

        <ul class="pagination">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link"><i class="ti ti-chevron-left"></i></span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="ti ti-chevron-left"></i></a>
                </li>
            @endif

            {{-- Numeros de pagina con puntos suspensivos (1 2 3 ... 100 101) --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item"><span class="page-dots">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="ti ti-chevron-right"></i></a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link"><i class="ti ti-chevron-right"></i></span></li>
            @endif
        </ul>
    </div>
@endif
