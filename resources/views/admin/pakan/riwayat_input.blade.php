<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pakan - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ activeTab: 'pemakaian', showModalUsage: false }">

    <div class="flex h-screen overflow-hidden relative">
        @include('admin.sidebar')
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Pakan</span><i class="ph-bold ph-caret-right"></i><span>Pusat Data</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Riwayat & Distribusi Pakan</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg"><i class="ph-bold ph-list text-2xl"></i></button>
                    
                    <!-- Tombol Kirim ke Unit -->
                    <button @click="showModalUsage = true" class="hidden md:flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20">
                        <i class="ph-bold ph-truck"></i> Kirim ke Unit
                    </button>
                </div>
            </header>

            <div class="p-4 md:p-8 w-full">
                @if(session('success')) <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 font-bold flex items-center gap-2 shadow-sm"><i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}</div> @endif
                @if(session('error')) <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 font-bold flex items-center gap-2 shadow-sm"><i class="ph-fill ph-warning-circle text-xl"></i> {{ session('error') }}</div> @endif

                <!-- Tabs Menu -->
                <div class="flex gap-2 mb-6 border-b border-gray-200 pb-1 overflow-x-auto">
                    <!-- Tab 1: Pemakaian (Laporan Mandor) -->
                    <button @click="activeTab = 'pemakaian'" 
                            :class="activeTab === 'pemakaian' ? 'border-b-2 border-red-600 text-red-700 bg-red-50' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 font-bold text-sm transition flex items-center gap-2 rounded-t-lg whitespace-nowrap">
                        <i class="ph-bold ph-chart-bar"></i> Laporan Pemakaian
                    </button>
                    
                    <!-- Tab 2: Distribusi (Pengiriman Admin) -->
                    <button @click="activeTab = 'distribusi'" 
                            :class="activeTab === 'distribusi' ? 'border-b-2 border-blue-600 text-blue-700 bg-blue-50' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 font-bold text-sm transition flex items-center gap-2 rounded-t-lg whitespace-nowrap">
                        <i class="ph-bold ph-truck"></i> Status Pengiriman
                    </button>
                    
                    <!-- Tab 3: Stok Masuk (Pusat) -->
                    <button @click="activeTab = 'masuk'" 
                            :class="activeTab === 'masuk' ? 'border-b-2 border-green-600 text-green-700 bg-green-50' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 font-bold text-sm transition flex items-center gap-2 rounded-t-lg whitespace-nowrap">
                        <i class="ph-bold ph-warehouse"></i> Stok Masuk (Pusat)
                    </button>
                </div>

                <!-- Filter Global -->
                <form action="{{ route('admin.pakan.riwayat') }}" method="GET" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative group"><span class="absolute left-3 top-2.5 text-gray-400 text-xs font-bold">DARI</span><input type="date" name="start_date" value="{{ request('start_date') }}" class="pl-12 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none"></div>
                        <div class="relative group"><span class="absolute left-3 top-2.5 text-gray-400 text-xs font-bold">SAMPAI</span><input type="date" name="end_date" value="{{ request('end_date') }}" class="pl-14 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none"></div>
                        
                        <!-- Filter Unit (Khusus Tab Pemakaian) -->
                        <div class="relative group" x-show="activeTab === 'pemakaian'">
                            <select name="unit_id" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none cursor-pointer">
                                <option value="">Semua Unit</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">Filter</button>
                    </div>
                </form>

                <!-- KONTEN TAB 1: LAPORAN PEMAKAIAN (Dari Mandor) -->
                <div x-show="activeTab === 'pemakaian'" x-transition>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-red-50/20"><h3 class="font-bold text-lg text-gray-800">Laporan Harian Mandor</h3><p class="text-xs text-gray-500">Pakan yang digunakan di kandang.</p></div>
                        <div class="overflow-x-auto table-container">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3">Mandor</th>
                                        <th class="px-6 py-3">Unit & Kandang</th>
                                        <th class="px-6 py-3">Jenis Pakan</th>
                                        <th class="px-6 py-3 text-right">Jumlah Pakai</th>
                                        <th class="px-6 py-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($riwayatPakai as $log)
                                        @if($log->jenis_mutasi == 'pemakaian')
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-3">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                            <td class="px-6 py-3 font-bold text-gray-700">{{ $log->user->name ?? '-' }}</td>
                                            <td class="px-6 py-3">
                                                <span class="font-bold text-gray-800">{{ $log->kandang->unit->nama_unit ?? ($log->unitAsal->nama_unit ?? '-') }}</span> 
                                                <span class="text-xs text-gray-500">({{ $log->kandang->nama_kandang ?? '-' }})</span>
                                            </td>
                                            <td class="px-6 py-3">{{ $log->pakan->nama_pakan }}</td>
                                            
                                            <!-- UPDATE: Menampilkan Sisa Stok Unit & Tambahan info Sak -->
                                            <td class="px-6 py-3 text-right">
                                                <div class="font-bold text-red-600 text-base">-{{ number_format($log->jumlah, 1) }} Kg</div>
                                                <div class="text-[10px] text-gray-400 font-medium">({{ number_format($log->jumlah / 40, 1) }} Sak)</div>
                                                
                                                @php
                                                    // Ambil sisa stok unit TERKINI (Realtime)
                                                    $unitId = $log->dari_unit_id ?? ($log->kandang->unit_id ?? null);
                                                    $sisaStok = 0;
                                                    if($unitId) {
                                                        $sisaStok = \App\Models\UnitPakanStock::where('unit_id', $unitId)
                                                                        ->where('pakan_id', $log->pakan_id)
                                                                        ->value('jumlah_stok') ?? 0;
                                                    }
                                                @endphp
                                                
                                                @if($unitId)
                                                    <div class="text-[10px] text-gray-500 mt-1 font-medium bg-gray-50 inline-block px-1.5 py-0.5 rounded border border-gray-200">
                                                        Sisa: {{ number_format($sisaStok, 1) }} Kg ({{ number_format($sisaStok / 40, 1) }} Sak)
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-6 py-3 text-xs text-gray-500 max-w-xs truncate">{{ $log->keterangan }}</td>
                                        </tr>
                                        @endif
                                    @empty
                                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data pemakaian.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100">{{ $riwayatPakai->appends(request()->query())->links() }}</div>
                    </div>
                </div>

                <!-- KONTEN TAB 2: STATUS PENGIRIMAN (Admin ke Unit) -->
                <div x-show="activeTab === 'distribusi'" x-cloak>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-blue-50/20"><h3 class="font-bold text-lg text-gray-800">Log Pengiriman (Distribusi)</h3><p class="text-xs text-gray-500">Status kiriman pakan ke unit.</p></div>
                        <div class="overflow-x-auto table-container">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3">Tanggal Kirim</th>
                                        <th class="px-6 py-3">Tujuan</th>
                                        <th class="px-6 py-3">Pakan</th>
                                        <th class="px-6 py-3 text-center">Jumlah</th>
                                        <th class="px-6 py-3 text-center">Status</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatDistribusi as $log)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                        <td class="px-6 py-3 font-bold text-gray-800">{{ $log->unitTujuan->nama_unit ?? '-' }}</td>
                                        <td class="px-6 py-3">{{ $log->pakan->nama_pakan }}</td>
                                        
                                        <!-- [UPDATE] Info Sak -->
                                        <td class="px-6 py-3 text-center">
                                            <div class="font-bold text-blue-600">{{ number_format($log->jumlah, 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-500 font-medium">({{ number_format($log->jumlah / 40, 1) }} Sak)</div>
                                        </td>
                                        
                                        <td class="px-6 py-3 text-center">
                                            @if($log->status == 'pending_terima')
                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold border border-yellow-200 animate-pulse">Menunggu Mandor</span>
                                            @else
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold border border-green-200">Diterima</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.pakan.surat_jalan', $log->id) }}" target="_blank" class="text-blue-600 hover:underline text-xs font-bold flex items-center gap-1"><i class="ph-bold ph-printer"></i> Surat Jalan</a>
                                                @if($log->status == 'pending_terima')
                                                <form action="{{ route('admin.pakan.delete_usage', $log->id) }}" method="POST" onsubmit="return confirm('Batalkan pengiriman?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 ml-2" title="Batalkan"><i class="ph-bold ph-x-circle text-lg"></i></button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada pengiriman.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100">{{ $riwayatDistribusi->links() }}</div>
                    </div>
                </div>

                <!-- TAB 3: LOG MASUK (PUSAT) -->
                <div x-show="activeTab === 'masuk'" x-cloak>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-green-50/20"><h3 class="font-bold text-lg text-gray-800">Log Stok Masuk (Pusat)</h3><p class="text-xs text-gray-500">Histori pembelian/produksi.</p></div>
                        <div class="overflow-x-auto table-container">
                             <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                    <tr><th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Pakan</th><th class="px-6 py-3">Sumber</th><th class="px-6 py-3 text-center">Jumlah Masuk</th><th class="px-6 py-3 text-center">Aksi</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatMasuk as $log)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                        <td class="px-6 py-3 font-bold">{{ $log->pakan->nama_pakan }}</td>
                                        <td class="px-6 py-3">{{ $log->sumber == 'produksi' ? 'Produksi Sendiri' : 'Supplier' }}</td>
                                        
                                        <!-- [UPDATE] Info Sak -->
                                        <td class="px-6 py-3 text-center">
                                            <div class="font-bold text-green-600">+{{ number_format($log->jumlah, 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-500 font-medium">({{ number_format($log->jumlah / 40, 1) }} Sak)</div>
                                        </td>
                                        
                                        <td class="px-6 py-3 text-center">
                                            <form action="{{ route('admin.pakan.delete_restock', $log->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus"><i class="ph-bold ph-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada stok masuk.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100">{{ $riwayatMasuk->appends(request()->query())->links() }}</div>
                    </div>
                </div>

            </div>
        </main>

        <!-- MODAL KIRIM STOK -->
        <div x-show="showModalUsage" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-data>
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModalUsage = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative z-50 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Kirim Stok ke Unit</h3>
                        <button @click="showModalUsage = false" class="text-gray-400 hover:text-red-500"><i class="ph-bold ph-x text-xl"></i></button>
                    </div>
                    <form action="{{ route('admin.pakan.store_distribution') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Pakan</label>
                            <select name="pakan_id" class="w-full border rounded-lg px-3 py-2">
                                @foreach($pakans as $o)
                                    <!-- [UPDATE] Tambahan info sisa sak di modal -->
                                    <option value="{{ $o->id }}">{{ $o->nama_pakan }} (Sisa: {{ number_format($o->stok_pusat) }} Kg / {{ number_format($o->stok_pusat / 40, 1) }} Sak)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Unit Tujuan</label>
                            <select name="unit_id" class="w-full border rounded-lg px-3 py-2" required>
                                <option value="" disabled selected>Pilih Unit...</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}">{{ $u->nama_unit }} ({{ $u->lokasi }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- [UPDATE] Dual Input Sak & Kg pada Modal Pengiriman -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jml Kirim (Sak)</label>
                                <input type="number" step="0.01" id="inputKirimSak" placeholder="0" class="w-full border rounded-lg px-3 py-2 font-bold text-blue-600 outline-none focus:ring-2 focus:ring-blue-500" oninput="hitungKirimSak()">
                                <span class="absolute right-3 top-7 text-xs text-gray-400 font-bold">Sak</span>
                            </div>
                            <div class="relative">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Total (Kg) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="jumlah" id="inputKirimKg" placeholder="0.00" class="w-full border rounded-lg px-3 py-2 font-bold text-blue-800 outline-none focus:ring-2 focus:ring-blue-500" oninput="hitungKirimKg()" required>
                                <span class="absolute right-3 top-7 text-xs text-gray-400 font-bold">Kg</span>
                            </div>
                        </div>

                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1 mt-2">Tanggal Kirim</label><input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2"></div>
                        
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catatan</label><textarea name="keterangan" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm resize-none"></textarea></div>
                        
                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                            <button type="button" @click="showModalUsage = false" class="px-5 py-2 rounded-xl border border-gray-300 text-sm font-bold text-gray-600 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md">Kirim Stok</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    
    <!-- Script Toggle Sidebar & Kalkulasi Sak -->
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

        // Script Kalkulator Sak <-> Kg
        function hitungKirimSak() {
            let sak = parseFloat(document.getElementById('inputKirimSak').value) || 0;
            document.getElementById('inputKirimKg').value = sak > 0 ? (sak * 40).toFixed(2) : '';
        }

        function hitungKirimKg() {
            let kg = parseFloat(document.getElementById('inputKirimKg').value) || 0;
            document.getElementById('inputKirimSak').value = kg > 0 ? (kg / 40).toFixed(2) : '';
        }
    </script>
</body>
</html>