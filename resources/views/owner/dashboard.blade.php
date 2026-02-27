<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - CV Cemara Sewu</title>
    
    <!-- Tailwind & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ chartMode: 'weekly', showDateFilter: true, showMonthFilter: false }">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Sidebar -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>
        @include('owner.sidebar')

        <!-- MAIN CONTENT -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- HEADER (Topbar) -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-8 sticky top-0 z-20 shadow-sm">
                <div class="flex items-center gap-3">
                    <!-- Tombol Hamburger (Wajib Ada untuk Mobile) -->
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg transition hover:bg-gray-100">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 font-poppins">Executive Dashboard</h2>
                        <p class="text-xs md:text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <a href="{{ route('owner.settingsowner') }}" class="p-2 text-gray-400 hover:text-cemara-900 transition bg-gray-50 hover:bg-gray-100 rounded-full">
                        <i class="ph ph-gear text-xl"></i>
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full max-w-full">
                
                <!-- Section 1: Key Metrics (Data Realtime) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    
                    <!-- Card 1: Produksi -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-40 relative overflow-hidden group hover:shadow-md transition">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-cemara-900"><i class="ph-fill ph-egg text-6xl"></i></div>
                        <div>
                            <p class="text-gray-500 font-bold uppercase tracking-wider text-xs mb-2">Produksi Hari Ini</p>
                            <h2 class="text-3xl font-sans font-bold text-gray-900">{{ number_format($produksiHariIni, 1) }} Kg</h2>
                        </div>
                        <div class="flex items-center gap-2 mt-auto">
                            <span class="text-xs text-green-600 font-medium">Data Terbaru</span>
                        </div>
                    </div>

                    <!-- Card 2: Populasi -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-40 relative overflow-hidden group hover:shadow-md transition">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-gold-600"><i class="ph-fill ph-bird text-6xl"></i></div>
                        <div>
                            <p class="text-gray-500 font-bold uppercase tracking-wider text-xs mb-2">Populasi Ayam</p>
                            <h2 class="text-3xl font-sans font-bold text-gray-900">{{ number_format($totalPopulasi) }}</h2>
                        </div>
                        <div class="flex items-center gap-2 mt-auto">
                            <span class="text-xs text-gray-500 font-medium">Ekor (Aktif)</span>
                        </div>
                    </div>

                    <!-- Card 3: FCR (Sudah Integrasi) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-40 relative overflow-hidden group hover:shadow-md transition">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-blue-600"><i class="ph-fill ph-chart-line-up text-6xl"></i></div>
                        <div>
                            <p class="text-gray-500 font-bold uppercase tracking-wider text-xs mb-2">Rata-rata FCR</p>
                            <h2 class="text-3xl font-sans font-bold text-gray-900">{{ number_format($avgFcr, 2) }}</h2>
                        </div>
                        <div class="flex items-center gap-2 mt-auto">
                            <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">Efisiensi Pakan</span>
                        </div>
                    </div>

                    <!-- Card 4: Stok Pakan (Sudah Integrasi) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-40 relative overflow-hidden group hover:shadow-md transition">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-red-600"><i class="ph-fill ph-grains text-6xl"></i></div>
                        <div>
                            <p class="text-gray-500 font-bold uppercase tracking-wider text-xs mb-2">Total Stok Pakan</p>
                            <h2 class="text-3xl font-sans font-bold text-gray-900">{{ number_format($totalStokPakan) }} Kg</h2>
                        </div>
                        <div class="flex items-center gap-2 mt-auto">
                            <span class="text-xs text-gray-500 font-medium">Sisa di Unit</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Chart Analysis (SAMA DENGAN ADMIN) -->
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 mb-10 w-full chart-container-wrapper">
                    <div class="flex flex-col xl:flex-row items-center justify-between mb-6 gap-4">
                        <div class="w-full xl:w-1/3">
                            <h3 class="font-bold text-xl text-gray-800 font-poppins flex items-center gap-2">
                                <i class="ph-fill ph-chart-line-up text-cemara-600"></i>
                                Analisis Performa
                            </h3>
                            <p class="text-sm text-gray-500 mt-1" id="chartSubtitle">Menampilkan data global seluruh peternakan</p>
                        </div>
                        
                        <!-- Filter Controls -->
                        <div class="flex flex-wrap items-center gap-3 w-full xl:w-2/3 justify-end">
                            
                            <!-- Toggle Mode -->
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button @click="chartMode = 'daily'; showDateFilter = false; showMonthFilter = false; changeMode('daily')" :class="chartMode === 'daily' ? 'bg-white shadow text-cemara-700' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-bold rounded-md transition">Harian</button>
                                <button @click="chartMode = 'weekly'; showDateFilter = true; showMonthFilter = false; changeMode('weekly')" :class="chartMode === 'weekly' ? 'bg-white shadow text-cemara-700' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-bold rounded-md transition">Mingguan</button>
                                <button @click="chartMode = 'monthly'; showDateFilter = false; showMonthFilter = true; changeMode('monthly')" :class="chartMode === 'monthly' ? 'bg-white shadow text-cemara-700' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-bold rounded-md transition">Bulanan</button>
                            </div>

                            <div class="h-6 w-px bg-gray-300 mx-1 hidden md:block"></div>

                            <!-- Filter Tanggal -->
                            <div class="flex gap-2" x-show="showDateFilter" x-transition>
                                <input type="date" id="startDate" class="px-2 py-1.5 border border-gray-200 rounded-lg text-xs w-28" onchange="applyDateFilter()">
                                <span class="text-gray-400 self-center">-</span>
                                <input type="date" id="endDate" class="px-2 py-1.5 border border-gray-200 rounded-lg text-xs w-28" onchange="applyDateFilter()">
                            </div>

                             <!-- Dropdown Filters (Data from DB) -->
                            <div class="relative group">
                                <select id="filterLokasi" onchange="filterUnitByLokasi()" class="pl-2 pr-6 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-lg block">
                                    <option value="all">Semua Lokasi</option>
                                    @foreach($lokasis as $lokasi) <option value="{{ $lokasi }}">{{ $lokasi }}</option> @endforeach
                                </select>
                            </div>
                            <div class="relative group">
                                <select id="filterUnit" onchange="filterKandangList()" class="pl-2 pr-6 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-lg block">
                                    <option value="all" selected>Pilih Unit</option>
                                    @foreach($units as $unit) <option value="{{ $unit->id }}" data-lokasi="{{ $unit->lokasi }}" data-kandangs='{{ json_encode($unit->kandangs) }}'>{{ $unit->nama_unit }}</option> @endforeach
                                </select>
                            </div>
                            <div class="relative group">
                                <select id="filterKandang" onchange="fetchChartData(this.value)" class="pl-2 pr-6 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-lg block">
                                    <option value="" disabled>Pilih Kandang</option>
                                    @if(isset($firstKandang)) <option value="{{ $firstKandang->id }}">{{ $firstKandang->nama_kandang }}</option> @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Canvas Chart -->
                    <div class="relative h-96 w-full chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                    
                    <!-- Legend Manual -->
                    <div class="mt-6 flex flex-wrap gap-4 justify-center text-xs text-gray-500 border-t border-gray-100 pt-4">
                        <span class="flex items-center gap-1 font-bold text-gray-700"><div class="w-8 h-1 bg-green-600 rounded-sm"></div> Produksi (HDP %)</span>
                        <span class="flex items-center gap-1 font-bold text-gray-500"><div class="w-8 h-1 bg-gray-400 rounded-sm border border-gray-400 border-dashed"></div> Daya Hidup (%)</span>
                        <span class="flex items-center gap-1"><div class="w-8 h-1 bg-blue-800 rounded-sm"></div> Berat Badan (g)</span>
                        <span class="flex items-center gap-1"><div class="w-8 h-1 bg-orange-500 rounded-sm"></div> Berat Telur (g)</span>
                        <span class="flex items-center gap-1 font-bold text-red-600"><div class="w-8 h-3 bg-red-500/50 rounded-sm border border-red-500"></div> Deplesi (%)</span>
                    </div>
                </div>

            </div>
        </main>
    </div>
    
    <!-- Script Sidebar & Chart Logic -->
    <script>
        // === TOGGLE SIDEBAR (FIXED) ===
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

        // === CHART LOGIC (SAMA DENGAN ADMIN) ===
        const ctx = document.getElementById('performanceChart').getContext('2d');
        let myChart;
        let currentMode = 'weekly';
        const emptyChartData = { labels: [], datasets: [] };

        document.addEventListener('DOMContentLoaded', () => {
             // Init filter
             filterKandangList();
             
             // Set date default
             const today = new Date().toISOString().split('T')[0];
             document.getElementById('endDate').value = today;
             const d = new Date(); d.setDate(d.getDate() - 21);
             document.getElementById('startDate').value = d.toISOString().split('T')[0];
             
             // Init chart with empty or first data
             initChart(emptyChartData);
             
             // Load first kandang if available
             @if(isset($firstKandang))
                document.getElementById('filterKandang').value = "{{ $firstKandang->id }}";
                fetchChartData("{{ $firstKandang->id }}");
             @endif
        });

        // Reuse Logic Chart yang sudah terbukti jalan di Admin
        function initChart(data, mode = 'weekly') {
            const existingChart = Chart.getChart("performanceChart");
            if (existingChart) existingChart.destroy();
            const bwGram = (data.body_weight || []).map(val => Number(val) * 1000); 

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [
                        { label: 'HDP (%)', data: (data.produksi || []).map(Number), borderColor: '#16a34a', backgroundColor: 'rgba(22, 163, 74, 0.1)', borderWidth: 3, pointRadius: 4, tension: 0.4, yAxisID: 'y-axis-left', fill: true },
                        { label: 'Daya Hidup (%)', data: (data.daya_hidup || []).map(Number), borderColor: '#9ca3af', borderWidth: 2, borderDash: [5, 5], pointRadius: 0, tension: 0.4, yAxisID: 'y-axis-left', fill: false },
                        { type: 'bar', label: 'Deplesi (%)', data: (data.deplesi || []).map(Number), backgroundColor: 'rgba(239, 68, 68, 0.6)', borderColor: '#dc2626', borderWidth: 1, yAxisID: 'y-axis-right-perc', barPercentage: 0.5 },
                        { label: 'Body Weight (g)', data: bwGram, borderColor: '#1e3a8a', borderWidth: 3, pointRadius: 4, pointStyle: 'rectRot', tension: 0.4, yAxisID: 'y-axis-right-gram', fill: false },
                        { label: 'Berat Telur (g)', data: (data.berat_telur || []).map(Number), borderColor: '#f97316', borderWidth: 2, pointRadius: 2, tension: 0.4, yAxisID: 'y-axis-right-gram', fill: false },
                        { label: 'FCR', data: (data.fcr || []).map(Number), borderColor: '#eab308', borderWidth: 2, borderDash: [2, 2], pointRadius: 2, tension: 0.4, yAxisID: 'y-axis-fcr', fill: false }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        'y-axis-left': { type: 'linear', display: true, position: 'left', min: 0, max: 100, grid: { color: '#f3f4f6' } },
                        'y-axis-right-gram': { type: 'linear', display: true, position: 'right', min: 0, grid: { display: false } },
                        'y-axis-right-perc': { type: 'linear', display: true, position: 'right', min: 0, max: 5, grid: { display: false }, ticks: { display: false } },
                        'y-axis-fcr': { type: 'linear', display: true, position: 'right', min: 0, max: 5, grid: { display: false }, ticks: { display: false } }
                    }
                }
            });
        }

        // Logic Filter & Fetch (Copy Paste dari Admin Dashboard)
        function changeMode(mode) {
            currentMode = mode;
            document.querySelector('[x-data]').__x.$data.showDateFilter = (mode === 'weekly');
            document.querySelector('[x-data]').__x.$data.showMonthFilter = (mode === 'monthly');
            const today = new Date().toISOString().split('T')[0];
            if(mode === 'weekly') {
                document.getElementById('endDate').value = today;
                const d = new Date(); d.setDate(d.getDate() - 21);
                document.getElementById('startDate').value = d.toISOString().split('T')[0];
            }
            const kandangId = document.getElementById('filterKandang').value;
            if(kandangId) fetchChartData(kandangId);
        }
        function applyDateFilter() {
            const kandangId = document.getElementById('filterKandang').value;
            if(kandangId) fetchChartData(kandangId);
        }
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
            let url = `{{ route('admin.analytic.chart_data') }}?kandang_id=${kandangId}&mode=${currentMode}`;
            if(currentMode === 'weekly') {
                const start = document.getElementById('startDate').value;
                const end = document.getElementById('endDate').value;
                if(start && end) url += `&start_date=${start}&end_date=${end}`;
            } else if(currentMode === 'monthly') {
                const month = document.getElementById('filterMonth').value;
                if(month) url += `&month=${month}`;
            }
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const kandangText = document.getElementById('filterKandang').selectedOptions[0].text;
                    if (data && data.labels && data.labels.length > 0) {
                        initChart(data, currentMode); 
                        subtitle.innerText = `Menampilkan performa untuk ${kandangText}`;
                    } else {
                        initChart(emptyChartData, currentMode); 
                        subtitle.innerText = `Belum ada data untuk ${kandangText}.`;
                    }
                })
                .catch(error => { console.error('Error:', error); subtitle.innerText = 'Gagal memuat data.'; });
        }
    </script>
</body>
</html>