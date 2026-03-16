<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Harian - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' },
                        blue: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 800: '#1e40af' }
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
                        <span>Analytic</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Input Data</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Input Data Harian</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Date Display -->
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-semibold border border-cemara-100">
                        <i class="ph-fill ph-calendar-check"></i>
                        <span id="headerDateDisplay"></span>
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 max-w-6xl mx-auto w-full">
                
                {{-- Pesan Sukses/Error --}}
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center gap-3">
                        <i class="ph-fill ph-check-circle text-2xl"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm flex items-center gap-3">
                        <i class="ph-fill ph-warning-circle text-2xl"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.analytic.store') }}" method="POST" class="space-y-6" id="dailyInputForm"> 
                    @csrf
                    
                    <!-- Section 1: Identitas Kandang & Waktu -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center gap-2 mb-4 text-cemara-700 border-b border-gray-100 pb-3">
                            <div class="p-2 bg-cemara-50 rounded-lg"><i class="ph-fill ph-map-pin text-xl"></i></div>
                            <h3 class="text-lg font-bold">Identitas Kandang</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
                            <!-- Tanggal (Bisa Backdate) -->
                            <div class="lg:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tanggal Input</label>
                                <input type="date" name="tanggal" id="inputDate" onchange="fetchKandangStats()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium" required>
                            </div>

                            <!-- Unit -->
                            <div class="lg:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Unit</label>
                                <div class="relative">
                                    <select name="unit_id" id="inputUnit" onchange="filterUnitData()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium" required>
                                        <option value="" disabled selected>Pilih Unit...</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" 
                                                    data-lokasi="{{ $unit->lokasi }}" 
                                                    data-kandangs='{{ json_encode($unit->kandangs) }}'
                                                    data-pakans='{{ json_encode($unit->pakanStocks) }}'>
                                                {{ $unit->nama_unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="ph-bold ph-caret-down absolute right-4 top-3 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- Kandang -->
                            <div class="lg:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kode Kandang</label>
                                <div class="relative">
                                    <select name="kandang_id" id="inputKandang" onchange="fetchKandangStats()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium" required disabled>
                                        <option value="" disabled selected>Pilih Unit Dulu...</option>
                                    </select>
                                    <i class="ph-bold ph-caret-down absolute right-4 top-3 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- Info Siklus/Batch (Otomatis) -->
                            <div class="lg:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Siklus / Batch</label>
                                <input type="text" id="displayBatch" class="w-full px-4 py-2.5 bg-blue-50 border border-blue-100 rounded-xl text-blue-800 font-bold text-sm cursor-not-allowed" placeholder="-" readonly>
                            </div>
                            
                            <!-- Lokasi -->
                            <div class="lg:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Lokasi Farm</label>
                                <input type="text" id="displayLokasi" class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed font-medium" placeholder="Otomatis..." readonly>
                            </div>

                            <!-- Umur Ayam (Minggu + Hari) Otomatis Berdasarkan Tanggal Input -->
                            <div class="lg:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Umur Sekarang</label>
                                <div class="relative">
                                    <input type="text" id="displayUmur" class="w-full px-4 py-2.5 bg-blue-50 border border-blue-100 rounded-xl text-blue-800 font-bold text-sm cursor-not-allowed" placeholder="Pilih Kandang..." readonly>
                                    <i class="ph-fill ph-calendar absolute right-4 top-3 text-blue-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Section 2: Data Populasi (Kiri) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full flex flex-col">
                            <div class="flex items-center gap-2 mb-4 text-red-600 border-b border-gray-100 pb-3">
                                <div class="p-2 bg-red-50 rounded-lg"><i class="ph-fill ph-bird text-xl"></i></div>
                                <h3 class="text-lg font-bold text-gray-800">Data Populasi</h3>
                            </div>

                            <div class="space-y-5 flex-1">
                                <!-- Info Populasi Awal -->
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 flex justify-between items-center">
                                    <div>
                                        <span class="text-xs text-gray-500 uppercase font-bold tracking-wide">Populasi Awal</span>
                                        <div class="text-xs text-gray-400 mt-1">Penyebut Rumus HH & Deplesi</div>
                                    </div>
                                    <input type="hidden" id="stokAwalMaster" value="0"> 
                                    <span class="text-xl font-bold text-gray-800" id="displayStokAwalMaster">-</span>
                                </div>
                                
                                <!-- Info Populasi Saat Ini -->
                                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex justify-between items-center">
                                    <div>
                                        <span class="text-xs text-blue-600 font-bold uppercase">Populasi Pagi Ini</span>
                                        <input type="hidden" name="populasi_awal" id="populasiSaatIni" value="0">
                                    </div>
                                    <span class="text-xl font-bold text-blue-800" id="displayPopulasiSaatIni">-</span>
                                </div>

                                <!-- Input Mortalitas -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Mati (Ekor)</label>
                                        <input type="number" id="inputMati" name="mati" placeholder="0" class="w-full px-4 py-3 bg-red-50 border border-red-100 rounded-xl focus:ring-2 focus:ring-red-500 outline-none transition text-red-700 font-bold placeholder-red-300" oninput="hitungOtomatis()" required>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Afkir (Ekor)</label>
                                        <input type="number" id="inputAfkir" name="afkir" placeholder="0" class="w-full px-4 py-3 bg-orange-50 border border-orange-100 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition text-orange-700 font-bold placeholder-orange-300" oninput="hitungOtomatis()" required>
                                    </div>
                                </div>

                                <!-- Indikator Deplesi Harian Beranimasi -->
                                <div class="mt-2 bg-red-50/50 p-3 rounded-xl border border-red-100 flex justify-between items-center">
                                    <div>
                                        <span class="text-xs text-red-600 font-bold uppercase tracking-wide">Deplesi Harian</span>
                                        <p class="text-[10px] text-red-400 mt-0.5">(Mati+Afkir) / Populasi Awal</p>
                                    </div>
                                    <span class="text-lg font-bold text-red-700 transition-colors duration-300" id="wrapperDeplesi">
                                        <span id="hasilDeplesi">0.00</span>%
                                    </span>
                                </div>

                                <!-- Input Keterangan Kematian -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Keterangan / Penyebab Kematian</label>
                                    <input type="text" name="ket_mati" placeholder="Contoh: Snot, Terjepit, Prolaps..." class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none text-sm transition text-gray-700">
                                </div>

                            </div>
                        </div>

                        <!-- Section 3: Pakan & FCR (Kanan) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full flex flex-col">
                            <div class="flex items-center gap-2 mb-4 text-yellow-600 border-b border-gray-100 pb-3">
                                <div class="p-2 bg-yellow-50 rounded-lg"><i class="ph-fill ph-grains text-xl"></i></div>
                                <h3 class="text-lg font-bold text-gray-800">Pakan & FCR</h3>
                            </div>

                            <div class="space-y-5 flex-1">
                                <!-- Pilih Nama Pakan -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Jenis Pakan (Stok Unit)</label>
                                    <div class="relative">
                                        <select name="pakan_id" id="inputPakanSelect" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-yellow-500 outline-none transition cursor-pointer font-medium text-gray-700" required>
                                            <option value="" disabled selected>Pilih Unit Terlebih Dahulu...</option>
                                        </select>
                                        <i class="ph-bold ph-caret-down absolute right-4 top-3 text-gray-400 pointer-events-none"></i>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">*Hanya menampilkan pakan yang tersedia di Gudang Unit ini.</p>
                                </div>

                                <!-- Input Total Pakan -->
                                <div class="relative">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Total Pakan Harian (Kg)</label>
                                    <input type="number" step="0.01" id="inputPakan" name="pakan_kg" placeholder="0.00" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 outline-none font-bold text-lg" oninput="hitungOtotalis()" required>
                                    <span class="absolute right-4 top-9 text-sm text-gray-400 font-bold">Kg</span>
                                </div>

                                <!-- Kalkulasi Otomatis -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 text-center">
                                        <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Konsumsi/Ekor</label>
                                        <div class="text-gray-800 font-bold text-lg"><span id="hasilKonsumsi">0</span> <span class="text-xs font-normal text-gray-500">gr</span></div>
                                    </div>
                                    <div class="bg-yellow-50 p-3 rounded-xl border border-yellow-200 text-center">
                                        <label class="block text-[10px] uppercase font-bold text-yellow-600 mb-1">FCR Harian</label>
                                        <div class="text-yellow-700 font-bold text-lg" id="hasilFCR">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Section 4: Produksi Telur (Full Width) -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center gap-2 mb-6 text-gold-600 border-b border-gray-100 pb-3">
                            <div class="p-2 bg-gold-50 rounded-lg"><i class="ph-fill ph-egg text-xl"></i></div>
                            <h3 class="text-lg font-bold text-gray-800">Hasil Produksi Telur</h3>

                            <!-- Hidden Inputs untuk Data Akumulasi -->
                            <input type="hidden" id="cumButirPrev" value="0">
                            <input type="hidden" id="cumKgPrev" value="0">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Jumlah Butir (Utuh) -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Telur Utuh (Butir)</label>
                                <input type="number" id="inputTelurButir" name="telur_butir" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none font-bold text-gray-800" placeholder="0" oninput="hitungOtomatis()" required>
                            </div>
                            
                            <!-- Total Berat -->
                            <div class="relative">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Total Berat (Kg)</label>
                                <input type="number" step="0.01" id="inputTelurBerat" name="telur_kg" class="w-full px-4 py-3 bg-gold-50 border border-gold-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none font-bold text-gold-700" placeholder="0.00" oninput="hitungOtomatis()" required>
                                <span class="absolute right-4 top-9 text-sm text-gold-600 font-bold">Kg</span>
                            </div>

                            <!-- Kalkulasi Otomatis (Berat per Butir) -->
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 flex flex-col justify-center">
                                <span class="text-xs text-gray-500 font-medium">Berat per Butir</span>
                                <span class="text-lg font-bold text-gray-800"><span id="hasilBeratPerButir">0</span> <span class="text-xs font-normal text-gray-500">gr/btr</span></span>
                            </div>
                        </div>
                        
                        <!-- Kalkulasi Otomatis (HD% & HH%) -->
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                             <!-- Hen Day -->
                             <div class="bg-blue-50 p-3 rounded-xl border border-blue-200 flex flex-col justify-center space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-blue-600 font-bold uppercase">Hen Day (HD%)</span>
                                    <span class="text-lg font-bold text-blue-700"><span id="hasilHD">0</span>%</span>
                                </div>
                                <p class="text-[10px] text-blue-400">Prod. Harian / Ayam Hidup</p>
                            </div>
                            
                            <!-- Hen House (HH) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-green-50 p-3 rounded-xl border border-green-100">
                                    <span class="text-[10px] font-bold text-green-600 block">HH (Butir)</span>
                                    <span class="text-lg font-bold text-green-700" id="hasilHHButir">0</span>
                                </div>
                                <div class="bg-green-50 p-3 rounded-xl border border-green-100">
                                    <span class="text-[10px] font-bold text-green-600 block">HH (Kg)</span>
                                    <span class="text-lg font-bold text-green-700" id="hasilHHKg">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="fixed bottom-0 left-0 right-0 md:left-64 bg-white border-t border-gray-200 p-4 z-20 flex items-center justify-end gap-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                        <button type="button" onclick="resetForm()" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition text-sm">
                            Batal
                        </button>
                        <button type="submit" class="px-8 py-2.5 rounded-xl bg-cemara-900 text-white font-bold hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 text-sm">
                            <i class="ph-bold ph-paper-plane-right"></i>
                            Simpan Laporan
                        </button>
                    </div>

                    <div class="h-16"></div> <!-- Spacer -->

                </form>

            </div>
        </main>
    </div>

    <!-- Script Logic -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function filterUnitData() {
            const unitSelect = document.getElementById('inputUnit');
            const kandangSelect = document.getElementById('inputKandang');
            const pakanSelect = document.getElementById('inputPakanSelect');
            
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];

            // Reset Fields
            kandangSelect.innerHTML = '<option value="" disabled selected>Pilih Kandang...</option>';
            kandangSelect.disabled = true;
            document.getElementById('displayLokasi').value = '';
            document.getElementById('displayBatch').value = '-'; 
            document.getElementById('displayUmur').value = ''; 

            pakanSelect.innerHTML = '<option value="" disabled selected>Pilih Pakan...</option>';
            pakanSelect.disabled = true;

            if (selectedOption) {
                const lokasi = selectedOption.getAttribute('data-lokasi');
                if(lokasi) document.getElementById('displayLokasi').value = lokasi;

                if (selectedOption.getAttribute('data-kandangs')) {
                    const kandangs = JSON.parse(selectedOption.getAttribute('data-kandangs'));
                    kandangs.forEach(k => {
                        const option = document.createElement('option');
                        option.value = k.id;
                        if (k.status === 'aktif' || k.stok_saat_ini > 0) {
                            option.text = k.nama_kandang;
                        } else {
                            option.text = `🚫 ${k.nama_kandang} (Kosong)`;
                            option.disabled = true;
                        }
                        kandangSelect.appendChild(option);
                    });
                    kandangSelect.disabled = false;
                }

                if (selectedOption.getAttribute('data-pakans')) {
                    const stokPakans = JSON.parse(selectedOption.getAttribute('data-pakans'));
                    let adaStok = false;
                    stokPakans.forEach(stock => {
                        if (stock.pakan && parseFloat(stock.jumlah_stok) > 0) {
                            const option = document.createElement('option');
                            option.value = stock.pakan.id;
                            option.text = `${stock.pakan.nama_pakan} (Sisa: ${parseFloat(stock.jumlah_stok).toLocaleString('id-ID')} Kg)`;
                            pakanSelect.appendChild(option);
                            adaStok = true;
                        }
                    });
                    if (adaStok) pakanSelect.disabled = false;
                }
            }
            updatePopulasiAwal();
        }

        function fetchKandangStats() {
            const kandangID = document.getElementById('inputKandang').value;
            const tanggalInput = document.getElementById('inputDate').value;
            
            if(!kandangID) return;

            // Efek loading
            const displayUmurField = document.getElementById('displayUmur');
            displayUmurField.value = 'Menghitung...';

            // Mengirimkan parameter ?tanggal= agar backend bisa menghitung umur mundur/maju
            fetch(`/admin/analytic/kandang-stats/${kandangID}?tanggal=${tanggalInput}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('stokAwalMaster').value = data.stok_awal;
                    document.getElementById('populasiSaatIni').value = data.stok_saat_ini;
                    document.getElementById('cumButirPrev').value = data.cum_butir_sebelumnya;
                    document.getElementById('cumKgPrev').value = data.cum_kg_sebelumnya;
                    document.getElementById('displayBatch').value = data.batch_name || '-';

                    // Menampilkan Umur yang sudah dihitung backend berdasarkan parameter tanggal
                    displayUmurField.value = data.umur_teks || (data.umur_minggu ? data.umur_minggu + ' Minggu' : '-');

                    document.getElementById('displayStokAwalMaster').innerText = data.stok_awal.toLocaleString('id-ID');
                    document.getElementById('displayPopulasiSaatIni').innerText = data.stok_saat_ini.toLocaleString('id-ID');

                    hitungOtomatis(); 
                })
                .catch(err => {
                    console.error(err);
                    displayUmurField.value = 'Gagal memuat';
                });
        }

        function updatePopulasiAwal() {
             if(!document.getElementById('inputKandang').value) {
                document.getElementById('displayStokAwalMaster').innerText = '-';
                document.getElementById('displayPopulasiSaatIni').innerText = '-';
                document.getElementById('displayBatch').value = '-';
                document.getElementById('displayUmur').value = '';
                return;
            }
            fetchKandangStats();
        }

        function hitungOtomatis() {
            let mati = parseFloat(document.getElementById('inputMati').value) || 0;
            let afkir = parseFloat(document.getElementById('inputAfkir').value) || 0;
            let pakanKg = parseFloat(document.getElementById('inputPakan').value) || 0;
            let telurButir = parseFloat(document.getElementById('inputTelurButir').value) || 0;
            let telurKg = parseFloat(document.getElementById('inputTelurBerat').value) || 0;

            let stokAwalMaster = parseFloat(document.getElementById('stokAwalMaster').value) || 0;
            let populasiHidup = parseFloat(document.getElementById('populasiSaatIni').value) || 0;
            let cumButirPrev = parseFloat(document.getElementById('cumButirPrev').value) || 0;
            let cumKgPrev = parseFloat(document.getElementById('cumKgPrev').value) || 0;

            let deplesiHarian = (stokAwalMaster > 0) ? ((mati + afkir) / stokAwalMaster) * 100 : 0;
            document.getElementById('hasilDeplesi').innerText = deplesiHarian.toFixed(2);

            let wrapperDeplesi = document.getElementById('wrapperDeplesi');
            if(deplesiHarian > 0.1) {
                wrapperDeplesi.classList.add('text-red-600', 'animate-pulse');
            } else {
                wrapperDeplesi.classList.remove('text-red-600', 'animate-pulse');
            }

            let sisaPopulasi = Math.max(0, populasiHidup - mati - afkir);
            document.getElementById('hasilKonsumsi').innerText = (sisaPopulasi > 0) ? ((pakanKg * 1000) / sisaPopulasi).toFixed(1) : 0;
            document.getElementById('hasilFCR').innerText = (telurKg > 0) ? (pakanKg / telurKg).toFixed(2) : '0.00';
            document.getElementById('hasilBeratPerButir').innerText = (telurButir > 0) ? ((telurKg * 1000) / telurButir).toFixed(1) : 0;
            document.getElementById('hasilHD').innerText = (sisaPopulasi > 0) ? ((telurButir / sisaPopulasi) * 100).toFixed(1) : 0;

            let totalCumButir = cumButirPrev + telurButir;
            let totalCumKg = cumKgPrev + telurKg;
            document.getElementById('hasilHHButir').innerText = (stokAwalMaster > 0) ? (totalCumButir / stokAwalMaster).toFixed(1) : 0;
            document.getElementById('hasilHHKg').innerText = (stokAwalMaster > 0) ? (totalCumKg / stokAwalMaster).toFixed(2) : '0.00';
        }

        function resetForm() {
            Swal.fire({
                title: 'Batalkan Input?',
                text: "Isian formulir akan dikosongkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('dailyInputForm').reset();
                    initDate();
                    filterUnitData();
                }
            })
        }

        function initDate() {
            const dateInput = document.getElementById('inputDate');
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayString = `${yyyy}-${mm}-${dd}`;
            
            if(dateInput) { dateInput.value = todayString; dateInput.max = todayString; } 
            document.getElementById('headerDateDisplay').innerText = today.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initDate();
            filterUnitData();
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar?.classList.toggle('-translate-x-full');
            if (overlay) {
                overlay.classList.toggle('hidden');
                setTimeout(() => overlay.classList.toggle('opacity-0'), 10);
            }
        }
    </script>
</body>
</html>