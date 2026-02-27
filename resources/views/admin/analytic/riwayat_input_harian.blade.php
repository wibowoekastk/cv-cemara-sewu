<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Input Harian - Admin Panel</title>
    
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

        <!-- Sidebar -->
        @include('admin.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Analytic</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Riwayat</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Riwayat Input Harian</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Export Button (Dummy Link) -->
                    <button class="hidden md:flex items-center gap-2 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm">
                        <i class="ph-bold ph-download-simple"></i> Export Log
                    </button>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- Filter Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <form method="GET" action="{{ route('admin.analytic.riwayat') }}" class="flex flex-wrap gap-3 w-full md:w-auto items-center">
                        <div class="relative group">
                            <i class="ph-bold ph-calendar-blank absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none" title="Dari Tanggal">
                        </div>
                        <div class="relative group">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none" title="Sampai Tanggal">
                        </div>
                        <div class="relative group">
                            <!-- Filter Lokasi/Unit -->
                             <select name="unit_id" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none cursor-pointer">
                                <option value="">Semua Unit</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-md">
                            Terapkan
                        </button>
                    </form>
                    
                    <div class="relative w-full md:w-64">
                        <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" placeholder="Cari Log ID atau User..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cemara-500 outline-none">
                    </div>
                </div>

                <!-- History Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4">Waktu Input</th>
                                    <th class="px-6 py-4">ID Log</th>
                                    <th class="px-6 py-4">Petugas Input</th>
                                    <th class="px-6 py-4">Lokasi & Kandang</th>
                                    <th class="px-6 py-4">Ringkasan Data</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                
                                @forelse($data as $row)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <!-- Kolom Waktu -->
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }} WIB</div>
                                    </td>
                                    
                                    <!-- Kolom ID -->
                                    <td class="px-6 py-4 font-mono text-xs text-gray-500">#LOG-{{ $row->id }}</td>
                                    
                                    <!-- Kolom Petugas -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <!-- Avatar Initials -->
                                            <div class="w-8 h-8 rounded-full bg-cemara-100 text-cemara-700 flex items-center justify-center font-bold border border-cemara-200">
                                                {{ substr($row->user->name ?? 'A', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">{{ $row->user->name ?? 'Admin' }}</p>
                                                <p class="text-xs text-gray-400">{{ $row->user->role ?? 'User' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Lokasi -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600">{{ $row->kandang->unit->nama_unit ?? '-' }}</span>
                                            <span class="px-2 py-1 bg-cemara-50 text-cemara-700 rounded text-xs font-bold border border-cemara-100">{{ $row->kandang->nama_kandang ?? '-' }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Ringkasan -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 text-xs">
                                                <i class="ph-fill ph-egg text-gold-500"></i> <span class="font-medium text-gray-700">{{ number_format($row->telur_kg, 2) }} Kg</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs">
                                                @if($row->mati > 0 || $row->afkir > 0)
                                                    <i class="ph-fill ph-warning text-red-400"></i> 
                                                    <span class="font-medium text-red-600">Mati/Afkir: {{ $row->mati + $row->afkir }} Ekor</span>
                                                @else
                                                    <i class="ph-fill ph-check-circle text-green-500"></i> <span class="font-medium text-gray-700">Sehat (0 Mati)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Aksi -->
                                    <td class="px-6 py-4 text-center">
                                        <button class="p-2 text-gray-400 hover:text-cemara-600 hover:bg-gray-100 rounded-lg transition" title="Lihat Detail">
                                            <i class="ph-bold ph-eye text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                                        Belum ada data riwayat input.
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