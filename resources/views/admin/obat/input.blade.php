<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Stok Obat - Admin Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-poppins'] },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' },
                        medical: { 50: '#ecfeff', 100: '#cffafe', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Sidebar -->
        @include('admin.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Manajemen Data</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Obat</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Input Stok</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Input Stok Masuk</h2>
                </div>
                <!-- Date Display -->
                <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-medical-50 text-medical-700 rounded-lg text-sm font-semibold border border-medical-100">
                    <i class="ph-fill ph-calendar-plus"></i>
                    <span id="headerDateDisplay"></span>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full max-w-6xl mx-auto">
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 font-bold flex items-center gap-2 shadow-sm">
                        <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.obat.store_stok') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    @csrf
                    
                    <!-- KOLOM KIRI: Informasi Produk (Master) -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                                <div class="w-10 h-10 rounded-full bg-medical-100 flex items-center justify-center text-medical-600">
                                    <i class="ph-fill ph-pill text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">Identitas Produk</h3>
                                    <p class="text-xs text-gray-500">Input manual nama obat baru atau yang sudah ada</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Nama Obat (Input Manual dengan Datalist) -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Obat / Produk</label>
                                    <div class="relative">
                                        <input type="text" name="nama_obat" list="obatList" placeholder="Ketik nama obat..." 
                                               class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-medical-500 outline-none transition font-bold text-gray-800" required>
                                        <i class="ph-bold ph-flask absolute left-3 top-3.5 text-gray-400"></i>
                                        
                                        <!-- Saran Otomatis dari Database -->
                                        <datalist id="obatList">
                                            @foreach($obats as $o)
                                                <option value="{{ $o->nama_obat }}">{{ $o->jenis_obat }} - {{ $o->satuan }}</option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">*Jika nama obat belum ada, akan otomatis dibuatkan Master baru.</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Kategori -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kategori</label>
                                        <div class="relative">
                                            <select name="jenis_obat" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-medical-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                <option value="Vaksin">Vaksin</option>
                                                <option value="Vitamin">Vitamin</option>
                                                <option value="Antibiotik">Antibiotik</option>
                                                <option value="Disinfektan">Disinfektan</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                            <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                        </div>
                                    </div>

                                    <!-- Satuan -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Satuan</label>
                                        <div class="relative">
                                            <select name="satuan" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-medical-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                <option value="Botol">Botol</option>
                                                <option value="Sachet">Sachet</option>
                                                <option value="Liter">Liter</option>
                                                <option value="Kg">Kg</option>
                                                <option value="Pcs">Pcs</option>
                                            </select>
                                            <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Detail Batch -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                                <div class="w-10 h-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600">
                                    <i class="ph-fill ph-package text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">Detail Batch</h3>
                                    <p class="text-xs text-gray-500">Informasi stok fisik yang masuk</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Jumlah -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Jumlah Masuk</label>
                                    <input type="number" name="stok_awal" placeholder="0" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-bold text-gray-800" required>
                                </div>

                                <!-- Batch Number -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">No. Batch (Opsional)</label>
                                    <input type="text" name="kode_batch" placeholder="AUTO" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-medium text-gray-700 uppercase">
                                </div>

                                <!-- Tanggal Masuk -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tanggal Masuk</label>
                                    <input type="date" name="tgl_masuk" id="dateInput" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-medical-500 outline-none text-sm font-medium">
                                </div>

                                <!-- Expired Date -->
                                <div>
                                    <label class="block text-xs font-bold text-red-500 uppercase tracking-wide mb-2">Expired Date</label>
                                    <div class="relative">
                                        <input type="date" name="tgl_kadaluarsa" class="w-full pl-10 pr-4 py-3 bg-red-50 border border-red-100 text-red-700 rounded-xl focus:ring-2 focus:ring-red-500 outline-none transition font-bold" required>
                                        <i class="ph-bold ph-calendar-x absolute left-3 top-3.5 text-red-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- KOLOM KANAN: Aksi -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                            <h3 class="text-sm font-bold text-gray-800 mb-4">Konfirmasi</h3>
                            <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                                Pastikan <strong>Nama Obat</strong> dan <strong>Expired Date</strong> sudah benar. Jika obat baru, data Master akan otomatis dibuat.
                            </p>

                            <div class="flex flex-col gap-3">
                                <button type="submit" class="w-full py-3 bg-medical-600 text-white font-bold rounded-xl hover:bg-medical-700 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <i class="ph-bold ph-floppy-disk"></i>
                                    Simpan Stok
                                </button>
                                <button type="reset" class="w-full py-3 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition">
                                    Reset Form
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dateInput = document.getElementById('dateInput');
            const displayDate = document.getElementById('headerDateDisplay');
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            
            if(dateInput) dateInput.value = `${yyyy}-${mm}-${dd}`;
            if(displayDate) {
                const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
                displayDate.innerText = today.toLocaleDateString('id-ID', options);
            }
        });
    </script>
</body>
</html>