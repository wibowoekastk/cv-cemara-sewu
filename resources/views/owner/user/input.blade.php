<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - Owner Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    {{-- Fallback jika $units belum dikirim controller --}}
    @php
        if(!isset($units)) {
            $units = \App\Models\Unit::all();
        }
    @endphp

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Overlay Mobile -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Sidebar Owner -->
        @include('owner.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Pengguna</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Input User</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Tambah Pengguna Baru</h2>
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
            <div class="p-4 md:p-8 w-full max-w-4xl mx-auto">
                
                <!-- x-data untuk kontrol logika role -->
                <form action="{{ route('owner.user.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ role: 'mandor' }">
                    @csrf
                    
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-full flex items-center justify-center text-xl shadow-sm">
                            <i class="ph-fill ph-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">Registrasi Akun</h3>
                            <p class="text-xs text-gray-500">Buat akun untuk Admin atau Mandor baru.</p>
                        </div>
                    </div>
                    
                    <!-- Pesan Sukses -->
                    @if(session('success'))
                        <div class="p-4 bg-green-50 text-green-700 text-sm font-bold border-b border-green-100 flex items-center gap-2">
                            <i class="ph-fill ph-check-circle text-lg"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Pesan Error -->
                    @if ($errors->any())
                        <div class="p-4 bg-red-50 text-red-700 text-sm border-b border-red-100">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="p-6 md:p-8 space-y-8">
                        
                        <!-- 1. Informasi Login -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wide border-b border-gray-100 pb-2">Informasi Dasar</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-medium" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Email (Username Login)</label>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="budi@cemara.com" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-medium" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Password Awal</label>
                                    <div class="relative">
                                        <input type="password" name="password" placeholder="Minimal 8 karakter" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-medium" required>
                                        <i class="ph-bold ph-lock-key absolute right-4 top-3.5 text-gray-400"></i>
                                    </div>
                                </div>
                                
                                <!-- Role Selection -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Role (Peran)</label>
                                    <div class="relative">
                                        <select x-model="role" name="role" class="w-full px-4 py-3 bg-white border-2 border-gold-100 rounded-xl appearance-none focus:ring-2 focus:ring-gold-500 outline-none transition cursor-pointer font-bold text-gray-800">
                                            <option value="mandor">Mandor Lapangan</option>
                                            <option value="admin">Administrator</option>
                                            <option value="owner">Owner</option>
                                        </select>
                                        <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Penugasan Wilayah (Hanya Muncul Jika Role = Mandor) -->
                        <div x-show="role === 'mandor'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="space-y-4 bg-gold-50/30 p-5 rounded-xl border border-gold-100">
                            
                            <div class="flex items-center gap-2 border-b border-gold-100 pb-2 mb-2">
                                <i class="ph-fill ph-map-pin text-gold-500"></i>
                                <h4 class="text-xs font-bold text-gold-700 uppercase tracking-wide">Penugasan Wilayah</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Pilih Lokasi (Filter Unit) -->
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Lokasi</label>
                                    <select id="lokasiSelector" onchange="filterUnit()" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gold-500 outline-none cursor-pointer">
                                        <option value="">Pilih Lokasi...</option>
                                        @foreach($units->unique('lokasi') as $u)
                                            <option value="{{ $u->lokasi }}">{{ $u->lokasi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Pilih Unit (Difilter oleh Lokasi) -->
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Unit</label>
                                    <select name="unit_id" id="unitSelector" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gold-500 outline-none cursor-pointer" disabled>
                                        <option value="" selected disabled>Pilih Unit...</option>
                                        @foreach($units as $unit)
                                            <!-- Menyimpan data-lokasi untuk filtering JS -->
                                            <option value="{{ $unit->id }}" data-lokasi="{{ $unit->lokasi }}" class="unit-option hidden">
                                                {{ $unit->nama_unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Actions -->
                    <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="reset" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-white transition text-sm">
                            Reset
                        </button>
                        <button type="submit" class="px-8 py-2.5 rounded-xl bg-cemara-900 text-white font-bold hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 text-sm">
                            <i class="ph-bold ph-check-circle"></i>
                            Simpan User
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <!-- Script Tanggal & Sidebar & Filter Lokasi -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const displayDate = document.getElementById('headerDateDisplay');
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
            if(displayDate) displayDate.innerText = today.toLocaleDateString('id-ID', options);
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

        // Script Filter Unit Berdasarkan Lokasi
        function filterUnit() {
            const lokasi = document.getElementById('lokasiSelector').value;
            const unitSelector = document.getElementById('unitSelector');
            const options = document.querySelectorAll('.unit-option');

            // Reset
            unitSelector.value = "";
            let hasUnit = false;

            options.forEach(option => {
                if (lokasi === "" || option.getAttribute('data-lokasi') === lokasi) {
                    option.classList.remove('hidden');
                    hasUnit = true;
                } else {
                    option.classList.add('hidden');
                }
            });

            if (lokasi !== "") {
                unitSelector.disabled = !hasUnit;
                if (!hasUnit) {
                    // Optional: Bisa tambah handling jika tidak ada unit di lokasi tsb
                }
            } else {
                unitSelector.disabled = true;
            }
        }
    </script>
</body>
</html>