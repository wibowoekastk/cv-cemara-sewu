<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Stok Pakan - Admin Panel</title>
    
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

        /* --- CSS TOOLTIP CUSTOM --- */
        .tooltip { position: relative; display: inline-block; cursor: help; }
        .tooltip .tooltip-text { visibility: hidden; width: 220px; background-color: #1f2937; color: #fff; text-align: left; border-radius: 8px; padding: 10px; position: absolute; z-index: 50; bottom: 125%; left: 50%; margin-left: -110px; opacity: 0; transition: opacity 0.3s, transform 0.3s; transform: translateY(10px); font-size: 11px; line-height: 1.4; font-weight: normal; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); pointer-events: none; }
        .tooltip .tooltip-text::after { content: ""; position: absolute; top: 100%; left: 50%; margin-left: -5px; border-width: 5px; border-style: solid; border-color: #1f2937 transparent transparent transparent; }
        .tooltip:hover .tooltip-text { visibility: visible; opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ showEditModal: false, editData: {} }">

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
                        <span>Pakan</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Data Stok</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Stok Gudang Pakan (Pusat)</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg"><i class="ph-bold ph-list text-2xl"></i></button>
                    <a href="{{ route('admin.pakan.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20"><i class="ph-bold ph-plus"></i> Stok Masuk Baru</a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                    <i class="ph-fill ph-check-circle text-xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <!-- Filter Section -->
                <form method="GET" action="{{ route('admin.pakan.data_input') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative group">
                            <select name="jenis" onchange="this.form.submit()" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none cursor-pointer">
                                <option value="">Semua Kategori</option>
                                <option value="Bahan Baku" {{ request('jenis') == 'Bahan Baku' ? 'selected' : '' }}>Bahan Baku</option>
                                <option value="Konsentrat" {{ request('jenis') == 'Konsentrat' ? 'selected' : '' }}>Konsentrat</option>
                                <option value="Pakan Jadi" {{ request('jenis') == 'Pakan Jadi' ? 'selected' : '' }}>Pakan Jadi</option>
                                <option value="Suplemen" {{ request('jenis') == 'Suplemen' ? 'selected' : '' }}>Suplemen</option>
                            </select>
                            <i class="ph-bold ph-caret-down absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                    <div class="relative w-full md:w-64">
                        <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pakan..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cemara-500 outline-none">
                    </div>
                </form>

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">#</th>
                                    <th class="px-6 py-4">Nama Pakan</th>
                                    <th class="px-6 py-4">Kategori</th>
                                    
                                    <!-- UPDATE: Judul Kolom Disesuaikan -->
                                    <th class="px-6 py-4 text-center">Stok Pusat</th>
                                    <th class="px-6 py-4 text-center">Min. Stok</th>
                                    
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center sticky right-0 bg-gray-50 z-10 shadow-[-4px_0_12px_-4px_rgba(0,0,0,0.05)]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                
                                @forelse($pakans as $index => $pakan)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-6 py-4 text-center">{{ $pakans->firstItem() + $index }}</td>
                                    
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        {{ $pakan->nama_pakan }}
                                        <!-- Penanda Ternak (Optional jika ada kolom jenis_ternak) -->
                                        @if(isset($pakan->jenis_ternak))
                                            <span class="ml-2 text-[10px] px-1.5 py-0.5 rounded border {{ $pakan->jenis_ternak == 'ayam' ? 'bg-green-50 text-green-600 border-green-200' : 'bg-red-50 text-red-600 border-red-200' }}">
                                                {{ ucfirst($pakan->jenis_ternak) }}
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- KOLOM KATEGORI DENGAN TOOLTIP -->
                                    <td class="px-6 py-4">
                                        <div class="tooltip">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600 border border-transparent hover:border-gray-300 hover:bg-white hover:shadow-sm transition duration-200">
                                                {{ $pakan->jenis_pakan }}
                                            </span>
                                            <!-- Isi Tooltip -->
                                            <div class="tooltip-text">
                                                <div class="flex items-center gap-1 mb-1 border-b border-gray-600 pb-1">
                                                    <i class="ph-bold ph-info text-gold-400"></i>
                                                    <span class="font-bold text-gold-400">Deskripsi</span>
                                                </div>
                                                <p class="text-gray-200">
                                                    {{ $pakan->deskripsi ?? 'Tidak ada data komposisi.' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- KOLOM STOK GUDANG (LOGIKA SAK/KG) -->
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $stok = $pakan->stok_gudang ?? $pakan->stok_pusat ?? 0;
                                        @endphp
                                        
                                        <div class="flex flex-col items-center">
                                            <span class="font-bold text-lg {{ $stok <= $pakan->min_stok ? 'text-red-600' : 'text-cemara-700' }}">
                                                {{ number_format($stok, 1) }} Kg
                                            </span>
                                            <span class="text-[10px] text-gray-500 font-medium">
                                                ({{ number_format($stok / 40, 1) }} Sak)
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- KOLOM MIN STOK -->
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        {{ number_format($pakan->min_stok) }} {{ $pakan->satuan }}
                                    </td>
                                    
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $stok = $pakan->stok_gudang ?? $pakan->stok_pusat ?? 0;
                                        @endphp
                                        @if($stok <= 0)
                                            <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-full text-xs font-bold border border-gray-300">Habis</span>
                                        @elseif($stok <= $pakan->min_stok)
                                            <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold border border-red-200 animate-pulse">Menipis</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold border border-green-200">Aman</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 text-center sticky right-0 bg-white group-hover:bg-gray-50 z-10 shadow-[-4px_0_12px_-4px_rgba(0,0,0,0.05)]">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Tombol Edit (Open Modal) -->
                                            <button @click="openEditModal({{ json_encode($pakan) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Master">
                                                <i class="ph-bold ph-pencil-simple"></i>
                                            </button>
                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('admin.pakan.delete', $pakan->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete(this);">
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
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400 italic">
                                        Belum ada data pakan. Silakan input stok masuk baru.
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-4 border-t border-gray-100">
                        {{ $pakans->appends(request()->query())->links() }}
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL EDIT MASTER PAKAN -->
    <div x-show="showEditModal" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all"
             @click.away="showEditModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-lg text-gray-800">Edit Data Pakan</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="ph-bold ph-x text-xl"></i>
                </button>
            </div>

            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Pakan</label>
                    <input type="text" name="nama_pakan" x-model="editData.nama_pakan" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Batas Minimum Stok (Warning)</label>
                    <div class="relative">
                        <input type="number" name="min_stok" x-model="editData.min_stok" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                        <span class="absolute right-4 top-2 text-xs text-gray-400 font-bold" x-text="editData.satuan"></span>
                    </div>
                </div>

                <!-- Deskripsi/Komposisi -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Keterangan / Komposisi</label>
                    <textarea name="deskripsi" x-model="editData.deskripsi" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none resize-none" placeholder="Masukkan detail komposisi..."></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-cemara-900 text-white rounded-lg font-bold hover:bg-cemara-800 transition shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script SweetAlert & Modal -->
    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: 'Hapus Master Pakan?',
                text: "Data stok dan riwayat yang terkait juga akan dihapus.",
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

        // AlpineJS Helper untuk Modal Edit
        document.addEventListener('alpine:init', () => {
            Alpine.data('modalHandler', () => ({
                showEditModal: false,
                editData: {},
            }))
        });

        function openEditModal(data) {
            const root = document.querySelector('[x-data]');
            const scope = Alpine.$data(root);
            
            scope.editData = data; // Isi data ke form modal
            
            const form = document.getElementById('editForm');
            form.action = "{{ url('/admin/pakan/update') }}/" + data.id; // Update URL Action

            scope.showEditModal = true;
        }
    </script>
</body>
</html>