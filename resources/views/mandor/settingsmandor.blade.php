<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Mandor Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Poppins"', 'sans-poppins'],
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

        <!-- Sidebar Mandor -->
        @include('mandor.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm glass-effect">
                <div>
                    <div class="flex items-center gap-2 text-gray-400 text-xs font-medium mb-1 uppercase tracking-wider">
                        <span>Akun</span>
                        <i class="ph-bold ph-caret-right text-gold-500"></i>
                        <span>Pengaturan</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full max-w-5xl mx-auto">
                
                <!-- Notifikasi Sukses -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex items-center gap-2">
                        <i class="ph-fill ph-check-circle text-lg"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- FORM UTAMA: Profil & Foto (Multipart untuk upload) -->
                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-3 grid grid-cols-1 lg:grid-cols-3 gap-8">
                        @csrf

                        <!-- KOLOM KIRI: Foto Profil & Info Wilayah -->
                        <div class="space-y-6">
                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 text-center relative overflow-hidden">
                                <!-- Hiasan Background Kecil -->
                                <div class="absolute top-0 left-0 w-full h-24 bg-cemara-900/5 z-0"></div>

                                <!-- Avatar Upload Logic (Alpine JS) -->
                                <div class="relative w-32 h-32 mx-auto mb-4 group z-10" 
                                     x-data="{ 
                                        photoName: null, 
                                        photoPreview: '{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : null }}' 
                                     }">
                                    
                                    <!-- Input File Hidden -->
                                    <input type="file" name="photo" id="photo" class="hidden" x-ref="photo"
                                           x-on:change="
                                                photoName = $refs.photo.files[0].name;
                                                const reader = new FileReader();
                                                reader.onload = (e) => { photoPreview = e.target.result; };
                                                reader.readAsDataURL($refs.photo.files[0]);
                                           ">

                                    <!-- Tampilan Gambar -->
                                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-gray-100 relative">
                                        <!-- Preview Baru -->
                                        <template x-if="photoPreview">
                                            <img :src="photoPreview" class="w-full h-full object-cover">
                                        </template>
                                        <!-- Default / Inisial -->
                                        <template x-if="!photoPreview">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=22c55e&color=fff&size=200" class="w-full h-full object-cover">
                                        </template>
                                    </div>

                                    <!-- Tombol Ganti Foto -->
                                    <button type="button" x-on:click.prevent="$refs.photo.click()" class="absolute bottom-0 right-0 bg-gold-500 text-white p-2 rounded-full shadow-md hover:bg-gold-600 transition" title="Ganti Foto">
                                        <i class="ph-bold ph-camera text-lg"></i>
                                    </button>
                                </div>

                                <div class="relative z-10">
                                    <!-- Nama & Role Dinamis -->
                                    <h3 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h3>
                                    <p class="text-sm text-gray-500 mb-4 capitalize">{{ Auth::user()->role }}</p>

                                    <!-- INFO WILAYAH DINAMIS -->
                                    <div class="text-xs text-left bg-cemara-50 p-4 rounded-2xl border border-cemara-100 text-cemara-800 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="font-bold text-gray-500">Status:</span>
                                            <span class="text-green-600 font-bold">Aktif</span>
                                        </div>
                                        
                                        <!-- UPDATE DI SINI: Unit & Lokasi Dinamis -->
                                        <div class="flex justify-between border-t border-cemara-200/50 pt-2">
                                            <span class="font-bold text-gray-500">Unit:</span>
                                            <span class="font-bold text-cemara-700">
                                                @if(Auth::user()->unit)
                                                    {{ Auth::user()->unit->nama_unit }}
                                                @elseif(Auth::user()->unit_id)
                                                    Unit {{ Auth::user()->unit_id }}
                                                @else
                                                    Belum Ada Unit
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex justify-between border-t border-cemara-200/50 pt-2">
                                            <span class="font-bold text-gray-500">Lokasi:</span>
                                            <span class="font-bold text-cemara-700">
                                                @if(Auth::user()->unit && Auth::user()->unit->lokasi)
                                                    {{ Auth::user()->unit->lokasi }}
                                                @elseif(Auth::user()->lokasi_id == 1)
                                                    Kalirambut
                                                @elseif(Auth::user()->lokasi_id == 2)
                                                    Sokawangi
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                        <!-- END UPDATE -->

                                        <div class="flex justify-between border-t border-cemara-200/50 pt-2">
                                            <span class="font-bold text-gray-500">Bergabung:</span>
                                            <span>{{ Auth::user()->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: Form Edit Profil & Password -->
                        <div class="lg:col-span-2 space-y-8">
                            
                            <!-- 1. Edit Profil -->
                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                        <i class="ph-fill ph-identification-card text-cemara-600 text-xl"></i> Informasi Pribadi
                                    </h3>
                                </div>
                                
                                <div class="p-6 space-y-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Lengkap</label>
                                        <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-800">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Email Address</label>
                                        <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium text-gray-800">
                                    </div>
                                </div>
                                
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                                    <button type="submit" class="px-6 py-2.5 bg-cemara-900 text-white rounded-xl font-bold text-sm hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20 hover:-translate-y-0.5">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- 2. Ganti Password -->
                    <div class="lg:col-span-3 lg:col-start-2">
                        <form action="{{ route('user.password.update') }}" method="POST" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            @csrf
                            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                    <i class="ph-fill ph-lock-key text-gold-500 text-xl"></i> Ganti Password
                                </h3>
                            </div>

                            @if ($errors->any())
                                <div class="p-4 bg-red-50 text-red-600 text-sm border-b border-red-100">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="p-6 space-y-5" x-data="{ showPass: false }">
                                <!-- Password Lama -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Password Saat Ini</label>
                                    <input :type="showPass ? 'text' : 'password'" name="current_password" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Password Baru -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Password Baru</label>
                                        <input :type="showPass ? 'text' : 'password'" name="new_password" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition">
                                    </div>
                                    <!-- Konfirmasi Password -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Konfirmasi Password</label>
                                        <input :type="showPass ? 'text' : 'password'" name="new_password_confirmation" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition">
                                    </div>
                                </div>

                                <!-- Show Password Toggle -->
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="showPass" class="w-4 h-4 text-gold-600 rounded border-gray-300 focus:ring-gold-500 cursor-pointer" @click="showPass = !showPass">
                                    <label for="showPass" class="text-sm text-gray-600 cursor-pointer select-none">Tampilkan Password</label>
                                </div>
                            </div>
                            
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </main>
    </div>

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