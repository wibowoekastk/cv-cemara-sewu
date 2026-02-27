<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Timbang Ayam - Admin Panel</title>
    
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
                        <span>Input Timbang</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Tambah Data Timbang</h2>
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
            <div class="p-4 md:p-8 w-full max-w-4xl mx-auto">
                
                <!-- Alert Success -->
                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="ph-fill ph-check-circle text-xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-fit">
                    
                    <!-- Card Header -->
                    <div class="p-6 border-b border-gray-100 flex items-center gap-3 bg-gray-50/30 rounded-t-2xl">
                        <div class="w-12 h-12 rounded-xl bg-gold-100 flex items-center justify-center text-gold-600 shadow-sm">
                            <i class="ph-fill ph-scales text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">Data Input Timbang</h3>
                            <p class="text-xs text-gray-500">Catat berat rata-rata, umur, dan keseragaman (uniformity)</p>
                        </div>
                    </div>
                    
                    <!-- Form Input -->
                    <form action="{{ route('admin.kandang.store_timbang') }}" method="POST" class="p-6 md:p-8 space-y-6">
                        @csrf
                        
                        <!-- Baris 1: Tanggal & Unit -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tanggal Input Timbang -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tanggal Timbang</label>
                                <div class="relative">
                                    <input type="date" name="tgl_timbang" id="dateTimbang" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-700">
                                    <i class="ph-bold ph-calendar-blank absolute left-3 top-3.5 text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Pilih Unit (DINAMIS) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Unit Farm</label>
                                <div class="relative">
                                    <select id="unitSelector" onchange="filterKandang()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                        <option value="" disabled selected>Pilih Unit...</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                        @endforeach
                                    </select>
                                    <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2: Lokasi & Kandang -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Lokasi Kandang (Auto-filled by JS - Readonly) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Lokasi Kandang</label>
                                <div class="relative">
                                    <input type="text" id="lokasiDisplay" readonly class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed" placeholder="Otomatis terisi...">
                                    <i class="ph-bold ph-map-pin absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- Pilih Kandang (DINAMIS & FILTERED) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kode Kandang</label>
                                <div class="relative">
                                    <select name="kandang_id" id="kandangSelector" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700" disabled>
                                        <option value="" disabled selected>Pilih Unit Terlebih Dahulu...</option>
                                        
                                        <!-- Opsi Kandang (Semua dirender dulu, nanti di-filter JS) -->
                                        @foreach($units as $unit)
                                            @foreach($unit->kandangs as $kandang)
                                                <option value="{{ $kandang->id }}" 
                                                        data-unit-id="{{ $unit->id }}" 
                                                        data-lokasi="{{ $unit->lokasi }}"
                                                        data-umur="{{ $kandang->umur_minggu ?? 0 }}"
                                                        class="kandang-option hidden">
                                                    {{ $kandang->nama_kandang }} ({{ ucfirst($kandang->status) }})
                                                </option>
                                            @endforeach
                                        @endforeach

                                    </select>
                                    <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                </div>
                                <p id="kandangEmptyMsg" class="text-xs text-red-500 mt-1 hidden">Tidak ada kandang aktif di unit ini.</p>
                            </div>
                        </div>

                        <!-- Baris 3: Umur & Berat -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            <!-- Umur Ayam (Auto-fill or Manual) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Umur Ayam</label>
                                <div class="relative">
                                    <input type="number" name="umur_minggu" id="umurAyam" required placeholder="0" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-bold text-gray-800">
                                    <span class="absolute right-4 top-3.5 text-xs text-gray-500 font-bold">Minggu</span>
                                </div>
                            </div>

                            <!-- Berat Ayam -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Berat Rata-rata</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="berat_rata" required placeholder="0.00" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-bold text-gray-800">
                                    <span class="absolute right-4 top-3.5 text-xs text-gray-500 font-bold">Gram</span>
                                </div>
                            </div>

                            <!-- Uniformity -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Uniformity</label>
                                <div class="relative">
                                    <input type="number" step="0.1" name="uniformity" required placeholder="85.5" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-bold text-gray-800">
                                    <span class="absolute right-4 top-3.5 text-xs text-gray-500 font-bold">%</span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 ml-1">*Persentase kerataan.</p>
                            </div>
                        </div>

                        <!-- Button Simpan -->
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('admin.kandang.data_timbang') }}" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-8 py-3 bg-cemara-900 text-white font-bold rounded-xl hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="ph-bold ph-floppy-disk"></i> Simpan Timbang
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Set Tanggal Hari Ini
        document.addEventListener('DOMContentLoaded', () => {
            const dateInput = document.getElementById('dateTimbang');
            const displayDate = document.getElementById('headerDateDisplay');
            
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            
            if(dateInput) dateInput.value = `${yyyy}-${mm}-${dd}`;
            
            const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
            if(displayDate) displayDate.innerText = today.toLocaleDateString('id-ID', options);
            
            // Inisialisasi dropdown (reset)
            const kandangSelector = document.getElementById('kandangSelector');
            kandangSelector.innerHTML = '<option value="" disabled selected>Pilih Unit Terlebih Dahulu...</option>' + 
                                      kandangSelector.innerHTML;
        });

        // Script Filter Kandang Berdasarkan Unit (FIXED)
        function filterKandang() {
            const unitId = document.getElementById('unitSelector').value;
            const kandangSelector = document.getElementById('kandangSelector');
            const options = kandangSelector.querySelectorAll('.kandang-option'); // Ambil semua opsi kandang
            const emptyMsg = document.getElementById('kandangEmptyMsg');
            const lokasiDisplay = document.getElementById('lokasiDisplay');
            const umurInput = document.getElementById('umurAyam');

            // Reset
            kandangSelector.value = "";
            kandangSelector.disabled = false; // Enable select
            lokasiDisplay.value = "";
            umurInput.value = "";
            
            let hasKandang = false;
            let firstVisible = true;

            options.forEach(option => {
                // Cek apakah data-unit-id sama dengan unit yg dipilih
                if (option.getAttribute('data-unit-id') == unitId) {
                    option.classList.remove('hidden'); // Tampilkan opsi
                    option.disabled = false;
                    
                    if (firstVisible) {
                         // Set lokasi dari kandang pertama yg cocok
                        lokasiDisplay.value = option.getAttribute('data-lokasi');
                        firstVisible = false;
                    }
                    hasKandang = true;
                } else {
                    option.classList.add('hidden'); // Sembunyikan opsi
                    option.disabled = true; // Biar tidak bisa dipilih lewat keyboard
                }
            });

            if (hasKandang) {
                // Pilih opsi default "Pilih Kandang..."
                kandangSelector.value = ""; 
                emptyMsg.classList.add('hidden');
            } else {
                kandangSelector.value = "";
                kandangSelector.disabled = true;
                emptyMsg.classList.remove('hidden');
                lokasiDisplay.value = "-";
            }
        }

        // Auto-fill Umur saat Kandang dipilih
        document.getElementById('kandangSelector').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const umur = selectedOption.getAttribute('data-umur');
            if(umur && umur != 0) {
                document.getElementById('umurAyam').value = umur;
            } else {
                document.getElementById('umurAyam').value = ""; // Kosongkan jika 0
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