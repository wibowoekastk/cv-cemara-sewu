<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Obat & Kesehatan - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-poppins'] },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' },
                        medical: { 50: '#ecfeff', 100: '#cffafe', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490' } // Cyan scheme
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
                        <span>Obat & Vaksin</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Dashboard Kesehatan</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Link ke Input Stok -->
                    <a href="{{ route('admin.obat.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-medical-600 text-white rounded-lg text-sm font-semibold hover:bg-medical-700 transition shadow-lg shadow-medical-500/20">
                        <i class="ph-bold ph-plus"></i> Stok Masuk
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- 1. Stats Cards (4 Kartu) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    <!-- Card 1: Stok Menipis (Dynamic) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-red-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-red-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Stok Menipis</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stokKritis }} <span class="text-sm font-normal text-gray-400">Item</span></h3>
                            </div>
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-siren"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-red-500 font-medium flex items-center gap-1">
                                Perlu Restock
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Item di bawah batas minimum</p>
                    </div>

                    <!-- Card 2: Segera Expired (Dynamic) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-orange-500 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-orange-500"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Segera Expired</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $nearExpired }} <span class="text-sm font-normal text-gray-400">Batch</span></h3>
                            </div>
                            <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-clock-countdown"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-orange-600 font-medium">Dalam 30 Hari</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Prioritaskan penggunaan (FEFO)</p>
                    </div>

                    <!-- Card 3: Total Jenis Obat (Dynamic) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-medical-400 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-medical-400"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Total Master</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalObat }} <span class="text-sm font-normal text-gray-400">Jenis</span></h3>
                            </div>
                            <div class="w-10 h-10 bg-medical-100 text-medical-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-pill"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="bg-medical-50 text-medical-700 px-2 py-0.5 rounded text-xs font-bold mr-2">Aktif</span>
                            <span class="text-gray-500">Terdaftar</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Obat, Vitamin, & Vaksin</p>
                    </div>

                    <!-- Card 4: Ayam Sakit (Static - Belum ada data produksi) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-cemara-500 transition duration-300">
                        <div class="absolute right-0 top-0 h-full w-1 bg-cemara-500"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Ayam Sakit</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">-</h3>
                            </div>
                            <div class="w-10 h-10 bg-cemara-100 text-cemara-600 rounded-full flex items-center justify-center text-xl">
                                <i class="ph-fill ph-heartbeat"></i>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500">Belum ada data</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Laporan harian mandor</p>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    
                    <!-- 2. Grafik: Tren Penggunaan Obat (Line Chart) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                    <i class="ph-fill ph-activity text-medical-600"></i>
                                    Tren Penggunaan Obat
                                </h3>
                                <p class="text-sm text-gray-400">Total volume/jumlah pemakaian 7 hari terakhir</p>
                            </div>
                        </div>
                        
                        <div class="relative h-80 w-full">
                            <canvas id="healthTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- 3. Grafik Donut: Komposisi Obat -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                        <div class="mb-4">
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-chart-pie-slice text-gold-500"></i>
                                Komposisi Stok
                            </h3>
                            <p class="text-sm text-gray-400">Total sisa stok berdasarkan kategori</p>
                        </div>
                        
                        <div class="relative flex-1 flex items-center justify-center">
                            <div class="h-64 w-full">
                                <canvas id="medicinePieChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Table Stok Obat (Dynamic from $topStok) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <i class="ph-fill ph-package"></i> Stok Obat Terbanyak
                        </h3>
                        <a href="{{ route('admin.obat.data_input') }}" class="text-sm text-medical-600 font-bold hover:underline">Lihat Semua Stok</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold">
                                <tr>
                                    <th class="px-6 py-3">Nama Obat</th>
                                    <th class="px-6 py-3">Kategori</th>
                                    <th class="px-6 py-3 text-center">Total Stok</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($topStok as $obat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 font-bold text-gray-900">{{ $obat->nama_obat }}</td>
                                        <td class="px-6 py-3">
                                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold">
                                                {{ $obat->jenis_obat ?? 'Umum' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-center font-bold text-gray-700">
                                            {{ $obat->total_stok }} {{ $obat->satuan }}
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            @if($obat->total_stok <= $obat->min_stok)
                                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">Menipis</span>
                                            @else
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Aman</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data stok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- SCRIPTS CHARTS (Dynamic Data from Controller) -->
    <script>
        // Toggle Sidebar
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

        // --- 1. Health Trend Chart (Line) - DATA REAL ---
        const ctxHealth = document.getElementById('healthTrendChart').getContext('2d');
        
        // Ambil data dari PHP menggunakan json_encode
        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartUsageData = {!! json_encode($chartUsageData) !!};

        new Chart(ctxHealth, {
            type: 'line',
            data: {
                labels: chartLabels, // Label Tanggal (H-7 sd Hari ini)
                datasets: [
                    {
                        label: 'Total Pemakaian Obat',
                        data: chartUsageData, // Data Jumlah Pakai
                        borderColor: '#fbbf24', // Gold
                        backgroundColor: 'rgba(251, 191, 36, 0.2)', // Semi transparent gold
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                    }
                    // Dataset Kasus Sakit dihapus sementara karena belum ada tabel 'Kesehatan/Produksi'
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        beginAtZero: true,
                        title: {display: true, text: 'Jumlah (Satuan)'}
                    }
                }
            }
        });

        // --- 2. Pie Chart (Komposisi Stok) - DATA REAL ---
        const ctxPie = document.getElementById('medicinePieChart').getContext('2d');
        
        const pieLabels = {!! json_encode($kategoriList) !!}; // ['Vitamin', 'Antibiotik', 'Vaksin', 'Disinfektan']
        const pieData = {!! json_encode($chartPieData) !!};   // Jumlah stok per kategori

        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: [
                        '#fbbf24', // Gold (Vit)
                        '#ef4444', // Red (Anti)
                        '#06b6d4', // Cyan (Vaksin)
                        '#22c55e'  // Green (Disinfektan)
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                }
            }
        });
    </script>
</body>
</html>