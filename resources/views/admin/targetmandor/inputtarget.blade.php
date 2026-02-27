<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Target - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-poppins'],
                        poppins: ['"Playfair Display"', 'poppins'],
                    },
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

        <!-- Sidebar Admin -->
        @include('admin.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-30 shadow-sm/50">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Produksi</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Input Target</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins tracking-tight">Input Target Mingguan</h2>
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
            <div class="p-4 md:p-8 w-full max-w-7xl mx-auto">
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 font-bold flex items-center gap-2 shadow-sm">
                        <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- KOLOM KIRI (1/3): Form Input -->
                    <div class="lg:col-span-1 space-y-6">
                        <form action="{{ route('admin.target.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
                            @csrf
                            
                            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-full flex items-center justify-center text-xl shadow-sm">
                                    <i class="ph-fill ph-target"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">Form Target Baru</h3>
                                    <p class="text-xs text-gray-500">Tentukan target performa untuk periode tertentu.</p>
                                </div>
                            </div>

                            <div class="p-6 space-y-6">
                                
                                <!-- 1. Lokasi & Unit (DINAMIS) -->
                                <div class="grid grid-cols-1 gap-4">
                                    
                                    <!-- Pilih Unit -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Unit Farm</label>
                                        <div class="relative">
                                            <select name="unit_id" id="unitSelect" onchange="updateLokasi()" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-gold-500 outline-none transition font-medium cursor-pointer">
                                                <option value="" disabled selected>Pilih Unit...</option>
                                                @if(isset($units))
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}" data-lokasi="{{ $unit->lokasi }}">{{ $unit->nama_unit }}</option>
                                                    @endforeach
                                                @else
                                                    <option disabled>Data unit tidak tersedia</option>
                                                @endif
                                            </select>
                                            <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                        </div>
                                    </div>

                                    <!-- Lokasi (Otomatis Terisi) -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Lokasi Farm</label>
                                        <input type="text" id="lokasiDisplay" readonly class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-600 cursor-not-allowed" placeholder="Otomatis terisi...">
                                        <!-- Hidden Input untuk kirim ke Controller -->
                                        <input type="hidden" name="lokasi_id" id="lokasiInput"> 
                                    </div>
                                </div>
                                
                                <!-- Periode -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Periode Berlaku</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="relative">
                                            <span class="absolute left-3 top-3 text-gray-400 text-[10px] font-bold uppercase">Mulai</span>
                                            <input type="date" name="start_date" class="w-full pl-14 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gold-500 outline-none transition" required>
                                        </div>
                                        <div class="relative">
                                            <span class="absolute left-3 top-3 text-gray-400 text-[10px] font-bold uppercase">Selesai</span>
                                            <input type="date" name="end_date" class="w-full pl-16 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gold-500 outline-none transition" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 my-2"></div>

                                <!-- 2. Parameter Target -->
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs font-bold text-gray-600">Hen Day (%)</label>
                                        <input type="number" step="0.01" name="target_hd" id="in_hd" class="w-full px-3 py-2 border rounded-lg font-bold" placeholder="95.5" required>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-bold text-gray-600">Berat Tlr (Gr)</label>
                                            <input type="number" step="0.1" name="target_egg_weight" id="in_ew" class="w-full px-3 py-2 border rounded-lg" required>
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-purple-600">FCR Target</label>
                                            <input type="number" step="0.01" name="target_fcr" id="in_fcr" placeholder="1.xx" class="w-full px-3 py-2 border border-purple-200 bg-purple-50 text-purple-700 rounded-lg font-bold" required>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-bold text-gray-600">BW (Gr)</label>
                                            <input type="number" step="1" name="target_bw" id="in_bw" class="w-full px-3 py-2 border rounded-lg" required>
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-red-600">Mortalitas (%)</label>
                                            <input type="number" step="0.01" name="target_mortality_percent" id="in_mo" class="w-full px-3 py-2 border border-red-200 rounded-lg text-red-600 font-bold" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" onclick="autoFillStandard()" class="w-full py-2 text-xs text-blue-600 hover:bg-blue-50 rounded border border-blue-200 transition">
                                    Isi Standar
                                </button>
                            </div>

                            <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-cemara-900 text-white rounded-lg font-bold text-sm hover:bg-cemara-800 shadow-md transition">
                                    Simpan Target
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- KOLOM KANAN (2/3): Tabel Riwayat (DINAMIS) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col">
                            <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Target Aktif</h3>
                            </div>
                            
                            <div class="flex-1 overflow-x-auto table-container">
                                <table class="w-full text-left text-sm text-gray-600">
                                    <thead class="bg-gray-50/80 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                                        <tr>
                                            <th class="px-6 py-4">Unit & Lokasi</th>
                                            <th class="px-6 py-4 text-center">Periode Berakhir</th>
                                            <th class="px-6 py-4 text-center bg-blue-50/30 text-blue-700">HD %</th>
                                            <th class="px-6 py-4 text-center text-purple-700">FCR</th>
                                            <th class="px-6 py-4 text-center">BW (gr)</th>
                                            <th class="px-6 py-4 text-center sticky right-0 bg-gray-50/80 backdrop-blur-sm z-10">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @if(isset($targets) && count($targets) > 0)
                                            @foreach($targets as $t)
                                                <tr class="hover:bg-gray-50 transition group">
                                                    <td class="px-6 py-4">
                                                        <!-- Menampilkan Nama Unit dari Relasi -->
                                                        <p class="font-bold text-gray-900">{{ $t->unit->nama_unit ?? 'Unit Hapus' }}</p>
                                                        <!-- Menampilkan Lokasi dari Relasi Unit -->
                                                        <span class="text-xs font-medium text-cemara-600 bg-cemara-50 px-2 py-0.5 rounded-md border border-cemara-100">
                                                            {{ $t->unit->lokasi ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <div class="flex items-center justify-center gap-2 text-gray-600 font-medium">
                                                            <i class="ph-fill ph-calendar-blank text-gray-400"></i>
                                                            {{ \Carbon\Carbon::parse($t->end_date)->format('d M Y') }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-center bg-blue-50/10">
                                                        <span class="font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                                                            {{ $t->hd }}%
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <span class="font-bold text-purple-600 bg-purple-50 px-3 py-1 rounded-full border border-purple-100">
                                                            {{ $t->fcr }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-medium text-gray-700">
                                                        {{ number_format($t->bw) }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center sticky right-0 bg-white group-hover:bg-gray-50 transition z-10">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <button onclick="openEditModal({{ json_encode($t) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                                                <i class="ph-bold ph-pencil-simple text-lg"></i>
                                                            </button>
                                                            <form action="{{ route('admin.target.delete', $t->id) }}" method="POST" onsubmit="return confirm('Hapus target?')" class="inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                                    <i class="ph-bold ph-trash text-lg"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Belum ada target aktif.</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- ================= MODAL EDIT (POP UP) ================= -->
    <div x-show="showEditModal" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showEditModal = false"></div>

            <!-- Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl relative z-50 transform transition-all"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center rounded-t-2xl">
                        <h3 class="font-bold text-lg text-gray-800">Edit Target Produksi</h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-red-500 transition"><i class="ph-bold ph-x text-xl"></i></button>
                    </div>

                    <form id="editForm" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                            <!-- Info Lokasi & Unit (Readonly) -->
                            <div class="grid grid-cols-2 gap-4 bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                                <div>
                                    <span class="text-xs text-gray-500">Unit Farm</span>
                                    <!-- Menampilkan Nama Unit, asumsi editData.unit ada datanya -->
                                    <p class="font-bold text-gray-800 text-sm" x-text="editData.unit ? editData.unit.nama_unit : '-'"></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Lokasi</span>
                                    <p class="font-bold text-gray-800 text-sm" x-text="editData.unit ? editData.unit.lokasi : '-'"></p>
                                </div>
                            </div>

                            <!-- Periode -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Mulai</label>
                                    <input type="date" name="start_date" x-model="editData.start_date" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Selesai</label>
                                    <input type="date" name="end_date" x-model="editData.end_date" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Status Target</label>
                                <select name="status" x-model="editData.status" class="w-full px-3 py-2 border rounded-lg text-sm font-bold text-gray-700">
                                    <option value="active">Active (Tampil di Dashboard)</option>
                                    <option value="history">History (Arsip)</option>
                                </select>
                            </div>
                            
                            <div class="border-t border-gray-100"></div>

                            <!-- Parameter -->
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-xs font-bold text-blue-600">HD (%)</label><input type="number" step="0.01" name="target_hd" x-model="editData.hd" class="w-full px-3 py-2 border rounded-lg font-bold"></div>
                                
                                <div><label class="text-xs font-bold text-gray-600">Berat Tlr</label><input type="number" step="0.1" name="target_egg_weight" x-model="editData.egg_weight" class="w-full px-3 py-2 border rounded-lg"></div>
                                
                                <!-- UPDATED FCR DI MODAL -->
                                <div><label class="text-xs font-bold text-purple-600">FCR Target</label><input type="number" step="0.01" name="target_fcr" x-model="editData.fcr" class="w-full px-3 py-2 border border-purple-200 rounded-lg font-bold text-purple-700" placeholder="1.xx"></div>
                                
                                <div><label class="text-xs font-bold text-gray-600">BW</label><input type="number" step="1" name="target_bw" x-model="editData.bw" class="w-full px-3 py-2 border rounded-lg"></div>
                                
                                <div class="col-span-2"><label class="text-xs font-bold text-red-600">Mortalitas (%)</label><input type="number" step="0.01" name="target_mortality_percent" x-model="editData.mortality" class="w-full px-3 py-2 border border-red-200 rounded-lg text-red-600 font-bold"></div>
                            </div>
                        </div>

                        <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="showEditModal = false" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-white transition text-sm">Batal</button>
                            <button type="submit" class="px-8 py-2.5 rounded-xl bg-cemara-900 text-white font-bold hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 text-sm">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const displayDate = document.getElementById('headerDateDisplay');
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
            if(displayDate) displayDate.innerText = today.toLocaleDateString('id-ID', options);
        });

        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('-translate-x-full'); }

        // SCRIPT: UPDATE LOKASI OTOMATIS
        function updateLokasi() {
            const unitSelect = document.getElementById('unitSelect');
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];
            const lokasi = selectedOption.getAttribute('data-lokasi');
            
            // Isi tampilan readonly
            document.getElementById('lokasiDisplay').value = lokasi;
            
            // Isi input hidden untuk dikirim ke controller
            document.getElementById('lokasiInput').value = lokasi; 
        }

        // AUTO FILL STANDARD FCR
        function autoFillStandard() {
            document.getElementById('in_hd').value = "94.5";
            document.getElementById('in_ew').value = "62.5";
            document.getElementById('in_fcr').value = "2.15"; 
            document.getElementById('in_bw').value = "1920";
            document.getElementById('in_mo').value = "0.05";
        }

        // Logic Modal Edit
        function openEditModal(data) {
            const alpineData = Alpine.$data(document.querySelector('[x-data]'));
            
            // Isi data ke modal, termasuk objek Unit untuk ditampilkan namanya
            alpineData.editData = {
                id: data.id,
                unit_id: data.unit_id,
                unit: data.unit, // Objek Unit lengkap dari relation
                start_date: data.start_date,
                end_date: data.end_date,
                status: data.status,
                hd: data.hd,
                egg_weight: data.egg_weight,
                fcr: data.fcr, 
                bw: data.bw,
                mortality: data.mortality
            };

            const form = document.getElementById('editForm');
            form.action = "{{ url('/admin/target/update') }}/" + data.id;

            alpineData.showEditModal = true;
        }
    </script>
</body>
</html>