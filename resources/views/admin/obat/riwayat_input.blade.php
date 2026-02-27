<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Obat - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-poppins'] },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' },
                        medical: { 50: '#ecfeff', 100: '#cffafe', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490' }
                    }
                }
            }
        }
    </script>
    <style>
        .table-container::-webkit-scrollbar { height: 8px; }
        .table-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        th, td { white-space: nowrap; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ activeTab: 'keluar', showModalUsage: false }">

    <div class="flex h-screen overflow-hidden relative">
        
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
                        <span>Obat</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Riwayat</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Log Keluar Masuk</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    
                    <!-- Tombol Catat Pemakaian (Quick Action) -->
                    <button @click="showModalUsage = true" class="hidden md:flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition shadow-lg shadow-red-500/20">
                        <i class="ph-bold ph-minus-circle"></i> Catat Pemakaian
                    </button>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 font-bold flex items-center gap-2 shadow-sm">
                        <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 font-bold flex items-center gap-2 shadow-sm">
                        <i class="ph-fill ph-warning-circle text-xl"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Tabs Menu -->
                <div class="flex gap-4 mb-6 border-b border-gray-200">
                    <button @click="activeTab = 'keluar'" 
                            :class="activeTab === 'keluar' ? 'border-b-2 border-red-500 text-red-600' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 font-bold text-sm transition flex items-center gap-2">
                        <i class="ph-bold ph-minus-circle"></i> Stok Keluar (Pemakaian)
                    </button>
                    <button @click="activeTab = 'masuk'" 
                            :class="activeTab === 'masuk' ? 'border-b-2 border-green-500 text-green-600' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 font-bold text-sm transition flex items-center gap-2">
                        <i class="ph-bold ph-plus-circle"></i> Stok Masuk (Pembelian)
                    </button>
                </div>

                <!-- TAB 1: RIWAYAT PEMAKAIAN (STOK KELUAR) -->
                <div x-show="activeTab === 'keluar'" x-transition:enter="transition ease-out duration-300">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-red-50/20">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">Log Pemakaian Obat</h3>
                                <p class="text-xs text-gray-500">Daftar obat yang digunakan di kandang.</p>
                            </div>
                            <div class="relative w-64 hidden sm:block">
                                <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                                <input type="text" placeholder="Cari penggunaan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none">
                            </div>
                        </div>

                        <div class="overflow-x-auto table-container">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4">Waktu</th>
                                        <th class="px-6 py-4">Petugas</th>
                                        <th class="px-6 py-4">Nama Obat</th>
                                        <th class="px-6 py-4 text-center">Jumlah</th>
                                        <th class="px-6 py-4">Asal Batch (FEFO)</th>
                                        <th class="px-6 py-4">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($riwayatPakai as $log)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($log->tgl_pakai)->format('d M Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    @if(optional($log->user)->avatar)
                                                        <img src="{{ asset('storage/' . $log->user->avatar) }}" alt="Avatar" class="w-6 h-6 rounded-full object-cover">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log->user->name ?? 'User') }}&background=e5e7eb&color=374151&size=32" alt="Avatar" class="w-6 h-6 rounded-full">
                                                    @endif
                                                    <span>{{ $log->user->name ?? 'Unknown' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="font-bold text-gray-800">{{ $log->batch->obat->nama_obat ?? '-' }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full font-bold text-xs border border-red-100">
                                                    -{{ $log->jumlah_pakai }} {{ $log->batch->obat->satuan ?? '' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded inline-block text-gray-600">
                                                    {{ $log->batch->kode_batch ?? 'N/A' }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 mt-0.5">
                                                    Exp: {{ \Carbon\Carbon::parse($log->batch->tgl_kadaluarsa)->format('d/m/y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600 text-xs max-w-xs truncate font-medium">
                                                {{ $log->keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">Belum ada riwayat pemakaian.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100">
                            {{ $riwayatPakai->links() }}
                        </div>
                    </div>
                </div>

                <!-- TAB 2: RIWAYAT MASUK (STOK MASUK) -->
                <div x-show="activeTab === 'masuk'" x-cloak x-transition:enter="transition ease-out duration-300">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-green-50/20">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">Log Stok Masuk</h3>
                                <p class="text-xs text-gray-500">Histori pembelian/penambahan stok.</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto table-container">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4">Tgl Masuk</th>
                                        <th class="px-6 py-4">Nama Obat</th>
                                        <th class="px-6 py-4">Kode Batch</th>
                                        <th class="px-6 py-4 text-center">Jumlah Masuk</th>
                                        <th class="px-6 py-4">Expired Date</th>
                                        <th class="px-6 py-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($riwayatMasuk as $masuk)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4">
                                                {{ \Carbon\Carbon::parse($masuk->tgl_masuk)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 font-bold text-gray-800">
                                                {{ $masuk->obat->nama_obat ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 font-mono text-xs">
                                                {{ $masuk->kode_batch }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full font-bold text-xs border border-green-100">
                                                    +{{ $masuk->stok_awal }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-red-500 font-medium">
                                                {{ \Carbon\Carbon::parse($masuk->tgl_kadaluarsa)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($masuk->status == 'active')
                                                    <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">Aktif</span>
                                                @elseif($masuk->status == 'empty')
                                                    <span class="text-xs bg-gray-100 text-gray-400 px-2 py-1 rounded">Habis</span>
                                                @else
                                                    <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Expired</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">Belum ada riwayat masuk.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100">
                            {{ $riwayatMasuk->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <!-- ================= MODAL INPUT PEMAKAIAN (FEFO) ================= -->
        <div x-show="showModalUsage" x-cloak 
             x-data="{ 
                 inputUnitId: '', 
                 inputKandangName: '',
                 inputCatatan: '' 
             }"
             class="fixed inset-0 z-50 overflow-y-auto">
            
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModalUsage = false"></div>
            
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative z-50 p-6 transform transition-all">
                    
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 bg-red-50 text-red-600 rounded-full flex items-center justify-center"><i class="ph-bold ph-minus"></i></div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Catat Pemakaian Obat</h3>
                            <p class="text-xs text-gray-500">Input penggunaan untuk kandang tertentu</p>
                        </div>
                        <button @click="showModalUsage = false" class="ml-auto text-gray-400 hover:text-red-500"><i class="ph-bold ph-x text-xl"></i></button>
                    </div>
                    
                    <form action="{{ route('admin.obat.store_usage') }}" method="POST">
                        @csrf
                        
                        <!-- Input Hidden Keterangan (Hasil Gabungan) -->
                        <input type="hidden" name="keterangan" :value="`[${inputKandangName}] ${inputCatatan}`">

                        <div class="space-y-4">
                            <!-- Pilih Obat -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Obat</label>
                                <div class="relative">
                                    <select name="obat_id" class="w-full border rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-red-500 outline-none text-sm font-medium">
                                        @if(isset($obats) && count($obats) > 0)
                                            @foreach($obats as $o)
                                                <option value="{{ $o->id }}">{{ $o->nama_obat }} (Sisa: {{ $o->total_stok }} {{ $o->satuan }})</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected>Data obat tidak tersedia</option>
                                        @endif
                                    </select>
                                    <i class="ph-bold ph-caret-down absolute right-4 top-3 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>
                            
                            <!-- Jumlah & Tanggal -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jumlah Pakai</label>
                                    <input type="number" name="jumlah_pakai" class="w-full border rounded-xl px-4 py-2.5 font-bold text-red-600 focus:ring-2 focus:ring-red-500 outline-none" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                                    <input type="date" name="tgl_pakai" value="{{ date('Y-m-d') }}" class="w-full border rounded-xl px-4 py-2.5 text-sm">
                                </div>
                            </div>

                            <!-- DETAIL LOKASI (DYNAMIC DROPDOWN) -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3">
                                <p class="text-xs font-bold text-gray-400 uppercase">Lokasi Pemakaian</p>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Pilih Unit -->
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 mb-1 block">Unit</label>
                                        <select x-model="inputUnitId" onchange="filterKandang()" id="unitSelector" class="w-full border rounded-lg px-2 py-1.5 text-xs bg-white cursor-pointer">
                                            <option value="">Pilih Unit...</option>
                                            @if(isset($units))
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <!-- Pilih Kandang -->
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 mb-1 block">Kandang</label>
                                        <select x-model="inputKandangName" id="kandangSelector" class="w-full border rounded-lg px-2 py-1.5 text-xs bg-white cursor-pointer" disabled>
                                            <option value="">Pilih Kandang...</option>
                                            @if(isset($units))
                                                @foreach($units as $unit)
                                                    @foreach($unit->kandangs as $kandang)
                                                        <option value="{{ $kandang->nama_kandang }} ({{ $unit->nama_unit }})" 
                                                                data-unit-id="{{ $unit->id }}" 
                                                                data-lokasi="{{ $unit->lokasi }}"
                                                                class="kandang-option hidden">
                                                            {{ $kandang->nama_kandang }}
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <!-- Lokasi Otomatis (Readonly) -->
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 mb-1 block">Lokasi Farm</label>
                                    <input type="text" id="lokasiDisplay" class="w-full border rounded-lg px-2 py-1.5 text-xs bg-gray-100 text-gray-600" readonly placeholder="Otomatis terisi...">
                                </div>

                                <!-- Catatan Tambahan -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catatan Tambahan</label>
                                    <textarea x-model="inputCatatan" rows="2" class="w-full border rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 outline-none resize-none" placeholder="Contoh: Vaksin rutin..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showModalUsage = false" class="px-5 py-2 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-white text-sm">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-md text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Filter Kandang -->
    <script>
        function filterKandang() {
            const unitId = document.getElementById('unitSelector').value;
            const options = document.querySelectorAll('.kandang-option');
            const selector = document.getElementById('kandangSelector');
            const lokasiDisplay = document.getElementById('lokasiDisplay');

            // Reset Pilihan & Lokasi
            selector.value = "";
            lokasiDisplay.value = "";
            let hasKandang = false;

            options.forEach(option => {
                if (unitId !== "" && option.getAttribute('data-unit-id') == unitId) {
                    option.classList.remove('hidden');
                    // Ambil lokasi dari data kandang pertama yang cocok (asumsi 1 unit 1 lokasi)
                    if (!lokasiDisplay.value) {
                        lokasiDisplay.value = option.getAttribute('data-lokasi');
                    }
                    hasKandang = true;
                } else {
                    option.classList.add('hidden');
                }
            });

            if (unitId !== "") {
                selector.disabled = !hasKandang;
                if (!hasKandang) {
                    // Opsional: Tampilkan pesan tidak ada kandang
                    lokasiDisplay.value = "Tidak ada kandang di unit ini";
                }
            } else {
                selector.disabled = true;
            }
        }
    </script>

</body>
</html>