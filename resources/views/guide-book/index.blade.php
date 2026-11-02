<x-app-layout>
    <!-- Guide Book Creative Menu Book Interactive Stage (Locked to Exactly 1 Viewport Height) -->
    <div 
        x-data="guideBookViewer()" 
        class="relative h-[calc(100vh-4.25rem)] max-h-[calc(100vh-4.25rem)] w-full pt-1 pb-12 sm:pb-14 px-3 sm:px-6 flex flex-col justify-start text-slate-100 select-none overflow-hidden print:h-auto print:max-h-none print:p-0 print:m-0 print:bg-white print:text-slate-900"
    >
        <!-- Translucent BBPOM Scenic Photo Background (Creative & Warm Atmosphere, Low Opacity) -->
        <div 
            class="absolute inset-0 bg-cover bg-center pointer-events-none -z-20 opacity-[0.055] blur-[2px] scale-105 print:hidden"
            style="background-image: url('{{ asset('images/footer-bbpom-semarang.jpg') }}');"
        ></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/95 via-slate-900/90 to-slate-950/95 -z-10 pointer-events-none print:hidden"></div>

        <!-- Accessible Semantic Screen-Reader Title (Visually Clean Stage) -->
        <h1 class="sr-only">Buku Panduan Resmi ITSM BBPOM di Semarang</h1>

        <!-- The Hardcover 3D Book Frame Component (Single-Page Executive Folio Stage) -->
        <div class="flex-1 flex flex-col justify-start items-center min-h-0 pt-0.5 sm:pt-1">
            <x-guide-book.book-frame>
                @include('guide-book.pages.page-0')
                @include('guide-book.pages.page-1')
                @include('guide-book.pages.page-2')
                @include('guide-book.pages.page-3')
                @include('guide-book.pages.page-4')
                @include('guide-book.pages.page-5')
                @include('guide-book.pages.page-6')
                @include('guide-book.pages.page-7')
                @include('guide-book.pages.page-8')
                @include('guide-book.pages.page-9')
                @include('guide-book.pages.page-10')
                @include('guide-book.pages.page-11')
                @include('guide-book.pages.page-12')
                @include('guide-book.pages.page-13')
            </x-guide-book.book-frame>
        </div>

        <!-- Floating Executive Toolbar Component (Docked Neatly at Bottom) -->
        <x-guide-book.toolbar />

        <!-- Table of Contents Modal Drawer -->
        <x-guide-book.toc-modal />
    </div>

    <!-- Alpine.js Dedicated Controller Script -->
    @include('guide-book.pages.viewer-script')
</x-app-layout>
