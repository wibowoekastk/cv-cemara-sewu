<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Obat PDF - Admin Panel</title>
    
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
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' },
                        medical: { 50: '#ecfeff', 100: '#cffafe', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        @include('admin.sidebar')

        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Cetak Laporan Obat</h2>
                    <p class="text-xs text-gray-500">Filter dan export data obat ke PDF</p>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-medical-50 text-medical-700 rounded-lg text-sm font-semibold border border-medical-100">
                        <i class="ph-fill ph-calendar-check"></i>
                        <span>{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
                    </div>
                </div>
            </header>

            <div class="p-4 md:p-8 w-full max-w-5xl mx-auto">
                
                <!-- Form Filter -->
                <form action="{{ route('admin.obat.laporan_pdf') }}" method="GET" id="filterForm">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- LEFT: Konfigurasi Laporan -->
                        <div class="md:col-span-2 space-y-6">
                            
                            <!-- Jenis Laporan -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <i class="ph-fill ph-files text-medical-600 text-xl"></i> Jenis Laporan
                                </h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="report_type" value="stok" class="peer hidden" {{ $tipeLaporan == 'stok' ? 'checked' : '' }} onchange="this.form.submit()">
                                        <div class="border-2 border-gray-100 rounded-xl p-4 peer-checked:border-medical-500 peer-checked:bg-medical-50 transition hover:bg-gray-50 flex flex-col items-center text-center h-full">
                                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-medical-600 mb-3 shadow-sm border border-gray-100"><i class="ph-bold ph-package text-2xl"></i></div>
                                            <span class="font-bold text-gray-800">Laporan Stok Masuk</span>
                                            <span class="text-xs text-gray-500 mt-1">History pembelian & batch</span>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" name="report_type" value="pakai" class="peer hidden" {{ $tipeLaporan == 'pakai' ? 'checked' : '' }} onchange="this.form.submit()">
                                        <div class="border-2 border-gray-100 rounded-xl p-4 peer-checked:border-gold-500 peer-checked:bg-gold-50 transition hover:bg-gray-50 flex flex-col items-center text-center h-full">
                                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gold-600 mb-3 shadow-sm border border-gray-100"><i class="ph-bold ph-syringe text-2xl"></i></div>
                                            <span class="font-bold text-gray-800">Laporan Pemakaian</span>
                                            <span class="text-xs text-gray-500 mt-1">Log penggunaan harian</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Filter Data -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <i class="ph-fill ph-faders text-medical-600 text-xl"></i> Filter Data
                                </h3>

                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Periode Laporan</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-medical-500 outline-none transition font-medium text-gray-700">
                                            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-medical-500 outline-none transition font-medium text-gray-700">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kategori Obat</label>
                                        <select name="kategori" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-medical-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                            <option value="semua" {{ $kategori == 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                                            <option value="Vaksin" {{ $kategori == 'Vaksin' ? 'selected' : '' }}>Vaksin</option>
                                            <option value="Vitamin" {{ $kategori == 'Vitamin' ? 'selected' : '' }}>Vitamin</option>
                                            <option value="Antibiotik" {{ $kategori == 'Antibiotik' ? 'selected' : '' }}>Antibiotik</option>
                                            <option value="Disinfektan" {{ $kategori == 'Disinfektan' ? 'selected' : '' }}>Disinfektan</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-cemara-900 text-white font-bold rounded-xl hover:bg-cemara-800 transition shadow-lg flex items-center gap-2">
                                        <i class="ph-bold ph-magnifying-glass"></i> Terapkan Filter
                                    </button>
                                </div>
                            </div>

                            <!-- PREVIEW RESULT -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                                    <h4 class="font-bold text-gray-800">Pratinjau Hasil</h4>
                                    <span class="text-xs bg-gray-200 px-2 py-1 rounded text-gray-600 font-bold">{{ count($data) }} Data Ditemukan</span>
                                </div>
                                <div class="p-0 overflow-x-auto max-h-80">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-white text-gray-500 font-semibold border-b border-gray-100 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-3">Tanggal</th>
                                                <th class="px-4 py-3">Nama Obat</th>
                                                <th class="px-4 py-3 text-center">Jumlah</th>
                                                <th class="px-4 py-3 text-center">Satuan</th>
                                                @if($tipeLaporan == 'stok')
                                                    <th class="px-4 py-3 text-center">Expired</th>
                                                @else
                                                    <th class="px-6 py-4">Keterangan</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @forelse($data as $item)
                                                <tr>
                                                    <td class="px-4 py-2 text-gray-600">
                                                        {{ \Carbon\Carbon::parse($tipeLaporan == 'stok' ? $item->tgl_masuk : $item->tgl_pakai)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-4 py-2 font-bold text-gray-800">
                                                        {{ $tipeLaporan == 'stok' ? $item->obat->nama_obat : $item->batch->obat->nama_obat }}
                                                    </td>
                                                    <td class="px-4 py-2 text-center font-bold {{ $tipeLaporan == 'stok' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $tipeLaporan == 'stok' ? $item->stok_awal : $item->jumlah_pakai }}
                                                    </td>
                                                    <td class="px-4 py-2 text-center text-gray-500">
                                                        {{ $tipeLaporan == 'stok' ? $item->obat->satuan : $item->batch->obat->satuan }}
                                                    </td>
                                                    @if($tipeLaporan == 'stok')
                                                        <td class="px-4 py-2 text-center text-red-500 text-xs font-bold">
                                                            {{ \Carbon\Carbon::parse($item->tgl_kadaluarsa)->format('d/m/y') }}
                                                        </td>
                                                    @else
                                                        <td class="px-4 py-2 text-xs text-gray-500 italic truncate max-w-37.5">
                                                            {{ $item->keterangan }}
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="p-4 text-center text-gray-400">Tidak ada data untuk filter ini.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Tombol Cetak -->
                        <div class="space-y-6">
                            <div class="bg-medical-700 rounded-2xl p-6 text-white text-center shadow-lg shadow-medical-700/20 sticky top-24">
                                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                                    <i class="ph-fill ph-printer text-3xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-lg mb-1">Cetak PDF</h3>
                                <p class="text-medical-100 text-xs mb-6 px-2">Hasil akan digenerate sesuai filter yang dipilih.</p>
                                
                                <!-- Tombol ini membuka tab baru dengan parameter ?print=true -->
                                <a href="{{ request()->fullUrlWithQuery(['print' => 'true']) }}" target="_blank" 
                                   class="w-full py-3 bg-white/20 border border-white/30 text-white rounded-xl font-bold transition shadow-md flex items-center justify-center gap-2 hover:bg-white hover:text-medical-700">
                                    <i class="ph-bold ph-download-simple"></i> Download / Cetak
                                </a>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </main>
    </div>

</body>
</html>