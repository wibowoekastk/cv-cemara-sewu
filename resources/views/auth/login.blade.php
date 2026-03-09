<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem - CV Cemara Sewu</title>
    
    <!-- Tailwind & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-poppins'],
                        poppins: ['"Playfair Display"', 'poppins'],
                    },
                    colors: {
                        cemara: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            800: '#166534', 
                            900: '#14532d', 
                            950: '#052e16',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-screen w-full bg-gray-50 flex overflow-hidden">

    <!-- LEFT SIDE: Image & Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-cemara-900 items-center justify-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://plus.unsplash.com/premium_photo-1661930553507-59420df08d82?q=80&w=1074&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
                 class="w-full h-full object-cover opacity-30 mix-blend-overlay" 
                 alt="Farm Background">
            <div class="absolute inset-0 bg-linear-to-br from-cemara-900/90 to-cemara-950/90"></div>
        </div>

        <!-- Tombol Kembali (Desktop) -->
        <a href="/" class="absolute top-8 left-8 z-20 flex items-center gap-2 text-white/70 hover:text-white transition group">
            <div class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center group-hover:bg-white/10 transition">
                <i class="ph-bold ph-arrow-left text-xl"></i>
            </div>
            <span class="text-sm font-medium tracking-wide">Kembali ke Beranda</span>
        </a>

        <!-- Content -->
        <div class="relative z-10 p-12 text-white max-w-lg">
            <div class="w-10 h-10  rounded-lg flex items-center justify-center shadow-md shadow-gold-500/20 overflow-hidden shrink-0">
                     <!-- Gunakan asset() untuk memanggil gambar dari folder public -->
                     <!-- Pastikan file cms.png ada di dalam folder public/ -->
                    <img src="{{ asset('cms.png') }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
            <h1 class="font-poppins text-5xl font-bold mb-6 leading-tight">
                Sistem Informasi <br>
                <span class="text-gold-400">Manajemen Peternakan</span>
            </h1>
            <p class="text-cemara-100 text-lg leading-relaxed mb-8 font-light">
                Kelola unit, pantau produksi telur, dan monitoring kesehatan ternak secara realtime dalam satu platform terintegrasi.
            </p>
            
            <!-- Quote Box -->
            <div class="bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/10">
                <div class="flex gap-1 text-gold-400 mb-3">
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                </div>
                <p class="text-sm italic text-gray-200">"Kualitas telur terbaik dimulai dari manajemen kandang yang cerdas dan terukur."</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 relative overflow-y-auto">
        
        <!-- Tombol Kembali (Mobile Only) -->
        <a href="/" class="lg:hidden absolute top-6 left-6 text-gray-500 hover:text-cemara-900 flex items-center gap-2 transition">
            <i class="ph-bold ph-arrow-left text-xl"></i>
            <span class="text-sm font-semibold">Kembali</span>
        </a>

        <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-3xl shadow-xl border border-gray-100 mt-10 lg:mt-0">
            
            <!-- Mobile Logo -->
            <div class="lg:hidden flex justify-center mb-8">
                <div class="w-10 h-10  rounded-lg flex items-center justify-center shadow-md shadow-gold-500/20 overflow-hidden shrink-0">
                     <!-- Gunakan asset() untuk memanggil gambar dari folder public -->
                     <!-- Pastikan file cms.png ada di dalam folder public/ -->
                    <img src="{{ asset('cms.png') }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
            </div>

            <div class="text-center mb-10">
                <h2 class="text-cemara-100 text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                <p class="text-gray-500">Silakan login untuk mengakses dashboard.</p>
            </div>

            <!-- Menampilkan Pesan Error jika Login Gagal -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start gap-3">
                    <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-red-700 text-sm">Login Gagal</h4>
                        <ul class="text-xs text-red-600 mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form Start -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf <!-- Token Keamanan Wajib Laravel -->

                <!-- Email Input -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-envelope-simple text-gray-400 text-xl group-focus-within:text-cemara-600 transition"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="nama@cemarasewu.com" 
                               class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cemara-500/20 focus:border-cemara-500 transition duration-200"
                               required autofocus>
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-lock-key text-gray-400 text-xl group-focus-within:text-cemara-600 transition"></i>
                        </div>
                        <input type="password" name="password" id="passwordInput"
                               placeholder="••••••••" 
                               class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cemara-500/20 focus:border-cemara-500 transition duration-200"
                               required>
                        <!-- Tombol Lihat Password -->
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i id="eyeIcon" class="ph ph-eye text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-cemara-600 focus:ring-cemara-500">
                        <span class="text-gray-600">Ingat Saya</span>
                    </label>
                    <a href="#" class="text-cemara-800 font-semibold hover:text-cemara-600 hover:underline">Lupa Password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="w-full bg-cemara-900 text-white font-bold py-4 rounded-xl shadow-lg shadow-cemara-900/20 hover:bg-cemara-800 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 group">
                    <span>Masuk ke Sistem</span>
                    <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

        </div>
        
        <!-- Footer Info Mobile -->
        <div class="absolute bottom-6 text-center w-full text-xs text-gray-400 lg:hidden">
            <?php echo date('© Y'); ?> CV Cemara Sewu. All rights reserved. Erkprd®
        </div>
    </div>

    <!-- Script Sederhana -->
    <script>
        // Toggle Lihat Password
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('ph-eye', 'ph-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('ph-eye-slash', 'ph-eye');
            }
        }

        // Helper untuk Demo (Opsional)
        function fillLogin(email) {
            document.querySelector('input[name="email"]').value = email;
            document.getElementById('passwordInput').value = 'password';
        }
    </script>

</body>
</html>