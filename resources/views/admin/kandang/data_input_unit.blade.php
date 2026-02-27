<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Input Unit - Admin Panel</title>
    
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
    <style>
        .table-container::-webkit-scrollbar { height: 8px; }
        .table-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        th, td { white-space: nowrap; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans" 
      x-data="{ 
          showEditModal: false, editType: '', editData: {},
          showChickInModal: false, chickInData: {},
          showAfkirModal: false, afkirData: {}
      }">

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
                        <span>Data Unit</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Data Master Unit & Kandang</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Tombol Tambah Data -->
                    <a href="{{ route('admin.kandang.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20">
                        <i class="ph-bold ph-plus"></i> Tambah Baru
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- Filter Section -->
                <form method="GET" action="{{ route('admin.kandang.data_input') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative group">
                            <select name="lokasi" onchange="this.form.submit()" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none cursor-pointer">
                                <option value="Semua Lokasi">Semua Lokasi</option>
                                @foreach($units->unique('lokasi') as $u)
                                    <option value="{{ $u->lokasi }}" {{ request('lokasi') == $u->lokasi ? 'selected' : '' }}>{{ $u->lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="relative w-full md:w-64">
                        <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari unit atau kandang..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cemara-500 outline-none">
                    </div>
                </form>

                <!-- ALERT ERROR/SUCCESS -->
                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="ph-fill ph-check-circle text-xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif
                
                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="ph-fill ph-warning-circle text-xl"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- TABEL 1: DATA UNIT -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                         <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <i class="ph-fill ph-house-line text-cemara-600"></i> Data Unit Farm
                        </h3>
                    </div>
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">#</th>
                                    <th class="px-6 py-4">Tgl Input</th>
                                    <th class="px-6 py-4">Nama Unit</th>
                                    <th class="px-6 py-4">Lokasi Unit</th>
                                    <th class="px-6 py-4 text-center">Jumlah Kandang</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($units as $index => $unit)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-center">{{ $units->firstItem() + $index }}</td>
                                    <td class="px-6 py-4">{{ $unit->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $unit->nama_unit }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600">{{ $unit->lokasi }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-cemara-600">{{ $unit->kandangs_count }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Tombol Edit -->
                                            <button @click="openEditModal('unit', {{ $unit }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                                <i class="ph-bold ph-pencil-simple"></i>
                                            </button>
                                            
                                            <!-- Tombol Delete -->
                                            <form action="{{ route('admin.kandang.delete_unit', $unit->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete(this);">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                    <i class="ph-bold ph-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-400">Belum ada data unit.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $units->appends(['kandangs_page' => request('kandangs_page')])->links() }}
                    </div>
                </div>

                <!-- TABEL 2: DATA KANDANG (WITH SIKLUS) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                         <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <i class="ph-fill ph-warehouse text-gold-600"></i> Data Kandang & Siklus
                        </h3>
                    </div>
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">#</th>
                                    <th class="px-6 py-4">Nama Kandang</th>
                                    <th class="px-6 py-4">Status Siklus</th>
                                    <th class="px-6 py-4">Batch Info</th>
                                    <th class="px-6 py-4 text-center">Kapasitas</th>
                                    <th class="px-6 py-4 text-center">Populasi Aktif</th>
                                    <th class="px-6 py-4 text-center">Usia</th>
                                    <th class="px-6 py-4 text-center sticky right-0 bg-gray-50 z-10">Manajemen Siklus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($kandangs as $index => $kandang)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-6 py-4 text-center">{{ $kandangs->firstItem() + $index }}</td>
                                    
                                    <!-- Nama Kandang -->
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $kandang->nama_kandang }}</div>
                                        <div class="text-xs text-gray-400">{{ $kandang->unit->nama_unit ?? '-' }} ({{ $kandang->unit->lokasi ?? '-' }})</div>
                                    </td>
                                    
                                    <!-- Status Badge -->
                                    <td class="px-6 py-4">
                                        @if($kandang->siklusAktif)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                                Aktif Produksi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold border border-gray-200">
                                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full"></span>
                                                Kosong
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Batch Info (Tgl Masuk) -->
                                    <td class="px-6 py-4 text-gray-600">
                                        @if($kandang->siklusAktif)
                                            <div class="font-medium text-gray-800">In: {{ $kandang->siklusAktif->tanggal_chick_in->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $kandang->siklusAktif->jenis_ayam }}</div>
                                        @else
                                            <span class="text-gray-400 italic text-xs">Menunggu Chick-In</span>
                                        @endif
                                    </td>

                                    <!-- Kapasitas -->
                                    <td class="px-6 py-4 text-center text-gray-700">{{ number_format($kandang->kapasitas) }}</td>
                                    
                                    <!-- Populasi Saat Ini -->
                                    <td class="px-6 py-4 text-center font-bold text-gray-800">
                                        {{ number_format($kandang->stok_saat_ini) }}
                                    </td>
                                    
                                    <!-- Usia -->
                                    <td class="px-6 py-4 text-center text-gray-600">
                                        @if($kandang->siklusAktif)
                                            <span class="font-bold text-cemara-700">{{ $kandang->siklusAktif->umur_sekarang }} Mgg</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <!-- AKSI / MANAJEMEN SIKLUS -->
                                    <td class="px-6 py-4 text-center sticky right-0 bg-white group-hover:bg-gray-50 z-10">
                                        <div class="flex items-center justify-center gap-2">
                                            
                                            <!-- LOGIKA TOMBOL BERDASARKAN STATUS SIKLUS -->
                                            @if($kandang->siklusAktif)
                                                <!-- Tombol AFKIR (Hanya muncul jika kandang Aktif) -->
                                                <button @click="openAfkirModal({{ $kandang }})" class="flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-xs font-bold transition border border-red-200">
                                                    <i class="ph-bold ph-stop-circle"></i> Afkir
                                                </button>
                                            @else
                                                <!-- Tombol CHICK-IN (Hanya muncul jika kandang Kosong) -->
                                                <button @click="openChickInModal({{ $kandang }})" class="flex items-center gap-1 px-3 py-1.5 bg-cemara-600 text-white hover:bg-cemara-700 rounded-lg text-xs font-bold transition shadow-sm">
                                                    <i class="ph-bold ph-egg-crack"></i> Chick-In
                                                </button>
                                            @endif

                                            <!-- Tombol Edit Master Kandang -->
                                            <button @click="openEditModal('kandang', {{ $kandang }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Data Fisik Kandang">
                                                <i class="ph-bold ph-pencil-simple"></i>
                                            </button>

                                            <!-- Tombol Delete -->
                                            <form action="{{ route('admin.kandang.delete_kandang', $kandang->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete(this);">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Kandang">
                                                    <i class="ph-bold ph-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-400">Belum ada data kandang.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $kandangs->appends(['units_page' => request('units_page')])->links() }}
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ============================================= -->
    <!-- MODAL EDIT DATA (UNIT & KANDANG FISIK) -->
    <!-- ============================================= -->
    <div x-show="showEditModal" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition.opacity>
        
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all"
             @click.away="showEditModal = false">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-lg text-gray-800" x-text="editType === 'unit' ? 'Edit Data Unit' : 'Edit Data Fisik Kandang'"></h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="ph-bold ph-x text-xl"></i>
                </button>
            </div>

            <div class="p-6">
                <!-- FORM EDIT UNIT -->
                <form x-show="editType === 'unit'" method="POST" :action="`{{ url('admin/kandang/unit/update') }}/${editData.id}`">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Unit</label>
                            <input type="text" name="nama_unit" x-model="editData.nama_unit" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lokasi</label>
                            <input type="text" name="lokasi" x-model="editData.lokasi" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-cemara-600 text-white rounded-lg font-bold hover:bg-cemara-700 transition">Simpan</button>
                    </div>
                </form>

                <!-- FORM EDIT KANDANG (Hanya Fisik) -->
                <form x-show="editType === 'kandang'" method="POST" :action="`{{ url('admin/kandang/update') }}/${editData.id}`">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div class="bg-yellow-50 text-yellow-800 p-3 rounded-lg text-xs mb-3 border border-yellow-200">
                            <i class="ph-bold ph-info"></i> Edit ini hanya mengubah data fisik kandang. Untuk mengubah populasi/ayam, gunakan tombol Chick-In/Afkir.
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kandang</label>
                            <input type="text" name="nama_kandang" x-model="editData.nama_kandang" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gold-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kapasitas Maksimal</label>
                            <input type="number" name="kapasitas" x-model="editData.kapasitas" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gold-500 outline-none">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-gold-500 text-white rounded-lg font-bold hover:bg-gold-600 transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- MODAL CHICK-IN BARU -->
    <!-- ============================================= -->
    <div x-show="showChickInModal" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition.opacity>
        
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all"
             @click.away="showChickInModal = false">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-cemara-50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-cemara-100 flex items-center justify-center text-cemara-600">
                        <i class="ph-bold ph-egg-crack text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">Chick-In Baru</h3>
                        <p class="text-xs text-gray-500" x-text="`Kandang: ${chickInData.nama_kandang}`"></p>
                    </div>
                </div>
                <button @click="showChickInModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="ph-bold ph-x text-xl"></i>
                </button>
            </div>

            <div class="p-6">
                <!-- FORM CHICK IN -->
                <!-- Menggunakan Dynamic Action URL -->
                <form method="POST" :action="`{{ url('admin/kandang') }}/${chickInData.id}/chick-in`">
                    @csrf
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Masuk</label>
                                <input type="date" name="tanggal_chick_in" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Ayam</label>
                                <select name="jenis_ayam" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                                    <option value="Pullet">Pullet (Dara)</option>
                                    <option value="DOC">DOC (Anakan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Populasi Awal (Ekor)</label>
                                <input type="number" name="populasi_awal" placeholder="Contoh: 1000" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Umur Masuk (Minggu)</label>
                                <input type="number" name="umur_awal_minggu" value="18" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Vendor / Asal Bibit</label>
                            <input type="text" name="vendor_bibit" placeholder="Nama PT / Farm Asal" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="showChickInModal = false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-cemara-600 text-white rounded-lg font-bold hover:bg-cemara-700 transition flex items-center gap-2">
                            <i class="ph-bold ph-check"></i> Mulai Siklus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- MODAL AFKIR KANDANG -->
    <!-- ============================================= -->
    <div x-show="showAfkirModal" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition.opacity>
        
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all"
             @click.away="showAfkirModal = false">
            
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="ph-bold ph-warning-octagon text-3xl text-red-600"></i>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Afkir Kandang</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Anda akan menutup siklus produksi untuk <b x-text="afkirData.nama_kandang"></b>.<br>
                    Sisa populasi saat ini (<b x-text="afkirData.stok_saat_ini"></b> ekor) akan dicatat sebagai hasil akhir. Tindakan ini tidak dapat dibatalkan.
                </p>

                <form method="POST" :action="`{{ url('admin/kandang') }}/${afkirData.id}/afkir`">
                    @csrf
                    <div class="flex gap-3 justify-center">
                        <button type="button" @click="showAfkirModal = false" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                            Batalkan
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition flex items-center gap-2 shadow-lg shadow-red-600/20">
                            <i class="ph-bold ph-stop-circle"></i> Ya, Afkir Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script SweetAlert & Modal Logic -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi Confirm Delete
        function confirmDelete(form) {
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        }

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

        // AlpineJS Helper Functions
        document.addEventListener('alpine:init', () => {
            Alpine.data('modalHandler', () => ({
                // ... logic moved to x-data in body
            }))
        });

        // Global functions to trigger modals from onClick
        function openEditModal(type, data) {
            const root = document.querySelector('[x-data]');
            const scope = Alpine.$data(root);
            scope.editType = type;
            scope.editData = data;
            scope.showEditModal = true;
        }

        function openChickInModal(data) {
            const root = document.querySelector('[x-data]');
            const scope = Alpine.$data(root);
            scope.chickInData = data;
            scope.showChickInModal = true;
        }

        function openAfkirModal(data) {
            const root = document.querySelector('[x-data]');
            const scope = Alpine.$data(root);
            scope.afkirData = data;
            scope.showAfkirModal = true;
        }
    </script>
</body>
</html>