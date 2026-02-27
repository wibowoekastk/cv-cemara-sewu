<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - CV Cemara Sewu</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ chartMode: 'weekly', showDateFilter: true, showMonthFilter: false }">

    <!-- PERBAIKAN: Menghapus wrapper ganda dan memastikan w-full -->
    <div class="flex h-screen overflow-hidden w-full relative">
        
        <!-- === OVERLAY MOBILE === -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- === SIDEBAR === -->
        @include('admin.sidebar')

        <!-- === MAIN CONTENT === -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- HEADER -->
            <header class="h-16 md:h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-8 sticky top-0 z-30 shadow-sm w-full">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Dashboard Overview</h2>
                        <p class="text-xs md:text-sm text-gray-500 hidden md:block">Selamat datang kembali, pantau peternakan secara realtime.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 md:gap-4">
                    <button class="relative p-2 text-gray-400 hover:text-cemara-900 transition bg-gray-50 hover:bg-gray-100 rounded-full">
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                        <i class="ph ph-bell text-xl"></i>
                    </button>
                    <a href="{{ route('admin.settings') }}" class="p-2 text-gray-400 hover:text-cemara-900 transition bg-gray-50 hover:bg-gray-100 rounded-full">
                        <i class="ph ph-gear text-xl"></i>
                    </a>
                </div>
            </header>
        
            <!-- Content Body -->
            <div class="p-4 md:p-8 space-y-8 w-full max-w-full">
                
                <!-- 1. KARTU STATISTIK -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                    <!-- Produksi -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-gold-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-gold-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Produksi Hari Ini</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($produksiHariIni ?? 0, 1) }} Kg</h3>
                            </div>
                            <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-egg"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-green-500 flex items-center font-semibold bg-green-50 px-2 py-0.5 rounded-full">
                                <i class="ph-bold ph-chart-line-up mr-1"></i> Realtime
                            </span>
                        </div>
                    </div>

                    <!-- Populasi -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-cemara-500 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-cemara-500"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Populasi Ayam</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalPopulasi ?? 0) }}</h3>
                            </div>
                            <div class="w-10 h-10 bg-cemara-100 text-cemara-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-bird"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 font-medium">Hidup & Produktif</span>
                        </div>
                    </div>

                    <!-- Unit Aktif -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-blue-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-blue-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Unit Aktif</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $unitAktif ?? 0 }} / {{ $totalUnit ?? 0 }}</h3>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-house-line"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 font-medium">Unit beroperasi</span>
                        </div>
                    </div>

                    <!-- Deplesi -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-red-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-red-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Deplesi Hari Ini</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalDeplesi ?? 0 }} Ekor</h3>
                            </div>
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-skull"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="{{ ($persenDeplesi ?? 0) > 0.05 ? 'text-red-500 bg-red-50' : 'text-green-600 bg-green-50' }} flex items-center font-semibold px-2 py-0.5 rounded-full">
                                <i class="ph-bold {{ ($persenDeplesi ?? 0) > 0.05 ? 'ph-warning' : 'ph-check-circle' }} mr-1"></i> 
                                {{ number_format($persenDeplesi ?? 0, 3) }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2. GRAFIK PERFORMA PRODUKSI (RESPONSIF) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 w-full">
                    <div class="flex flex-col xl:flex-row items-center justify-between mb-6 gap-4">
                        <div class="w-full xl:w-1/3">
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-chart-line-up text-cemara-600"></i>
                                Grafik Performa Mingguan
                            </h3>
                            <p class="text-sm text-gray-400" id="chartSubtitle">Pilih kandang untuk melihat detail</p>
                        </div>
                        
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-3 w-full xl:w-2/3 justify-end">
                            <div class="relative group">
                                <select id="filterLokasi" onchange="filterUnitByLokasi()" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-cemara-500 block">
                                    <option value="all">Semua Lokasi</option>
                                    @foreach($lokasis as $lokasi)
                                        <option value="{{ $lokasi }}">{{ $lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative group">
                                <select id="filterUnit" onchange="filterKandangList()" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-cemara-500 block">
                                    <option value="all" selected>Pilih Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" data-lokasi="{{ $unit->lokasi }}" data-kandangs='{{ json_encode($unit->kandangs) }}'>{{ $unit->nama_unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative group">
                                <select id="filterKandang" onchange="fetchChartData(this.value)" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-cemara-500 block">
                                    <option value="" disabled selected>Pilih Kandang</option>
                                    <!-- Opsi diisi via JS -->
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative h-80 w-full chart-container">
                        <canvas id="kandangChart"></canvas>
                    </div>
                    
                    <div class="mt-4 flex flex-wrap gap-4 justify-center text-xs text-gray-500 border-t border-gray-50 pt-4">
                        <span class="flex items-center gap-1 font-bold text-gray-700"><div class="w-8 h-1 bg-green-600 rounded-sm"></div> Produksi (HDP %)</span>
                        <span class="flex items-center gap-1 font-bold text-gray-500"><div class="w-8 h-1 bg-gray-400 rounded-sm border border-gray-400 border-dashed"></div> Daya Hidup (%)</span>
                        <span class="flex items-center gap-1"><div class="w-8 h-1 bg-blue-800 rounded-sm"></div> Berat Badan (g)</span>
                        <span class="flex items-center gap-1"><div class="w-8 h-1 bg-orange-500 rounded-sm"></div> Berat Telur (g)</span>
                        <span class="flex items-center gap-1 font-bold text-red-600"><div class="w-8 h-3 bg-red-500/50 rounded-sm border border-red-500"></div> Deplesi (%)</span>
                    </div>
                </div>

                <!-- 3. WIDGET BAWAH -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 w-full">
                    
                    <!-- Status Pakan -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 w-full">
                        <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                            <i class="ph-fill ph-grains text-cemara-600"></i> Status Pakan Terkini
                        </h3>
                        <div class="space-y-4">
                            @forelse($pakans as $pakan)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-cemara-600 shadow-sm border border-gray-100">
                                        <i class="ph-bold ph-package"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">{{ $pakan->nama_pakan }}</p>
                                        <p class="text-xs text-gray-500">Min: {{ number_format($pakan->min_stok) }} Kg</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold {{ $pakan->stok_pusat <= $pakan->min_stok ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ number_format($pakan->stok_pusat) }} Kg
                                    </p>
                                    @if($pakan->stok_pusat <= $pakan->min_stok)
                                        <span class="text-[10px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">Restock!</span>
                                    @else
                                        <span class="text-[10px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded">Aman</span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-gray-400 text-sm italic py-4">Belum ada data pakan.</p>
                            @endforelse
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.pakan.dashboard') }}" class="text-sm font-semibold text-cemara-700 hover:text-cemara-900 transition">Lihat Detail Pakan &rarr;</a>
                        </div>
                    </div>

                    <!-- Shortcut Cepat -->
                    <div class="bg-cemara-900 rounded-2xl p-6 text-white shadow-lg shadow-cemara-900/20 w-full">
                        <h3 class="font-bold text-lg mb-2">Aksi Cepat</h3>
                        <p class="text-cemara-200 text-sm mb-6">Pintas menu yang sering digunakan.</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('admin.analytic.input') }}" class="flex flex-col items-center justify-center p-4 bg-white/10 rounded-xl hover:bg-white/20 transition cursor-pointer border border-white/10 group">
                                <i class="ph-bold ph-pencil-simple text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-semibold">Input Harian</span>
                            </a>
                            <a href="{{ route('admin.pakan.input') }}" class="flex flex-col items-center justify-center p-4 bg-white/10 rounded-xl hover:bg-white/20 transition cursor-pointer border border-white/10 group">
                                <i class="ph-bold ph-truck text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-semibold">Stok Masuk</span>
                            </a>
                            <a href="{{ route('admin.analytic.dashboard') }}" class="flex flex-col items-center justify-center p-4 bg-white/10 rounded-xl hover:bg-white/20 transition cursor-pointer border border-white/10 group">
                                <i class="ph-bold ph-chart-line-up text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-semibold">Analisa Lengkap</span>
                            </a>
                            <a href="{{ route('admin.kandang.dashboard') }}" class="flex flex-col items-center justify-center p-4 bg-white/10 rounded-xl hover:bg-white/20 transition cursor-pointer border border-white/10 group">
                                <i class="ph-bold ph-house text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-semibold">Kandang</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
    
    <!-- Script Sidebar & Chart Logic -->
    <script>
        // === TOGGLE SIDEBAR ===
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

        // === CHART LOGIC ===
        const ctx = document.getElementById('kandangChart').getContext('2d');
        let myChart;
        // Default chart data kosong
        const emptyChartData = { labels: [], datasets: [] };

        // Init Chart Kosong
        document.addEventListener('DOMContentLoaded', () => {
            initChart(emptyChartData);
            
            // Trigger filter pertama kali untuk mengisi dropdown kandang jika unit terpilih
            filterKandangList();
        });

        function initChart(data) {
            const existingChart = Chart.getChart("kandangChart");
            if (existingChart) existingChart.destroy();

            const bwGram = (data.body_weight || []).map(val => Number(val) * 1000); 

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [
                         // Sumbu Kiri (Persen)
                        { label: 'HDP (%)', data: (data.produksi || []).map(Number), borderColor: '#16a34a', backgroundColor: 'rgba(22, 163, 74, 0.1)', borderWidth: 3, pointRadius: 4, tension: 0.4, yAxisID: 'y-axis-left', fill: true },
                        { label: 'Daya Hidup (%)', data: (data.daya_hidup || []).map(Number), borderColor: '#9ca3af', borderWidth: 2, borderDash: [5, 5], pointRadius: 0, tension: 0.4, yAxisID: 'y-axis-left', fill: false },
                        { type: 'bar', label: 'Deplesi (%)', data: (data.deplesi || []).map(Number), backgroundColor: 'rgba(239, 68, 68, 0.6)', borderColor: '#dc2626', borderWidth: 1, yAxisID: 'y-axis-right-perc', barPercentage: 0.5 },
                        
                        // Sumbu Kanan (Gram)
                        { label: 'Body Weight (g)', data: bwGram, borderColor: '#1e3a8a', backgroundColor: '#1e3a8a', borderWidth: 3, pointRadius: 4, pointStyle: 'rectRot', tension: 0.4, yAxisID: 'y-axis-right-gram', fill: false },
                        { label: 'Berat Telur (g)', data: (data.berat_telur || []).map(Number), borderColor: '#f97316', borderWidth: 2, pointRadius: 2, tension: 0.4, yAxisID: 'y-axis-right-gram', fill: false },
                        { label: 'Feed Intake (g)', data: (data.feed_intake || []).map(Number), borderColor: '#a855f7', borderWidth: 2, pointRadius: 2, tension: 0.4, yAxisID: 'y-axis-right-gram', fill: false, hidden: true },
                        
                        // FCR (Hidden Axis)
                        { label: 'FCR', data: (data.fcr || []).map(Number), borderColor: '#eab308', borderWidth: 2, borderDash: [2, 2], pointRadius: 2, tension: 0.4, yAxisID: 'y-axis-fcr', fill: false }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } }, title: { display: true, text: 'Umur (Minggu)', font: { weight: 'bold' } } },
                        'y-axis-left': { type: 'linear', display: true, position: 'left', min: 0, max: 100, title: { display: true, text: 'Persen (%)', color: '#16a34a' }, grid: { color: '#f3f4f6' } },
                        'y-axis-right-gram': { type: 'linear', display: true, position: 'right', min: 0, title: { display: true, text: 'Berat (Gram)', color: '#1e3a8a' }, grid: { display: false } },
                        'y-axis-right-perc': { type: 'linear', display: true, position: 'right', min: 0, max: 5, grid: { display: false }, ticks: { display: false } },
                        'y-axis-fcr': { type: 'linear', display: true, position: 'right', min: 0, max: 5, grid: { display: false }, ticks: { display: false } }
                    }
                }
            });
        }

        // Logic Filter
        function filterUnitByLokasi() {
            const lokasiSelect = document.getElementById('filterLokasi');
            const unitSelect = document.getElementById('filterUnit');
            const kandangSelect = document.getElementById('filterKandang');
            const selectedLokasi = lokasiSelect.value;
            const unitOptions = unitSelect.querySelectorAll('option');

            unitSelect.value = "all";
            kandangSelect.innerHTML = '<option value="" disabled selected>Pilih Kandang</option>';
            
            unitOptions.forEach(option => {
                if (option.value === "all") return;
                const unitLokasi = option.getAttribute('data-lokasi');
                option.style.display = (selectedLokasi === 'all' || unitLokasi === selectedLokasi) ? '' : 'none';
            });
        }

        function filterKandangList() {
            const unitSelect = document.getElementById('filterUnit');
            const kandangSelect = document.getElementById('filterKandang');
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];

            if (unitSelect.value === 'all' || !selectedOption) {
                kandangSelect.innerHTML = '<option value="" disabled selected>Pilih Kandang</option>';
                return;
            }

            const kandangs = selectedOption.getAttribute('data-kandangs') ? JSON.parse(selectedOption.getAttribute('data-kandangs')) : [];

            kandangSelect.innerHTML = '<option value="" disabled>Pilih Kandang</option>';
            kandangs.forEach(k => {
                const option = document.createElement('option');
                option.value = k.id;
                option.text = k.nama_kandang;
                kandangSelect.appendChild(option);
            });

            if(kandangs.length > 0) {
                kandangSelect.value = kandangs[0].id;
                fetchChartData(kandangs[0].id);
            }
        }

        function fetchChartData(kandangId) {
            const subtitle = document.getElementById('chartSubtitle');
            subtitle.innerText = 'Memuat data...';

            fetch(`{{ route('admin.analytic.chart_data') }}?kandang_id=${kandangId}&mode=weekly`)
                .then(response => response.json())
                .then(data => {
                    const kandangText = document.getElementById('filterKandang').selectedOptions[0].text;
                    if (data && data.labels && data.labels.length > 0) {
                        initChart(data); 
                        subtitle.innerText = `Menampilkan performa Mingguan untuk ${kandangText}`;
                    } else {
                        const existingChart = Chart.getChart("kandangChart");
                        if (existingChart) existingChart.destroy();
                        initChart(emptyChartData); 
                        subtitle.innerText = `Belum ada data untuk ${kandangText}.`;
                    }
                })
                .catch(error => { console.error('Error:', error); subtitle.innerText = 'Gagal memuat data.'; });
        }
    </script>
</body>
</html>