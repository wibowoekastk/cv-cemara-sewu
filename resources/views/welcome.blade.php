<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Cemara Sewu</title>
    
    <!-- 1. Panggil Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- 2. Font Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- 3. KONFIGURASI TEMA -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'poppins'],
                        poppins: ['"Playfair Display"', 'poppins'],
                    },
                    colors: {
                        // Palet Warna Premium CV Cemara Sewu
                        cemara: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            800: '#166534', // Hijau Hutan
                            900: '#14532d', // Hijau Sangat Gelap (Premium)
                            950: '#052e16',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706', // Emas Elegan
                            700: '#b45309',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans','poppins'; }
        h1, h2, h3, h4 { font-family: 'poppins', 'sans-serif'; }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-800">

    <!-- NAVBAR -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-24">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10  rounded-lg flex items-center justify-center shadow-md shadow-gold-500/20 overflow-hidden shrink-0">
                     <!-- Gunakan asset() untuk memanggil gambar dari folder public -->
                     <!-- Pastikan file cms.png ada di dalam folder public/ -->
                    <img src="{{ asset('cms.png') }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="font-bold text-2xl tracking-tight text-cemara-950 leading-none">Cemara Sewu</span>
                        <span class="text-[10px] text-gold-600 font-bold tracking-[0.2em] uppercase mt-1">Peternakan Ayam Petelur</span>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-10 items-center">
                    <a href="#home" class="text-gray-600 hover:text-cemara-900 font-medium transition text-sm uppercase tracking-wider border-b-2 border-transparent hover:border-gold-500 pb-1">Home</a>
                    <a href="#about" class="text-gray-600 hover:text-cemara-900 font-medium transition text-sm uppercase tracking-wider border-b-2 border-transparent hover:border-gold-500 pb-1">Tentang Kami</a>
                    <a href="#services" class="text-gray-600 hover:text-cemara-900 font-medium transition text-sm uppercase tracking-wider border-b-2 border-transparent hover:border-gold-500 pb-1">Layanan</a>
                    <a href="#contact" class="text-gray-600 hover:text-cemara-900 font-medium transition text-sm uppercase tracking-wider border-b-2 border-transparent hover:border-gold-500 pb-1">Kontak</a>
                </div>

                <!-- LOGIN BUTTON -->
                <div class="flex items-center gap-4">
                    <a href="/login" class="group relative px-8 py-3 bg-cemara-900 text-white rounded-full font-semibold shadow-[0_10px_20px_rgba(20,83,45,0.3)] hover:shadow-[0_15px_30px_rgba(20,83,45,0.5)] transition-all duration-300 overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2 tracking-wide text-sm">
                            LOGIN SISTEM
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </span>
                        <div class="absolute inset-0 h-full w-full bg-linear-to-r from-cemara-800 to-cemara-950 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="home" class="relative pt-32 pb-20 lg:pt-52 lg:pb-40 overflow-hidden bg-cemara-950">
        <!-- Background Overlay -->
        <div class="absolute inset-0 opacity-30">
            <img src="https://plus.unsplash.com/premium_photo-1661930553507-59420df08d82?q=80&w=1074&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Farm" class="w-full h-full object-cover grayscale mix-blend-multiply">
        </div>
        <div class="absolute inset-0 bg-linear-to-b from-cemara-950/80 via-transparent to-gray-50"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-gold-400 text-xs font-bold tracking-[0.2em] uppercase mb-8 animate-fade-in-up">
                <span class="w-2 h-2 rounded-full bg-gold-500 animate-pulse"></span>
                Official Website CV Cemara Sewu
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-poppins text-white mb-8 leading-tight drop-shadow-2xl">
                Kualitas Kami Yang Terbaik <br>
                <span class="text-transparent bg-clip-text bg-linear-to-r from-gold-300 via-gold-500 to-gold-700">Terbaik & Alami</span>
            </h1>
            
            <p class="mt-4 text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto mb-12 font-light leading-relaxed">
                Sistem Informasi Manajemen Terintegrasi untuk memantau kualitas produksi, kesehatan ayam, dan distribusi dari Unit Pusat hingga Kandang.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="/dashboard" class="px-10 py-4 bg-gold-600 hover:bg-gold-500 text-white rounded-full font-bold transition transform hover:-translate-y-1 shadow-lg hover:shadow-gold-500/50 flex items-center justify-center gap-2">
                    Masuk Dashboard
                </a>
                <a href="#about" class="px-10 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 text-white rounded-full font-bold transition flex items-center justify-center gap-2">
                    Tentang Kami
                </a>
            </div>
        </div>
    </section>

    <!-- STATS BAR -->
    <div class="relative z-20 -mt-24 max-w-6xl mx-auto px-4">
        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] p-10 grid grid-cols-1 md:grid-cols-3 gap-8 border border-gray-100">
            <div class="text-center group hover:-translate-y-1 transition duration-300">
                <div class="w-16 h-16 mx-auto bg-cemara-50 text-cemara-600 rounded-full flex items-center justify-center text-3xl mb-4 group-hover:bg-cemara-900 group-hover:text-white transition">📈</div>
                <div class="text-4xl font-poppins font-bold text-cemara-900 mb-2">Realtime</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest font-bold">Data Monitoring</div>
            </div>
            <div class="text-center group hover:-translate-y-1 transition duration-300 border-l-0 md:border-l border-r-0 md:border-r border-gray-100">
                <div class="w-16 h-16 mx-auto bg-cemara-50 text-cemara-600 rounded-full flex items-center justify-center text-3xl mb-4 group-hover:bg-cemara-900 group-hover:text-white transition">🥚</div>
                <div class="text-4xl font-poppins font-bold text-cemara-900 mb-2">Grade A</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest font-bold">Kualitas Produk</div>
            </div>
            <div class="text-center group hover:-translate-y-1 transition duration-300">
                <div class="w-16 h-16 mx-auto bg-cemara-50 text-cemara-600 rounded-full flex items-center justify-center text-3xl mb-4 group-hover:bg-cemara-900 group-hover:text-white transition">🛡️</div>
                <div class="text-4xl font-poppins font-bold text-cemara-900 mb-2">Terjamin</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest font-bold">Biosecurity Kandang</div>
            </div>
        </div>
    </div>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-32 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="relative">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-gold-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-cemara-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                    
                    <!-- DIGANTI DI SINI: Menggunakan gambar lokal 'hero-farm.png' -->
                    <img src="hero-farm.jpg" class="relative rounded-4xl shadow-2xl z-10 w-full object-cover h-150 border-8 border-white" alt="Peternakan">
                    
                    <div class="absolute bottom-10 -left-10 bg-white p-8 rounded-2xl shadow-xl z-20 max-w-sm border-l-8 border-gold-500 hidden md:block">
                        <p class="text-cemara-950 font-poppins text-xl italic leading-relaxed">"Teknologi bukan pengganti alam, tapi alat untuk merawatnya dengan lebih baik Alam Juga Merasakan Semuanya."</p>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-gold-600 font-bold tracking-[0.2em] uppercase text-sm mb-6 flex items-center gap-3">
                        <span class="w-10 h-0.5 bg-gold-600"></span> Tentang Kami
                    </h2>
                    <h3 class="text-4xl md:text-5xl font-poppins font-bold text-cemara-950 mb-8 leading-tight">Dedikasi Modernisasi <br>Industri Peternakan</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed text-lg text-justify">
                        CV Cemara Sewu berdiri dengan visi memodernisasi industri peternakan ayam petelur. Melalui SIM (Sistem Informasi Manajemen) ini, Owner dapat memantau setiap unit kandang secara realtime tanpa batasan jarak, memastikan efisiensi pakan, dan kesehatan ternak yang optimal.
                    </p>
                    
                    <div class="space-y-8 mt-10">
                        <div class="flex items-start group">
                            <div class="shrink-0 w-14 h-14 bg-white border border-gray-100 shadow-md rounded-2xl flex items-center justify-center text-cemara-900 text-2xl group-hover:bg-cemara-900 group-hover:text-white transition duration-300">
                                🏢
                            </div>
                            <div class="ml-6">
                                <h4 class="text-xl font-bold text-cemara-900 mb-2">Manajemen Terpusat</h4>
                                <p class="text-gray-500">Kontrol penuh operasional dari hulu ke hilir dalam satu dashboard.</p>
                            </div>
                        </div>
                        <div class="flex items-start group">
                            <div class="shrink-0 w-14 h-14 bg-white border border-gray-100 shadow-md rounded-2xl flex items-center justify-center text-cemara-900 text-2xl group-hover:bg-cemara-900 group-hover:text-white transition duration-300">
                                📱
                            </div>
                            <div class="ml-6">
                                <h4 class="text-xl font-bold text-cemara-900 mb-2">Monitoring Jarak Jauh</h4>
                                <p class="text-gray-500">Pemilik & Mandor dapat mengakses data kapanpun dan dimanapun.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES -->
    <section id="services" class="py-32 bg-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gray-50/50 skew-x-12 transform translate-x-20"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-20 max-w-3xl mx-auto">
                <h2 class="text-gold-600 font-bold tracking-[0.2em] uppercase text-sm mb-4">Layanan & Fasilitas</h2>
                <h3 class="text-4xl md:text-5xl font-poppins font-bold text-cemara-950 mb-6">Keunggulan Operasional</h3>
                <p class="text-gray-600 text-lg">Standar tinggi dalam setiap aspek pemeliharaan dan produksi.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-white p-10 rounded-4xl shadow-xl border border-gray-100 hover:border-gold-400 transition duration-500 group">
                    <div class="w-16 h-16 bg-cemara-50 text-cemara-800 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition duration-300">🍃</div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-4 font-poppins">Pakan Berkualitas</h4>
                    <p class="text-gray-600 leading-relaxed">Formulasi pakan yang diatur oleh ahli nutrisi untuk menjamin kesehatan ayam dan kualitas cangkang telur.</p>
                </div>
                <div class="bg-cemara-900 p-10 rounded-4xl shadow-2xl border border-cemara-800 transform md:-translate-y-4 group">
                    <div class="w-16 h-16 bg-white/10 text-white rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition duration-300">📊</div>
                    <h4 class="text-2xl font-bold text-white mb-4 font-poppins">Digital Reporting</h4>
                    <p class="text-cemara-100 leading-relaxed">Sistem pelaporan digital yang menghilangkan human-error dan mempercepat pengambilan keputusan.</p>
                </div>
                <div class="bg-white p-10 rounded-4xl shadow-xl border border-gray-100 hover:border-gold-400 transition duration-500 group">
                    <div class="w-16 h-16 bg-cemara-50 text-cemara-800 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition duration-300">🚛</div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-4 font-poppins">Distribusi Cepat</h4>
                    <p class="text-gray-600 leading-relaxed">Armada logistik yang siap mengantar pasokan telur segar ke berbagai mitra di seluruh wilayah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="contact" class="bg-cemara-950 text-white pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-16 border-b border-white/10 pb-16">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10  rounded-lg flex items-center justify-center shadow-md shadow-gold-500/20 overflow-hidden shrink-0">
                        <!-- Gunakan asset() untuk memanggil gambar dari folder public -->
                        <!-- Pastikan file cms.png ada di dalam folder public/ -->
                        <img src="{{ asset('cms.png') }}" alt="Logo" class="w-full h-full object-cover">
        </div>
                        <span class="font-poppins font-bold text-3xl">Cemara Sewu</span>
                    </div>
                    <p class="text-gray-400 leading-relaxed mb-8 max-w-md text-lg">
                        Kami Hadir Untuk Memenuhi Kebutuhan Kalian Semua Konsumen Cemara Sewu Yang Kami Kasihi.
                    </p>
                    
                    
                <script src="https://unpkg.com/@phosphor-icons/web"></script>

                    <div class="flex gap-4">
                        <a href="https://www.instagram.com/cemara_sewu_farm?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="class="w-10 h-10 rounded-full bg-white/5 hover:bg-gold-500 flex items-center justify-center transition text-white"><i class="ph ph-instagram-logo text-2xl"></i></a>
                        <a href="#"class="w-10 h-10 rounded-full bg-white/5 hover:bg-gold-500 flex items-center justify-center transition text-white"><i class="ph ph-facebook-logo text-2xl"></i></a>
                        <a href="#"class="w-10 h-10 rounded-full bg-white/5 hover:bg-gold-500 flex items-center justify-center transition text-white"><i class="ph ph-whatsapp-logo text-2xl"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-bold mb-8 text-gold-500 tracking-widest uppercase text-xs">Navigasi</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li><a href="#home" class="hover:text-white transition flex items-center gap-2"><span class="w-1 h-1 bg-gold-500 rounded-full"></span> Beranda</a></li>
                        <li><a href="#about" class="hover:text-white transition flex items-center gap-2"><span class="w-1 h-1 bg-gold-500 rounded-full"></span> Tentang Kami</a></li>
                        <li><a href="#services" class="hover:text-white transition flex items-center gap-2"><span class="w-1 h-1 bg-gold-500 rounded-full"></span> Layanan</a></li>
                        <li><a href="/login" class="text-white font-bold transition flex items-center gap-2 hover:text-gold-500">Login Sistem 📥</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-8 text-gold-500 tracking-widest uppercase text-xs">Hubungi Kami</h4>
                    <ul class="space-y-6 text-gray-400">
                        <li class="flex items-start gap-4">
                            <span class="mt-1 text-gold-500">📍</span>
                            <span>Jl. Raya Pantura Tegal Pemalang,<br>Kedondong, Padaharja, Kramat, Tegal Regency,<br>Jawa Tengah, Indonesia</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <span class="text-gold-500">📞</span>
                            <span>+62 812-3456-7890</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <span class="text-gold-500">✉️</span>
                            <span></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-10 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
               <?php echo date('© Y'); ?> CV Cemara Sewu. All rights reserved. Erkprd®
                <div class="flex gap-8 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white">Privacy Policy</a>
                    <a href="#" class="hover:text-white">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>