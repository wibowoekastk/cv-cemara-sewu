<!-- FILE: resources/views/mandor/sidebar.blade.php -->
<!-- Tambahkan CDN SweetAlert2 agar notifikasi berfungsi -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Alpine.js untuk Dropdown -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-cemara-950 text-white transition-transform duration-300 ease-in-out transform -translate-x-full md:translate-x-0 shadow-xl flex flex-col h-full overflow-hidden">
    
    
    <!-- Header: Logo & Brand -->
    <div class="h-20 flex items-center gap-3 px-6 border-b border-cemara-800 bg-cemara-900 sticky top-0 z-10">
        <!-- Logo Wrapper -->
        <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-md shadow-gold-500/20 overflow-hidden shrink-0">
            <!-- Gunakan asset() untuk memanggil gambar dari folder public -->
            <!-- Pastikan file cms.png ada di dalam folder public/ -->
            <img src="{{ asset('cms.png') }}" alt="Logo" class="w-full h-full object-cover">
        </div>
    
        <div>
            <h1 class="font-bold text-lg tracking-wide font-poppins">Cemara Sewu</h1>
            
            <!-- UPDATE DI SINI: Menampilkan Lokasi Dinamis -->
            <p class="text-[9px] text-gold-400 uppercase tracking-wider font-semibold flex items-center gap-1">
                <span>Mandor Panel</span>
                
                {{-- Cek apakah user punya relasi ke unit dan lokasi --}}
                @if(Auth::user()->unit && Auth::user()->unit->lokasi)
                    <span class="text-white/70">• {{ Auth::user()->unit->lokasi }}</span>
                @else
                    {{-- Fallback jika tidak ada relasi, cek manual lokasi_id (opsional) --}}
                    @if(Auth::user()->lokasi_id == 1)
                        <span class="text-white/70">• Kalirambut</span>
                    @elseif(Auth::user()->lokasi_id == 2)
                        <span class="text-white/70">• Sokawangi</span>
                    @else
                        <span class="text-white/70">• -</span>
                    @endif
                @endif
            </p>
        </div>
    </div>

    <!-- Navigasi Menu -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 custom-scrollbar pb-16">
        
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 font-sans">Utama</p>
        
        <!-- Menu Dashboard -->
        <a href="{{ route('mandor.dashboard') }}" class="flex items-center gap-3 px-3 py-3 bg-cemara-800 text-white rounded-xl transition shadow-lg border border-cemara-700 relative overflow-hidden group mb-4">
            <div class="absolute inset-0 w-1 bg-gold-500"></div>
            <i class="ph-fill ph-squares-four text-xl text-gold-400 group-hover:scale-110 transition"></i>
            <span class="font-medium tracking-wide">Dashboard</span>
        </a>

        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2 font-sans">Manajemen Produksi</p>

        <!-- Dropdown: Input Harian Produksi -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-gray-300 hover:text-white hover:bg-cemara-800/50 rounded-xl transition group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-clipboard-text text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium text-left">Input Harian</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            
            <!-- Isi Dropdown -->
            <div x-show="open" x-transition class="pl-11 pr-2 py-1 space-y-1 bg-cemara-900/30 rounded-b-xl mt-1">
                
                <!-- Input Laporan -->
                <a href="{{ route('mandor.produksi.input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-pencil-simple text-lg"></i>
                    <span>Input Laporan</span>
                </a>

                <!-- Data Terinput -->
                <a href="{{ route('mandor.produksi.data') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Terinput</span>
                </a>
            </div>
        </div>

        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2 font-sans">Manajemen Laporan</p>

        <!-- Dropdown: Pusat Laporan Mandor -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-gray-300 hover:text-white hover:bg-cemara-800/50 rounded-xl transition group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-files text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium text-left">Pusat Pakan</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
             <div x-show="open" x-transition class="pl-11 pr-2 py-1 space-y-1 bg-cemara-900/30 rounded-b-xl mt-1">
                
                <!-- Input Laporan -->
                <a href="{{ route('mandor.pakan.input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-pencil-simple text-lg"></i>
                    <span>Input Pakan</span>
                </a>
        </div>

    </nav>

    <!-- Footer: User Profile & Logout (DINAMIS) -->
    <div class="p-4 border-t border-cemara-800 bg-cemara-900/50 z-20 relative bg-cemara-900">
        <div class="flex items-center gap-3 group cursor-pointer">
            <!-- Link ke Settings (Profil Dinamis) -->
            <a href="{{ route('mandor.settingsmandor') }}" class="flex items-center gap-3 flex-1" title="Pengaturan Profil">
                <!-- Avatar dari Inisial Nama -->
                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=22c55e&color=fff' }}" 
                     alt="Profile" 
                     class="w-10 h-10 rounded-full border-2 border-cemara-500 p-0.5 group-hover:border-white transition object-cover">
                
                <div class="flex-1 min-w-0">
                    <!-- Nama Mandor Dinamis -->
                    <p class="text-sm font-bold text-white truncate font-poppins group-hover:text-gold-400 transition">
                        {{ Auth::user()->name }}
                    </p>
                    <!-- Info Role & Unit (Logika Dinamis) -->
                    <p class="text-xs text-gold-400 truncate">
                        {{ ucfirst(Auth::user()->role) }}
                        @if(Auth::user()->unit)
                             • {{ Auth::user()->unit->nama_unit }}
                        @elseif(Auth::user()->unit_id)
                             • Unit {{ Auth::user()->unit_id }}
                        @else
                             • Belum Ada Unit
                        @endif
                    </p>
                </div>
            </a>
            
            <!-- Form Logout -->
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>

            <!-- Tombol Logout -->
            <button type="button" onclick="confirmLogout()" class="text-gray-400 hover:text-red-400 transition p-1 hover:bg-white/5 rounded-lg" title="Keluar Aplikasi">
                <i class="ph ph-sign-out text-xl"></i>
            </button>
        </div>
    </div>
    
    <!-- CSS Internal Scrollbar & Animation -->
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #166534; border-radius: 2px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #22c55e; }

        @keyframes walk-jump {
            0% { transform: translateX(-100%) translateY(0); }
            10% { transform: translateX(-60%) translateY(-5px); }
            20% { transform: translateX(-20%) translateY(0); }
            30% { transform: translateX(20%) translateY(-5px); }
            40% { transform: translateX(60%) translateY(0); }
            50% { transform: translateX(100%) translateY(-5px); }
            60% { transform: translateX(140%) translateY(0); }
            70% { transform: translateX(180%) translateY(-5px); }
            80% { transform: translateX(220%) translateY(0); }
            90% { transform: translateX(260%) translateY(-5px); }
            100% { transform: translateX(300%) translateY(0); }
        }
        .animate-walk-jump {
            animation: walk-jump 8s linear infinite;
        }
    </style>

    <!-- Script SweetAlert & Toggle -->
    <script>
        function confirmLogout() {
            if (typeof Swal === 'undefined') {
                alert("Library SweetAlert belum dimuat. Silakan cek koneksi internet atau script CDN.");
                return;
            }

            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Anda harus login kembali untuk mengakses panel.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', 
                cancelButtonColor: '#3085d6', 
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                background: '#fff', 
                color: '#1f2937', 
                customClass: {
                    popup: 'rounded-2xl font-sans', 
                    title: 'text-xl font-bold text-gray-800',
                    confirmButton: 'px-4 py-2 rounded-lg font-bold',
                    cancelButton: 'px-4 py-2 rounded-lg font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
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
    </script>
</aside>