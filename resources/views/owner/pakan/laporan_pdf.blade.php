<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pakan - Owner Panel</title>
    
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
                        <span>Laporan PDF</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Export Laporan Pakan</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Date Display -->
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-semibold border border-cemara-100">
                        <i class="ph-fill ph-calendar-check"></i>
                        <span id="headerDateDisplay"></span>
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full max-w-6xl mx-auto" 
                 x-data="{ 
                    showResult: {{ count($detailMutasi) > 0 ? 'true' : 'false' }}, 
                    isLoading: false,
                    searchLaporan() {
                        this.isLoading = true;
                        document.getElementById('reportForm').submit();
                    }
                 }">
                
                <form action="{{ route('owner.pakan.laporan') }}" method="GET" id="reportForm">
                    <input type="hidden" name="filter" value="true">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- LEFT: Form Pilihan & Filter -->
                        <div class="md:col-span-2 space-y-6">
                            
                            <!-- Card Filter -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                                    <div class="w-10 h-10 bg-cemara-100 rounded-full flex items-center justify-center text-cemara-600">
                                        <i class="ph-fill ph-faders text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-lg">Filter Data Laporan</h3>
                                        <p class="text-xs text-gray-500">Tentukan periode dan kategori untuk cetak PDF.</p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <!-- Filter Tanggal -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Periode Laporan</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="relative">
                                                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="w-full pl-4 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-700">
                                                <span class="absolute right-4 top-3 text-xs text-gray-400 pointer-events-none">Dari</span>
                                            </div>
                                            <div class="relative">
                                                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="w-full pl-4 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-700">
                                                <span class="absolute right-4 top-3 text-xs text-gray-400 pointer-events-none">Sampai</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filter Pakan & Unit -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Jenis Pakan</label>
                                            <div class="relative">
                                                <select name="pakan_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                    <option value="all">Semua Pakan</option>
                                                    @foreach($pakans as $pakan)
                                                        <option value="{{ $pakan->id }}" {{ request('pakan_id') == $pakan->id ? 'selected' : '' }}>{{ $pakan->nama_pakan }}</option>
                                                    @endforeach
                                                </select>
                                                <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Unit / Lokasi</label>
                                            <div class="relative">
                                                <select name="unit_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                    <option value="all">Semua Unit</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_unit }}</option>
                                                    @endforeach
                                                </select>
                                                <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tombol Cari -->
                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-cemara-900 text-white font-bold rounded-xl hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 w-full md:w-auto justify-center">
                                        <i class="ph-bold ph-magnifying-glass" x-show="!isLoading"></i>
                                        <i class="ph-bold ph-spinner animate-spin" x-show="isLoading" style="display: none;"></i>
                                        <span x-text="isLoading ? 'Mencari Data...' : 'Cari & Preview'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Result Preview (Tabel Transaksi) -->
                            @if(count($detailMutasi) > 0)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                                    <div>
                                        <h4 class="font-bold text-gray-800">Pratinjau Data Transaksi</h4>
                                        <p class="text-xs text-gray-500">Menampilkan {{ count($detailMutasi) }} data terbaru</p>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded border border-green-200">Data Siap</span>
                                </div>
                                
                                <div class="p-0 overflow-x-auto table-container max-h-100">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-white text-gray-500 font-semibold border-b border-gray-100 sticky top-0 z-10">
                                            <tr>
                                                <th class="px-4 py-3 w-24 bg-gray-50">Tanggal</th>
                                                <th class="px-4 py-3 bg-gray-50">Aktivitas</th>
                                                <th class="px-4 py-3 bg-gray-50">Item Pakan</th>
                                                <th class="px-4 py-3 text-center text-green-600 bg-green-50/50">Masuk</th>
                                                <th class="px-4 py-3 text-center text-red-600 bg-red-50/50">Keluar</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($detailMutasi as $row)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 font-mono text-xs text-gray-500">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M') }}</td>
                                                <td class="px-4 py-2 font-medium text-gray-700">
                                                    @if($row->jenis_mutasi == 'masuk_pusat') <span class="text-blue-600 font-bold">Pembelian</span>
                                                    @elseif($row->jenis_mutasi == 'pemakaian') <span class="text-red-600">Pemakaian</span> ({{ $row->kandang->nama_kandang ?? 'Unit' }})
                                                    @elseif($row->jenis_mutasi == 'distribusi') <span class="text-orange-500">Distribusi</span> -> {{ $row->unitTujuan->nama_unit ?? '-' }}
                                                    @elseif($row->jenis_mutasi == 'produksi') <span class="text-green-600">Produksi</span>
                                                    @else {{ ucfirst($row->jenis_mutasi) }}
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-xs text-gray-500">{{ $row->pakan->nama_pakan }}</td>
                                                
                                                <!-- Masuk -->
                                                <td class="px-4 py-2 text-center font-bold text-green-600">
                                                    @if(in_array($row->jenis_mutasi, ['masuk_pusat', 'produksi', 'terima_unit']))
                                                        +{{ number_format($row->jumlah) }}
                                                    @else - @endif
                                                </td>
                                                
                                                <!-- Keluar -->
                                                <td class="px-4 py-2 text-center font-bold text-red-500">
                                                    @if(in_array($row->jenis_mutasi, ['pemakaian', 'distribusi']))
                                                        -{{ number_format($row->jumlah) }}
                                                    @else - @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @elseif(request('filter'))
                            <div class="bg-red-50 p-6 rounded-2xl text-center border border-red-200">
                                <p class="text-red-600">Tidak ada data pakan pada periode ini.</p>
                            </div>
                            @endif

                        </div>

                        <!-- RIGHT COLUMN: Opsi Cetak & Download -->
                        <div class="space-y-6">
                            
                            <!-- Panel Opsi -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide border-b border-gray-100 pb-2">Opsi Dokumen</h3>
                                
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition group select-none relative">
                                        <input type="checkbox" checked class="w-5 h-5 text-cemara-600 rounded border-gray-300 focus:ring-cemara-500 transition cursor-pointer z-10">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-700 group-hover:text-cemara-700 transition">Detail Transaksi</span>
                                            <span class="text-xs text-gray-400">Rincian per tanggal</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="bg-cemara-900 rounded-2xl p-6 text-white text-center shadow-lg shadow-cemara-900/20 sticky top-24">
                                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                                    <i class="ph-fill ph-file-pdf text-3xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-lg mb-1 font-poppins">Siap Mengunduh</h3>
                                <p class="text-cemara-200 text-xs mb-6 px-2 leading-relaxed">
                                    Laporan PDF akan mencakup ringkasan stok awal/akhir dan detail mutasi.
                                </p>
                                
                                <button type="submit" name="download_pdf" value="true" 
                                        {{ count($detailMutasi) == 0 ? 'disabled' : '' }}
                                        class="w-full py-3.5 rounded-xl font-bold transition shadow-md flex items-center justify-center gap-2 group {{ count($detailMutasi) == 0 ? 'bg-gray-600 cursor-not-allowed opacity-50' : 'bg-white text-cemara-900 hover:bg-gray-100' }}">
                                    <i class="ph-bold ph-download-simple group-hover:scale-110 transition-transform"></i>
                                    Download PDF
                                </button>
                            </div>

                        </div>

                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Script -->
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
        
        document.addEventListener('DOMContentLoaded', () => {
            const displayDate = document.getElementById('headerDateDisplay');
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
            if(displayDate) displayDate.innerText = today.toLocaleDateString('id-ID', options);
        });
    </script>
</body>
</html>