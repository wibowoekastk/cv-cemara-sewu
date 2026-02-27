<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytic Dashboard - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <style>
        /* CSS Khusus Cetak (Print) */
        @media print {
            @page { 
                size: landscape; /* Orientasi Mendatar */
                margin: 5mm; /* Margin tipis agar muat banyak */
            }
            body { 
                background: white !important; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
                width: 100%;
            }
            
            /* Sembunyikan elemen tidak penting */
            .no-print, header button, #sidebar, .bg-black\/50, .grid-cols-1, header { display: none !important; }
            
            /* Layout Utama */
            main { margin: 0 !important; width: 100% !important; padding: 0 !important; overflow: visible !important; }
            .p-4, .md\:p-8 { padding: 5px !important; }
            .chart-container-wrapper { 
                box-shadow: none !important; 
                border: none !important; 
                margin: 0 !important; 
                padding: 0 !important; 
            }

            /* Header Laporan Khusus Print */
            .print-header { 
                display: block !important; 
                text-align: center; 
                margin-bottom: 10px; 
                border-bottom: 2px solid #000; 
                padding-bottom: 5px; 
            }
            .print-header h1 { font-size: 18pt; font-weight: bold; color: #000; }
            .print-header p { font-size: 9pt; color: #555; }

            /* ATUR TINGGI GRAFIK AGAR TIDAK TERPOTONG */
            .chart-container { 
                height: 400px !important; /* Dikurangi agar muat satu halaman dengan keterangan */
                width: 100% !important; 
            }

            /* Legenda & Detail */
            .legend-detail { margin-top: 5px !important; padding-top: 5px !important; border-top: 1px solid #ddd; }
            .legend-items { gap: 10px !important; margin-bottom: 10px !important; font-size: 9pt !important; }

            /* Box Keterangan */
            .detail-box { 
                padding: 8px !important; 
                border: 1px solid #ccc !important;
                background-color: #f8f8f8 !important;
                page-break-inside: avoid; /* Mencegah kotak terpotong ke halaman berikutnya */
            }
            .detail-box h4 { font-size: 10pt !important; margin-bottom: 5px !important; }
            .detail-box .grid { gap: 8px !important; }
            .detail-box p, .detail-box strong { font-size: 8pt !important; line-height: 1.2 !important; }
            .detail-box .w-3 { width: 8px; height: 8px; }
        }

        .print-header { display: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ chartMode: 'weekly', showDateFilter: true, showMonthFilter: false }">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Sidebar -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0 no-print"></div>
        <div class="no-print">
            @include('admin.sidebar')
        </div>

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm no-print">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Manajemen Data</span><i class="ph-bold ph-caret-right"></i><span>Analytic</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Analytic Dashboard</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg"><i class="ph-bold ph-list text-2xl"></i></button>
                    <!-- Tombol Cetak -->
                    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20">
                        <i class="ph-bold ph-printer"></i> Cetak Laporan
                    </button>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8">

                <!-- Header Khusus Print (Muncul hanya di kertas) -->
                <div class="print-header">
                    <h1 class="text-3xl font-bold text-cemara-900 mb-1">LAPORAN PERFORMA PRODUKSI</h1>
                    <p class="text-gray-600 text-sm">Dicetak pada: <script>document.write(new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }));</script></p>
                </div>
                
                <!-- 1. Stats Cards (Hidden on Print via CSS) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 no-print">
                    <!-- ... Card 1: Laporan Terinput ... -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-cemara-500 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-cemara-500"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Laporan Terinput</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $inputToday ?? 0 }}/{{ $totalKandang ?? 0 }}</h3>
                            </div>
                            <div class="w-10 h-10 bg-cemara-100 text-cemara-600 rounded-full flex items-center justify-center text-xl"><i class="ph-fill ph-file-plus"></i></div>
                        </div>
                        <div class="flex items-center text-sm"><span class="text-gray-500 font-medium">Hari ini ({{ number_format($inputPercentage ?? 0, 0) }}%)</span></div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3"><div class="bg-cemara-500 h-1.5 rounded-full" style="width: {{ $inputPercentage ?? 0 }}%"></div></div>
                    </div>

                    <!-- ... Card 2: Penurunan Produksi ... -->
                     <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-red-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-red-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div><p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Penurunan Produksi</p><h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $productionDropCount ?? 0 }} Unit</h3></div>
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl"><i class="ph-fill ph-trend-down"></i></div>
                        </div>
                         <div class="flex items-center text-sm gap-2">
                             <span class="{{ ($productionDropCount ?? 0) > 0 ? 'text-red-500' : 'text-green-500' }} font-medium flex items-center gap-1">
                                <i class="ph-bold {{ ($productionDropCount ?? 0) > 0 ? 'ph-warning-circle' : 'ph-check-circle' }}"></i> {{ ($productionDropCount ?? 0) > 0 ? 'Perlu dicek' : 'Stabil' }}
                             </span>
                         </div>
                    </div>

                    <!-- Card 3: FCR Rata-rata -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-gold-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-gold-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div><p class="text-sm text-gray-500 font-medium uppercase tracking-wide">FCR Rata-rata</p><h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($avgFcr ?? 0, 2) }}</h3></div>
                            <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-full flex items-center justify-center text-xl"><i class="ph-fill ph-chart-bar"></i></div>
                        </div>
                        <div class="flex items-center text-sm gap-2"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">Optimal</span></div>
                    </div>

                    <!-- Card 4: Mortality Rate -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-blue-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-blue-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div><p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Mortality Rate</p><h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($mortalityRate ?? 0, 2) }}%</h3></div>
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl"><i class="ph-fill ph-heartbeat"></i></div>
                        </div>
                        <div class="flex items-center text-sm gap-2"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">Aman</span></div>
                    </div>
                </div>

                <!-- 2. Grafik Detail Kandang (Dynamic & Flexible) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8 chart-container-wrapper">
                    <div class="flex flex-col xl:flex-row items-center justify-between mb-6 gap-4">
                        <div class="w-full xl:w-1/3">
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-chart-line-up text-cemara-600"></i>
                                Performa Produksi
                            </h3>
                            <p class="text-sm text-gray-400" id="chartSubtitle">
                                {{ isset($firstKandang) ? 'Menampilkan data untuk ' . $firstKandang->nama_kandang : 'Belum ada data kandang' }}
                            </p>
                        </div>
                        
                        <!-- Filters & Controls (Disembunyikan saat print) -->
                        <div class="flex flex-wrap items-center gap-3 no-print w-full xl:w-2/3 justify-end">
                            
                            <!-- Tombol Mode Grafik -->
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button @click="chartMode = 'daily'; showDateFilter = false; showMonthFilter = false; changeMode('daily')" 
                                        :class="chartMode === 'daily' ? 'bg-white shadow text-cemara-700' : 'text-gray-500 hover:text-gray-700'" 
                                        class="px-3 py-1.5 text-xs font-bold rounded-md transition">Harian</button>
                                
                                <button @click="chartMode = 'weekly'; showDateFilter = true; showMonthFilter = false; changeMode('weekly')" 
                                        :class="chartMode === 'weekly' ? 'bg-white shadow text-cemara-700' : 'text-gray-500 hover:text-gray-700'" 
                                        class="px-3 py-1.5 text-xs font-bold rounded-md transition">Mingguan</button>
                                
                                <button @click="chartMode = 'monthly'; showDateFilter = false; showMonthFilter = true; changeMode('monthly')" 
                                        :class="chartMode === 'monthly' ? 'bg-white shadow text-cemara-700' : 'text-gray-500 hover:text-gray-700'" 
                                        class="px-3 py-1.5 text-xs font-bold rounded-md transition">Bulanan</button>
                            </div>

                            <div class="h-6 w-px bg-gray-300 mx-1"></div>

                            <!-- Filter Tanggal (Khusus Mingguan) -->
                            <div class="flex gap-2" x-show="showDateFilter" x-transition>
                                <input type="date" id="startDate" class="px-2 py-1.5 border border-gray-200 rounded-lg text-xs w-28" title="Mulai Tanggal" onchange="applyDateFilter()">
                                <span class="text-gray-400 self-center">-</span>
                                <input type="date" id="endDate" class="px-2 py-1.5 border border-gray-200 rounded-lg text-xs w-28" title="Sampai Tanggal" onchange="applyDateFilter()">
                            </div>

                            <!-- Filter Bulan (Khusus Bulanan) -->
                            <div class="flex gap-2" x-show="showMonthFilter" x-transition style="display: none;">
                                <input type="month" id="filterMonth" class="px-2 py-1.5 border border-gray-200 rounded-lg text-xs" onchange="applyDateFilter()">
                            </div>

                            <div class="h-6 w-px bg-gray-300 mx-1"></div>

                            <!-- Dropdown Filters (Unit & Kandang) -->
                            <div class="relative group">
                                <select id="filterLokasi" onchange="filterUnitByLokasi()" class="pl-2 pr-6 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-lg focus:ring-cemara-500 block">
                                    <option value="all">Semua Lokasi</option>
                                    @foreach($lokasis as $lokasi)
                                        <option value="{{ $lokasi }}">{{ $lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative group">
                                <select id="filterUnit" onchange="filterKandangList()" class="pl-2 pr-6 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-lg focus:ring-cemara-500 block">
                                    <option value="all" selected>Pilih Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" data-lokasi="{{ $unit->lokasi }}" data-kandangs='{{ json_encode($unit->kandangs, JSON_HEX_APOS) }}'>{{ $unit->nama_unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative group">
                                <select id="filterKandang" onchange="fetchChartData(this.value)" class="pl-2 pr-6 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-lg focus:ring-cemara-500 block">
                                    <option value="" disabled>Pilih Kandang</option>
                                    @if(isset($firstKandang))
                                        <option value="{{ $firstKandang->id }}" selected>{{ $firstKandang->nama_kandang }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- AREA GRAFIK -->
                    <div class="relative h-96 w-full chart-container">
                        <canvas id="kandangChart"></canvas>
                    </div>
                    
                    <!-- LEGEND MANUAL & PENJELASAN (DIPERBAIKI) -->
                    <div class="mt-6 pt-4 border-t border-gray-100 legend-detail">
                        
                        <!-- Legenda Warna -->
                        <div class="flex flex-wrap gap-4 justify-center text-xs text-gray-600 mb-6 legend-items">
                            <span class="flex items-center gap-1 font-bold text-gray-800"><div class="w-8 h-1 bg-green-600 rounded-sm"></div> Produksi (HDP %)</span>
                            <span class="flex items-center gap-1"><div class="w-8 h-1 bg-blue-800 rounded-sm"></div> Body Weight (g)</span>
                            <span class="flex items-center gap-1"><div class="w-8 h-1 bg-orange-500 rounded-sm"></div> Berat Telur (g)</span>
                            <span class="flex items-center gap-1"><div class="w-8 h-1 bg-purple-500 rounded-sm"></div> Feed Intake (g)</span>
                            <span class="flex items-center gap-1 font-bold text-red-600"><div class="w-8 h-3 bg-red-500/50 rounded-sm border border-red-500"></div> Deplesi (%)</span>
                        </div>

                        <!-- KETERANGAN DETAIL (Permintaan: Penjelasan garis) -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 detail-box">
                            <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ph-bold ph-info"></i> Detail Indikator Grafis
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600">
                                <div class="flex gap-3 items-start">
                                    <div class="w-3 h-3 bg-green-600 rounded-full mt-0.5 shrink-0"></div>
                                    <div>
                                        <strong class="text-gray-800">Produksi (HDP %)</strong>
                                        <p>Kurva hijau menunjukkan persentase produksi telur harian (Hen Day Production). Semakin tinggi kurva, semakin produktif ayam bertelur.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-start">
                                    <div class="w-3 h-3 bg-blue-800 rounded-full mt-0.5 shrink-0"></div>
                                    <div>
                                        <strong class="text-gray-800">Body Weight (Gram)</strong>
                                        <p>Garis biru tua menunjukkan rata-rata berat badan ayam. Berat yang ideal mempengaruhi kestabilan produksi telur.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-start">
                                    <div class="w-3 h-3 bg-orange-500 rounded-full mt-0.5 shrink-0"></div>
                                    <div>
                                        <strong class="text-gray-800">Berat Telur (Gram)</strong>
                                        <p>Garis oranye menunjukkan rata-rata berat per butir telur yang dihasilkan.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-start">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mt-0.5 shrink-0"></div>
                                    <div>
                                        <strong class="text-gray-800">Feed Intake (Gram)</strong>
                                        <p>Garis ungu menunjukkan jumlah konsumsi pakan per ekor per hari. Indikator efisiensi pakan.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-start md:col-span-2">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mt-0.5 shrink-0"></div>
                                    <div>
                                        <strong class="text-red-600">Deplesi (%)</strong>
                                        <p>Batang merah (di bagian bawah) menunjukkan persentase pengurangan populasi ayam (kematian/afkir). Batang tinggi menandakan adanya masalah kesehatan di kandang.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Init Data PHP -->
    @php
        $safeInitialData = $initialChartData ?? [
            'labels' => [], 'deplesi' => [], 'produksi' => [], 
            'feed_intake' => [], 'total_pakan' => [], 'berat_telur' => [], 'fcr' => [], 'body_weight' => []
        ];
    @endphp

    <!-- Script Chart -->
    <script>
        const ctx = document.getElementById('kandangChart').getContext('2d');
        let myChart;
        let currentMode = 'weekly';

        // Data Asli
        const initialData = @json($safeInitialData);
        // Data Dummy (Untuk Preview Layout - Kurva Naik)
        const dummyData = {
            labels: Array.from({length: 12}, (_, i) => '' + (18 + i)), // Hapus teks "Minggu"
            deplesi: [0.1, 0.2, 0.1, 0.1, 0.1, 0.2, 0.1, 0.1, 0.1, 0.1, 0.2, 0.1],
            produksi: [10, 35, 60, 80, 88, 92, 94, 95, 95, 94, 93, 92], // Kurva S
            feed_intake: [80, 90, 100, 110, 115, 115, 115, 115, 115, 115, 115, 115],
            berat_telur: [0, 45, 48, 52, 58, 60, 61, 62, 62.5, 63, 63, 63.5],
            fcr: [0, 0, 1.5, 1.8, 2.0, 2.1, 2.15, 2.2, 2.2, 2.25, 2.3, 2.3],
            body_weight: [1.4, 1.5, 1.6, 1.7, 1.75, 1.8, 1.85, 1.9, 1.95, 1.98, 2.0, 2.02]
        };
        const emptyChartData = { labels: [], datasets: [] };

        const chartData = (initialData.labels.length > 0) ? initialData : dummyData;

        function initChart(data, mode = 'weekly') {
            const existingChart = Chart.getChart("kandangChart");
            if (existingChart) existingChart.destroy();

            // 1. Bersihkan Label Sumbu X (Hapus kata "Minggu")
            let cleanLabels = (data.labels || []).map(label => label.replace('Minggu ', ''));

            // Konversi Kg ke Gram untuk Berat Badan agar satu sumbu dengan Intake & Telur
            const bwGram = (data.body_weight || []).map(val => val * 1000); 

            // Transformasi Data Harian (Trik Garis Lurus)
            let datasetProduksi = (data.produksi || []).map(Number);
            let datasetDeplesi = (data.deplesi || []).map(Number);
            let datasetFeed = (data.feed_intake || []).map(Number);
            let datasetTelur = (data.berat_telur || []).map(Number);
            let datasetFCR = (data.fcr || []).map(Number);
            let datasetBB = bwGram.map(Number);

            if (mode === 'daily' && cleanLabels.length === 1) {
                const singleLabel = cleanLabels[0];
                cleanLabels = ['Awal', 'Akhir']; // Trik agar garis lurus
                // Duplikat nilai agar membentuk garis
                datasetProduksi = [datasetProduksi[0], datasetProduksi[0]];
                datasetDeplesi = [datasetDeplesi[0], datasetDeplesi[0]];
                datasetFeed = [datasetFeed[0], datasetFeed[0]];
                datasetTelur = [datasetTelur[0], datasetTelur[0]];
                datasetFCR = [datasetFCR[0], datasetFCR[0]];
                datasetBB = [datasetBB[0], datasetBB[0]];
            }

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: cleanLabels, // Label Angka (16, 17, 18...)
                    datasets: [
                        // --- SUMBU KIRI (PERSENTASE: HDP) ---
                        {
                            label: 'HDP (%)',
                            data: datasetProduksi,
                            borderColor: '#16a34a', // Hijau
                            backgroundColor: 'rgba(22, 163, 74, 0.1)', // Fill hijau transparan
                            borderWidth: 3,
                            pointRadius: mode === 'daily' ? 0 : 4,
                            pointHoverRadius: 6,
                            tension: 0.4, // Kurva Halus
                            yAxisID: 'y-axis-left',
                            fill: true // Area di bawah kurva diarsir
                        },
                        
                        // --- SUMBU KANAN 1 (BERAT: GRAM) ---
                        {
                            label: 'Body Weight (g)',
                            data: datasetBB, // Data dalam gram
                            borderColor: '#1e3a8a', // Biru Tua
                            backgroundColor: '#1e3a8a',
                            borderWidth: 3,
                            pointRadius: mode === 'daily' ? 0 : 4,
                            pointStyle: 'rectRot',
                            tension: 0.4,
                            yAxisID: 'y-axis-right-gram',
                            fill: false
                        },
                        {
                            label: 'Berat Telur (g)',
                            data: datasetTelur,
                            borderColor: '#f97316', // Oranye
                            backgroundColor: '#f97316',
                            borderWidth: 2,
                            pointRadius: mode === 'daily' ? 0 : 2,
                            tension: 0.4,
                            yAxisID: 'y-axis-right-gram',
                            fill: false
                        },
                        {
                            label: 'Feed Intake (g)',
                            data: datasetFeed,
                            borderColor: '#a855f7', // Ungu
                            backgroundColor: '#a855f7',
                            borderWidth: 2,
                            pointRadius: mode === 'daily' ? 0 : 2,
                            tension: 0.4,
                            yAxisID: 'y-axis-right-gram',
                            fill: false
                        },

                        // --- SUMBU KANAN 2 (PERSEN KECIL: DEPLESI) ---
                        {
                            type: 'bar', // Bar chart untuk Deplesi
                            label: 'Deplesi (%)',
                            data: datasetDeplesi,
                            backgroundColor: 'rgba(239, 68, 68, 0.5)', // Merah transparan
                            borderColor: '#dc2626',
                            borderWidth: 1,
                            yAxisID: 'y-axis-right-perc', // Sumbu khusus persen kecil
                            barPercentage: 0.5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { 
                        legend: { display: false }, // Legend manual di HTML
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    // Tambahkan "Minggu" jika labelnya angka
                                    return 'Minggu ' + tooltipItems[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            grid: { display: false }, 
                            ticks: { font: { size: 11 } },
                            title: { display: true, text: 'Umur (Minggu)', font: { weight: 'bold' } }
                        },
                        // SUMBU KIRI: PRODUKSI (0-100%)
                        'y-axis-left': { 
                            type: 'linear', display: true, position: 'left', min: 0, max: 100, 
                            title: { display: true, text: 'Produksi (HDP %)', color: '#16a34a', font: { weight: 'bold' } },
                            grid: { color: '#f3f4f6' }
                        },
                        // SUMBU KANAN 1: BERAT (0-Auto)
                        'y-axis-right-gram': { 
                            type: 'linear', display: true, position: 'right', min: 0, 
                            title: { display: true, text: 'Berat (Gram)', color: '#1e3a8a', font: { weight: 'bold' } },
                            grid: { display: false }
                        },
                        // SUMBU KANAN 2: DEPLESI (0-5%)
                        'y-axis-right-perc': {
                            type: 'linear', display: true, position: 'right', min: 0, max: 5,
                            title: { display: true, text: 'Deplesi (%)', color: '#dc2626', font: { weight: 'bold' } },
                            grid: { display: false },
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // Logic Filter & Fetch (Sama, pastikan UI filter toggling benar)
        function changeMode(mode) {
            currentMode = mode;
            // Toggle Visibility Filter
            document.querySelector('[x-data]').__x.$data.showDateFilter = (mode === 'weekly');
            document.querySelector('[x-data]').__x.$data.showMonthFilter = (mode === 'monthly');

            // Set default dates logic
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
                option.value = k.id; option.text = k.nama_kandang;
                kandangSelect.appendChild(option);
            });

            if(kandangs.length > 0) {
                kandangSelect.value = kandangs[0].id;
                fetchChartData(kandangs[0].id);
            } else {
                const existingChart = Chart.getChart("kandangChart");
                if (existingChart) existingChart.destroy();
                initChart(emptyChartData);
                document.getElementById('chartSubtitle').innerText = 'Tidak ada kandang di unit ini';
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
                        subtitle.innerText = `Menampilkan data ${currentMode === 'daily' ? 'Harian' : (currentMode === 'weekly' ? 'Mingguan' : 'Bulanan')} untuk ${kandangText}`;
                    } else {
                        const existingChart = Chart.getChart("kandangChart");
                        if (existingChart) existingChart.destroy();
                        
                        initChart(emptyChartData, currentMode); 
                        subtitle.innerText = `Belum ada data untuk ${kandangText}.`;
                    }
                })
                .catch(error => { console.error('Error:', error); subtitle.innerText = 'Gagal memuat data.'; });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Set default date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('endDate').value = today;
            const d = new Date(); d.setDate(d.getDate() - 21);
            document.getElementById('startDate').value = d.toISOString().split('T')[0];

            initChart(chartData);
            if (!initialData.labels.length) {
                document.getElementById('chartSubtitle').innerText = 'Data belum tersedia (Menampilkan contoh)';
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden'); setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0'); setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }
    </script>
</body>
</html>