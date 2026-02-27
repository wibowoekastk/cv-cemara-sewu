<!-- FILE: resources/views/owner/sidebar.blade.php -->
<!-- Tambahkan CDN SweetAlert2 agar notifikasi berfungsi -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Alpine.js untuk Dropdown -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-cemara-950 text-white flex flex-col h-full transition-transform duration-300 ease-in-out transform -translate-x-full md:translate-x-0 shadow-2xl">
    
    <!-- Header: Logo & Brand -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-cemara-800 bg-cemara-900 sticky top-0 z-10 shrink-0">
        <div class="flex items-center gap-3">
             <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-md shadow-gold-500/20 overflow-hidden shrink-0">
                <!-- Pastikan file cms.png ada di folder public -->
                <img src="{{ asset('cms.png') }}" alt="Logo" class="w-full h-full object-cover">
            </div>
            <div>
                <h1 class="font-bold text-lg tracking-wide font-poppins">Cemara Sewu</h1>
                <p class="text-[10px] text-gold-400 uppercase tracking-wider font-semibold">Owner Panel</p>
            </div>
        </div>

        <!-- Tombol Close (Hanya Muncul di Mobile) -->
        <!-- Ini untuk menutup sidebar saat mode HP -->
        <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white transition p-1">
            <i class="ph-bold ph-x text-2xl"></i>
        </button>
    </div>

    <!-- Navigasi Menu (Scrollable) -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 custom-scrollbar">
        
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 font-sans">Menu Utama</p>
        
        <!-- Menu Dashboard -->
        <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl transition group mb-4 {{ request()->routeIs('owner.dashboard') ? 'bg-cemara-800 text-white shadow-lg border border-cemara-700' : 'text-gray-300 hover:bg-cemara-800/50 hover:text-white' }}">
            @if(request()->routeIs('owner.dashboard'))
                <div class="absolute left-0 w-1 h-8 bg-gold-500 rounded-r-full"></div>
            @endif
            <i class="ph-fill ph-presentation-chart text-xl {{ request()->routeIs('owner.dashboard') ? 'text-gold-400' : 'group-hover:text-gold-400 transition' }}"></i>
            <span class="font-medium tracking-wide">Dashboard</span>
        </a>

        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 font-sans">Manajemen & Laporan</p>

        <!-- 1. Manajemen Analytic & Laporan -->
        <div x-data="{ open: {{ request()->routeIs('owner.analytic.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    :class="{'bg-cemara-800/50 text-white': open, 'text-gray-300 hover:text-white hover:bg-cemara-800/50': !open}"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-chart-pie-slice text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium">Analytic & Laporan</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="pl-11 pr-2 py-2 space-y-1 bg-cemara-900/50 rounded-b-xl border-t border-cemara-800/50 -mt-1 mx-1">
                <a href="{{ route('owner.analytic.data') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.analytic.data') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Input Harian</span>
                </a>
                <a href="{{ route('owner.analytic.laporan-pdf') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.analytic.laporan-pdf') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-file-pdf text-lg"></i>
                    <span>Laporan PDF</span>
                </a>
            </div>
        </div>

        <!-- 2. Manajemen Kandang -->
        <div x-data="{ open: {{ request()->routeIs('owner.kandang.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    :class="{'bg-cemara-800/50 text-white': open, 'text-gray-300 hover:text-white hover:bg-cemara-800/50': !open}"
                    class="w-full flex items-center justify-between px-2 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-1">
                    <i class="ph ph-warehouse text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium">Manajemen Kandang</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            <div x-show="open" class="pl-11 pr-2 py-2 space-y-1 bg-cemara-900/50 rounded-b-xl border-t border-cemara-800/50 -mt-1 mx-1" x-transition>
                <a href="{{ route('owner.kandang.data') }}" class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.kandang.data') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Kandang</span>
                </a>
                <a href="{{ route('owner.kandang.laporan-pdf') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.kandang.laporan-pdf') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-file-pdf text-lg"></i>
                    <span>Laporan PDF</span>
                </a>
            </div>
        </div>

        <!-- 3. Manajemen Pakan -->
        <div x-data="{ open: {{ request()->routeIs('owner.pakan.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    :class="{'bg-cemara-800/50 text-white': open, 'text-gray-300 hover:text-white hover:bg-cemara-800/50': !open}"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-grains text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium">Manajemen Pakan</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            <div x-show="open" class="pl-11 pr-2 py-2 space-y-1 bg-cemara-900/50 rounded-b-xl border-t border-cemara-800/50 -mt-1 mx-1" x-transition>
                <a href="{{ route('owner.pakan.data_input') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.pakan.data_input') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Input Pakan</span>
                </a>
                <a href="{{ route('owner.pakan.laporan') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.pakan.laporan') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-file-pdf text-lg"></i>
                    <span>Laporan PDF</span>
                </a>
            </div>
        </div>

        <!-- 4. Manajemen Obat -->
        <div x-data="{ open: {{ request()->routeIs('owner.obat.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    :class="{'bg-cemara-800/50 text-white': open, 'text-gray-300 hover:text-white hover:bg-cemara-800/50': !open}"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-pill text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium">Manajemen Obat</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            <div x-show="open" class="pl-11 pr-2 py-2 space-y-1 bg-cemara-900/50 rounded-b-xl border-t border-cemara-800/50 -mt-1 mx-1" x-transition>
                <a href="{{ route('owner.obat.data_input') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.obat.data_input') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Input Obat</span>
                </a>
                <a href="{{ route('owner.obat.laporan') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.obat.laporan') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-file-pdf text-lg"></i>
                    <span>Laporan PDF</span>
                </a>
            </div>
        </div>

        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2 font-sans">Pengguna</p>

        <!-- 5. Manajemen User (Dropdown) -->
        <div x-data="{ open: {{ request()->routeIs('owner.user.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    :class="{'bg-cemara-800/50 text-white': open, 'text-gray-300 hover:text-white hover:bg-cemara-800/50': !open}"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-users text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium">Manajemen User</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            <div x-show="open" class="pl-11 pr-2 py-2 space-y-1 bg-cemara-900/50 rounded-b-xl border-t border-cemara-800/50 -mt-1 mx-1" x-transition>
                <a href="{{ route('owner.user.dashboarduser') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.user.dashboarduser') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-squares-four text-lg"></i>
                    <span>Dashboard User</span>
                </a>
                <a href="{{ route('owner.user.input') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.user.input') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-user-plus text-lg"></i>
                    <span>Tambah User</span>
                </a>
                <a href="{{ route('owner.user.data') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 {{ request()->routeIs('owner.user.data') ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-users-three text-lg"></i>
                    <span>Data User</span>
                </a>
            </div>
        </div>

    </nav>

    <!-- Footer: User Profile & Logout -->
    <div class="p-4 border-t border-cemara-800 bg-cemara-900 shrink-0">
        <div class="flex items-center gap-3 group cursor-pointer">
            <a href="{{ route('owner.settingsowner') }}" class="flex items-center gap-3 flex-1" title="Pengaturan Profil">
                 <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=f59e0b&color=fff' }}" 
                      alt="Profile" 
                      class="w-10 h-10 rounded-full border-2 border-gold-500 p-0.5 group-hover:border-white transition object-cover">
                
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate font-poppins group-hover:text-gold-400 transition">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-xs text-gold-400 truncate capitalize">
                        {{ Auth::user()->role }}
                    </p>
                </div>
            </a>
            
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>

            <button type="button" onclick="confirmLogout()" class="text-gray-400 hover:text-red-400 transition p-1 hover:bg-white/5 rounded-lg" title="Keluar Aplikasi">
                <i class="ph ph-sign-out text-xl"></i>
            </button>
        </div>
    </div>
    
    <!-- CSS Internal Scrollbar -->
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #166534; border-radius: 2px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #22c55e; }
    </style>

    <!-- SCRIPT UTAMA UNTUK TOGGLE SIDEBAR -->
    <!-- Letakkan di dalam file ini agar selalu terpanggil dimanapun sidebar di-include -->
    <script>
        function toggleSidebar() {
            // Ambil elemen sidebar berdasarkan ID yang sudah kita tambahkan
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            // Toggle class untuk sidebar (mobile)
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
            
            // Toggle overlay
            if (overlay) {
                if (overlay.classList.contains('hidden')) {
                    overlay.classList.remove('hidden');
                    // Timeout kecil untuk transisi opacity
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                } else {
                    overlay.classList.add('opacity-0');
                    // Timeout menunggu transisi selesai baru hide
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }
        }
        
        // Fungsi Logout (Opsional, tapi sudah ada di tombol)
        function confirmLogout() {
            if (typeof Swal === 'undefined') {
                if(confirm("Yakin ingin keluar?")) { document.getElementById('logout-form').submit(); }
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
                customClass: { popup: 'rounded-2xl font-sans' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
</aside>