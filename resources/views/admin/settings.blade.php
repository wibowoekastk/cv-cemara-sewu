<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-poppins'], poppins: ['"Playfair Display"', 'poppins'] },
                    colors: { cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' }, gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' } }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Overlay Mobile -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Sidebar Admin -->
        @include('admin.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Akun</span>
                        <i class="ph-bold ph-caret-right"></i>
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
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex items-center gap-2">
                        <i class="ph-fill ph-check-circle text-lg"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- FORM UTAMA: Profil & Foto -->
                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-3 grid grid-cols-1 lg:grid-cols-3 gap-8">
                        @csrf
                        
                        <!-- KOLOM KIRI: Foto Profil -->
                        <div class="space-y-6">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                                
                                <!-- Logic Preview Gambar AlpineJS -->
                                <div class="relative w-32 h-32 mx-auto mb-4 group" 
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
                                        <!-- Jika ada preview baru -->
                                        <template x-if="photoPreview">
                                            <img :src="photoPreview" class="w-full h-full object-cover">
                                        </template>
                                        <!-- Jika tidak ada (Pakai Inisial/Default) -->
                                        <template x-if="!photoPreview">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=14532d&color=fff&size=200" class="w-full h-full object-cover">
                                        </template>
                                    </div>

                                    <!-- Tombol Ganti Foto -->
                                    <button type="button" x-on:click.prevent="$refs.photo.click()" 
                                            class="absolute bottom-0 right-0 bg-gold-500 text-white p-2 rounded-full shadow-md hover:bg-gold-600 transition" 
                                            title="Ganti Foto">
                                        <i class="ph-bold ph-camera text-lg"></i>
                                    </button>
                                </div>

                                <h3 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h3>
                                <p class="text-sm text-gray-500 mb-4 capitalize">{{ Auth::user()->role }}</p>

                                <div class="text-xs text-left bg-green-50 p-3 rounded-lg border border-green-100 text-green-800">
                                    <p class="mb-1"><span class="font-bold">Status:</span> Aktif</p>
                                    <p><span class="font-bold">Bergabung:</span> {{ Auth::user()->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: Form Edit Profil -->
                        <div class="lg:col-span-2 space-y-8">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                        <i class="ph-fill ph-identification-card text-cemara-600 text-xl"></i> Informasi Pribadi
                                    </h3>
                                </div>
                                
                                <div class="p-6 space-y-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Lengkap</label>
                                        <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Email Address</label>
                                        <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium">
                                    </div>
                                </div>
                                
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                                    <button type="submit" class="px-6 py-2 bg-cemara-900 text-white rounded-lg font-bold text-sm hover:bg-cemara-800 transition shadow-md">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- FORM GANTI PASSWORD -->
                    <div class="lg:col-span-3 lg:col-start-2">
                        <form action="{{ route('user.password.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            @csrf
                            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                    <i class="ph-fill ph-lock-key text-gold-500 text-xl"></i> Ganti Password
                                </h3>
                            </div>
                            
                            @if ($errors->any())
                                <div class="p-4 bg-red-50 text-red-600 text-sm">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="p-6 space-y-5" x-data="{ showPass: false }">
                                <!-- Password Lama -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Password Saat Ini</label>
                                    <input :type="showPass ? 'text' : 'password'" name="current_password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Password Baru -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Password Baru</label>
                                        <input :type="showPass ? 'text' : 'password'" name="new_password" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none">
                                    </div>
                                    <!-- Konfirmasi Password -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Konfirmasi Password</label>
                                        <input :type="showPass ? 'text' : 'password'" name="new_password_confirmation" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none">
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="showPass" class="w-4 h-4 text-gold-600 rounded border-gray-300 focus:ring-gold-500" @click="showPass = !showPass">
                                    <label for="showPass" class="text-sm text-gray-600 cursor-pointer">Tampilkan Password</label>
                                </div>
                            </div>
                            
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-100 transition">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Script Sidebar -->
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