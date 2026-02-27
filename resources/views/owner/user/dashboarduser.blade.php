<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Owner Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Poppins"', 'sans-poppins'], 
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
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Overlay Mobile -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Sidebar Owner -->
        @include('owner.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm glass-effect">
                <div>
                    <div class="flex items-center gap-2 text-gray-400 text-xs font-medium mb-1 uppercase tracking-wider">
                        <span>Pengguna</span>
                        <i class="ph-bold ph-caret-right text-gold-500"></i>
                        <span>Dashboard</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Tambah User Shortcut -->
                    <a href="{{ route('owner.user.input') }}" class="hidden md:flex items-center gap-2 px-5 py-2.5 bg-cemara-900 text-white rounded-xl text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20 hover:-translate-y-0.5">
                        <i class="ph-bold ph-user-plus text-lg"></i> Tambah User
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full max-w-7xl mx-auto space-y-8">
                
                {{-- LOGIKA HITUNG DINAMIS --}}
                @php
                    // Ambil data $users yang dikirim dari Route Closure
                    $countTotal = $users->count();
                    $countAdmin = $users->where('role', 'admin')->count();
                    $countMandor = $users->where('role', 'mandor')->count();
                    $countOwner = $users->where('role', 'owner')->count();
                @endphp

                <!-- 1. Stats Overview (DINAMIS) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Total User -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition duration-300">
                        <div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Akun</p>
                            <h3 class="text-4xl font-bold text-gray-800">{{ $countTotal }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-gray-50 text-gray-600 rounded-2xl flex items-center justify-center text-3xl">
                            <i class="ph-fill ph-users"></i>
                        </div>
                    </div>

                    <!-- Admin -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-200 border-l-4 border-l-purple-500 shadow-sm flex items-center justify-between hover:shadow-md transition duration-300">
                        <div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Admin</p>
                            <h3 class="text-4xl font-bold text-gray-800">{{ $countAdmin }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-3xl">
                            <i class="ph-fill ph-crown"></i>
                        </div>
                    </div>

                    <!-- Mandor -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition duration-300 border-l-4 border-l-blue-500">
                        <div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Mandor</p>
                            <h3 class="text-4xl font-bold text-gray-800">{{ $countMandor }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-3xl">
                            <i class="ph-fill ph-hard-hat"></i>
                        </div>
                    </div>

                    <!-- Owner -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition duration-300 border-l-4 border-gold-500">
                        <div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Owner</p>
                            <h3 class="text-4xl font-bold text-gray-800">{{ $countOwner }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-gold-50 text-gold-600 rounded-2xl flex items-center justify-center text-3xl">
                            <i class="ph-fill ph-briefcase"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. Role Permissions & Access Info (STATIC UI) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Title Section -->
                    <div class="lg:col-span-3 mb-2">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="ph-fill ph-shield-check text-cemara-600"></i> Hak Akses & Wewenang
                        </h3>
                        <p class="text-sm text-gray-500">Detail perizinan untuk setiap peran pengguna dalam sistem.</p>
                    </div>

                    <!-- Card: Admin -->
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                            <i class="ph-fill ph-crown text-8xl text-purple-600"></i>
                        </div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-lg shadow-sm">
                                <i class="ph-bold ph-crown"></i>
                            </div>
                            <h4 class="font-bold text-lg text-gray-800">Administrator</h4>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-green-500 mt-0.5"></i>
                                <span>Mengelola seluruh data Master (Unit, Kandang, Pakan, Obat).</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-green-500 mt-0.5"></i>
                                <span>Menambah & menghapus user Mandor.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-green-500 mt-0.5"></i>
                                <span>Memverifikasi & mengedit laporan harian.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-green-500 mt-0.5"></i>
                                <span>Akses penuh ke semua laporan & grafik.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Card: Mandor -->
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                            <i class="ph-fill ph-hard-hat text-8xl text-blue-600"></i>
                        </div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                                <i class="ph-bold ph-hard-hat"></i>
                            </div>
                            <h4 class="font-bold text-lg text-gray-800">Mandor Lapangan</h4>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-green-500 mt-0.5"></i>
                                <span>Input laporan harian (Produksi, Pakan, Kematian).</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-green-500 mt-0.5"></i>
                                <span>Hanya akses ke <strong>Unit</strong> yang ditugaskan.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-x text-red-500 mt-0.5"></i>
                                <span>Tidak bisa menghapus data master.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-x text-red-500 mt-0.5"></i>
                                <span>Tidak bisa melihat data keuangan.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Card: Owner -->
                    <div class="bg-cemara-900 rounded-3xl p-6 border border-cemara-800 shadow-lg text-white relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                            <i class="ph-fill ph-briefcase text-8xl text-gold-500"></i>
                        </div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-gold-500 text-cemara-950 flex items-center justify-center text-lg shadow-lg">
                                <i class="ph-bold ph-briefcase"></i>
                            </div>
                            <h4 class="font-bold text-lg text-white">Owner (Pemilik)</h4>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-300">
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-gold-500 mt-0.5"></i>
                                <span>Monitoring seluruh aktivitas secara Real-time.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-gold-500 mt-0.5"></i>
                                <span>Akses penuh Laporan Keuangan & Profit.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-check text-gold-500 mt-0.5"></i>
                                <span>Manajemen User tingkat tinggi (Tambah Admin).</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ph-bold ph-info text-blue-400 mt-0.5"></i>
                                <span>Mode 'Read-Only' pada data operasional (untuk keamanan data).</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 3. Recent Activity (TABEL DINAMIS) -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                <i class="ph-fill ph-clock-user text-gray-400"></i> Pengguna Terbaru
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">5 pengguna terakhir yang terdaftar.</p>
                        </div>
                        <a href="{{ route('owner.user.data') }}" class="px-4 py-2 bg-white border border-gray-200 text-cemara-700 text-xs font-bold rounded-xl hover:bg-gray-50 transition shadow-sm">
                            Lihat Semua User
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold uppercase tracking-wider text-xs">
                                <tr>
                                    <th class="px-6 py-4">User</th>
                                    <th class="px-6 py-4">Role</th>
                                    <th class="px-6 py-4">Lokasi / Unit</th>
                                    <th class="px-6 py-4">Tanggal Daftar</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                {{-- Ambil 5 user terbaru --}}
                                @forelse($users->take(5) as $user)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/'.$user->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-cemara-100 text-cemara-700 flex items-center justify-center font-bold text-sm shadow-sm group-hover:scale-110 transition">
                                                    {{ substr($user->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- Role --}}
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border shadow-sm capitalize 
                                            {{ $user->role == 'admin' ? 'bg-purple-50 text-purple-700 border-purple-100' : 
                                               ($user->role == 'mandor' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gold-50 text-gold-700 border-gold-100') }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>

                                    {{-- Lokasi --}}
                                    <td class="px-6 py-4 text-xs font-medium text-gray-500">
                                        {{ $user->role == 'mandor' ? 'Unit ' . ($user->unit_id ?? '-') : 'Kantor Pusat' }}
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 text-gray-500 text-xs">
                                        {{ $user->created_at->diffForHumans() }}
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Active
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-xs italic">Belum ada data user.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Script Toggle Sidebar -->
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
    </script>
</body>
</html>