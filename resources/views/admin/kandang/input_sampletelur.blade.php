<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Timbang Telur - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Tambahkan Chart.js -->
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
        @include('admin.sidebar')
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Produksi</span><i class="ph-bold ph-caret-right"></i><span>Audit QC</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Timbang Sampel Telur (3 Tray)</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg"><i class="ph-bold ph-list text-2xl"></i></button>
                    <a href="{{ route('admin.kandang.data_timbang') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-200 transition">
                        <i class="ph-bold ph-clock-counter-clockwise"></i> Riwayat Audit
                    </a>
                </div>
            </header>

            <div class="p-4 md:p-8 w-full max-w-6xl mx-auto">

                @if(session('success')) <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 font-bold flex items-center gap-2"><i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}</div> @endif

                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6 flex gap-4 items-start shadow-sm">
                    <div class="text-blue-500 mt-1"><i class="ph-fill ph-info text-2xl"></i></div>
                    <div>
                        <h4 class="font-bold text-blue-800">Sistem Anti-Sabotase Aktif</h4>
                        <p class="text-sm text-blue-600 mt-1">Masukkan berat bersih dari 3 tray telur (Total 90 butir) yang diambil secara acak. Sistem akan otomatis menghitung rata-rata Gram/Butir dan membandingkannya dengan laporan Mandor untuk mendeteksi manipulasi data.</p>
                    </div>
                </div>

                <form action="{{ route('admin.kandang.store_timbang') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
                    @csrf
                    
                    <div class="p-6 md:p-8 border-b border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Kandang</label>
                                <select name="kandang_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cemara-500 outline-none">
                                    <option value="" disabled selected>-- Pilih Kandang yang diaudit --</option>
                                    <option value="1">Kandang 01 - Unit Kalirambut</option>
                                    <option value="2">Kandang 02 - Unit Kalirambut</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Audit</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cemara-500 outline-none">
                            </div>
                        </div>

                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Input Fisik (Netto Telur)</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- TRAY 1 -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center relative overflow-hidden group hover:border-cemara-300 transition">
                                <div class="absolute top-0 left-0 w-full h-1 bg-cemara-400"></div>
                                <h4 class="font-bold text-gray-700 mb-1">Tray Atas</h4>
                                <p class="text-xs text-gray-400 mb-4">Isi 30 Butir</p>
                                <div class="relative">
                                    <input type="number" step="0.01" id="tray1" name="berat_tray_1" required placeholder="0.00" oninput="hitungRataRata()" class="w-full px-4 py-3 text-center font-bold text-xl text-cemara-700 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                                    <span class="absolute right-3 top-3.5 text-sm font-bold text-gray-400">Kg</span>
                                </div>
                            </div>

                            <!-- TRAY 2 -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center relative overflow-hidden group hover:border-blue-300 transition">
                                <div class="absolute top-0 left-0 w-full h-1 bg-blue-400"></div>
                                <h4 class="font-bold text-gray-700 mb-1">Tray Tengah</h4>
                                <p class="text-xs text-gray-400 mb-4">Isi 30 Butir</p>
                                <div class="relative">
                                    <input type="number" step="0.01" id="tray2" name="berat_tray_2" required placeholder="0.00" oninput="hitungRataRata()" class="w-full px-4 py-3 text-center font-bold text-xl text-blue-700 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                    <span class="absolute right-3 top-3.5 text-sm font-bold text-gray-400">Kg</span>
                                </div>
                            </div>

                            <!-- TRAY 3 -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center relative overflow-hidden group hover:border-orange-300 transition">
                                <div class="absolute top-0 left-0 w-full h-1 bg-orange-400"></div>
                                <h4 class="font-bold text-gray-700 mb-1">Tray Bawah</h4>
                                <p class="text-xs text-gray-400 mb-4">Isi 30 Butir</p>
                                <div class="relative">
                                    <input type="number" step="0.01" id="tray3" name="berat_tray_3" required placeholder="0.00" oninput="hitungRataRata()" class="w-full px-4 py-3 text-center font-bold text-xl text-orange-700 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
                                    <span class="absolute right-3 top-3.5 text-sm font-bold text-gray-400">Kg</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HASIL KALKULASI REAL-TIME -->
                    <div class="bg-gray-800 p-6 md:p-8 text-white">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="flex gap-8">
                                <div>
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Sampel</p>
                                    <p class="text-2xl font-bold"><span id="displayTotalTelur">90</span> <span class="text-sm font-normal text-gray-400">Butir</span></p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Berat</p>
                                    <p class="text-2xl font-bold text-gold-400"><span id="displayTotalBerat">0.00</span> <span class="text-sm font-normal text-gray-400">Kg</span></p>
                                </div>
                            </div>
                            <div class="text-right w-full md:w-auto bg-gray-900/50 p-4 rounded-xl border border-gray-700">
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Rata-rata Fisik (Real)</p>
                                <p class="text-4xl font-black text-green-400"><span id="displayRataRata">0.0</span> <span class="text-lg font-normal text-gray-400">Gram / Butir</span></p>
                                <input type="hidden" name="rata_rata_gram" id="inputRataRata">
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-cemara-600 text-white font-bold rounded-xl hover:bg-cemara-700 transition shadow-lg shadow-cemara-500/30 flex items-center gap-2">
                            <i class="ph-bold ph-floppy-disk"></i> Simpan Hasil Audit
                        </button>
                    </div>
                </form>

                <!-- ========================================== -->
                <!-- SEGMEN GRAFIK KOMBINASI (Semua dalam 1 Canvas) -->
                <!-- ========================================== -->
                <div class="mb-10">
                    <h3 class="font-bold text-xl text-gray-800 mb-6 flex items-center gap-2">
                        <i class="ph-fill ph-chart-line-up text-cemara-600"></i> Tren Kualitas & Audit Sabotase
                    </h3>
                    
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="mb-2 flex items-center justify-between">
                            <h4 class="text-sm font-bold text-gray-700">Grafik Kombinasi: Berat Telur vs Selisih Laporan Mandor</h4>
                            <div class="flex gap-4 text-[10px] font-bold uppercase tracking-wider">
                                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-600"></span> Real QC (Gram)</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border-2 border-dashed border-gold-500"></span> Total 3 Tray (Kg)</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-red-500"></span> Selisih Laporan</span>
                            </div>
                        </div>
                        
                        <!-- Satu Canvas Besar -->
                        <div class="relative h-96 w-full mt-4">
                            <canvas id="combinedChart"></canvas>
                        </div>
                        <p class="text-xs text-gray-500 mt-4 text-center">
                            *Batang merah (di bawah garis 0) menunjukkan laporan Mandor lebih berat dari aslinya, mengindikasikan <b>Sabotase FCR</b>.
                        </p>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Script Kalkulasi Form -->
    <script>
        function hitungRataRata() {
            let t1 = parseFloat(document.getElementById('tray1').value) || 0;
            let t2 = parseFloat(document.getElementById('tray2').value) || 0;
            let t3 = parseFloat(document.getElementById('tray3').value) || 0;

            let totalKg = t1 + t2 + t3;
            document.getElementById('displayTotalBerat').innerText = totalKg.toFixed(2);

            // Jika ada isinya, hitung rata-rata. 90 butir telur. Jadikan Gram (Kg * 1000)
            if(totalKg > 0) {
                let totalGram = totalKg * 1000;
                let rataRata = totalGram / 90;
                
                document.getElementById('displayRataRata').innerText = rataRata.toFixed(1);
                document.getElementById('inputRataRata').value = rataRata.toFixed(2);
            } else {
                document.getElementById('displayRataRata').innerText = '0.0';
                document.getElementById('inputRataRata').value = '';
            }
        }
    </script>

    <!-- Script Chart.js untuk 1 Grafik Kombinasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data Dummy untuk presentasi ke Boss. Nanti bisa diganti pakai variabel dari Controller.
            const labels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Hari Ini'];

            const ctx = document.getElementById('combinedChart').getContext('2d');
            new Chart(ctx, {
                data: {
                    labels: labels,
                    datasets: [
                        {
                            type: 'bar', // Batang (Bar) untuk Deviasi/Selisih
                            label: 'Selisih Laporan Mandor (Gram)',
                            // Jika minus = Mandor memanipulasi angka lebih tinggi dari aslinya
                            data: [-1.2, -1.0, -0.5, 0, -2.5, -0.2, -3.1], 
                            backgroundColor: function(context) {
                                const index = context.dataIndex;
                                const value = context.dataset.data[index];
                                return value < 0 ? 'rgba(239, 68, 68, 0.8)' : 'rgba(34, 197, 94, 0.8)'; // Merah (minus), Hijau (plus)
                            },
                            borderRadius: 4,
                            yAxisID: 'yDeviasi',
                            order: 2 // Di belakang garis
                        },
                        {
                            type: 'line', // Garis Solid untuk Rata-rata Gram
                            label: 'Rata-rata Real QC (Gram/Butir)',
                            data: [61.2, 61.5, 62.1, 62.0, 61.8, 62.3, 62.7],
                            borderColor: '#2563eb', // Biru
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'yGram',
                            order: 1
                        },
                        {
                            type: 'line', // Garis Putus-putus untuk Total Kg
                            label: 'Total Berat 3 Tray (Kg)',
                            data: [5.5, 5.53, 5.58, 5.58, 5.56, 5.6, 5.65],
                            borderColor: '#f59e0b', // Gold/Orange
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [5, 5], // Garis putus-putus
                            tension: 0.4,
                            yAxisID: 'yKg',
                            order: 0 // Paling depan
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false }, // Disembunyikan karena sudah buat custom legend di atas
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 }
                        }
                    },
                    scales: {
                        // Sumbu Y Kiri (Untuk Rata-rata Gram)
                        yGram: {
                            type: 'linear',
                            position: 'left',
                            title: { display: true, text: 'Berat Rata-rata (Gram/Btr)', font: {weight: 'bold'} },
                            suggestedMin: 55,
                            suggestedMax: 65,
                            grid: { drawOnChartArea: true, color: '#f3f4f6' }
                        },
                        // Sumbu Y Kanan 1 (Untuk Deviasi/Selisih)
                        yDeviasi: {
                            type: 'linear',
                            position: 'right',
                            title: { display: true, text: 'Selisih Deviasi (Gram)', font: {weight: 'bold'} },
                            suggestedMin: -5,
                            suggestedMax: 5,
                            grid: {
                                drawOnChartArea: false, // Jangan gambar gridnya biar gak tabrakan
                                color: function(context) {
                                    if (context.tick.value === 0) {
                                        return '#000000'; // Garis nol ditebalkan agar jelas
                                    }
                                    return '#e5e7eb';
                                }
                            }
                        },
                        // Sumbu Y Kanan 2 (Untuk Total Kg, disembunyikan labelnya agar rapi, tapi valuenya akurat di tooltip)
                        yKg: {
                            type: 'linear',
                            position: 'right',
                            display: false, // Disembunyikan axis Y-nya biar nggak sumpek, tapi garis tetep ada
                            suggestedMin: 4.5,
                            suggestedMax: 6.5
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>