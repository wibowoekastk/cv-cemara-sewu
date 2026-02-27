<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kandang - Owner Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- [UPDATE] Mengganti Playfair Display dengan Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        // [UPDATE] Konfigurasi font Poppins yang benar
                        poppins: ['"Poppins"', 'sans-serif'],
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
                        <span>Manajemen Kandang</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Data Master</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Monitoring Unit & Kandang</h2>
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
            <div class="p-4 md:p-8 w-full space-y-8">
                
                <!-- Tabel Unit -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center gap-2 font-poppins">
                        <i class="ph-fill ph-house-line text-blue-500"></i> Daftar Unit Farm
                    </h3>
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3">Nama Unit</th>
                                    <th class="px-4 py-3">Lokasi</th>
                                    <th class="px-4 py-3 text-center">Jml Kandang</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($units as $unit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-bold">{{ $unit->nama_unit }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $unit->lokasi }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-bold">{{ $unit->kandangs_count }} Kandang</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-green-600 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">Aktif</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Belum ada data unit.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $units->links() }}</div>
                </div>

                <!-- Tabel Kandang -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 font-poppins">
                            <i class="ph-fill ph-square text-orange-500"></i> Daftar Kandang & Batch
                        </h3>
                        
                        <!-- Filter -->
                        <form method="GET" action="{{ route('owner.kandang.data') }}" class="flex gap-3 w-full md:w-auto">
                            <select name="lokasi" class="px-3 py-2 border rounded-lg text-sm bg-gray-50 focus:ring-2 focus:ring-cemara-500 outline-none" onchange="this.form.submit()">
                                <option value="">Semua Lokasi</option>
                                @foreach($lokasis as $loc)
                                    <option value="{{ $loc }}" {{ request('lokasi') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                            </select>
                            <div class="relative w-full">
                                <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kandang..." class="w-full pl-9 pr-4 py-2 border rounded-lg text-sm bg-gray-50 focus:ring-2 focus:ring-cemara-500 outline-none">
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3">Unit</th>
                                    <th class="px-4 py-3">Nama Kandang</th>
                                    
                                    <!-- [BARU] Kolom Batch/Siklus -->
                                    <th class="px-4 py-3">Batch / Siklus Aktif</th>
                                    
                                    <th class="px-4 py-3 text-right">Kapasitas</th>
                                    <th class="px-4 py-3 text-right">Populasi</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($kandangs as $kandang)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-600">{{ $kandang->unit->nama_unit ?? '-' }}</td>
                                    <td class="px-4 py-3 font-bold">{{ $kandang->nama_kandang }}</td>
                                    
                                    <!-- Data Batch -->
                                    <td class="px-4 py-3">
                                        @if($kandang->siklusAktif)
                                            <div class="flex flex-col">
                                                <span class="font-bold text-xs text-blue-600">
                                                    {{ $kandang->siklusAktif->batch->nama_batch ?? 'Siklus Mandiri' }}
                                                </span>
                                                <span class="text-[10px] text-gray-500">
                                                    In: {{ \Carbon\Carbon::parse($kandang->siklusAktif->tanggal_chick_in)->format('d M Y') }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Menunggu Chick-In</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-right">{{ number_format($kandang->kapasitas) }}</td>
                                    <td class="px-4 py-3 text-right font-bold {{ $kandang->stok_saat_ini < 100 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($kandang->stok_saat_ini) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($kandang->status == 'aktif')
                                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Kosong</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400 italic">Data kandang tidak ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $kandangs->appends(request()->query())->links() }}</div>
                </div>

            </div>
        </main>
    </div>

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