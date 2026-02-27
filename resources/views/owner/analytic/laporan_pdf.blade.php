<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Laporan - Owner Panel</title>
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
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">
    
    <!-- Sidebar Owner -->
    <div class="flex h-screen overflow-hidden relative">
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>
        @include('owner.sidebar')

        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Laporan</span><i class="ph-bold ph-caret-right"></i><span>Export PDF</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Pusat Download Laporan</h2>
                </div>
                
                <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                    <i class="ph-bold ph-list text-2xl"></i>
                </button>
            </header>

            <div class="p-4 md:p-8 w-full max-w-6xl mx-auto" 
                 x-data="{ 
                    reportType: '{{ request('report_type', 'harian') }}', 
                    unitID: '{{ request('unit', 'all') }}'
                 }">
                
                <!-- Form Filter -->
                <form action="{{ route('owner.analytic.laporan-pdf') }}" method="GET" id="reportForm">
                    <input type="hidden" name="filter" value="true">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Kolom Filter -->
                        <div class="md:col-span-2 space-y-6">
                            <!-- Card Filter -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <i class="ph-fill ph-faders text-cemara-600 text-xl"></i> Filter Data
                                </h3>
                                
                                <div class="space-y-6">
                                    <!-- Tanggal -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Periode</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <input type="date" name="dateStart" value="{{ request('dateStart', date('Y-m-d')) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none">
                                            <input type="date" name="dateEnd" value="{{ request('dateEnd', date('Y-m-d')) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none">
                                        </div>
                                    </div>
                                    
                                    <!-- Unit & Kandang -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Unit</label>
                                            <select name="unit" x-model="unitID" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none">
                                                <option value="all">Semua Unit</option>
                                                @foreach($units as $u)
                                                    <option value="{{ $u->id }}">{{ $u->nama_unit }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kandang</label>
                                            <select name="kandang" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none">
                                                <option value="all">Semua Kandang</option>
                                                <!-- Loop kandang berdasarkan unit bisa ditambahkan disini -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-cemara-900 text-white font-bold rounded-xl hover:bg-cemara-800 transition">Cari & Preview</button>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Opsi & Download -->
                        <div class="space-y-6">
                             <!-- Action Button -->
                             <div class="bg-cemara-900 rounded-2xl p-6 text-white text-center shadow-lg shadow-cemara-900/20 sticky top-24">
                                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                                    <i class="ph-fill ph-file-pdf text-3xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-lg mb-1 font-poppins">Siap Mengunduh</h3>
                                <p class="text-cemara-200 text-xs mb-6 px-2 leading-relaxed">
                                    {{ isset($laporanData) ? count($laporanData) : 0 }} Data ditemukan.
                                </p>
                                <button type="submit" name="download_pdf" value="true" class="w-full py-3.5 rounded-xl font-bold transition shadow-md bg-white text-cemara-900 hover:bg-gray-100 flex items-center justify-center gap-2">
                                    <i class="ph-bold ph-download-simple"></i> Download PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Tabel Preview -->
                @if(isset($laporanData) && count($laporanData) > 0)
                <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h4 class="font-bold text-gray-800">Pratinjau Data</h4>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-white text-gray-500 font-semibold border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Unit / Kandang</th>
                                    
                                    <!-- [BARU] Kolom Batch -->
                                    <th class="px-4 py-3">Batch / Siklus</th>
                                    
                                    <th class="px-4 py-3 text-center">Produksi (Kg)</th>
                                    <th class="px-4 py-3 text-center">HH (Btr)</th>
                                    <th class="px-4 py-3 text-center">HH (Kg)</th>
                                    <th class="px-4 py-3 text-center">FCR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($laporanData as $row)
                                @php
                                    $stokAwal = $row->kandang->stok_awal ?? 1;
                                    $hhButir = $stokAwal > 0 ? $row->telur_butir / $stokAwal : 0;
                                    $hhKg = $stokAwal > 0 ? $row->telur_kg / $stokAwal : 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M') }}</td>
                                    
                                    <!-- Unit & Kandang -->
                                    <td class="px-4 py-3 text-gray-600">
                                        <span class="font-bold text-gray-700">{{ $row->kandang->nama_kandang ?? '-' }}</span><br>
                                        <span class="text-xs">{{ $row->kandang->unit->nama_unit ?? '-' }}</span>
                                    </td>
                                    
                                    <!-- Info Batch -->
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        @if($row->siklus)
                                            <span class="block text-blue-600 font-bold">Batch {{ $row->siklus->tanggal_chick_in->format('Y') }}</span>
                                            <span class="text-[10px]">({{ $row->siklus->jenis_ayam }})</span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center font-bold text-gold-600">{{ number_format($row->telur_kg, 2) }}</td>
                                    <td class="px-4 py-3 text-center text-blue-500">{{ number_format($hhButir, 3) }}</td>
                                    <td class="px-4 py-3 text-center text-blue-500">{{ number_format($hhKg, 3) }}</td>
                                    <td class="px-4 py-3 text-center font-bold">{{ number_format($row->fcr, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
            </div>
        </main>
    </div>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>