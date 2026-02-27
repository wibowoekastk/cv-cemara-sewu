<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kandang & Timbang - Admin Panel</title>
    
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
                        <span>Manajemen Data</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Kandang</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Laporan</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Pusat Laporan & Export</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-semibold border border-cemara-100">
                        <i class="ph-fill ph-calendar-check"></i>
                        <span id="headerDateDisplay"></span>
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full max-w-5xl mx-auto" x-data="{ activeTab: '{{ request('tipe', 'kandang') }}', includeChart: false }">
                
                <!-- TABS NAVIGASI -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 mb-6 inline-flex gap-2">
                    <a href="{{ route('admin.kandang.laporan', ['tipe' => 'kandang']) }}" 
                       :class="activeTab === 'kandang' ? 'bg-cemara-900 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                       class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                        <i class="ph-bold ph-warehouse text-lg"></i> Laporan Kandang
                    </a>
                    <a href="{{ route('admin.kandang.laporan', ['tipe' => 'timbang']) }}" 
                       :class="activeTab === 'timbang' ? 'bg-cemara-900 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                       class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                        <i class="ph-bold ph-scales text-lg"></i> Laporan Timbang
                    </a>
                </div>

                <!-- FORM FILTER -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8">
                    <form action="{{ route('admin.kandang.laporan') }}" method="GET">
                        <input type="hidden" name="tipe" :value="activeTab">
                        
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                            <div class="w-10 h-10 bg-cemara-100 rounded-full flex items-center justify-center text-cemara-600">
                                <i class="ph-fill ph-faders text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Filter Data <span x-text="activeTab == 'kandang' ? 'Kandang' : 'Timbang'"></span></h3>
                                <p class="text-xs text-gray-500">Sesuaikan parameter untuk mendapatkan laporan spesifik.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Filter Tanggal (Hanya relevan untuk Timbang) -->
                            <div x-show="activeTab === 'timbang'">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Periode Tanggal</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-700">
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-700">
                                </div>
                            </div>

                            <!-- Filter Unit -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pilih Unit (Opsional)</label>
                                <div class="relative">
                                    <select name="unit_id" id="unitSelector" onchange="filterKandang()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                        <option value="">Semua Unit</option>
                                        @if(isset($units))
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->nama_unit }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- Filter Kandang -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pilih Kandang</label>
                                <div class="relative">
                                    <select name="kandang_id" id="kandangSelector" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                        <option value="" selected>Semua Kandang</option>
                                        @if(isset($units))
                                            @foreach($units as $unit)
                                                @foreach($unit->kandangs as $kandang)
                                                    <option value="{{ $kandang->id }}" 
                                                            data-unit-id="{{ $unit->id }}"
                                                            class="kandang-option {{ request('unit_id') && request('unit_id') != $unit->id ? 'hidden' : '' }}"
                                                            {{ request('kandang_id') == $kandang->id ? 'selected' : '' }}>
                                                        {{ $kandang->nama_kandang }}
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        @endif
                                    </select>
                                    <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Opsi Tambahan -->
                        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4">
                            <!-- Checkbox Sertakan Grafik -->
                            <label class="flex items-center gap-2 cursor-pointer group select-none">
                                <div class="relative flex items-center">
                                    <input type="checkbox" x-model="includeChart" class="peer sr-only">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded peer-checked:bg-cemara-600 peer-checked:border-cemara-600 transition"></div>
                                    <i class="ph-bold ph-check text-white text-xs absolute left-0.5 top-0.5 opacity-0 peer-checked:opacity-100 transition"></i>
                                </div>
                                <span class="text-sm text-gray-600 font-medium group-hover:text-cemara-700 transition">Sertakan Grafik (PDF)</span>
                            </label>

                            <button type="submit" class="px-8 py-3 bg-cemara-900 text-white font-bold rounded-xl hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 w-full md:w-auto justify-center">
                                <i class="ph-bold ph-magnifying-glass"></i> Cari Laporan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Result Section -->
                @if(isset($data) && $data->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">Hasil Pencarian ({{ $data->count() }} Data)</h4>
                            <p class="text-sm text-gray-500">Laporan Tipe: <strong>{{ ucfirst($tipe) }}</strong></p>
                        </div>
                        
                        <!-- Tombol Download dengan Logic Grafik -->
                        <!-- Menggunakan event handler Javascript untuk dynamic URL -->
                        <a href="#" @click.prevent="downloadPDF('{{ request()->fullUrlWithQuery(['print' => 'true']) }}')" 
                           class="px-6 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition shadow-md flex items-center gap-2">
                            <i class="ph-bold ph-file-pdf"></i> Download PDF
                        </a>
                    </div>
                    
                    <div class="p-6 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    @if($tipe == 'timbang')
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Unit</th>
                                        <th class="px-4 py-3">Kandang</th>
                                        <th class="px-4 py-3 text-center">Umur</th>
                                        <th class="px-4 py-3 text-right">Berat</th>
                                        <th class="px-4 py-3 text-center">Uniformity</th>
                                    @else
                                        <th class="px-4 py-3">Nama Unit</th>
                                        <th class="px-4 py-3">Kandang</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-right">Kapasitas</th>
                                        <th class="px-4 py-3 text-right">Populasi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($data as $index => $item)
                                    <tr>
                                        <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                        @if($tipe == 'timbang')
                                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_timbang)->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3">{{ $item->kandang->unit->nama_unit ?? '-' }}</td>
                                            <td class="px-4 py-3 font-bold">{{ $item->kandang->nama_kandang ?? '-' }}</td>
                                            <td class="px-4 py-3 text-center">{{ $item->umur_minggu }}</td>
                                            <td class="px-4 py-3 text-right">{{ number_format($item->berat_rata) }} gr</td>
                                            <td class="px-4 py-3 text-center">{{ $item->uniformity }}%</td>
                                        @else
                                            <td class="px-4 py-3">{{ $item->unit->nama_unit ?? '-' }}</td>
                                            <td class="px-4 py-3 font-bold">{{ $item->nama_kandang }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $item->status }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right">{{ number_format($item->kapasitas) }}</td>
                                            <td class="px-4 py-3 text-right font-bold">{{ number_format($item->stok_saat_ini) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Preview Status Grafik -->
                        <div x-show="includeChart" class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center gap-3 text-blue-700 transition" style="display: none;">
                            <i class="ph-fill ph-chart-line-up text-xl"></i>
                            <span class="text-sm font-semibold">Grafik visualisasi data akan disertakan dalam halaman PDF saat Anda mengklik tombol Download.</span>
                        </div>
                    </div>
                </div>
                @elseif(request()->has('tipe'))
                <div class="mt-8 text-center py-12 bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <i class="ph-fill ph-magnifying-glass text-3xl"></i>
                    </div>
                    <h3 class="text-gray-800 font-bold">Data Tidak Ditemukan</h3>
                    <p class="text-gray-500 text-sm">Coba sesuaikan filter tanggal atau unit.</p>
                </div>
                @endif

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        function downloadPDF(baseUrl) {
            // Ambil status checkbox dari scope AlpineJS (cara manual via DOM agar aman)
            // Atau lebih mudah: tambahkan param manual ke URL
            const checkbox = document.querySelector('input[type="checkbox"][x-model="includeChart"]');
            const isChecked = checkbox ? (checkbox.checked ? 1 : 0) : 0;
            
            // Redirect ke URL download
            window.open(baseUrl + '&include_chart=' + isChecked, '_blank');
        }

        // Filter Kandang Dinamis via JS
        function filterKandang() {
            const unitId = document.getElementById('unitSelector').value;
            const options = document.querySelectorAll('.kandang-option');
            const selector = document.getElementById('kandangSelector');

            selector.value = ""; // Reset pilihan

            options.forEach(option => {
                if (unitId === "" || option.getAttribute('data-unit-id') == unitId) {
                    option.classList.remove('hidden');
                } else {
                    option.classList.add('hidden');
                }
            });
        }

        // Set Tanggal Hari Ini untuk Header
        document.addEventListener('DOMContentLoaded', () => {
            const displayDate = document.getElementById('headerDateDisplay');
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
            if(displayDate) displayDate.innerText = today.toLocaleDateString('id-ID', options);
        });

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