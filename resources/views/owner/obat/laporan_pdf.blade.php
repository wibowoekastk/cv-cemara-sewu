<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Obat - Owner Panel</title>
    
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
                        <span>Manajemen Obat</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Laporan PDF</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Export Laporan Obat</h2>
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
                    isLoading: false,
                    searchLaporan() {
                        this.isLoading = true;
                        document.getElementById('reportForm').submit();
                    }
                 }">
                
                <form action="{{ route('owner.obat.laporan') }}" method="GET" id="reportForm">
                    <input type="hidden" name="filter" value="true">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- LEFT COLUMN: Filter & Settings -->
                        <div class="md:col-span-2 space-y-6">
                            
                            <!-- 1. Jenis Laporan -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <i class="ph-fill ph-files text-medical-600 text-xl"></i> Jenis Laporan
                                </h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Laporan Stok Masuk -->
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="report_type" value="stok" class="peer sr-only" {{ $tipeLaporan == 'stok' ? 'checked' : '' }}>
                                        <div class="border-2 border-gray-100 rounded-xl p-4 peer-checked:border-medical-500 peer-checked:bg-medical-50/50 transition hover:bg-gray-50 flex flex-col items-center text-center h-full relative overflow-hidden z-0">
                                            <div class="absolute top-2 right-2 text-medical-500 opacity-0 peer-checked:opacity-100 transition z-10">
                                                <i class="ph-fill ph-check-circle text-xl"></i>
                                            </div>
                                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-medical-600 mb-3 shadow-sm border border-gray-100 group-hover:scale-110 transition z-10 relative">
                                                <i class="ph-bold ph-package text-2xl"></i>
                                            </div>
                                            <span class="font-bold text-gray-800 relative z-10">Riwayat Stok Masuk</span>
                                            <span class="text-xs text-gray-500 mt-1 relative z-10">Pembelian & Produksi</span>
                                        </div>
                                    </label>

                                    <!-- Laporan Pemakaian -->
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="report_type" value="pakai" class="peer sr-only" {{ $tipeLaporan == 'pakai' ? 'checked' : '' }}>
                                        <div class="border-2 border-gray-100 rounded-xl p-4 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition hover:bg-gray-50 flex flex-col items-center text-center h-full relative overflow-hidden z-0">
                                            <div class="absolute top-2 right-2 text-gold-500 opacity-0 peer-checked:opacity-100 transition z-10">
                                                <i class="ph-fill ph-check-circle text-xl"></i>
                                            </div>
                                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gold-600 mb-3 shadow-sm border border-gray-100 group-hover:scale-110 transition z-10 relative">
                                                <i class="ph-bold ph-syringe text-2xl"></i>
                                            </div>
                                            <span class="font-bold text-gray-800 relative z-10">Riwayat Pemakaian</span>
                                            <span class="text-xs text-gray-500 mt-1 relative z-10">Penggunaan di kandang</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- 2. Filter Data -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <i class="ph-fill ph-faders text-medical-600 text-xl"></i> Filter Data
                                </h3>

                                <div class="space-y-6">
                                    <!-- Filter Tanggal -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Periode Laporan</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="relative">
                                                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full pl-4 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-medical-500 outline-none transition font-medium text-gray-700">
                                                <span class="absolute right-4 top-3 text-xs text-gray-400 pointer-events-none">Dari</span>
                                            </div>
                                            <div class="relative">
                                                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full pl-4 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-medical-500 outline-none transition font-medium text-gray-700">
                                                <span class="absolute right-4 top-3 text-xs text-gray-400 pointer-events-none">Sampai</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filter Kategori -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kategori Obat</label>
                                        <div class="relative">
                                            <select name="jenis_obat" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-medical-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                <option value="semua">Semua Kategori</option>
                                                @foreach($kategoris as $kat)
                                                    <option value="{{ $kat }}" {{ $kategori == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                                @endforeach
                                            </select>
                                            <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tombol Cari -->
                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-medical-600 text-white font-bold rounded-xl hover:bg-medical-700 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 w-full md:w-auto justify-center">
                                        <i class="ph-bold ph-magnifying-glass" x-show="!isLoading"></i>
                                        <i class="ph-bold ph-spinner animate-spin" x-show="isLoading" style="display: none;"></i>
                                        <span x-text="isLoading ? 'Mencari Data...' : 'Cari & Preview'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Opsi Cetak & Download -->
                        <div class="space-y-6">
                            
                            <!-- Panel Opsi -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide border-b border-gray-100 pb-2">Opsi Dokumen</h3>
                                
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition group select-none relative">
                                        <input type="checkbox" checked class="w-5 h-5 text-medical-600 rounded border-gray-300 focus:ring-medical-500 transition cursor-pointer z-10">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-700 group-hover:text-medical-700 transition">Detail Transaksi</span>
                                            <span class="text-xs text-gray-400">Rincian per tanggal</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="bg-medical-700 rounded-2xl p-6 text-white text-center shadow-lg shadow-medical-700/20 sticky top-24">
                                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                                    <i class="ph-fill ph-file-pdf text-3xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-lg mb-1 font-poppins">Siap Mengunduh</h3>
                                <p class="text-medical-100 text-xs mb-6 px-2 leading-relaxed">
                                    Laporan {{ $tipeLaporan == 'stok' ? 'Stok Masuk' : 'Pemakaian' }} akan digenerate.
                                </p>
                                
                                <button type="submit" name="download_pdf" value="true" 
                                        {{ count($data) == 0 ? 'disabled' : '' }}
                                        class="w-full py-3.5 rounded-xl font-bold transition shadow-md flex items-center justify-center gap-2 group {{ count($data) == 0 ? 'bg-gray-600 cursor-not-allowed opacity-50' : 'bg-white text-medical-700 hover:bg-gray-100' }}">
                                    <i class="ph-bold ph-download-simple group-hover:scale-110 transition-transform"></i>
                                    Download PDF
                                </button>
                            </div>

                        </div>

                    </div>
                </form>

                <!-- 3. Preview Section (Tabel) -->
                @if(count($data) > 0)
                <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-gray-800">Pratinjau Data</h4>
                            <p class="text-xs text-gray-500">Menampilkan {{ count($data) }} data transaksi</p>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded border border-green-200">Data Siap</span>
                    </div>
                    
                    <div class="p-0 overflow-x-auto table-container max-h-100">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-white text-gray-500 font-semibold border-b border-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 w-24 bg-gray-50">Tanggal</th>
                                    <th class="px-4 py-3 bg-gray-50">Nama Obat</th>
                                    <th class="px-4 py-3 bg-gray-50">Kategori</th>
                                    
                                    @if($tipeLaporan == 'pakai')
                                        <th class="px-4 py-3 text-center bg-gray-50">Jml Pakai</th>
                                        <th class="px-4 py-3 bg-gray-50">Keterangan</th>
                                    @else
                                        <th class="px-4 py-3 text-center bg-gray-50">Jml Masuk</th>
                                        <th class="px-4 py-3 bg-gray-50">Batch / Exp</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($data as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 font-mono text-xs text-gray-500">
                                        {{ $tipeLaporan == 'pakai' ? \Carbon\Carbon::parse($row->tgl_pakai)->format('d M Y') : \Carbon\Carbon::parse($row->tgl_masuk)->format('d M Y') }}
                                    </td>
                                    
                                    @if($tipeLaporan == 'pakai')
                                        <td class="px-4 py-2 font-medium text-gray-700">{{ $row->batch->obat->nama_obat ?? '-' }}</td>
                                        <td class="px-4 py-2 text-xs text-gray-500">{{ $row->batch->obat->jenis_obat ?? '-' }}</td>
                                        <td class="px-4 py-2 text-center font-bold text-red-600">{{ number_format($row->jumlah_pakai) }}</td>
                                        <td class="px-4 py-2 text-xs text-gray-500">{{ $row->keterangan ?? '-' }}</td>
                                    @else
                                        <td class="px-4 py-2 font-medium text-gray-700">{{ $row->obat->nama_obat ?? '-' }}</td>
                                        <td class="px-4 py-2 text-xs text-gray-500">{{ $row->obat->jenis_obat ?? '-' }}</td>
                                        <td class="px-4 py-2 text-center font-bold text-green-600">{{ number_format($row->stok_awal) }}</td>
                                        <td class="px-4 py-2 text-xs text-gray-500">
                                            {{ $row->kode_batch }} <br>
                                            Exp: {{ \Carbon\Carbon::parse($row->tgl_kadaluarsa)->format('d M Y') }}
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @elseif(request('filter'))
                <div class="mt-8 bg-red-50 p-6 rounded-2xl text-center border border-red-200">
                    <p class="text-red-600">Tidak ada data obat pada periode ini.</p>
                </div>
                @endif
                
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