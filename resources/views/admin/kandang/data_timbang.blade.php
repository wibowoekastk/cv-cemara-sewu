<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Timbang Ayam - Admin Panel</title>
    
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
                        <span>Data Timbang</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Rekap Data Timbang</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    
                    <!-- Tombol Tambah Data -->
                    <a href="{{ route('admin.kandang.input_timbang') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20">
                        <i class="ph-bold ph-plus"></i> Input Baru
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- Filter Section (FORM FILTER) -->
                <form method="GET" action="{{ route('admin.kandang.data_timbang') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <!-- Filter Tanggal -->
                        <div class="relative group">
                            <i class="ph-bold ph-calendar-blank absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none transition">
                        </div>
                        <!-- Filter Unit -->
                        <div class="relative group">
                            <select name="unit_id" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none cursor-pointer">
                                <option value="">Semua Unit</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['tanggal', 'unit_id']))
                            <a href="{{ route('admin.kandang.data_timbang') }}" class="px-4 py-2 text-red-500 text-sm hover:underline">Reset</a>
                        @endif
                    </div>
                </form>

                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="ph-fill ph-check-circle text-xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">#</th>
                                    <th class="px-6 py-4">Tgl Input</th>
                                    <th class="px-6 py-4">Unit</th>
                                    <th class="px-6 py-4">Lokasi</th>
                                    <th class="px-6 py-4">Kandang</th>
                                    <!-- [BARU] Kolom Batch -->
                                    <th class="px-6 py-4">Batch / Siklus</th>
                                    <th class="px-6 py-4 text-center">Umur (Minggu)</th>
                                    <th class="px-6 py-4 text-center">Berat Ayam (gr)</th>
                                    <th class="px-6 py-4 text-center">Uniformity (%)</th>
                                    <th class="px-6 py-4">Keterangan</th>
                                    <th class="px-6 py-4 text-center sticky right-0 bg-gray-50 z-10 shadow-[-4px_0_12px_-4px_rgba(0,0,0,0.05)]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($timbangans as $index => $timbang)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-6 py-4 text-center">{{ $timbangans->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <i class="ph-fill ph-calendar text-gray-400"></i>
                                            {{ \Carbon\Carbon::parse($timbang->tanggal_timbang)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600">
                                            {{ $timbang->kandang->unit->nama_unit ?? '-' }}
                                        </span>
                                    </td>
                                    <!-- Lokasi Baru -->
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600">
                                            {{ $timbang->kandang->unit->lokasi ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800">{{ $timbang->kandang->nama_kandang ?? '-' }}</div>
                                    </td>

                                    <!-- [BARU] Info Batch / Siklus -->
                                    {{-- Logika: Cari siklus yang tanggal Chick-In nya sebelum tgl timbang & belum selesai (atau selesai setelah tgl timbang) --}}
                                    @php
                                        $siklusFound = null;
                                        if($timbang->kandang && $timbang->kandang->historySiklus) {
                                            $siklusFound = $timbang->kandang->historySiklus->first(function($s) use ($timbang) {
                                                // Gunakan tanggal timbang jika ada, jika tidak created_at
                                                $tglTimbang = $timbang->tanggal_timbang ? \Carbon\Carbon::parse($timbang->tanggal_timbang) : $timbang->created_at;
                                                $tglSelesai = $s->tanggal_selesai ? \Carbon\Carbon::parse($s->tanggal_selesai) : now()->addYear();
                                                return $tglTimbang >= $s->tanggal_chick_in && $tglTimbang <= $tglSelesai;
                                            });
                                        }
                                    @endphp
                                    <td class="px-6 py-4">
                                        @if($siklusFound)
                                            <span class="block text-xs font-bold text-blue-600">Batch {{ $siklusFound->tanggal_chick_in->format('Y') }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $siklusFound->jenis_ayam }}</span>
                                        @else
                                            <span class="text-xs text-gray-300 italic">-</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center"> 
                                        <span class="font-bold text-gray-700">{{ $timbang->umur_minggu }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-bold text-gold-600 bg-gold-50 px-3 py-1 rounded-full border border-gold-100">
                                            {{ number_format($timbang->berat_rata) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-bold {{ $timbang->uniformity >= 85 ? 'text-blue-600 bg-blue-50 border-blue-100' : 'text-red-600 bg-red-50 border-red-100' }} px-3 py-1 rounded-full border">
                                            {{ $timbang->uniformity }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 max-w-50 truncate" title="{{ $timbang->keterangan }}">
                                        {{ $timbang->keterangan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center sticky right-0 bg-white group-hover:bg-gray-50 z-10 shadow-[-4px_0_12px_-4px_rgba(0,0,0,0.05)]">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                                <i class="ph-bold ph-pencil-simple"></i>
                                            </button>
                                            <button class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" onclick="confirmDelete()">
                                                <i class="ph-bold ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-8 text-center text-gray-400 italic">
                                        Belum ada data penimbangan. <br>
                                        <a href="{{ route('admin.kandang.input_timbang') }}" class="text-cemara-600 font-bold hover:underline">Input data baru</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-4 border-t border-gray-100">
                        {{ $timbangans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Script SweetAlert & Sidebar -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Data?',
                text: "Fitur hapus akan segera tersedia.",
                icon: 'info',
                confirmButtonColor: '#3085d6',
            });
        }

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