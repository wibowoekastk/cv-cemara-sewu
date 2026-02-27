<!-- FILE: resources/views/admin/sidebar.blade.php -->
<!-- Tambahkan CDN SweetAlert2 agar notifikasi berfungsi -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-cemara-950 text-white transition-transform duration-300 ease-in-out transform -translate-x-full md:translate-x-0 shadow-xl flex flex-col h-full">
    
    <!-- Header: Logo & Close Button -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-cemara-800 bg-cemara-900 sticky top-0 z-10">
        <!-- Logo Wrapper -->
        <div class="flex items-center gap-3">
          <div class="w-10 h-10  rounded-lg flex items-center justify-center shadow-md shadow-gold-500/20 overflow-hidden shrink-0">
            <!-- Gunakan asset() untuk memanggil gambar dari folder public -->
            <img src="{{ asset('cms.png') }}" alt="Logo" class="w-full h-full object-cover">
        </div>
            <div>
                <h1 class="font-bold text-lg tracking-wide font-sans">Cemara Sewu</h1>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-medium">Admin Panel</p>
            </div>
        </div>
        
        <!-- Tombol Close (Hanya Muncul di Mobile) -->
        <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white transition p-1 rounded-md hover:bg-cemara-800 focus:outline-none">
            <i class="ph-bold ph-x text-xl"></i>
        </button>
    </div>

    <!-- Navigasi -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 custom-scrollbar">
        
        <!-- === GROUP: UTAMA === -->
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 font-sans">Utama</p>
        
        <!-- Dashboard Global -->
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-3 bg-cemara-800 text-white rounded-xl transition shadow-lg border border-cemara-700 hover:bg-cemara-700 group">
            <i class="ph-fill ph-squares-four text-xl text-gold-400 group-hover:text-gold-300"></i>
            <span class="font-medium">Dashboard Overview</span>
        </a>

        <!-- === GROUP: MANAJEMEN DATA === -->
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2 font-sans">Manajemen Data</p>

        <!-- 1. Dropdown: Manajemen Analytic & Laporan -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-gray-300 hover:text-white hover:bg-cemara-800/50 rounded-xl transition group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-chart-pie-slice text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium text-left">Manajemen Analytic</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            
            <!-- Isi Dropdown -->
            <div x-show="open" x-transition class="pl-11 pr-2 py-1 space-y-1 bg-cemara-900/30 rounded-b-xl mt-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.analytic.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-squares-four text-lg"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Input Harian -->
                <a href="{{ route('admin.analytic.input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-pencil-simple text-lg"></i>
                    <span>Input Harian</span>
                </a>

                <!-- Data Input Harian -->
                <a href="{{ route('admin.analytic.data') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Input Harian</span>
                </a>

                <!-- Riwayat Input Harian -->
                <a href="{{ route('admin.analytic.riwayat') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-clock-counter-clockwise text-lg"></i>
                    <span>Riwayat Input</span>
                </a>

                <!-- Laporan PDF -->
                <a href="{{ route('admin.analytic.laporan_pdf') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-file-pdf text-lg"></i>
                    <span>Laporan PDF</span>
                </a>
            </div>
        </div>

        <!-- 2. Dropdown: Manajemen Kandang -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-gray-300 hover:text-white hover:bg-cemara-800/50 rounded-xl transition group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-warehouse text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium text-left">Manajemen Kandang</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            
            <!-- Isi Dropdown Kandang -->
            <div x-show="open" x-transition class="pl-4 pr-2 py-1 space-y-1 bg-cemara-900/30 rounded-b-xl mt-1">
                
                <!-- Dashboard Kandang -->
                <a href="{{ route('admin.kandang.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-squares-four text-lg"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Tambah Unit -->
                <a href="{{ route('admin.kandang.input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-plus-circle text-lg"></i>
                    <span>Tambah Unit</span>
                </a>

                <!-- Data Input Unit -->
                <a href="{{ route('admin.kandang.data_input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Input Unit</span>
                </a>

                <!-- Tambah Input Timbang -->
                <a href="{{ route('admin.kandang.input_timbang') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-scales text-lg"></i>
                    <span>Tambah Input Timbang</span>
                </a>

                <!-- Data Timbang -->
                <a href="{{ route('admin.kandang.data_timbang') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-chart-bar text-lg"></i>
                    <span>Data Timbang</span>
                </a>

                 <a href="{{ route('admin.kandang.input_sampletelur') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-chart-bar text-lg"></i>
                    <span>Input Sample Telur</span>
                </a>

                <!-- riwayat -->
                <a href="{{ route('admin.kandang.riwayat.unit') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-clock-counter-clockwise text-lg"></i>
                    <span>Riwayat</span>
                </a>
                
                <!-- Laporan PDF -->
                <a href="{{ route('admin.kandang.laporan') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-file-pdf text-lg"></i>
                    <span>Laporan PDF</span>
                </a>
            </div>
        </div>

        <!-- 3. Dropdown: Manajemen Pakan -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-gray-300 hover:text-white hover:bg-cemara-800/50 rounded-xl transition group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-grains text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium text-left">Manajemen Pakan</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            
            <!-- Isi Dropdown -->
            <div x-show="open" x-transition class="pl-11 pr-2 py-1 space-y-1 bg-cemara-900/30 rounded-b-xl mt-1">
                <!-- Dashboard Pakan -->
                <a href="{{ route('admin.pakan.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-squares-four text-lg"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Input Pakan -->
                <a href="{{ route('admin.pakan.input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-pencil-simple text-lg"></i>
                    <span>Input Pakan</span>
                </a>

                <!-- Data Input Pakan -->
                <a href="{{ route('admin.pakan.data_input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Input Pakan</span>
                </a>

                <!-- Data Input Pakan -->
                <a href="{{ route('admin.pakan.monitoring') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-projector-screen-chart"></i>
                    <span>Monitoring Pakan</span>
                </a>

                <!-- Riwayat Input Pakan -->
                <a href="{{ route('admin.pakan.riwayat') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-clock-counter-clockwise text-lg"></i>
                    <span>Riwayat Input</span>
                </a>

                <!-- Laporan Pakan PDF -->
                <a href="{{ route('admin.pakan.laporan') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-file-pdf text-lg"></i>
                    <span>Laporan PDF</span>
                </a>
            </div>
        </div>

        <!-- 4. Dropdown: Manajemen Obat -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-gray-300 hover:text-white hover:bg-cemara-800/50 rounded-xl transition group cursor-pointer focus:outline-none">
                <div class="flex items-center gap-3">
                    <i class="ph ph-pill text-xl group-hover:text-gold-400 transition"></i>
                    <span class="font-medium text-left">Manajemen Obat</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300 text-gray-500 group-hover:text-white" :class="{'rotate-180': open}"></i>
            </button>
            
            <!-- Isi Dropdown -->
            <div x-show="open" x-transition class="pl-11 pr-2 py-1 space-y-1 bg-cemara-900/30 rounded-b-xl mt-1">
                <!-- Dashboard Obat -->
                <a href="{{ route('admin.obat.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-squares-four text-lg"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Input Obat -->
                <a href="{{ route('admin.obat.input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-syringe text-lg"></i>
                    <span>Input Obat</span>
                </a>

                <!-- Data Obat -->
                <a href="{{ route('admin.obat.data_input') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-table text-lg"></i>
                    <span>Data Obat</span>
                </a>

                <!-- Riwayat Obat -->
                <a href="{{ route('admin.obat.riwayat') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-clock-counter-clockwise text-lg"></i>
                    <span>Riwayat Obat</span>
                </a>

                <!-- Laporan PDF Obat -->
                <a href="{{ route('admin.obat.laporan_pdf') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-cemara-800/30 rounded-lg transition border-l-2 border-transparent hover:border-gold-400">
                    <i class="ph ph-file-pdf text-lg"></i>
                    <span>Laporan PDF</span>
                </a>
            </div>
        </div>

         <!-- 5. Dropdown: Pusat Target (PERBAIKAN LOGIKA) -->
         <!-- Check URL: Jika admin/target... atau admin/targetmandor... maka OPEN = TRUE -->
        <div x-data="{ open: {{ request()->is('admin/target*') || request()->is('admin/targetmandor*') ? 'true' : 'false' }} }">
            <!-- Button Induk -->
            <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition group cursor-pointer focus:outline-none 
                {{ request()->is('admin/target*') || request()->is('admin/targetmandor*') ? 'text-white bg-cemara-800/50' : 'text-gray-300 hover:text-white hover:bg-cemara-800/50' }}">
                
                <div class="flex items-center gap-3">
                    <i class="ph ph-target text-xl transition {{ request()->is('admin/target*') || request()->is('admin/targetmandor*') ? 'text-gold-400' : 'group-hover:text-gold-400' }}"></i>
                    <span class="font-medium text-left">Pusat Target</span>
                </div>
                <i class="ph-bold ph-caret-down transition-transform duration-300" 
                   :class="{'rotate-180': open, 'text-white': open, 'text-gray-500 group-hover:text-white': !open}"></i>
            </button>
            
            <!-- Isi Dropdown (Satu Container) -->
            <div x-show="open" x-transition class="pl-11 pr-2 py-1 space-y-1 bg-cemara-900/30 rounded-b-xl mt-1">
                
                <!-- Input Target -->
                <!-- Cek route: admin.target.input ATAU admin.targetmandor.inputtarget -->
                <a href="{{ route('admin.target.input') }}" 
                   class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 
                   {{ (request()->routeIs('admin.target.input') || request()->routeIs('admin.targetmandor.inputtarget')) ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-plus-circle text-lg"></i>
                    <span>Input Target</span>
                </a>

                 <!-- Riwayat Target -->
                 <!-- Cek route: admin.target.riwayat ATAU admin.targetmandor.riwayattarget -->
                 <a href="{{ route('admin.targetmandor.riwayattarget') }}" 
                    class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition border-l-2 
                    {{ (request()->routeIs('admin.target.riwayat') || request()->routeIs('admin.targetmandor.riwayattarget')) ? 'text-white border-gold-400 bg-cemara-800/30' : 'text-gray-400 border-transparent hover:text-white hover:bg-cemara-800/30 hover:border-gold-400' }}">
                    <i class="ph ph-target text-lg"></i>
                    <span>Riwayat Target</span>
                </a>
            </div>
        </div>        

        <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-300 hover:text-white hover:bg-cemara-800/50 rounded-xl transition group">
            <i class="ph ph-users text-xl group-hover:text-gold-400 transition"></i>
            <span class="font-medium">Pengguna (User)</span>
        </a>

    </nav>
    
    <!-- Footer: User Profile & Logout (DINAMIS) -->
    <div class="p-4 border-t border-cemara-800 bg-cemara-900/50 z-20 relative bg-cemara-900">
        <div class="flex items-center gap-3 group cursor-pointer">
            <!-- Link ke Settings (Profil Dinamis) -->
            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 flex-1" title="Pengaturan Profil">
                <!-- LOGIKA AVATAR: Cek apakah ada file di storage, jika tidak pakai inisial -->
                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=f59e0b&color=fff' }}" 
                     alt="Profile" 
                     class="w-10 h-10 rounded-full border-2 border-gold-500 p-0.5 group-hover:border-white transition object-cover">
                
                <div class="flex-1 min-w-0">
                    <!-- Nama Admin Dinamis -->
                    <p class="text-sm font-bold text-white truncate font-poppins group-hover:text-gold-400 transition">
                        {{ Auth::user()->name }}
                    </p>
                    <!-- Role Dinamis -->
                    <p class="text-xs text-gold-400 truncate capitalize">
                        {{ ucfirst(Auth::user()->role) }}
                    </p>
                </div>
            </a>
            <!-- Form Logout (Hidden) -->
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>

            <!-- Tombol Logout dengan Konfirmasi -->
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

    <!-- Script SweetAlert Konfirmasi Logout -->
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