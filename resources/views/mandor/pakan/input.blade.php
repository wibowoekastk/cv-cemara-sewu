<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input & Distribusi Pakan - Mandor</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Sidebar Mandor -->
        @include('mandor.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Operasional</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Pakan</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Manajemen Stok Pakan</h2>
                </div>
                
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Lokasi Mandor (Readonly info) -->
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-bold border border-cemara-100">
                        <i class="ph-fill ph-map-pin"></i>
                        <span>{{ Auth::user()->unit->nama_unit ?? 'Unit Belum Diset' }} ({{ Auth::user()->unit->lokasi ?? '-' }})</span>
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full max-w-5xl mx-auto" x-data="{ activeTab: 'terima' }">
                
                <!-- Notifikasi / Alert Sukses Error -->
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

                <!-- [BARU] ALERT WARNING STOK MENIPIS (<= 100 Sak / 4000 Kg) -->
                @php
                    // Filter pakan yang stoknya kurang dari atau sama dengan 4000 Kg (100 Sak)
                    $lowStockItems = collect($stokSaatIni)->filter(function($s) {
                        return $s->jumlah_stok <= 4000;
                    });
                @endphp

                @if($lowStockItems->count() > 0)
                    <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-500 text-orange-800 rounded-r-lg shadow-sm">
                        <div class="flex items-start gap-3">
                            <i class="ph-fill ph-warning text-2xl text-orange-500 mt-0.5"></i>
                            <div>
                                <h3 class="font-bold uppercase tracking-wide text-sm mb-1">Peringatan: Stok Pakan Menipis</h3>
                                <p class="text-xs mb-2">Pakan berikut tersisa <strong>&le; 100 Sak</strong> di Gudang Unit. Harap segera lapor Admin Pusat.</p>
                                <ul class="list-disc list-inside text-xs font-semibold">
                                    @foreach($lowStockItems as $low)
                                        <li>{{ $low->pakan->nama_pakan }} - Sisa &plusmn; {{ number_format($low->jumlah_stok / 40, 1) }} Sak</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TABS NAVIGASI -->
                <div class="bg-white rounded-2xl p-2 shadow-sm border border-gray-100 flex flex-wrap gap-2 mb-6">
                    
                    <!-- Tab 1: Terima Kiriman (Default) -->
                    <button @click="activeTab = 'terima'" 
                            :class="activeTab === 'terima' ? 'bg-cemara-50 text-cemara-700 shadow-sm ring-1 ring-cemara-200' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                        <i class="ph-bold ph-download-simple text-lg"></i> Terima Kiriman
                    </button>

                    <!-- Tab 2: Cek Stok (Opname) -->
                    <button @click="activeTab = 'opname'" 
                            :class="activeTab === 'opname' ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                        <i class="ph-bold ph-clipboard-text text-lg"></i> Cek Stok (Blind Opname)
                    </button>

                    <!-- Tab 3: Pakan Rusak -->
                    <button @click="activeTab = 'pakai'" 
                            :class="activeTab === 'pakai' ? 'bg-red-50 text-red-700 shadow-sm ring-1 ring-red-200' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                        <i class="ph-bold ph-trash text-lg"></i> Pakan Rusak / Hilang
                    </button>
                </div>

                <!-- ================================================= -->
                <!-- KONTEN TAB 1: TERIMA KIRIMAN -->
                <!-- ================================================= -->
                <div x-show="activeTab === 'terima'" x-transition>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="ph-fill ph-truck text-cemara-600"></i> Kiriman Masuk (Pending)
                        </h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal Kirim</th>
                                        <th class="px-4 py-3">Dari</th>
                                        <th class="px-4 py-3">Jenis Pakan</th>
                                        <th class="px-4 py-3 text-center">Jumlah (Kg)</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($pendingMutations as $mutasi)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($mutasi->tanggal)->format('d M Y') }}</td>
                                        <td class="px-4 py-3">{{ $mutasi->dariUnit->nama_unit ?? 'Gudang Pusat' }}</td>
                                        <td class="px-4 py-3 font-bold">{{ $mutasi->pakan->nama_pakan }}</td>
                                        
                                        <td class="px-4 py-3 text-center">
                                            <div class="font-bold text-cemara-700 text-base">{{ number_format($mutasi->jumlah) }} Kg</div>
                                            <div class="text-[10px] text-gray-500">({{ number_format($mutasi->jumlah / 40, 1) }} Sak)</div>
                                        </td>
                                        
                                        <td class="px-4 py-3 text-center"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Dalam Perjalanan</span></td>
                                        
                                        <td class="px-4 py-3 text-center">
                                            <form action="{{ route('mandor.pakan.terima_stok_id', $mutasi->id) }}" method="POST" id="form-terima-{{ $mutasi->id }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" onclick="confirmTerima('form-terima-{{ $mutasi->id }}')" class="px-4 py-2 bg-cemara-600 text-white rounded-lg text-xs font-bold hover:bg-cemara-700 transition shadow-md flex items-center justify-center gap-1 mx-auto">
                                                    <i class="ph-bold ph-check-circle text-sm"></i> Terima
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada kiriman pakan yang pending.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Riwayat Penerimaan -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                        <div class="p-4 bg-gray-50 border-b border-gray-100"><h4 class="font-bold text-gray-700 text-sm">Riwayat Penerimaan Terakhir</h4></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500"><tr><th class="px-4 py-2">Tanggal</th><th class="px-4 py-2">Pakan</th><th class="px-4 py-2 text-right">Jumlah</th></tr></thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($riwayatTerima as $log)
                                    <tr>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 font-bold">{{ $log->pakan->nama_pakan }}</td>
                                        
                                        <td class="px-4 py-2 text-right">
                                            <span class="text-green-600 font-bold">+{{ number_format($log->jumlah, 1) }} Kg</span>
                                            <span class="text-gray-400 text-xs ml-1">({{ number_format($log->jumlah / 40, 1) }} Sak)</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400 italic">Belum ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- KONTEN TAB 2: CEK STOK (BLIND OPNAME) -->
                <!-- ================================================= -->
                <div x-show="activeTab === 'opname'" x-transition style="display: none;">
                    
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
                        <div class="flex items-start gap-3">
                            <i class="ph-fill ph-info text-2xl text-blue-600 mt-0.5"></i>
                            <div>
                                <h3 class="font-bold text-blue-800 uppercase text-sm mb-1">Sistem Blind Opname Aktif</h3>
                                <p class="text-xs text-blue-700 leading-relaxed">
                                    Sistem sengaja menyembunyikan stok berjalan. Silakan hitung jumlah karung secara fisik dan input angkanya dengan jujur. Segala selisih (susut/lebih) akan tercatat dan dilaporkan ke Admin Pusat.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-clipboard-text text-blue-600"></i> Lapor Stok Fisik
                            </h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Gudang Unit: {{ Auth::user()->unit->nama_unit ?? '-' }}</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($stokSaatIni as $stok)
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-300 transition bg-gray-50 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="font-bold text-gray-800 text-lg">{{ $stok->pakan->nama_pakan }}</h4>
                                        <span class="text-[10px] bg-white border border-gray-200 px-2 py-1 rounded text-gray-500 uppercase tracking-wide">{{ $stok->pakan->jenis_pakan }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-end mt-4 border-t border-gray-200 pt-4">
                                    <div>
                                        <!-- [UPDATE] Menyembunyikan Stok Sistem dari View -->
                                        <p class="text-xs text-gray-500 mb-1">Status Pengecekan:</p>
                                        <p class="font-bold text-blue-700 text-sm">Menunggu Input Fisik</p>
                                    </div>
                                    
                                    <!-- Tombol Koreksi (Modal) -->
                                    <button onclick="openOpnameModal('{{ $stok->pakan_id }}', '{{ $stok->pakan->nama_pakan }}')" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 shadow-sm transition flex items-center gap-1">
                                        <i class="ph-bold ph-pencil-simple"></i> Lapor Fisik
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Riwayat Opname Mandor (Hanya Status) -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
                        <div class="p-4 bg-gray-50 border-b border-gray-100"><h4 class="font-bold text-gray-700 text-sm">Riwayat Pelaporan Terakhir</h4></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500"><tr><th class="px-4 py-2">Tanggal Lapor</th><th class="px-4 py-2">Pakan</th><th class="px-4 py-2">Keterangan Tambahan</th></tr></thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($riwayatOpname as $log)
                                    <tr>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2 font-bold">{{ $log->pakan->nama_pakan }}</td>
                                        <td class="px-4 py-2 text-xs text-gray-500">{{ $log->keterangan }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400 italic">Belum ada riwayat pelaporan fisik.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- KONTEN TAB 3: PAKAN RUSAK / HILANG -->
                <!-- ================================================= -->
                <div x-show="activeTab === 'pakai'" x-transition style="display: none;">
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg shadow-sm">
                        <div class="flex">
                            <div class="shrink-0">
                                <i class="ph-fill ph-warning text-yellow-500 text-3xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-bold text-yellow-800 uppercase">PERHATIAN: Bukan untuk Pakan Harian!</h3>
                                <div class="mt-2 text-sm text-yellow-800 leading-relaxed">
                                    <p class="mb-2">Menu ini <strong>HANYA</strong> digunakan untuk mencatat pakan yang <strong>Rusak, Basah, Tumpah, atau Hilang</strong> di gudang.</p>
                                    <p>Untuk mencatat pemberian makan ayam harian, silakan gunakan menu <strong><a href="{{ route('mandor.produksi.input') }}" class="underline font-bold text-blue-700 hover:text-blue-800">Manajemen Produksi > Input Harian</a></strong> agar FCR terhitung otomatis.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="ph-fill ph-trash"></i> Form Lapor Pakan Rusak / Hilang
                        </h3>
                        
                        <form action="{{ route('mandor.pakan.store_usage') }}" method="POST">
                            @csrf
                            <input type="hidden" name="is_damaged" value="true">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2">Jenis Pakan</label>
                                    <select name="pakan_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none" required>
                                        <option value="" disabled selected>Pilih Pakan...</option>
                                        @foreach($stokSaatIni as $stok)
                                            <!-- [UPDATE] Sembunyikan Info Stok Akurat -->
                                            <option value="{{ $stok->pakan_id }}">{{ $stok->pakan->nama_pakan }} (Tersedia di Gudang)</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Input Sak -->
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-gray-500 mb-2">Rusak (Sak)</label>
                                        <input type="number" step="0.01" id="inputRusakSak" placeholder="0" class="w-full px-4 py-3 bg-red-50 border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none font-bold text-lg text-red-700 placeholder-red-300" oninput="hitungRusakSakKeKg()">
                                        <span class="absolute right-4 top-10 text-sm text-red-600 font-bold">Sak</span>
                                    </div>

                                    <!-- Input Kg -->
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-gray-500 mb-2">Total (Kg) <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" name="jumlah" id="inputRusakKg" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none font-bold text-red-600" placeholder="0.00" oninput="hitungRusakKgKeSak()" required>
                                        <span class="absolute right-4 top-10 text-sm text-gray-400 font-bold">Kg</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2">Lokasi Kerusakan (Opsional)</label>
                                    <select name="kandang_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none">
                                        <option value="" selected>Gudang Unit (Umum)</option>
                                        @if(Auth::user()->unit)
                                            @foreach(Auth::user()->unit->kandangs as $kandang)
                                                <option value="{{ $kandang->id }}">{{ $kandang->nama_kandang }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2">Alasan / Keterangan (Wajib)</label>
                                    <textarea name="keterangan" rows="1" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none" placeholder="Contoh: 1 Karung tumpah di jalan, basah kena hujan..." required></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end pt-4 border-t border-gray-100">
                                <button type="submit" class="px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-600/20 flex items-center gap-2">
                                    <i class="ph-bold ph-warning"></i> Laporkan Insiden Pakan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Riwayat Pemakaian Non-Rutin -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                        <div class="p-4 bg-gray-50 border-b border-gray-100"><h4 class="font-bold text-gray-700 text-sm">Riwayat Laporan Insiden</h4></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500"><tr><th class="px-4 py-2">Tanggal</th><th class="px-4 py-2">Pakan</th><th class="px-4 py-2 text-right">Jumlah Dilaporkan</th><th class="px-4 py-2">Ket</th></tr></thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($riwayatPakai as $log)
                                    <tr>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 font-bold">{{ $log->pakan->nama_pakan }}</td>
                                        
                                        <td class="px-4 py-2 text-right">
                                            <span class="text-red-600 font-bold">-{{ number_format($log->jumlah, 1) }} Kg</span>
                                            <span class="text-gray-400 text-xs ml-1">({{ number_format($log->jumlah / 40, 1) }} Sak)</span>
                                        </td>
                                        
                                        <td class="px-4 py-2 text-xs text-gray-500 italic">{{ $log->keterangan }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400 italic">Belum ada data pakan rusak.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ================================================= -->
    <!-- MODAL OPNAME BLIND (TANPA INFO STOK SISTEM) -->
    <!-- ================================================= -->
    <div id="modalOpname" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeOpnameModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form action="{{ route('mandor.pakan.stock_opname') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pb-6 pt-6">
                            <h3 class="text-xl font-bold leading-6 text-gray-900 mb-2 flex items-center gap-2">
                                <i class="ph-fill ph-clipboard-text text-blue-600"></i> Lapor Stok Fisik
                            </h3>
                            <p class="text-xs text-gray-500 mb-6">Laporkan jumlah karung/kilogram fisik yang ada di gudang saat ini. Harap hitung dengan teliti.</p>
                            
                            <input type="hidden" name="pakan_id" id="opnamePakanId">
                            
                            <div class="space-y-5">
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide mb-1">Merek / Jenis Pakan</p>
                                    <p class="font-bold text-blue-800 text-xl" id="opnameNamaPakan">-</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-gray-500 mb-1">Stok Fisik (Sak)</label>
                                        <input type="number" step="0.01" id="opnameFisikSak" class="w-full px-4 py-3 border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800" placeholder="0" oninput="hitungOpnameSak()">
                                        <span class="absolute right-4 top-9 text-xs text-gray-400 font-bold">Sak</span>
                                    </div>
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-gray-500 mb-1">Total Fisik (Kg) <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" name="stok_fisik" id="opnameFisikKg" class="w-full px-4 py-3 border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-blue-700" placeholder="0.00" oninput="hitungOpnameKg()" required>
                                        <span class="absolute right-4 top-9 text-xs text-blue-400 font-bold">Kg</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2">Keterangan / Temuan Khusus</label>
                                    <input type="text" name="keterangan" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Isi jika ada catatan tambahan untuk admin pusat..." required>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-blue-700 transition">Kirim Laporan</button>
                            <button type="button" onclick="closeOpnameModal()" class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Ditambahkan SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        // Konfirmasi SweetAlert2 untuk Terima Pakan
        function confirmTerima(formId) {
            Swal.fire({
                title: 'Terima Kiriman?',
                text: "Pastikan fisik barang sudah diterima sesuai jumlah di surat jalan.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Terima!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // Kalkulator Tab Pakan Rusak
        function hitungRusakSakKeKg() {
            let sak = parseFloat(document.getElementById('inputRusakSak').value) || 0;
            let kg = sak * 40; 
            document.getElementById('inputRusakKg').value = kg > 0 ? kg.toFixed(2) : '';
        }

        function hitungRusakKgKeSak() {
            let kg = parseFloat(document.getElementById('inputRusakKg').value) || 0;
            let sak = kg / 40;
            document.getElementById('inputRusakSak').value = sak > 0 ? sak.toFixed(2) : '';
        }

        // [BARU] Kalkulator Modal Opname
        function hitungOpnameSak() {
            let sak = parseFloat(document.getElementById('opnameFisikSak').value) || 0;
            let kg = sak * 40; 
            document.getElementById('opnameFisikKg').value = kg > 0 ? kg.toFixed(2) : '';
        }

        function hitungOpnameKg() {
            let kg = parseFloat(document.getElementById('opnameFisikKg').value) || 0;
            let sak = kg / 40;
            document.getElementById('opnameFisikSak').value = sak > 0 ? sak.toFixed(2) : '';
        }

        // Update fungsi modal (hapus parsing stok)
        function openOpnameModal(id, nama) {
            document.getElementById('opnamePakanId').value = id;
            document.getElementById('opnameNamaPakan').innerText = nama;
            
            // Reset input field setiap kali modal dibuka
            document.getElementById('opnameFisikSak').value = '';
            document.getElementById('opnameFisikKg').value = '';

            document.getElementById('modalOpname').classList.remove('hidden');
        }

        function closeOpnameModal() {
            document.getElementById('modalOpname').classList.add('hidden');
        }
    </script>
</body>
</html>