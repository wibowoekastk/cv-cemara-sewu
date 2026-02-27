<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pakan - Admin Panel</title>
    
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
                        <span>Pakan</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Dashboard Pakan</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Input Pakan Shortcut -->
                    <a href="{{ route('admin.pakan.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20">
                        <i class="ph-bold ph-plus"></i> Stok Masuk
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- 1. Stats Cards (4 Kartu) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    <!-- Card 1: Pemakaian Harian -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-cemara-500 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-cemara-500"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Pemakaian Harian</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($pemakaianHarian, 0) }} Kg</h3>
                            </div>
                            <div class="w-10 h-10 bg-cemara-100 text-cemara-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-bowl-food"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 font-medium">Total seluruh unit (Hari ini)</span>
                        </div>
                    </div>

                    <!-- Card 2: Total Stok Gudang (Pusat) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-blue-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-blue-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Stok Gudang Pusat</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stokGudangPusat / 1000, 2) }} Ton</h3>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-warehouse"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 font-medium">{{ number_format($stokGudangPusat, 0) }} Kg (Belum didistribusi)</span>
                        </div>
                    </div>

                    <!-- Card 3: Sisa Stok (Available di Unit) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-red-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-red-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Stok di Unit</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stokDiUnit, 0) }} Kg</h3>
                            </div>
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-warning-octagon"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="{{ $estimasiHari <= 3 ? 'text-red-500 bg-red-50' : 'text-green-600 bg-green-50' }} font-bold px-2 py-0.5 rounded mr-2">Estimasi: {{ $estimasiHari }} Hari</span>
                        </div>
                    </div>

                    <!-- Card 4: Pakan Produktivitas Tinggi -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-gold-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-gold-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Performa Terbaik</p>
                                <h3 class="text-xl font-bold text-gray-800 mt-1 truncate" title="{{ $bestPakan->nama_pakan ?? 'Belum ada data' }}">
                                    {{ $bestPakan->nama_pakan ?? 'N/A' }}
                                </h3>
                            </div>
                            <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-trophy"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 font-medium">FCR Rata-rata: <strong class="text-gold-600">{{ number_format($bestPakan->avg_fcr ?? 0, 2) }}</strong></span>
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    
                    <!-- 2. Grafik: Konsumsi Pakan vs Produktivitas -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                    <i class="ph-fill ph-chart-line-up text-cemara-600"></i>
                                    Efisiensi Pakan
                                </h3>
                                <p class="text-sm text-gray-400">Perbandingan konsumsi pakan vs hasil produksi telur (7 Hari Terakhir)</p>
                            </div>
                        </div>
                        
                        <div class="relative h-80 w-full">
                            <canvas id="feedProductivityChart"></canvas>
                        </div>
                    </div>

                    <!-- 3. Grafik Helikopter (Radar): Komparasi Pakan -->
                    <!-- Ini masih data Dummy untuk visualisasi karena butuh fitur penilaian pakan terpisah -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                        <div class="mb-4">
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-scales text-blue-500"></i>
                                Komparasi Pakan
                            </h3>
                            <p class="text-sm text-gray-400">Analisa kualitas (Data Contoh)</p>
                        </div>
                        
                        <div class="relative flex-1 flex items-center justify-center">
                            <div class="h-64 w-full">
                                <canvas id="feedComparisonChart"></canvas>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                             <div class="flex flex-wrap justify-center gap-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1"><div class="w-3 h-3 bg-cemara-500 rounded-sm"></div> Konsentrat A</span>
                                <span class="flex items-center gap-1"><div class="w-3 h-3 bg-gold-500 rounded-sm"></div> Konsentrat B</span>
                             </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- SCRIPTS CHARTS -->
    <script>
        // --- 1. Mixed Chart (Pakan vs Produktivitas) ---
        const ctxFeed = document.getElementById('feedProductivityChart').getContext('2d');
        
        // Data dari Controller
        const labels = @json($chartLabels);
        const dataPakan = @json($chartPakan);
        const dataProd = @json($chartHDP);

        new Chart(ctxFeed, {
            data: {
                labels: labels,
                datasets: [
                    // Bar: Konsumsi Pakan (Kg) - Sumbu Kiri
                    {
                        type: 'bar',
                        label: 'Total Pakan (Kg)',
                        data: dataPakan,
                        backgroundColor: '#e5e7eb', // Abu muda
                        hoverBackgroundColor: '#9ca3af',
                        yAxisID: 'y-left',
                        barPercentage: 0.5,
                        order: 2
                    },
                    // Line: Produktivitas (%) - Sumbu Kanan
                    {
                        type: 'line',
                        label: 'Produktivitas HDP (%)',
                        data: dataProd,
                        borderColor: '#166534', // Hijau Cemara
                        backgroundColor: '#166534',
                        borderWidth: 3,
                        pointRadius: 4,
                        tension: 0.4,
                        yAxisID: 'y-right',
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    x: { grid: { display: false } },
                    'y-left': { 
                        type: 'linear', 
                        position: 'left', 
                        title: {display: true, text: 'Pakan (Kg)'},
                        grid: { borderDash: [2, 2] }
                    },
                    'y-right': { 
                        type: 'linear', 
                        position: 'right', 
                        min: 0, 
                        max: 100, // HDP max 100%
                        grid: { display: false },
                        title: {display: true, text: 'Produksi (%)'} 
                    }
                }
            }
        });

        // --- 2. Radar Chart (Komparasi Pakan) ---
        // Masih data dummy karena fitur penilaian pakan belum ada di DB
        const ctxRadar = document.getElementById('feedComparisonChart').getContext('2d');
        
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                // Parameter Penilaian
                labels: ['FCR (Rendah=Bagus)', 'Berat Telur', 'Kekerasan Cangkang', 'Kesehatan Ayam', 'Harga (Ekonomis)'], 
                datasets: [
                    {
                        label: 'Konsentrat A',
                        data: [90, 85, 88, 95, 80], // Skor 0-100
                        backgroundColor: 'rgba(34, 197, 94, 0.2)', // Hijau transparan
                        borderColor: '#22c55e',
                        pointBackgroundColor: '#22c55e',
                        borderWidth: 2
                    },
                    {
                        label: 'Konsentrat B',
                        data: [80, 92, 90, 85, 95], // Skor 0-100
                        backgroundColor: 'rgba(234, 179, 8, 0.2)', // Kuning transparan
                        borderColor: '#eab308',
                        pointBackgroundColor: '#eab308',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        angleLines: { color: '#e5e7eb' },
                        grid: { color: '#e5e7eb' },
                        pointLabels: {
                            font: { size: 10, family: "'Plus Jakarta Sans', sans-poppins" },
                            color: '#4b5563'
                        },
                        suggestedMin: 50,
                        suggestedMax: 100
                    }
                }
            }
        });

        // Script Toggle Sidebar
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