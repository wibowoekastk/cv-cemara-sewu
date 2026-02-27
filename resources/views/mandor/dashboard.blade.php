<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mandor - CV Cemara Sewu</title>
    
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
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden glass-effect transition-opacity duration-300 opacity-0"></div>

        @include('mandor.sidebar')

        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Halo, {{ Auth::user()->name }} 👋</h2>
                    <p class="text-sm text-gray-500">
                        Target Unit: 
                        @if($target->id)
                            <span class="font-bold text-cemara-700">Aktif ({{ \Carbon\Carbon::parse($target->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($target->end_date)->format('d M') }})</span>
                        @else
                            <span class="text-red-500 font-bold">Belum diset Admin</span>
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-semibold border border-cemara-100 items-center gap-2">
                        <i class="ph-fill ph-calendar-check"></i> {{ date('l, d M Y') }}
                    </div>
                </div>
            </header>

            <div class="p-4 md:p-8 max-w-7xl mx-auto w-full">
                
                <!-- LOGIKA PENGUMUMAN / PERINGATAN -->
                @php
                    $alerts = [];
                    // Cek Target vs Realisasi
                    if($target->id) {
                        // Hen Day: Jika kurang dari target
                        if($realisasi['hd'] < $target->hd) {
                            $diff = $target->hd - $realisasi['hd'];
                            $alerts[] = ['type' => 'warning', 'icon' => 'ph-chart-line-down', 'title' => 'Produksi (HD) Rendah', 'msg' => 'Capaian HD saat ini <b>'.number_format($realisasi['hd'], 1).'%</b>, di bawah target <b>'.$target->hd.'%</b>.'];
                        }
                        // FCR: Jika lebih tinggi dari target (FCR makin rendah makin bagus)
                        if($realisasi['fcr'] > $target->fcr && $realisasi['fcr'] > 0) {
                            $alerts[] = ['type' => 'warning', 'icon' => 'ph-grains', 'title' => 'FCR Tinggi', 'msg' => 'Efisiensi pakan (FCR) <b>'.$realisasi['fcr'].'</b> melebihi target <b>'.$target->fcr.'</b>.'];
                        }
                        // Kematian: Jika persentase kematian hari ini tinggi (misal > 0.1% harian atau > target mortality)
                         // Asumsi target mortality adalah batas kumulatif, kita cek nominal aja
                        if($realisasi['mortality'] > 5) { // Contoh batas hardcoded 5 ekor, atau sesuaikan logika bisnis
                            $alerts[] = ['type' => 'danger', 'icon' => 'ph-skull', 'title' => 'Kematian Tinggi', 'msg' => 'Jumlah kematian hari ini <b>'.$realisasi['mortality'].' ekor</b>.'];
                        }
                    } else {
                        $alerts[] = ['type' => 'info', 'icon' => 'ph-info', 'title' => 'Target Belum Ada', 'msg' => 'Admin belum mengatur target produksi untuk unit ini.'];
                    }
                @endphp

                <!-- TAMPILAN ALERT -->
                @if(count($alerts) > 0)
                    <div class="grid gap-3 mb-6">
                        @foreach($alerts as $alert)
                            <div class="p-4 rounded-xl flex items-start gap-3 shadow-sm border-l-4 
                                {{ $alert['type'] == 'danger' ? 'bg-red-50 border-red-500 text-red-800' : 
                                  ($alert['type'] == 'warning' ? 'bg-yellow-50 border-yellow-500 text-yellow-800' : 'bg-blue-50 border-blue-500 text-blue-800') }}">
                                <i class="ph-fill {{ $alert['icon'] }} text-xl mt-0.5"></i>
                                <div>
                                    <h4 class="font-bold text-sm">{{ $alert['title'] }}</h4>
                                    <p class="text-xs mt-1">{!! $alert['msg'] !!}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($target->id)
                    <!-- Jika tidak ada alert dan target ada, berarti performa bagus -->
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl flex items-center gap-3 shadow-sm">
                        <i class="ph-fill ph-check-circle text-green-600 text-xl"></i>
                        <div>
                            <h4 class="font-bold text-green-800 text-sm">Performa Bagus!</h4>
                            <p class="text-xs text-green-700">Semua indikator utama masih dalam batas aman target.</p>
                        </div>
                    </div>
                @endif

                <!-- 1. KARTU INDIKATOR -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                    
                    <!-- Card 1: Produksi Telur -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Produksi Hari Ini</p>
                                <h3 class="text-2xl font-poppins font-bold text-gray-900 mt-1">
                                    {{ number_format($realisasi['telur_kg'], 1) }} <span class="text-sm font-sans text-gray-400">Kg</span>
                                </h3>
                            </div>
                            <div class="w-8 h-8 bg-gold-50 text-gold-600 rounded-lg flex items-center justify-center text-lg"><i class="ph-fill ph-egg"></i></div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center">
                            <span class="text-[10px] text-gray-400">Total Berat</span>
                             <!-- Target Display Seragam -->
                             <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500">
                                Target: -
                            </span>
                        </div>
                    </div>

                    <!-- Card 2: Body Weight -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Target BW</p>
                                <h3 class="text-2xl font-poppins font-bold text-gray-900 mt-1">
                                    {{ number_format($target->bw) }} <span class="text-sm font-sans text-gray-400">Gr</span>
                                </h3>
                            </div>
                            <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-lg"><i class="ph-fill ph-scales"></i></div>
                        </div>
                         <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center">
                            <span class="text-[10px] text-gray-400">Aktual: {{ number_format($realisasi['bw']) }} Gr</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600">
                                Target: {{ number_format($target->bw) }}
                            </span>
                        </div>
                    </div>

                    <!-- Card 3: Berat Telur -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Target Berat Telur</p>
                                <h3 class="text-2xl font-poppins font-bold text-gray-900 mt-1">
                                    {{ $target->egg_weight }} <span class="text-sm font-sans text-gray-400">Gr</span>
                                </h3>
                            </div>
                            <div class="w-8 h-8 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center text-lg"><i class="ph-fill ph-cookie"></i></div>
                        </div>
                         <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center">
                            <span class="text-[10px] text-gray-400">Aktual: {{ $realisasi['egg_weight'] }} Gr</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-50 text-yellow-600">
                                Target: {{ $target->egg_weight }}
                            </span>
                        </div>
                    </div>

                    <!-- Card 4: Kematian -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Kematian Hari Ini</p>
                                <h3 class="text-2xl font-poppins font-bold text-red-600 mt-1">
                                    {{ $realisasi['mortality'] }} <span class="text-sm font-sans text-gray-400">Ekor</span>
                                </h3>
                            </div>
                            <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-lg"><i class="ph-fill ph-skull"></i></div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center">
                            <span class="text-[10px] text-gray-400">Deplesi</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600">
                                Batas: {{ $target->mortality }}%
                            </span>
                        </div>
                    </div>

                    <!-- Card 5: Hen Day -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition border-l-4 
                        {{ $realisasi['hd'] >= $target->hd ? 'border-green-400' : 'border-red-400' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Hen Day (HD)</p>
                                <h3 class="text-2xl font-poppins font-bold {{ $realisasi['hd'] >= $target->hd ? 'text-green-600' : 'text-red-600' }} mt-1">
                                    {{ number_format($realisasi['hd'], 1) }} <span class="text-sm font-sans text-gray-400">%</span>
                                </h3>
                            </div>
                            <div class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center text-lg"><i class="ph-fill ph-chart-line-up"></i></div>
                        </div>
                         <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center">
                            <span class="text-[10px] text-gray-400">Produktivitas</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-600">
                                Target: {{ $target->hd }}%
                            </span>
                        </div>
                    </div>

                    <!-- Card 6: FCR Harian -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">FCR Harian</p>
                                <h3 class="text-2xl font-poppins font-bold text-gray-900 mt-1">
                                    {{ number_format($realisasi['fcr'], 2) }} 
                                </h3>
                            </div>
                            <div class="w-8 h-8 bg-cemara-50 text-cemara-600 rounded-lg flex items-center justify-center text-lg"><i class="ph-fill ph-calculator"></i></div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center">
                             <span class="text-[10px] text-gray-400">Efisiensi Pakan</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cemara-50 text-cemara-600">
                                Target: {{ $target->fcr }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2. GRAFIK & FILTER TARGET -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8" x-data="{ selectedTarget: 'hd' }">
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-trend-up text-cemara-600"></i> Realisasi vs Target
                            </h3>
                            <div class="relative group w-full sm:w-64">
                                <select x-model="selectedTarget" @change="updateChart(selectedTarget)" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm font-bold rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none block w-full appearance-none cursor-pointer">
                                    <option value="hd">Hen Day (HD%)</option>
                                    <option value="egg_weight">Berat Telur (Gr)</option>
                                    <option value="bw">Body Weight (Gr)</option>
                                    <option value="fcr">FCR (Ratio)</option>
                                    <option value="mortality">Kematian (Ekor)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                    <i class="ph-bold ph-caret-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="relative h-80 w-full">
                            <canvas id="productionTargetChart"></canvas>
                        </div>
                    </div>

                    <!-- Donut Chart -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                        <div class="mb-4">
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-target text-gold-500"></i> Pencapaian Hari Ini
                            </h3>
                        </div>
                        <div class="relative flex-1 flex items-center justify-center">
                            <div class="h-56 w-full relative">
                                <canvas id="achievementChart"></canvas>
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-3xl font-bold text-gray-800" id="donutPercentage">0%</span>
                                    <span class="text-xs text-gray-400 uppercase tracking-wide">Capaian</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- SCRIPT CHART -->
    <script>
        let chartLine = null;
        let chartDonut = null;

        const targets = {
            'hd': {{ $target->hd ?? 0 }},
            'egg_weight': {{ $target->egg_weight ?? 0 }},
            'fcr': {{ $target->fcr ?? 0 }}, 
            'bw': {{ $target->bw ?? 0 }},
            'mortality': {{ $target->mortality ?? 0 }}
        };

        const seriesData = @json($chartSeries);
        const chartLabels = @json($dates);
        
        const todayData = {
            'hd': {{ $realisasi['hd'] }},
            'egg_weight': {{ $realisasi['egg_weight'] }},
            'fcr': {{ $realisasi['fcr'] }},
            'bw': {{ $realisasi['bw'] }},
            'mortality': {{ $realisasi['mortality'] }}
        };

        document.addEventListener('DOMContentLoaded', () => {
            initCharts('hd'); 
        });

        function initCharts(type) {
            const targetVal = targets[type];
            const targetLine = Array(chartLabels.length).fill(targetVal);

            const ctxLine = document.getElementById('productionTargetChart').getContext('2d');
            if (chartLine) chartLine.destroy();

            chartLine = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Target',
                            data: targetLine,
                            borderColor: '#9ca3af',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false,
                            tension: 0
                        },
                        {
                            label: 'Realisasi',
                            data: seriesData[type] || Array(7).fill(null), 
                            borderColor: '#166534',
                            backgroundColor: 'rgba(22, 101, 52, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#166534',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' }, tooltip: { mode: 'index', intersect: false } },
                    scales: { y: { beginAtZero: false } }
                }
            });

            const ctxDonut = document.getElementById('achievementChart').getContext('2d');
            const todayVal = todayData[type];
            
            let percent = 0;
            if (targetVal > 0) {
                if (type === 'mortality' || type === 'fcr') {
                    percent = todayVal <= targetVal ? 100 : Math.max(0, 100 - ((todayVal - targetVal)/targetVal * 100));
                } else {
                    percent = Math.min(100, Math.round((todayVal / targetVal) * 100));
                }
            }
            
            document.getElementById('donutPercentage').innerText = Math.round(percent) + '%';

            if (chartDonut) chartDonut.destroy();

            chartDonut = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Tercapai', 'Gap'],
                    datasets: [{
                        data: [percent, 100 - percent],
                        backgroundColor: ['#22c55e', '#f3f4f6'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                }
            });
        }

        function updateChart(type) {
            initCharts(type);
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            if (overlay) {
                overlay.classList.toggle('hidden');
                overlay.classList.toggle('opacity-0');
            }
        }
    </script>
</body>
</html>