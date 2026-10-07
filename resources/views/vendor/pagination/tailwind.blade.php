@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        {{-- Mobile Pagination View --}}
        <div class="flex flex-col gap-2.5 sm:hidden">
            <div class="flex items-center justify-between text-xs text-gray-600">
                <p class="text-sm font-medium text-gray-700">
                    Showing {{ $paginator->firstItem() ?? 1 }} to {{ $paginator->lastItem() ?? $paginator->count() }} of {{ $paginator->total() }} results
                </p>
                <div class="flex items-center gap-1">
                    <label for="mobile_per_page" class="text-sm font-medium text-gray-700">Tampilkan:</label>
                    <select
                        id="mobile_per_page"
                        name="per_page"
                        class="text-xs py-1 px-1.5 rounded-lg border-gray-300 bg-white text-gray-700 font-medium focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs"
                        onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.set('page', '1'); window.location.href = u.toString();"
                    >
                        @foreach([10, 25, 50, 100] as $count)
                            <option value="{{ $count }}" {{ (int)request('per_page', 10) === $count ? 'selected' : '' }}>
                                {{ $count }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-2 items-center justify-between">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed rounded-lg">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        {!! __('pagination.previous') !!}
                    </a>
                @endif

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        {!! __('pagination.next') !!}
                    </a>
                @else
                    <span class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed rounded-lg">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </div>
        </div>

        {{-- Desktop Pagination View --}}
        <div class="hidden sm:flex-1 sm:flex sm:gap-4 sm:items-center sm:justify-between">
            {{-- Left Side: Showing Count + Per-Page Dropdown (Styled exact like Filament Admin) --}}
            <div class="flex flex-wrap items-center gap-3">
                <p class="text-sm font-medium text-gray-700 leading-5">
                    Showing {{ $paginator->firstItem() ?? 1 }} to {{ $paginator->lastItem() ?? $paginator->count() }} of {{ $paginator->total() }} results
                </p>

                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="text-gray-300">|</span>
                    <label for="pagination_per_page" class="text-sm font-medium text-gray-700 whitespace-nowrap">Tampilkan:</label>
                    <select
                        id="pagination_per_page"
                        name="per_page"
                        class="text-xs py-1 px-2.5 rounded-lg border-gray-300 bg-white text-gray-700 font-medium focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs cursor-pointer"
                        onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.set('page', '1'); window.location.href = u.toString();"
                    >
                        @foreach([10, 25, 50, 100] as $count)
                            <option value="{{ $count }}" {{ (int)request('per_page', 10) === $count ? 'selected' : '' }}>
                                {{ $count }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-sm font-medium text-gray-700 whitespace-nowrap">per halaman</span>
                </div>
            </div>

            {{-- Right Side: Pagination Navigation Buttons --}}
            <div>
                <span class="inline-flex rtl:flex-row-reverse shadow-2xs rounded-xl overflow-hidden border border-slate-200">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-slate-400 bg-slate-50 cursor-not-allowed leading-5" aria-hidden="true">
                                <i class="ri-arrow-left-s-line text-sm"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-slate-600 bg-white leading-5 hover:bg-slate-50 hover:text-emerald-700 transition" aria-label="{{ __('pagination.previous') }}">
                            <i class="ri-arrow-left-s-line text-sm"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center px-3 py-1.5 -ml-px text-xs font-medium text-slate-400 bg-white cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-3.5 py-1.5 -ml-px text-xs font-bold text-white bg-emerald-700 cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-3.5 py-1.5 -ml-px text-xs font-medium text-slate-700 bg-white leading-5 hover:bg-slate-50 hover:text-emerald-800 transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2.5 py-1.5 -ml-px text-xs font-medium text-slate-600 bg-white leading-5 hover:bg-slate-50 hover:text-emerald-700 transition" aria-label="{{ __('pagination.next') }}">
                            <i class="ri-arrow-right-s-line text-sm"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center px-2.5 py-1.5 -ml-px text-xs font-medium text-slate-400 bg-slate-50 cursor-not-allowed leading-5" aria-hidden="true">
                                <i class="ri-arrow-right-s-line text-sm"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
