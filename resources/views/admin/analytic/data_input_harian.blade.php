<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Input Harian - Admin Panel</title>
    
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
    <style>
        .table-container::-webkit-scrollbar { height: 8px; }
        .table-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        th, td { white-space: nowrap; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ showEditModal: false, editData: {} }">

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
                        <span>Data Input</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Rekap Data Harian</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.analytic.laporan_pdf') }}?report_type=pecah_konsumsi" target="_blank" class="hidden md:flex items-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm">
                            <i class="ph-bold ph-file-csv"></i> Rekap Telur Rusak
                        </a>
    
                        <a href="{{ route('admin.analytic.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20">
                            <i class="ph-bold ph-plus"></i> Input Baru
                        </a>
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                <!-- Filter Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <form method="GET" action="{{ route('admin.analytic.data') }}" class="flex flex-wrap gap-3 w-full md:w-auto items-center">
                        <div class="relative group">
                            <i class="ph-bold ph-calendar-blank absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none">
                        </div>
                        <span class="text-gray-400 text-sm">-</span>
                        <div class="relative group">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none">
                        </div>

                        <div class="relative group">
                            <select name="unit_id" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-cemara-500 outline-none cursor-pointer">
                                <option value="">Semua Unit</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                            Filter
                        </button>
                    </form>
                </div>

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

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-4 sticky left-0 bg-gray-50 z-20 w-12 text-center border-r border-gray-200">#</th>
                                    <th class="px-4 py-4 sticky left-12 bg-gray-50 z-20 w-28 border-r border-gray-200">Tanggal</th>
                                    <th class="px-4 py-4 sticky left-40 bg-gray-50 z-20 w-32 border-r border-gray-200">Kandang</th>
                                    
                                    <!-- Kolom Batch -->
                                    <th class="px-4 py-4 bg-gray-50 border-r border-gray-200">Batch / Siklus</th>
                                    
                                    <!-- Group Populasi -->
                                    <th class="px-4 py-4 text-center bg-red-50/50 text-red-700">Mati</th>
                                    <th class="px-4 py-4 text-center bg-red-50/50 text-red-700">Afkir</th>
                                    <th class="px-4 py-4 text-center bg-red-50/50 text-red-700">Populasi</th>
                                    
                                    <!-- Group Produksi -->
                                    <th class="px-4 py-4 text-center bg-gold-50/50 text-gold-700">Jml Telur</th>
                                    <th class="px-4 py-4 text-center bg-gold-50/50 text-gold-700">Berat/Btr</th>
                                    <th class="px-4 py-4 text-center bg-gold-50/50 text-gold-700 border-r border-gray-200">Total Kg</th>
                                    
                                    <!-- Group Performa -->
                                    <th class="px-4 py-4 text-center bg-blue-50/50 text-blue-700">HD %</th>
                                    <th class="px-4 py-4 text-center bg-blue-50/50 text-blue-700" title="Hen House Butir">HH (Btr)</th>
                                    <th class="px-4 py-4 text-center bg-blue-50/50 text-blue-700" title="Hen House Kg">HH (Kg)</th>
                                    <th class="px-4 py-4 text-center bg-blue-50/50 text-blue-700 border-r border-gray-200">FCR</th>
                                    
                                    <!-- Group Pakan -->
                                    <th class="px-4 py-4 text-center bg-cemara-50/50 text-cemara-800">Nama Pakan</th>
                                    <th class="px-4 py-4 text-center bg-cemara-50/50 text-cemara-800">Total Pakan</th>
                                    <th class="px-4 py-4 text-center bg-cemara-50/50 text-cemara-800 border-r border-gray-200">Gr/Ekor</th>

                                    <th class="px-4 py-4">Dibuat Oleh</th>
                                    <th class="px-4 py-4 text-center sticky right-0 bg-gray-50 z-20 shadow-[-4px_0_12px_-4px_rgba(0,0,0,0.1)]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($data as $index => $row)
                                <tr class="hover:bg-gray-50/80 transition group">
                                    <td class="px-4 py-3 text-center sticky left-0 bg-white z-10 border-r border-gray-100 group-hover:bg-gray-50">
                                        {{ $data->firstItem() + $index }}
                                    </td>
                                    
                                    <!-- Tanggal -->
                                    <td class="px-4 py-3 sticky left-12 bg-white z-10 border-r border-gray-100 group-hover:bg-gray-50">
                                        <div class="text-sm font-medium text-gray-700">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</div>
                                    </td>
                                    
                                    <!-- Kandang & Unit -->
                                    <td class="px-4 py-3 sticky left-40 bg-white z-10 border-r border-gray-100 group-hover:bg-gray-50 shadow-[4px_0_12px_-4px_rgba(0,0,0,0.05)]">
                                        <div class="font-bold text-gray-900">{{ $row->kandang->nama_kandang ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $row->kandang->unit->nama_unit ?? '-' }}</div>
                                    </td>

                                    <!-- Info Batch / Siklus -->
                                    <td class="px-4 py-3 border-r border-gray-100 text-xs">
                                        @if($row->siklus)
                                            @if($row->siklus->batch)
                                                <span class="block font-bold text-blue-600">{{ $row->siklus->batch->nama_batch }}</span>
                                            @else
                                                <span class="block font-bold text-blue-600">Batch {{ $row->siklus->tanggal_chick_in->format('Y') }}</span>
                                            @endif
                                            <!-- Tulisan jenis ayam dihapus di sini agar lebih bersih -->
                                        @else
                                            <span class="text-gray-300 italic text-[10px]">No Batch</span>
                                        @endif
                                    </td>
                                    
                                    <!-- KOLOM MATI DENGAN TOOLTIP -->
                                    <td class="px-4 py-3 text-center border-gray-100">
                                        <div class="group/tooltip relative inline-block">
                                            <!-- Angka Mati -->
                                            <span class="font-bold text-red-600 {{ $row->ket_mati ? 'cursor-help border-b-2 border-dotted border-red-300' : '' }}">
                                                {{ $row->mati }}
                                            </span>
                                            
                                            <!-- Tooltip (Hanya muncul jika ada keterangan) -->
                                            @if($row->ket_mati)
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 hidden group-hover/tooltip:block z-50">
                                                <div class="bg-gray-900 text-white text-xs rounded-lg p-3 shadow-xl relative text-left">
                                                    <div class="flex items-center gap-1.5 mb-1 text-gold-400 font-bold uppercase tracking-wider text-[10px]">
                                                        <i class="ph-fill ph-info"></i> Penyebab
                                                    </div>
                                                    <p class="leading-relaxed font-normal">{{ $row->ket_mati }}</p>
                                                    
                                                    <!-- Panah Kecil Bawah -->
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-center text-orange-600 font-bold">{{ $row->afkir }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-800">
                                        {{ number_format($row->populasi_awal - $row->mati - $row->afkir) }}
                                    </td>
                                    
                                    <!-- Produksi -->
                                    <td class="px-4 py-3 text-center text-gold-600 font-bold">{{ number_format($row->telur_butir) }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">
                                        {{ $row->telur_butir > 0 ? number_format(($row->telur_kg * 1000) / $row->telur_butir, 1) : 0 }} gr
                                    </td>
                                    <td class="px-4 py-3 text-center text-gold-700 font-bold border-r border-gray-100">{{ number_format($row->telur_kg, 2) }} Kg</td>
                                    
                                    <!-- Performa -->
                                    <td class="px-4 py-3 text-center font-bold text-blue-600">{{ number_format($row->hdp, 1) }}%</td>
                                    @php
                                        $stokAwal = $row->kandang->stok_awal ?? 1;
                                        $hhButir = $stokAwal > 0 ? $row->telur_butir / $stokAwal : 0;
                                        $hhKg = $stokAwal > 0 ? $row->telur_kg / $stokAwal : 0;
                                    @endphp
                                    <td class="px-4 py-3 text-center text-blue-500 font-medium" title="HH Harian">
                                        {{ number_format($hhButir, 3) }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-blue-500 font-medium" title="HH Harian">
                                        {{ number_format($hhKg, 3) }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-800 border-r border-gray-100">{{ number_format($row->fcr, 2) }}</td>
                                    
                                    <!-- Pakan -->
                                    <td class="px-4 py-3 text-center text-xs">{{ $row->pakan->nama_pakan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-700">{{ number_format($row->pakan_kg, 1) }} Kg</td>
                                    <td class="px-4 py-3 text-center text-gray-600 border-r border-gray-100">
                                        @php
                                            $populasiAkhir = $row->populasi_awal - $row->mati - $row->afkir;
                                            $konsumsi = $populasiAkhir > 0 ? ($row->pakan_kg * 1000) / $populasiAkhir : 0;
                                        @endphp
                                        {{ number_format($konsumsi, 1) }} gr
                                    </td>

                                    <td class="px-4 py-3 text-xs text-gray-500 truncate">
                                        {{ $row->user->name ?? 'Admin' }}
                                    </td>
                                    
                                    <!-- Aksi -->
                                    <td class="px-4 py-3 text-center sticky right-0 bg-white z-10 shadow-[-4px_0_12px_-4px_rgba(0,0,0,0.05)] group-hover:bg-gray-50">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- [UPDATE] Tombol Edit Memotong Tanggal agar Sesuai Input HTML -->
                                            <button @click="editData = {{ json_encode($row) }}; editData.tanggal = editData.tanggal ? editData.tanggal.substring(0, 10) : ''; showEditModal = true" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                                <i class="ph-bold ph-pencil-simple"></i>
                                            </button>
                                            <button onclick="confirmDelete('{{ route('admin.analytic.delete', $row->id) }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                <i class="ph-bold ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="17" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada data input harian.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($data->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $data->withQueryString()->links() }}
                    </div>
                    @endif
                </div>

            </div>
        </main>
        
        <!-- Modal Edit -->
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showEditModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <form :action="`{{ url('admin/analytic/update') }}/${editData.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Edit Data Harian
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <!-- [BARU] Input Ubah Tanggal -->
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Laporan</label>
                                    <input type="date" name="tanggal" x-model="editData.tanggal" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Mati (Ekor)</label>
                                    <input type="number" name="mati" x-model="editData.mati" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Afkir (Ekor)</label>
                                    <input type="number" name="afkir" x-model="editData.afkir" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                                </div>
                                
                                <!-- Input Edit Keterangan -->
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Keterangan Kematian</label>
                                    <input type="text" name="ket_mati" x-model="editData.ket_mati" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none" placeholder="Isi penyebab kematian jika ada">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Telur (Butir)</label>
                                    <input type="number" name="telur_butir" x-model="editData.telur_butir" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Telur (Kg)</label>
                                    <input type="number" step="0.01" name="telur_kg" x-model="editData.telur_kg" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Pakan (Kg)</label>
                                    <input type="number" step="0.01" name="pakan_kg" x-model="editData.pakan_kg" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cemara-500 outline-none">
                                </div>
                            </div>
                            <p class="text-xs text-red-500 mt-3 italic">*Perubahan akan mengupdate ulang stok pakan & ayam.</p>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-cemara-900 text-base font-medium text-white hover:bg-cemara-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan Perubahan
                            </button>
                            <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Sidebar & SweetAlert -->
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

        // Fungsi Konfirmasi Hapus
        function confirmDelete(url) {
            Swal.fire({
                title: 'Hapus Data Input?',
                text: "Data yang dihapus akan mengembalikan stok pakan & ayam!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
</body>
</html>