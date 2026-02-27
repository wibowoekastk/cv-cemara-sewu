<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Input Unit - Admin Panel</title>
    
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
                        <span>Manajemen Data</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Kandang</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Riwayat Input</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Riwayat Data</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Export Button (Placeholder) -->
                    <button class="hidden md:flex items-center gap-2 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm">
                        <i class="ph-bold ph-download-simple"></i> Export Excel
                    </button>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- TAB NAVIGASI -->
                <div class="mb-6 border-b border-gray-200">
                    <div class="flex flex-wrap gap-6">
                        <!-- Tab Unit (Aktif) -->
                        <a href="{{ route('admin.kandang.riwayat.unit') }}" class="pb-3 border-b-2 border-cemara-600 text-cemara-700 font-bold text-sm flex items-center gap-2">
                            <i class="ph-fill ph-house-line text-lg"></i> Riwayat Unit
                        </a>
                        <!-- Tab Kandang -->
                        <a href="{{ route('admin.kandang.riwayat.kandang') }}" class="pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm flex items-center gap-2 transition">
                            <i class="ph ph-warehouse text-lg"></i> Riwayat Kandang
                        </a>
                        <!-- Tab Timbang -->
                        <a href="{{ route('admin.kandang.riwayat.timbang') }}" class="pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm flex items-center gap-2 transition">
                            <i class="ph ph-scales text-lg"></i> Riwayat Timbang
                        </a>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative group">
                            <i class="ph-bold ph-calendar-blank absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="date" class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none transition">
                        </div>
                        <div class="relative w-full md:w-64">
                            <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="text" placeholder="Cari nama unit..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cemara-500 outline-none">
                        </div>
                        <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">#</th>
                                    <th class="px-6 py-4">Tgl Input</th>
                                    <th class="px-6 py-4">Penginput</th>
                                    <th class="px-6 py-4 text-center">Role</th>
                                    <th class="px-6 py-4">Nama Unit</th>
                                    <th class="px-6 py-4">Lokasi</th>
                                    <th class="px-6 py-4">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($riwayatUnits as $index => $unit)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-6 py-4 text-center">{{ $riwayatUnits->firstItem() + $index }}</td>
                                    
                                    <!-- Tanggal Input -->
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $unit->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $unit->created_at->format('H:i') }} WIB</div>
                                    </td>

                                    <!-- Penginput (Placeholder karena belum ada tabel relasi user di unit) -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <!-- Avatar statis berdasarkan nama user login -->
                                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=14532d&color=fff" class="w-8 h-8 rounded-full border border-gray-200" alt="Avatar">
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">{{ Auth::user()->name ?? 'Admin Sistem' }}</p>
                                                <p class="text-xs text-gray-400">ID: ADM-{{ Auth::id() ?? '01' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role (Badge) -->
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                            {{ Auth::user()->role ?? 'Admin' }}
                                        </span>
                                    </td>

                                    <!-- Nama Unit -->
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $unit->nama_unit }}
                                    </td>

                                    <!-- Lokasi -->
                                    <td class="px-6 py-4 font-medium text-gray-600">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600">{{ $unit->lokasi }}</span>
                                    </td>

                                    <!-- Keterangan (Logika Sederhana) -->
                                    <td class="px-6 py-4 text-xs text-gray-500 max-w-50 truncate">
                                        @if($unit->created_at == $unit->updated_at)
                                            Input data unit baru
                                        @else
                                            Pembaruan data unit
                                        @endif
                                        @if($unit->trashed())
                                            <span class="text-red-500 font-bold">(Terhapus)</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400 italic">
                                        Belum ada riwayat input unit.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-4 border-t border-gray-100">
                        {{ $riwayatUnits->links() }}
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