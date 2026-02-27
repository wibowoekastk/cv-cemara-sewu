<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan PDF - Admin Panel</title>
    
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
                        <span>Laporan PDF</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Download Laporan</h2>
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
                    reportType: '{{ request('report_type', 'harian') }}', 
                    unitID: '{{ request('unit', 'all') }}',
                    showPreview: {{ count($laporanData ?? []) > 0 ? 'true' : 'false' }}
                 }">
                
                <form action="{{ route('admin.analytic.laporan_pdf') }}" method="GET" id="reportForm">
                    <input type="hidden" name="filter" value="true">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- LEFT: Form Pilihan Laporan -->
                        <div class="md:col-span-2 space-y-6">
                            
                            <!-- Card 1: Pilihan Jenis -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <i class="ph-fill ph-files text-cemara-600 text-xl"></i> Pilih Jenis Laporan
                                </h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Opsi Harian -->
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="report_type" value="harian" class="peer sr-only" x-model="reportType">
                                        <div class="border-2 border-gray-100 rounded-xl p-4 peer-checked:border-cemara-500 peer-checked:bg-cemara-50 transition hover:bg-gray-50 flex flex-col items-center text-center h-full relative overflow-hidden z-0">
                                            <div class="absolute top-2 right-2 text-cemara-500 opacity-0 peer-checked:opacity-100 transition z-10">
                                                <i class="ph-fill ph-check-circle text-xl"></i>
                                            </div>
                                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-cemara-600 mb-3 shadow-sm border border-gray-100 group-hover:scale-110 transition z-10 relative">
                                                <i class="ph-fill ph-calendar-blank text-3xl"></i>
                                            </div>
                                            <span class="font-bold text-gray-800 relative z-10">Laporan Harian</span>
                                            <span class="text-xs text-gray-500 mt-1 relative z-10">Detail per Kandang</span>
                                        </div>
                                    </label>

                                    <!-- Opsi Global -->
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="report_type" value="global" class="peer sr-only" x-model="reportType">
                                        <div class="border-2 border-gray-100 rounded-xl p-4 peer-checked:border-gold-500 peer-checked:bg-gold-50 transition hover:bg-gray-50 flex flex-col items-center text-center h-full relative overflow-hidden z-0">
                                            <div class="absolute top-2 right-2 text-gold-500 opacity-0 peer-checked:opacity-100 transition z-10">
                                                <i class="ph-fill ph-check-circle text-xl"></i>
                                            </div>
                                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gold-600 mb-3 shadow-sm border border-gray-100 group-hover:scale-110 transition z-10 relative">
                                                <i class="ph-bold ph-globe-hemisphere-west text-2xl"></i>
                                            </div>
                                            <span class="font-bold text-gray-800 relative z-10">Laporan Global</span>
                                            <span class="text-xs text-gray-500 mt-1 relative z-10">Rekap Unit (Tanpa Kandang)</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Card 2: Filter Data -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <i class="ph-fill ph-faders text-cemara-600 text-xl"></i> Filter Data
                                </h3>

                                <div class="space-y-6">
                                    <!-- Filter Tanggal -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2" x-text="reportType === 'harian' ? 'Tanggal Laporan' : 'Periode Laporan (Rentang)'"></label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Tanggal Mulai -->
                                            <div class="relative">
                                                <input type="date" name="dateStart" value="{{ request('dateStart', date('Y-m-d')) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-700">
                                                <span class="absolute right-4 top-3 text-xs text-gray-400 pointer-events-none" x-show="reportType === 'global'">Dari</span>
                                            </div>
                                            
                                            <!-- Tanggal Akhir (Hanya Global) -->
                                            <div class="relative" x-show="reportType === 'global'">
                                                <input type="date" name="dateEnd" value="{{ request('dateEnd', date('Y-m-d')) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-700">
                                                <span class="absolute right-4 top-3 text-xs text-gray-400 pointer-events-none">Sampai</span>
                                            </div>
                                            
                                            <!-- Placeholder Harian -->
                                            <div class="hidden md:block p-3 text-xs text-gray-400 italic items-center" x-show="reportType === 'harian'">
                                                *Data per hari spesifik
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Unit -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Unit</label>
                                            <div class="relative">
                                                <select name="unit" x-model="unitID" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                    <option value="all">Semua Unit</option>
                                                    @foreach($units as $u)
                                                        <option value="{{ $u->id }}">{{ $u->nama_unit }}</option>
                                                    @endforeach
                                                </select>
                                                <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                            </div>
                                        </div>

                                        <!-- Filter Kandang -->
                                        <div x-show="reportType === 'harian' && unitID !== 'all'">
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pilih Kandang</label>
                                            <div class="relative">
                                                <select name="kandang" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                    <option value="all">Semua Kandang</option>
                                                    @foreach($units as $u)
                                                        @foreach($u->kandangs as $k)
                                                            <option value="{{ $k->id }}" x-show="unitID == '{{ $u->id }}'">{{ $k->nama_kandang }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                                <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- [BARU] Filter Batch / Siklus -->
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pilih Batch / Siklus (Opsional)</label>
                                        <div class="relative">
                                            <select name="batch_id" class="w-full px-4 py-3 bg-blue-50/50 border border-blue-100 rounded-xl appearance-none focus:ring-2 focus:ring-blue-500 outline-none transition cursor-pointer font-bold text-blue-800">
                                                <option value="all">-- Tampilkan Semua Angkatan --</option>
                                                @if(isset($batches))
                                                    @foreach($batches as $batch)
                                                        <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                                            {{ $batch->nama_batch }} (Mulai: {{ \Carbon\Carbon::parse($batch->tanggal_mulai)->format('M Y') }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-blue-400 pointer-events-none"></i>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1 italic">*Gunakan filter ini agar data laporan tidak tercampur dengan angkatan/batch lain.</p>
                                    </div>

                                </div>
                                
                                <!-- Tombol Cari -->
                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-cemara-900 text-white font-bold rounded-xl hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 w-full md:w-auto justify-center">
                                        <i class="ph-bold ph-magnifying-glass"></i> Cari & Preview
                                    </button>
                                </div>
                            </div>

                            <!-- Result Preview (Muncul setelah Cari) -->
                            @if(isset($laporanData) && count($laporanData) > 0)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" id="previewArea">
                                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                                    <div>
                                        <h4 class="font-bold text-gray-800">Pratinjau Data</h4>
                                        <p class="text-xs text-gray-500">{{ count($laporanData) }} Data ditemukan</p>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded border border-green-200">Siap Unduh</span>
                                </div>
                                
                                <div class="p-0 overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-white text-gray-500 font-semibold border-b border-gray-100">
                                            <tr>
                                                <th class="px-4 py-3">Tanggal</th>
                                                <th class="px-4 py-3">Kandang</th>
                                                
                                                <!-- [BARU] Kolom Batch di Preview -->
                                                <th class="px-4 py-3">Batch / Siklus</th>
                                                
                                                <th class="px-4 py-3 text-center">Mati</th>
                                                <th class="px-4 py-3 text-center">Produksi</th>
                                                
                                                <th class="px-4 py-3 text-center text-blue-600 bg-blue-50/30">HD %</th>
                                                <th class="px-4 py-3 text-center text-blue-600 bg-blue-50/30">HH (Btr)</th>
                                                <th class="px-4 py-3 text-center text-blue-600 bg-blue-50/30">HH (Kg)</th>
                                                <th class="px-4 py-3 text-center">FCR</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($laporanData as $data)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 font-medium text-gray-800">{{ \Carbon\Carbon::parse($data->tanggal)->format('d M') }}</td>
                                                <td class="px-4 py-3 text-gray-600">
                                                    <span class="font-bold text-gray-700">{{ $data->kandang->nama_kandang ?? '-' }}</span><br>
                                                    <span class="text-xs">{{ $data->kandang->unit->nama_unit ?? '-' }}</span>
                                                </td>
                                                
                                                <!-- [BARU] Data Batch Preview -->
                                                <td class="px-4 py-3 text-xs text-gray-500">
                                                    @if($data->siklus)
                                                        <span class="block text-blue-600 font-bold">Batch {{ $data->siklus->tanggal_chick_in->format('Y') }}</span>
                                                        <span class="text-[10px]">({{ $data->siklus->jenis_ayam }})</span>
                                                    @else
                                                        <span class="text-gray-300">-</span>
                                                    @endif
                                                </td>

                                                <td class="px-4 py-3 text-center text-red-600 font-bold">{{ $data->mati + $data->afkir }}</td>
                                                <td class="px-4 py-3 text-center text-gold-600 font-bold">{{ number_format($data->telur_kg, 1) }} Kg</td>
                                                
                                                <!-- Nilai HH -->
                                                @php
                                                    $stokAwal = $data->kandang->stok_awal ?? 1;
                                                    $hhButir = $stokAwal > 0 ? $data->telur_butir / $stokAwal : 0;
                                                    $hhKg = $stokAwal > 0 ? $data->telur_kg / $stokAwal : 0;
                                                @endphp
                                                <td class="px-4 py-3 text-center text-blue-600 font-bold bg-blue-50/10">{{ number_format($data->hdp, 1) }}%</td>
                                                <td class="px-4 py-3 text-center text-blue-500 bg-blue-50/10">{{ number_format($hhButir, 3) }}</td>
                                                <td class="px-4 py-3 text-center text-blue-500 bg-blue-50/10">{{ number_format($hhKg, 3) }}</td>
                                                
                                                <td class="px-4 py-3 text-center">{{ number_format($data->fcr, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @elseif(request('filter'))
                            <div class="bg-red-50 p-4 rounded-xl border border-red-200 text-red-600 text-center text-sm">
                                Tidak ada data ditemukan untuk filter ini.
                            </div>
                            @endif

                        </div>

                        <!-- RIGHT: Opsi Cetak & Download -->
                        <div class="space-y-6">
                            
                            <!-- Panel Opsi -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide border-b border-gray-100 pb-2">Opsi Dokumen</h3>
                                
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition group select-none">
                                        <div class="relative flex items-center justify-center w-5 h-5">
                                             <input type="checkbox" checked class="w-5 h-5 text-cemara-600 rounded focus:ring-cemara-500 border-gray-300 checked:bg-cemara-600 checked:border-cemara-600 transition-all cursor-pointer">
                                            <i class="ph-bold ph-check text-white absolute text-xs opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
                                        </div>
                                        <div class="flex flex-col cursor-pointer">
                                            <span class="text-sm font-bold text-gray-700 group-hover:text-cemara-700 transition">Sertakan Grafik</span>
                                            <span class="text-xs text-gray-400">Visualisasi tren data</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Input Manual untuk Cetak (Muncul jika ada data) -->
                            @if(isset($laporanData) && count($laporanData) > 0)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-sm font-bold text-gold-600 mb-4 uppercase tracking-wide border-b border-gray-100 pb-2">Input Manual (Cetak)</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 mb-1 block">Total Peti</label>
                                        <input type="number" name="manual_peti" class="w-full p-2 border border-gray-200 rounded-lg text-sm" placeholder="0">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 mb-1 block">Total Pecah (Kg)</label>
                                        <input type="number" step="0.01" name="manual_pecah" class="w-full p-2 border border-gray-200 rounded-lg text-sm" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 mb-1 block">Total Konsumsi (Kg)</label>
                                        <input type="number" step="0.01" name="manual_konsumsi" class="w-full p-2 border border-gray-200 rounded-lg text-sm" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Action Button -->
                            <div class="bg-cemara-900 rounded-2xl p-6 text-white text-center shadow-lg shadow-cemara-900/20 sticky top-24">
                                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                                    <i class="ph-fill ph-file-pdf text-3xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-lg mb-1 font-poppins">Siap Mengunduh</h3>
                                <p class="text-cemara-200 text-xs mb-6 px-2 leading-relaxed">
                                    {{ isset($laporanData) ? count($laporanData) : 0 }} Data ditemukan. Klik unduh untuk menyimpan PDF.
                                </p>
                                
                                <button type="submit" name="download_pdf" value="true" 
                                        {{ (!isset($laporanData) || count($laporanData) == 0) ? 'disabled' : '' }}
                                        class="w-full py-3.5 rounded-xl font-bold transition shadow-md flex items-center justify-center gap-2 group {{ (!isset($laporanData) || count($laporanData) == 0) ? 'bg-gray-600 cursor-not-allowed opacity-50' : 'bg-white text-cemara-900 hover:bg-gray-100' }}">
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