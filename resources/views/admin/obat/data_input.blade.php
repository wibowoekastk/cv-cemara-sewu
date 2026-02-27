<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Stok Obat - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- SweetAlert -->
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
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ showEditModal: false, editData: { total_stok: 0, satuan: '' } }">

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
                        <span>Obat</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Data Stok</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Rekap Stok Obat</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Tombol Tambah Data -->
                    <a href="{{ route('admin.obat.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-medical-600 text-white rounded-lg text-sm font-semibold hover:bg-medical-700 transition shadow-lg shadow-medical-500/20">
                        <i class="ph-bold ph-plus"></i> Stok Masuk
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">

                @php
                    // Helper Hitung Statistik
                    $totalItem = $obats->total(); 
                    $stokMenipis = $obats->filter(fn($o) => $o->total_stok <= $o->min_stok)->count();
                    
                    // PERBAIKAN LOGIKA EXPIRED:
                    // Menghitung selisih hari. Jika 0 <= selisih <= 30, maka dihitung segera expired.
                    $nearExpired = 0; 
                    foreach($obats as $o) {
                        foreach($o->batches as $b) {
                            if($b->stok_saat_ini > 0) {
                                $expDate = \Carbon\Carbon::parse($b->tgl_kadaluarsa);
                                $daysDiff = now()->diffInDays($expDate, false);
                                
                                if($daysDiff <= 30) { 
                                    $nearExpired++;
                                }
                            }
                        }
                    }
                @endphp

                <!-- 1. Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Total Item -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Item</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalItem }} <span class="text-sm font-normal text-gray-400">Jenis</span></h3>
                        </div>
                        <div class="w-12 h-12 bg-medical-50 text-medical-600 rounded-xl flex items-center justify-center text-2xl">
                            <i class="ph-fill ph-first-aid-kit"></i>
                        </div>
                    </div>

                    <!-- Stok Menipis -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-gold-400">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Stok Menipis</p>
                            <h3 class="text-3xl font-bold text-gold-500 mt-1">{{ $stokMenipis }} <span class="text-sm font-normal text-gray-400">Item</span></h3>
                        </div>
                        <div class="w-12 h-12 bg-gold-50 text-gold-500 rounded-xl flex items-center justify-center text-2xl">
                            <i class="ph-fill ph-warning"></i>
                        </div>
                    </div>

                    <!-- Warning Expired -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between border-l-4">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Segera Expired</p>
                            <h3 class="text-3xl font-bold text-red-500 mt-1">{{ $nearExpired }} <span class="text-sm font-normal text-gray-400">Batch</span></h3>
                            <p class="text-[10px] text-red-400 mt-1">< 30 Hari</p>
                        </div>
                        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-2xl">
                            <i class="ph-fill ph-calendar-x"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <form action="{{ route('admin.obat.data_input') }}" method="GET" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative group">
                            <select name="kategori" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-medical-500 outline-none cursor-pointer">
                                <option value="">Semua Kategori</option>
                                <option value="Vaksin" {{ request('kategori') == 'Vaksin' ? 'selected' : '' }}>Vaksin</option>
                                <option value="Vitamin" {{ request('kategori') == 'Vitamin' ? 'selected' : '' }}>Vitamin</option>
                                <option value="Antibiotik" {{ request('kategori') == 'Antibiotik' ? 'selected' : '' }}>Antibiotik</option>
                                <option value="Disinfektan" {{ request('kategori') == 'Disinfektan' ? 'selected' : '' }}>Disinfektan</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">Filter</button>
                    </div>
                    <div class="relative w-full md:w-64">
                        <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama obat..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-medical-500 outline-none">
                    </div>
                </form>

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">#</th>
                                    <th class="px-6 py-4">Nama Produk</th>
                                    <th class="px-6 py-4">Kategori</th>
                                    <th class="px-6 py-4 text-center">Total Stok</th>
                                    <th class="px-6 py-4">Rincian Stok (FEFO)</th> 
                                    <th class="px-6 py-4 text-center sticky right-0 bg-gray-50 z-10 shadow-[-4px_0_12px_-4px_rgba(0,0,0,0.05)]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($obats as $index => $obat)
                                    <tr class="hover:bg-gray-50 transition group align-top">
                                        <td class="px-6 py-4 text-center">{{ $obats->firstItem() + $index }}</td>
                                        
                                        <!-- Nama Produk -->
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">{{ $obat->nama_obat }}</div>
                                            <div class="text-xs text-gray-500">Min. Stok: {{ $obat->min_stok }} {{ $obat->satuan }}</div>
                                        </td>

                                        <!-- Kategori Badge -->
                                        <td class="px-6 py-4">
                                            @php
                                                $catColor = match($obat->jenis_obat) {
                                                    'Vaksin' => 'bg-medical-50 text-medical-700 border-medical-100',
                                                    'Vitamin' => 'bg-gold-50 text-gold-700 border-gold-100',
                                                    'Antibiotik' => 'bg-red-50 text-red-700 border-red-100',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs font-bold border {{ $catColor }}">
                                                {{ $obat->jenis_obat ?? 'Umum' }}
                                            </span>
                                        </td>

                                        <!-- Stok Total -->
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-bold text-base {{ $obat->total_stok <= $obat->min_stok ? 'text-red-600' : 'text-gray-800' }}">
                                                {{ $obat->total_stok }}
                                            </span>
                                            <span class="text-xs text-gray-500 block">{{ $obat->satuan }}</span>
                                        </td>

                                        <!-- Rincian Batch -->
                                        <td class="px-6 py-4">
                                            @if($obat->batches->where('stok_saat_ini', '>', 0)->count() > 0)
                                                <div class="flex flex-col gap-1.5">
                                                    @php
                                                        $groupedBatches = $obat->batches->where('stok_saat_ini', '>', 0)
                                                                            ->sortBy('tgl_kadaluarsa')
                                                                            ->groupBy('tgl_kadaluarsa')
                                                                            ->take(3);
                                                    @endphp

                                                    @foreach($groupedBatches as $date => $batches)
                                                        @php
                                                            $expDate = \Carbon\Carbon::parse($date);
                                                            $daysLeft = now()->diffInDays($expDate, false);
                                                            $totalStokGroup = $batches->sum('stok_saat_ini');
                                                            
                                                            $bgClass = $daysLeft < 0 ? 'bg-red-100 text-red-700 border-red-200' : 
                                                                      ($daysLeft < 30 ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-gray-50 text-gray-600 border-gray-200');
                                                        @endphp
                                                        
                                                        <div class="flex items-center justify-between text-xs p-1.5 rounded border {{ $bgClass }}">
                                                            <div class="flex items-center gap-1.5 font-bold">
                                                                <i class="ph-fill {{ $daysLeft < 0 ? 'ph-warning-octagon' : 'ph-calendar-check' }}"></i>
                                                                Exp: {{ $expDate->format('d M y') }}
                                                            </div>
                                                            <div class="font-mono font-bold">
                                                                {{ $totalStokGroup }} {{ $obat->satuan }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic bg-gray-50 px-2 py-1 rounded">Stok Kosong</span>
                                            @endif
                                        </td>

                                        <!-- AKSI -->
                                        <td class="px-6 py-4 text-center sticky right-0 bg-white group-hover:bg-gray-50 z-10 shadow-[-4px_0_12px_-4px_rgba(0,0,0,0.05)]">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Tombol Edit: Kirim total_stok secara manual di sini -->
                                                <button onclick="openEditModal({{ json_encode(array_merge($obat->toArray(), ['total_stok' => $obat->total_stok])) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Master Obat">
                                                    <i class="ph-bold ph-pencil-simple text-lg"></i>
                                                </button>
                                                
                                                <form action="{{ route('admin.obat.delete', $obat->id) }}" method="POST" class="delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDelete(this)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Data">
                                                        <i class="ph-bold ph-trash text-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">
                                            Belum ada data obat. Silakan input stok dulu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-4 border-t border-gray-100">
                        {{ $obats->links() }}
                    </div>
                </div>

            </div>
        </main>

        <!-- ================= MODAL EDIT (MASTER DATA) ================= -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showEditModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md relative z-50 transform transition-all">
                    
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center rounded-t-2xl">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Edit Informasi Produk</h3>
                            <p class="text-xs text-gray-500">Ubah data master obat/vaksin</p>
                        </div>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-red-500"><i class="ph-bold ph-x text-xl"></i></button>
                    </div>

                    <form id="editForm" method="POST">
                        @csrf @method('PUT')
                        <div class="p-6 space-y-5">
                            
                            <!-- WARNING BOX -->
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex gap-3 items-start">
                                <i class="ph-fill ph-info text-blue-500 text-xl mt-0.5"></i>
                                <div class="text-xs text-blue-700">
                                    <p class="font-bold mb-1">Informasi Stok</p>
                                    <p>Menu ini hanya untuk edit nama/kategori. Untuk menambah/mengurangi stok fisik, gunakan menu <strong>Stok Masuk</strong> atau <strong>Catat Pemakaian</strong>.</p>
                                </div>
                            </div>

                            <!-- Read Only Total Stok -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Total Stok Saat Ini</label>
                                <div class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-600 font-bold text-sm" x-text="editData.total_stok + ' ' + editData.satuan"></div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Obat</label>
                                <input type="text" name="nama_obat" x-model="editData.nama_obat" class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-medical-500 outline-none text-sm font-medium" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                                <select name="jenis_obat" x-model="editData.jenis_obat" class="w-full border rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-medical-500 outline-none text-sm cursor-pointer">
                                    <option value="Vaksin">Vaksin</option>
                                    <option value="Vitamin">Vitamin</option>
                                    <option value="Antibiotik">Antibiotik</option>
                                    <option value="Disinfektan">Disinfektan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Satuan</label>
                                    <select name="satuan" x-model="editData.satuan" class="w-full border rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-medical-500 outline-none text-sm">
                                        <option value="Botol">Botol</option>
                                        <option value="Sachet">Sachet</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Liter">Liter</option>
                                        <option value="Pcs">Pcs</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Batas Minimum (Alert)</label>
                                    <input type="number" name="min_stok" x-model="editData.min_stok" class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-medical-500 outline-none text-sm">
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="showEditModal = false" class="px-5 py-2 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-white text-sm">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 text-sm shadow-md">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
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

        // Logic Modal Edit
        function openEditModal(data) {
            const alpineData = Alpine.$data(document.querySelector('[x-data]'));
            alpineData.editData = {
                id: data.id,
                nama_obat: data.nama_obat,
                jenis_obat: data.jenis_obat,
                satuan: data.satuan,
                min_stok: data.min_stok,
                total_stok: data.total_stok // Data untuk display readonly
            };
            const form = document.getElementById('editForm');
            form.action = "{{ url('/admin/obat/update') }}/" + data.id;
            
            alpineData.showEditModal = true;
        }

        // Logic SweetAlert Delete
        function confirmDelete(button) {
            Swal.fire({
                title: 'Hapus Data Obat?',
                text: "Data Master & seluruh stok batch terkait akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#fff',
                customClass: {
                    popup: 'rounded-2xl font-sans',
                    confirmButton: 'rounded-xl px-4 py-2 font-bold',
                    cancelButton: 'rounded-xl px-4 py-2 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl font-sans' }
            });
        @endif
    </script>
</body>
</html>