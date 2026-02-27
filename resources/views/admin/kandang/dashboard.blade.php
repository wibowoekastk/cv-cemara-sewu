<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kandang - Admin Panel</title>
    
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
                        <span>Kandang</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Dashboard Kandang</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Add Kandang Button -->
                    <a href="{{ route('admin.kandang.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20">
                        <i class="ph-bold ph-plus"></i> Tambah Kandang
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- 1. Stats Cards (4 Kartu) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    <!-- Card 1: Unit Aktif -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-cemara-500 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-cemara-500"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Total Unit</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalUnit }} Unit</h3>
                            </div>
                            <div class="w-10 h-10 bg-cemara-100 text-cemara-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-house-line"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 font-medium">Terdaftar di Sistem</span>
                        </div>
                    </div>

                    <!-- Card 2: Kandang Aktif -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-blue-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-blue-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Kandang Aktif</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $kandangAktif }} KDG</h3>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-warehouse"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 font-medium">Total: {{ $totalKandang }} Kandang</span>
                        </div>
                        <!-- Progress Bar Sederhana -->
                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $totalKandang > 0 ? ($kandangAktif / $totalKandang) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Card 3: Warning (Mortalitas Tinggi / FCR Buruk) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-red-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-red-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Kdg Warning</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $kandangWarning }} KDG</h3>
                            </div>
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-trend-down"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-red-500 font-medium flex items-center gap-1">
                                <i class="ph-bold ph-warning-circle"></i> Kematian > 5%
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Perlu evaluasi kesehatan</p>
                    </div>

                    <!-- Card 4: Populasi Efektif -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-gold-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-gold-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Populasi Efektif</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($populasiEfektif) }}</h3>
                            </div>
                            <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-bird"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded mr-2">Hidup</span>
                            <span class="text-gray-500">Saat Ini</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Total ayam di semua kandang</p>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    
                    <!-- 2. Grafik Utama: Performa Harian Kandang (Line Chart) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col gap-4 mb-6">
                            
                            <!-- Header & Toggle Chart Type -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                        <i class="ph-fill ph-chart-line-up text-cemara-600"></i>
                                        Monitoring Performa
                                    </h3>
                                    <p class="text-sm text-gray-400">Analisa pertumbuhan berdasarkan umur (Data Real Timbang)</p>
                                </div>
                                <div class="flex bg-gray-100 rounded-lg p-1">
                                    <!-- Sementara hanya Bobot yang aktif karena data produksi belum ada -->
                                    <button class="px-3 py-1.5 rounded-md text-xs font-bold text-gray-500 hover:text-gray-700 transition cursor-not-allowed" title="Data belum tersedia">Produksi</button>
                                    <button class="px-3 py-1.5 rounded-md text-xs font-bold transition bg-white text-gray-800 shadow-sm border border-gray-200">Bobot Ayam</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative h-80 w-full">
                            <canvas id="dailyPerformanceChart"></canvas>
                        </div>
                    </div>

                    <!-- 3. Grafik Helikopter (Radar Chart) -->
                    <!-- Bagian ini masih Statis karena Controller belum menyiapkan data kompleks untuk Radar -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                        <div class="mb-4">
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-crosshair text-gold-500"></i>
                                Radar Performa
                            </h3>
                            <p class="text-sm text-gray-400">Target Ideal vs Realisasi</p>
                        </div>
                        
                        <div class="relative flex-1 flex items-center justify-center">
                            <div class="h-64 w-full">
                                <canvas id="radarChart"></canvas>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                             <div class="flex flex-wrap justify-center gap-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1"><div class="w-3 h-3 bg-cemara-500/50 border border-cemara-600 rounded-sm"></div> Target</span>
                                <span class="flex items-center gap-1"><div class="w-3 h-3 bg-blue-500/50 border border-blue-600 rounded-sm"></div> Realisasi</span>
                             </div>
                        </div>
                    </div>

                </div>

                <!-- Table Status Kandang Terkini (Dinamis) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">Top Kandang Padat</h3>
                        <a href="{{ route('admin.kandang.data_input') }}" class="text-sm text-cemara-600 font-bold hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold">
                                <tr>
                                    <th class="px-6 py-3">Kandang</th>
                                    <th class="px-6 py-3">Lokasi (Unit)</th>
                                    <th class="px-6 py-3">Populasi</th>
                                    <th class="px-6 py-3">Umur</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($topKandangs as $kandang)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 font-bold text-gray-900">{{ $kandang->nama_kandang }}</td>
                                        <td class="px-6 py-3 text-gray-500">{{ $kandang->unit->nama_unit ?? '-' }}</td>
                                        <td class="px-6 py-3 font-bold text-gold-600">{{ number_format($kandang->stok_saat_ini) }} Ekor</td>
                                        <td class="px-6 py-3 text-blue-600 font-bold">{{ $kandang->umur_minggu }} Mgg</td>
                                        <td class="px-6 py-3">
                                            @if($kandang->status == 'aktif')
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Aktif</span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold">{{ ucfirst($kandang->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">Belum ada data kandang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- SCRIPTS CHARTS -->
    <script>
        // --- 1. Line Chart (Grafik Performa - Dinamis) ---
        const ctxLine = document.getElementById('dailyPerformanceChart').getContext('2d');
        
        // Ambil Data dari Controller
        const labels = {!! json_encode($chartLabels) !!};       // Label Minggu
        const bobotData = {!! json_encode($chartBobotData) !!}; // Data Berat

        let myLineChart = new Chart(ctxLine, {
            data: {
                labels: labels.length > 0 ? labels : ['Minggu 18', 'Minggu 19', 'Minggu 20'], // Fallback jika kosong
                datasets: [
                    { 
                        type: 'line', 
                        label: 'Berat Badan (gr)', 
                        data: bobotData.length > 0 ? bobotData : [0, 0, 0], 
                        borderColor: '#fbbf24', // Gold
                        borderWidth: 3, 
                        tension: 0.4, 
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#fbbf24',
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    x: { 
                        grid: { display: false },
                        title: { display: true, text: 'Umur Ayam' }
                    },
                    y: { 
                        beginAtZero: false, // Berat tidak mulai dari 0
                        title: {display: true, text: 'Berat (gram)'} 
                    }
                }
            }
        });


        // --- 2. Radar Chart (Grafik Helikopter - Statis/Dummy) ---
        // Karena data detail performa (FCR, Livability) belum ada di Controller
        const ctxRadar = document.getElementById('radarChart').getContext('2d');
        
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: ['Produksi', 'Berat Telur', 'FCR', 'Livability', 'Berat Badan'],
                datasets: [
                    {
                        label: 'Target',
                        data: [95, 90, 95, 99, 95],
                        backgroundColor: 'rgba(34, 197, 94, 0.2)', // Cemara 500
                        borderColor: '#22c55e',
                        borderWidth: 2
                    },
                    {
                        label: 'Realisasi',
                        data: [85, 80, 82, 95, 88],
                        backgroundColor: 'rgba(59, 130, 246, 0.2)', // Blue 500
                        borderColor: '#3b82f6',
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
                        pointLabels: { font: { size: 10, family: "'Plus Jakarta Sans', sans-poppins" }, color: '#4b5563' },
                        suggestedMin: 50,
                        suggestedMax: 100
                    }
                }
            }
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