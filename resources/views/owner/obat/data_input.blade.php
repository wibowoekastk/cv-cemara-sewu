<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Stok Obat - Owner Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-poppins'] },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' },
                        medical: { 50: '#ecfeff', 100: '#cffafe', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490' }
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

        <!-- Sidebar -->
        @include('owner.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Manajemen Obat</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Data Stok</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Rekap Stok Obat</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    
                    <!-- Export Button (Placeholder) -->
                    <button class="hidden md:flex items-center gap-2 px-4 py-2 bg-white text-medical-700 border border-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm">
                        <i class="ph-bold ph-download-simple text-lg"></i> Unduh Data
                    </button>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">

                <!-- 1. Stats Summary (Optional/Placeholder) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Total Item -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Item</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $data->total() }} <span class="text-sm font-normal text-gray-400">Jenis</span></h3>
                        </div>
                        <div class="w-12 h-12 bg-medical-50 text-medical-600 rounded-xl flex items-center justify-center text-2xl">
                            <i class="ph-fill ph-first-aid-kit"></i>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <form method="GET" action="{{ route('owner.obat.data_input') }}" class="flex flex-wrap gap-3 w-full md:w-auto items-center">
                        
                        <div class="relative inline-block" x-data="{ open: false, selected: 'Semua Kategori' }">
                        <button @click="open = !open" @click.away="open = false" type="button"
                            class="flex items-center justify-between w-48 px-4 py-2 bg-white border-2 border-cyan-400 rounded-xl text-sm text-gray-700 outline-none transition-all duration-300">
                        <span x-text="selected"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                              </svg>
                         </button>

                    <div x-show="open" 
                         x-transition.opacity.scale.95
                    class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
        
                    <div class="flex flex-col">
                    <button @click="selected = 'Semua Kategori'; open = false" 
                class="px-4 py-2 text-left text-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                Semua Kategori
            </button>
            
            @foreach($kategoris as $kat)
                <button @click="selected = '{{ $kat }}'; open = false" 
                    class="px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                    {{ $kat }}
                </button>
              @endforeach
                     </div>
                </div>
            </div>

                        <button type="submit" class="px-4 py-2 bg-medical-600 text-white rounded-lg text-sm font-semibold hover:bg-medical-700 transition shadow-md">
                            Filter
                        </button>
                    </form>

                    <div class="relative w-full md:w-64">
                        <form method="GET" action="{{ route('owner.obat.data_input') }}">
                            <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama obat..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-medical-500 outline-none">
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
                                    <th class="px-6 py-4">Nama Produk</th>
                                    <th class="px-6 py-4">Kategori</th>
                                    <th class="px-6 py-4 text-center">Total Stok</th>
                                    <th class="px-6 py-4">Keterangan</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($data as $index => $row)
                                    <tr class="hover:bg-gray-50 transition group align-top">
                                        <td class="px-6 py-4 text-center">{{ $data->firstItem() + $index }}</td>
                                        
                                        <!-- Nama Produk -->
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">{{ $row->nama_obat }}</div>
                                            <!-- Fallback jika min_stok belum ada di DB migrasi -->
                                            <div class="text-xs text-gray-500">Min. Stok: {{ $row->min_stok ?? 5 }} {{ $row->satuan }}</div>
                                        </td>

                                        <!-- Kategori Badge (Gunakan $row->jenis_obat) -->
                                        <td class="px-6 py-4">
                                            @php
                                                $catColor = match($row->jenis_obat) {
                                                    'Vaksin' => 'bg-medical-50 text-medical-700 border-medical-100',
                                                    'Vitamin' => 'bg-gold-50 text-gold-700 border-gold-100',
                                                    'Antibiotik' => 'bg-red-50 text-red-700 border-red-100',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs font-bold border {{ $catColor }}">
                                                {{ $row->jenis_obat }}
                                            </span>
                                        </td>

                                        <!-- Stok Total (Gunakan Accessor total_stok) -->
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-bold text-base {{ $row->total_stok <= ($row->min_stok ?? 5) ? 'text-red-600' : 'text-gray-800' }}">
                                                {{ number_format($row->total_stok) }}
                                            </span>
                                            <span class="text-xs text-gray-500 block">{{ $row->satuan }}</span>
                                        </td>

                                        <!-- Keterangan -->
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-gray-500 max-w-xs truncate">
                                                {{ $row->deskripsi ?? 'Stok tersedia di gudang' }}
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 text-center">
                                            @if($row->total_stok <= ($row->min_stok ?? 5))
                                                <span class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs font-bold border border-red-100">
                                                    Menipis
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-green-50 text-green-600 rounded text-xs font-bold border border-green-100">
                                                    Aman
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">
                                            Belum ada data obat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-4 border-t border-gray-100">
                        {{ $data->withQueryString()->links() }}
                    </div>
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