<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Stok Pakan - Owner Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-poppins'],
                        poppins: ['"Playfair Display"', 'poppins'],
                    },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' }
                    }
                }
            }
        }
    </script>
    <style>
        .table-container::-webkit-scrollbar { height: 8px; }
        .table-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        th, td { white-space: nowrap; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Overlay Mobile -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Sidebar Owner -->
        @include('owner.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Manajemen Pakan</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Data Stok</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Monitoring Riwayat Masuk</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Export Button (Placeholder) -->
                    <button class="hidden md:flex items-center gap-2 px-4 py-2 bg-white text-cemara-900 border border-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm">
                        <i class="ph-bold ph-download-simple text-lg"></i> Unduh Data
                    </button>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- Filter Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <!-- Form Filter -->
                    <form method="GET" action="{{ route('owner.pakan.data_input') }}" class="flex flex-wrap gap-3 w-full md:w-auto items-center">
                        <div class="relative group">
                            <i class="ph-bold ph-calendar-blank absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-gold-500 outline-none">
                        </div>
                        <span class="text-gray-400">-</span>
                        <div class="relative group">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-gold-500 outline-none">
                        </div>
                        
                       <div class="relative inline-block" x-data="{ open: false, selected: '{{ request('lokasi') && request('lokasi') != 'all' ? request('lokasi') : 'Semua Lokasi' }}' }">
    <input type="hidden" name="lokasi" :value="selected === 'Semua Lokasi' ? 'all' : selected">

    <button @click="open = !open" @click.away="open = false" type="button"
        class="flex items-center justify-between min-w-40 px-4 py-2 bg-white border-2 border-yellow-400 rounded-xl text-sm text-gray-700 outline-none transition-all duration-300 hover:border-yellow-500">
        <span x-text="selected"></span>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
         style="display: none;">
        
        <div class="flex flex-col">
            <button type="button" @click="selected = 'Semua Lokasi'; open = false" 
                class="px-4 py-2 text-left text-sm transition-colors"
                :class="selected === 'Semua Lokasi' ? 'bg-yellow-500 text-white' : 'hover:bg-yellow-50 text-gray-700'">
                Semua Lokasi
            </button>
            
            @foreach($lokasis as $lok)
                <button type="button" @click="selected = '{{ $lok }}'; open = false" 
                    class="px-4 py-2 text-left text-sm transition-colors border-t border-gray-50"
                    :class="selected === '{{ $lok }}' ? 'bg-yellow-500 text-white' : 'hover:bg-yellow-50 text-gray-700'">
                    {{ $lok }}
                </button>
            @endforeach
        </div>
    </div>
</div>
<div class="relative inline-block" x-data="{ 
    open: false, 
    selectedName: '{{ request('unit_id') && request('unit_id') != 'all' ? $units->firstWhere('id', request('unit_id'))->nama_unit : 'Semua Unit' }}',
    selectedValue: '{{ request('unit_id') ?? 'all' }}'
}">
    <input type="hidden" name="unit_id" :value="selectedValue">

    <button @click="open = !open" @click.away="open = false" type="button"
        class="flex items-center justify-between min-w-40 px-4 py-2 bg-white border-2 border-yellow-400 rounded-xl text-sm text-gray-700 outline-none transition-all duration-300 hover:border-yellow-500">
        <span x-text="selectedName"></span>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
         style="display: none;">
        
        <div class="flex flex-col max-h-60 overflow-y-auto">
            <button type="button" @click="selectedName = 'Semua Unit'; selectedValue = 'all'; open = false" 
                class="px-4 py-2 text-left text-sm transition-colors"
                :class="selectedValue === 'all' ? 'bg-yellow-500 text-white' : 'hover:bg-yellow-50 text-gray-700'">
                Semua Unit
            </button>
            
            @foreach($units as $unit)
                <button type="button" @click="selectedName = '{{ $unit->nama_unit }}'; selectedValue = '{{ $unit->id }}'; open = false" 
                    class="px-4 py-2 text-left text-sm transition-colors border-t border-gray-50"
                    :class="selectedValue == '{{ $unit->id }}' ? 'bg-yellow-500 text-white' : 'hover:bg-yellow-50 text-gray-700'">
                    {{ $unit->nama_unit }}
                </button>
            @endforeach
        </div>
    </div>
</div>
                        
                        <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg text-sm font-semibold hover:bg-gold-600 transition shadow-md">
                            Filter
                        </button>
                    </form>

                    <div class="relative w-full md:w-64">
                        <form method="GET" action="{{ route('owner.pakan.data_input') }}">
                            <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pakan..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gold-500 outline-none">
                        </form>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">#</th>
                                    <th class="px-6 py-4">Tgl Masuk</th>
                                    <th class="px-6 py-4">Tujuan (Unit)</th> <!-- Kolom Baru: Unit Tujuan -->
                                    <th class="px-6 py-4">Kategori</th>
                                    <th class="px-6 py-4">Nama Pakan</th>
                                    <th class="px-6 py-4 text-center">Jml Karung</th>
                                    <th class="px-6 py-4 text-center">Berat/Sak</th>
                                    <th class="px-6 py-4 text-center border-l border-gray-200 bg-cemara-50/50 text-cemara-800">Total Berat</th>
                                    <!-- Header Diubah dari Sumber ke Keterangan -->
                                    <th class="px-6 py-4">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($data as $index => $row)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-center">{{ $data->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <i class="ph-fill ph-calendar text-gray-400"></i>
                                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600">
                                            {{ $row->unitTujuan->nama_unit ?? 'Gudang Pusat' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold border border-yellow-200">
                                            {{ $row->pakan->jenis_pakan ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $row->pakan->nama_pakan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium">{{ $row->jml_karung }}</td>
                                    <td class="px-6 py-4 text-center text-gray-500">{{ $row->berat_sak }}</td>
                                    
                                    <td class="px-6 py-4 text-center font-bold text-cemara-700 bg-cemara-50/30 border-l border-gray-100">
                                        {{ number_format($row->jumlah) }} Kg
                                    </td>

                                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" title="{{ $row->keterangan }}">
                                        <!-- Menampilkan Keterangan Asli (Supplier/Asal) -->
                                        @if($row->jenis_mutasi == 'masuk_pusat')
                                            <span class="text-blue-600 font-semibold block mb-0.5">Pembelian</span>
                                            {{ $row->keterangan }}
                                        @elseif($row->jenis_mutasi == 'produksi')
                                            <span class="text-green-600 font-semibold block mb-0.5">Produksi Sendiri</span>
                                            {{ $row->keterangan }}
                                        @else
                                            <span class="text-gray-600 font-semibold block mb-0.5">Distribusi</span>
                                            {{ $row->keterangan }}
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data riwayat pakan masuk.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    @if($data->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $data->withQueryString()->links() }}
                    </div>
                    @endif
                </div>

            </div>
        </main>
    </div>

    <!-- Script Sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) {
                if (overlay.classList.contains('hidden')) {
                    overlay.classList.remove('hidden');
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                } else {
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }
        }
    </script>
</body>
</html>