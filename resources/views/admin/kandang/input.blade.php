<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Unit, Batch & Kandang - Admin Panel</title>
    
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
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' }
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
                        <span>Master Data</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Setup Unit, Batch & Kandang</h2>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-semibold border border-cemara-100">
                        <i class="ph-fill ph-calendar-check"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</span>
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="ph-fill ph-check-circle text-xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="ph-fill ph-warning-circle text-xl"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- KOLOM KIRI: DATA MASTER (UNIT & BATCH) -->
                    <div class="space-y-8">
                        
                        <!-- FORM 1: TAMBAH UNIT -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-cemara-50 flex items-center justify-center text-cemara-600">
                                    <i class="ph-fill ph-house-line text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">1. Input Unit (Lokasi)</h3>
                                    <p class="text-xs text-gray-500">Buat lokasi farm baru</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.kandang.store_unit') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Unit</label>
                                        <input type="text" name="nama_unit" placeholder="Contoh: Unit Alpha" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lokasi Farm</label>
                                        <input type="text" name="lokasi" placeholder="Masukkan Lokasi (Contoh: Kalirambut)" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition" required>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="w-full py-3 bg-cemara-900 text-white rounded-xl font-bold hover:bg-cemara-800 transition flex items-center justify-center gap-2">
                                        <i class="ph-bold ph-plus"></i> Simpan Unit
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- FORM 2: TAMBAH BATCH (MASTER DATA) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 border-l-4 border-l-blue-500">
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                    <i class="ph-fill ph-calendar-plus text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">2. Input Master Batch</h3>
                                    <p class="text-xs text-gray-500">Buat Periode/Angkatan Ayam Baru</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.batch.store') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Batch / Angkatan</label>
                                        <input type="text" name="nama_batch" placeholder="Contoh: Batch Lebaran 2026" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Mulai (Perkiraan)</label>
                                        <input type="date" name="tanggal_mulai" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition flex items-center justify-center gap-2">
                                        <i class="ph-bold ph-plus"></i> Simpan Batch Baru
                                    </button>
                                </div>
                            </form>

                            <!-- LIST BATCH YANG SUDAH ADA (AGAR BISA DIHAPUS JIKA DUPLIKAT) -->
                            <div class="mt-8 border-t border-gray-100 pt-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 flex items-center justify-between">
                                    <span>Daftar Batch Aktif</span>
                                    <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ isset($batches) ? $batches->count() : 0 }}</span>
                                </h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                    @if(isset($batches) && count($batches) > 0)
                                        @foreach($batches as $batch)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm border border-gray-100 hover:border-blue-200 transition group">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-800">{{ $batch->nama_batch }}</span>
                                                <span class="text-[10px] text-gray-400">
                                                    {{ $batch->is_active ? '🟢 Aktif' : '🔴 Non-Aktif' }} • 
                                                    {{ \Carbon\Carbon::parse($batch->tanggal_mulai)->format('d M Y') }}
                                                </span>
                                            </div>
                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('admin.batch.delete', $batch->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus Batch ini? Pastikan tidak ada kandang yang menggunakan batch ini.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition" title="Hapus Batch">
                                                    <i class="ph-bold ph-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-center text-xs text-gray-400 py-2 italic">Belum ada batch dibuat.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- KOLOM KANAN: DUA FORM TERPISAH (BANGUN KANDANG & CHICK-IN) -->
                    <div class="space-y-8">

                        <!-- FORM 3: BANGUN KANDANG BARU (FISIK) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gold-50 rounded-bl-full -mr-4 -mt-4 opacity-50 pointer-events-none"></div>
                            
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100 relative z-10">
                                <div class="w-10 h-10 rounded-full bg-gold-50 flex items-center justify-center text-gold-600">
                                    <i class="ph-fill ph-warehouse text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">3a. Bangun Kandang Baru</h3>
                                    <p class="text-xs text-gray-500">Hanya membuat data fisik kandang</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.kandang.store_kandang') }}" method="POST">
                                @csrf
                                <div class="space-y-5 relative z-10">
                                    <!-- Pilih Unit -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Lokasi Unit</label>
                                        <div class="relative">
                                            <select name="unit_id" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-gold-500 outline-none transition cursor-pointer" required>
                                                <option value="" disabled selected>Pilih Unit...</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }} ({{ $unit->lokasi }})</option>
                                                @endforeach
                                            </select>
                                            <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                        </div>
                                    </div>

                                    <!-- Detail Fisik Kandang -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kandang</label>
                                            <input type="text" name="nama_kandang" placeholder="Ex: KDG-01" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kapasitas Max</label>
                                            <div class="relative">
                                                <input type="number" name="kapasitas" placeholder="0" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition" required>
                                                <span class="absolute right-4 top-3.5 text-xs font-bold text-gray-400">Ekor</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="w-full py-3 bg-gold-500 text-white rounded-xl font-bold hover:bg-gold-600 transition flex items-center justify-center gap-2 shadow-lg shadow-gold-500/20">
                                        <i class="ph-bold ph-plus-circle"></i> Simpan Kandang
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- FORM 4: CHICK-IN (ISI AYAM) -->
                        <div class="bg-blue-50/50 rounded-2xl shadow-sm border border-blue-100 p-6 h-fit relative">
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-blue-200">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <i class="ph-fill ph-egg-crack text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">3b. Chick-In (Isi Ayam)</h3>
                                    <p class="text-xs text-gray-500">Isi kandang kosong dengan Batch baru</p>
                                </div>
                            </div>

                            <!-- Form Chick-In dengan JS Action Update -->
                            <form id="chickInForm" method="POST" action="javascript:void(0)" onsubmit="submitChickIn(event)">
                                @csrf
                                <div class="space-y-5">
                                    
                                    <!-- Pilih Unit & Kandang -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Unit</label>
                                            <select id="chickinUnit" onchange="filterKandangChickin()" class="w-full px-3 py-2 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                                                <option value="" disabled selected>Pilih...</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}" data-kandangs='{{ json_encode($unit->kandangs) }}'>{{ $unit->nama_unit }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Kandang</label>
                                            <select id="chickinKandang" name="kandang_id_fake" class="w-full px-3 py-2 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" disabled required>
                                                <option value="" disabled selected>Pilih Unit Dulu...</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Pilih Batch (UPDATE LOGIKA STATUS) -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Batch / Siklus</label>
                                        <div class="relative">
                                            <select name="batch_id" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl appearance-none focus:ring-2 focus:ring-blue-500 outline-none transition cursor-pointer" required>
                                                <option value="" disabled selected>Pilih Batch yang Aktif...</option>
                                                @if(isset($batches) && count($batches) > 0)
                                                    @foreach($batches as $batch)
                                                        @if($batch->is_active)
                                                            <option value="{{ $batch->id }}" class="font-bold text-gray-800">
                                                                🟢 {{ $batch->nama_batch }} (Mulai: {{ \Carbon\Carbon::parse($batch->tanggal_mulai)->format('d M Y') }})
                                                            </option>
                                                        @else
                                                            <option value="{{ $batch->id }}" disabled class="text-gray-400 bg-gray-50">
                                                                🔴 {{ $batch->nama_batch }} (Selesai/Non-Aktif)
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <option value="" disabled>Belum ada batch. Buat di form kiri!</option>
                                                @endif
                                            </select>
                                            <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                        </div>
                                    </div>

                                    <!-- Detail Chick-In -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tgl Masuk</label>
                                            <input type="date" name="tanggal_chick_in" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Umur Masuk (Mgg)</label>
                                            <input type="number" name="umur_awal_minggu" placeholder="18" class="w-full px-3 py-2 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Populasi Awal</label>
                                        <div class="relative">
                                            <input type="number" name="populasi_awal" placeholder="Jumlah Ayam Masuk" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition font-bold text-blue-900" required>
                                            <span class="absolute right-4 top-3.5 text-xs font-bold text-gray-400">Ekor</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden Fields for Defaults -->
                                    <input type="hidden" name="jenis_ayam" value="Pullet">

                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20">
                                        <i class="ph-bold ph-check-circle"></i> Simpan Chick-In
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        // Filter Kandang untuk Form Chick-In
        function filterKandangChickin() {
            const unitSelect = document.getElementById('chickinUnit');
            const kandangSelect = document.getElementById('chickinKandang');
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];
            
            kandangSelect.innerHTML = '<option value="" disabled selected>Pilih Kandang...</option>';
            kandangSelect.disabled = true;

            if (selectedOption && selectedOption.getAttribute('data-kandangs')) {
                const kandangs = JSON.parse(selectedOption.getAttribute('data-kandangs'));
                
                kandangs.forEach(k => {
                    const option = document.createElement('option');
                    option.value = k.id;
                    
                    // Cek status kandang
                    if (k.status === 'aktif' || k.stok_saat_ini > 0) {
                        option.text = `⚠️ ${k.nama_kandang} (Sedang Isi)`;
                        // Opsional: disable jika tidak mau menumpuk
                        // option.disabled = true; 
                    } else {
                        option.text = `✅ ${k.nama_kandang} (Kosong)`;
                    }
                    
                    kandangSelect.appendChild(option);
                });
                kandangSelect.disabled = false;
            }
        }

        // [FIXED] Submit Logic untuk Chick-In URL tanpa /admin/
        function submitChickIn(event) {
            event.preventDefault();
            const form = document.getElementById('chickInForm');
            const kandangId = document.getElementById('chickinKandang').value;

            if (!kandangId) {
                alert('Silakan pilih kandang terlebih dahulu!');
                return;
            }

            // Set action URL secara dinamis ke route siklus.store
            // URL Pattern: /admin/kandang/{id}/chick-in
            form.action = `{{ url('admin/kandang') }}/${kandangId}/chick-in`;
            
            // Submit form
            form.submit();
        }
    </script>

</body>
</html>