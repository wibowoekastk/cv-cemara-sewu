<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Terinput - Mandor Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-poppins'] },
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

        <!-- Sidebar Mandor -->
        @include('mandor.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Manajemen Produksi</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Data Terinput</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Riwayat Laporan Saya</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Button Input Baru -->
                    <a href="{{ route('mandor.produksi.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20">
                        <i class="ph-bold ph-plus"></i> Lapor Lagi
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- Filter Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <form method="GET" action="{{ route('mandor.produksi.data') }}" class="flex flex-wrap gap-3 w-full md:w-auto items-center">
                        <!-- Filter Tanggal -->
                        <div class="relative group">
                            <i class="ph-bold ph-calendar-blank absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="date" name="date" value="{{ request('date') }}" class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none">
                        </div>

                        <!-- Filter Kandang (Dinamis dari Unit Mandor) -->
                        <div class="relative group">
                            <select name="kandang_id" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none cursor-pointer">
                                <option value="">Semua Kandang</option>
                                @foreach($units as $unit)
                                    @foreach($unit->kandangs as $kdg)
                                        <option value="{{ $kdg->id }}" {{ request('kandang_id') == $kdg->id ? 'selected' : '' }}>
                                            {{ $kdg->nama_kandang }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-4 w-12 text-center border-r border-gray-200">#</th>
                                    <th class="px-4 py-4 border-r border-gray-200">Tanggal</th>
                                    <th class="px-4 py-4 border-r border-gray-200">Kandang</th>
                                    
                                    <!-- [BARU] Kolom Batch -->
                                    <th class="px-4 py-4 border-r border-gray-200">Batch / Siklus</th>
                                    
                                    <!-- Group Populasi -->
                                    <th class="px-4 py-4 text-center bg-red-50/50 text-red-700">Mati</th>
                                    <th class="px-4 py-4 text-center bg-red-50/50 text-red-700">Afkir</th>
                                    <th class="px-4 py-4 text-center bg-red-50/50 text-red-700 border-r border-gray-200">Populasi</th>
                                    
                                    <!-- Group Produksi -->
                                    <th class="px-4 py-4 text-center bg-gold-50/50 text-gold-700">Butir</th>
                                    <th class="px-4 py-4 text-center bg-gold-50/50 text-gold-700">Berat/Btr</th>
                                    <th class="px-4 py-4 text-center bg-gold-50/50 text-gold-700 border-r border-gray-200">Total Kg</th>
                                    
                                    <!-- Group Performa -->
                                    <th class="px-4 py-4 text-center bg-blue-50/50 text-blue-700">HD %</th>
                                    <th class="px-4 py-4 text-center bg-blue-50/50 text-blue-700 border-r border-gray-200">FCR</th>
                                    
                                    <!-- Group Pakan -->
                                    <th class="px-4 py-4 text-center bg-cemara-50/50 text-cemara-800">Nama Pakan</th>
                                    <th class="px-4 py-4 text-center bg-cemara-50/50 text-cemara-800">Total Pakan</th>
                                    <th class="px-4 py-4 text-center bg-cemara-50/50 text-cemara-800">Gr/Ekor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($data as $index => $row)
                                <tr class="hover:bg-gray-50/80 transition group">
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $data->firstItem() + $index }}
                                    </td>
                                    
                                    <!-- Tanggal -->
                                    <td class="px-4 py-3 border-r border-gray-100">
                                        <div class="text-sm font-medium text-gray-700">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</div>
                                    </td>
                                    
                                    <!-- Kandang -->
                                    <td class="px-4 py-3 border-r border-gray-100">
                                        <div class="font-bold text-gray-900">{{ $row->kandang->nama_kandang ?? '-' }}</div>
                                    </td>

                                    <!-- [BARU] Info Batch / Siklus -->
                                    <td class="px-4 py-3 border-r border-gray-100 text-xs">
                                        @if($row->siklus)
                                            <span class="block font-bold text-blue-600">Batch {{ $row->siklus->tanggal_chick_in->format('Y') }}</span>
                                            <span class="text-gray-400">{{ $row->siklus->jenis_ayam }}</span>
                                        @else
                                            <span class="text-gray-300 italic text-[10px]">No Batch</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Populasi -->
                                    <td class="px-4 py-3 text-center text-red-600 font-bold">{{ $row->mati }}</td>
                                    <td class="px-4 py-3 text-center text-orange-600 font-bold">{{ $row->afkir }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-800 border-r border-gray-100">
                                        {{ number_format($row->populasi_awal - $row->mati - $row->afkir) }}
                                    </td>
                                    
                                    <!-- Produksi -->
                                    <td class="px-4 py-3 text-center text-gold-600 font-bold">{{ number_format($row->telur_butir) }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">
                                        {{-- Berat per butir (gram) --}}
                                        {{ $row->telur_butir > 0 ? number_format(($row->telur_kg * 1000) / $row->telur_butir, 1) : 0 }} gr
                                    </td>
                                    <td class="px-4 py-3 text-center text-gold-700 font-bold border-r border-gray-100">{{ number_format($row->telur_kg, 2) }} Kg</td>
                                    
                                    <!-- Performa -->
                                    <td class="px-4 py-3 text-center font-bold text-blue-600">{{ number_format($row->hdp, 1) }}%</td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-800 border-r border-gray-100">{{ number_format($row->fcr, 2) }}</td>
                                    
                                    <!-- Pakan -->
                                    <td class="px-4 py-3 text-center text-xs">{{ $row->pakan->nama_pakan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-700">{{ number_format($row->pakan_kg, 1) }} Kg</td>
                                    <td class="px-4 py-3 text-center text-gray-600">
                                        {{-- Konsumsi per ekor (gram) --}}
                                        @php
                                            $populasiAkhir = $row->populasi_awal - $row->mati - $row->afkir;
                                            $konsumsi = $populasiAkhir > 0 ? ($row->pakan_kg * 1000) / $populasiAkhir : 0;
                                        @endphp
                                        {{ number_format($konsumsi, 1) }} gr
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="px-4 py-12 text-center text-gray-400 italic">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="ph-duotone ph-file-x text-4xl mb-2"></i>
                                            <span>Belum ada laporan yang Anda input.</span>
                                        </div>
                                    </td>
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