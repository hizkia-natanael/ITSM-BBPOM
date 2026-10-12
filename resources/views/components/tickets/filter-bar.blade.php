@props([
    'categories' => [],
    'selectedStatus' => null,
    'selectedCategory' => null,
    'search' => '',
])

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <form method="GET" action="{{ route('tickets.index') }}" class="space-y-4">
        <!-- Status Filter Pills -->
        <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 pb-4">
            <span class="text-xs font-semibold text-gray-600 me-2">Status:</span>
            <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']))) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ empty($selectedStatus) ? 'bg-emerald-700 text-white font-semibold shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua Status
            </a>
            <a href="{{ route('tickets.index', array_merge(request()->except(['page']), ['status' => 'pending'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedStatus === 'pending' ? 'bg-amber-500 text-white font-semibold shadow-xs' : 'bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100' }}">
                Menunggu (Pending)
            </a>
            <a href="{{ route('tickets.index', array_merge(request()->except(['page']), ['status' => 'diproses'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedStatus === 'diproses' ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100' }}">
                Sedang Diproses
            </a>
            <a href="{{ route('tickets.index', array_merge(request()->except(['page']), ['status' => 'selesai'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedStatus === 'selesai' ? 'bg-emerald-600 text-white font-semibold shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' }}">
                Selesai
            </a>
            <a href="{{ route('tickets.index', array_merge(request()->except(['page']), ['status' => 'ditolak'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedStatus === 'ditolak' ? 'bg-rose-600 text-white font-semibold shadow-xs' : 'bg-rose-50 text-rose-800 border border-rose-200 hover:bg-rose-100' }}">
                Ditolak
            </a>
        </div>

        <!-- Search & Category Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
            <div class="sm:col-span-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="ri-search-line text-xs text-slate-400"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Cari nomor tiket, subjek, atau kata kunci..." 
                        class="pl-8.5 block w-full rounded-xl border-slate-200 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs py-2"
                    />
                </div>
            </div>

            <div class="sm:col-span-4">
                <select 
                    name="category_id" 
                    class="block w-full rounded-xl border-slate-200 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs py-2"
                >
                    <option value="">Semua Kategori Layanan</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                @if(request()->filled('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-2xs transition">
                    Terapkan
                </button>
                @if(request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('tickets.index') }}" class="inline-flex justify-center items-center px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
