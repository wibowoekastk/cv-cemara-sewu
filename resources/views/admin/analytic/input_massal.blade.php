<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Massal - Admin Panel</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' },
                        blue: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 800: '#1e40af' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        @include('admin.sidebar')

        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Analytic</span> <i class="ph-bold ph-caret-right"></i> <span>Input Massal</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Lapor Massal (Interaktif)</h2>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/admin/analytic/input" class="hidden md:flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300 transition">
                        <i class="ph-bold ph-arrow-left"></i> Kembali ke Mode Harian
                    </a>
                </div>
            </header>

            <!-- ALPINE JS WRAPPER -->
            <div class="p-4 md:p-8 max-w-7xl mx-auto w-full" x-data="massInputForm()">
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center gap-3">
                        <i class="ph-fill ph-check-circle text-2xl"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm flex items-center gap-3">
                        <i class="ph-fill ph-warning-circle text-2xl"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('admin.analytic.storeMassal') }}" method="POST" id="formMassal"> 
                    @csrf
                    
                    <!-- Tahap 1: Pengaturan Awal -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex items-center gap-2 mb-6 text-cemara-700 border-b border-gray-100 pb-3">
                            <div class="p-2 bg-cemara-50 rounded-lg"><i class="ph-fill ph-sliders text-xl"></i></div>
                            <h3 class="text-lg font-bold">Langkah 1: Setup Parameter Kandang</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Unit</label>
                                <select id="selectUnit" x-model="unit_id" @change="updateKandangOptions()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium">
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" data-kandangs='{{ json_encode($unit->kandangs) }}'>{{ $unit->nama_unit }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Kandang</label>
                                <select name="kandang_id" x-model="kandang_id" @change="fetchPopulasi()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium" required>
                                    <option value="">-- Pilih Kandang --</option>
                                    <template x-for="k in kandangOptions" :key="k.id">
                                        <option :value="k.id" x-text="k.nama_kandang"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- [BARU] Info Siklus/Batch (Otomatis) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Siklus / Batch</label>
                                <input type="text" x-model="batch_name" class="w-full px-4 py-2.5 bg-blue-50 border border-blue-100 rounded-xl text-blue-800 font-bold text-sm cursor-not-allowed" placeholder="-" readonly>
                            </div>

                            <!-- [BARU] Info Umur Ayam (Otomatis) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Umur (Tgl Mulai)</label>
                                <div class="relative">
                                    <input type="text" x-model="umur_teks" class="w-full px-4 py-2.5 bg-blue-50 border border-blue-100 rounded-xl text-blue-800 font-bold text-sm cursor-not-allowed" placeholder="Otomatis..." readonly>
                                    <i class="ph-fill ph-calendar absolute right-4 top-3 text-blue-400"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Jenis Pakan</label>
                                <select name="pakan_id" id="selectPakan" x-model="pakan_id" @change="updatePakanStock()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium" required>
                                    <option value="">-- Pilih Pakan --</option>
                                    @foreach($stokPakanUnit as $stock)
                                        @if($stock->pakan)
                                            <option value="{{ $stock->pakan->id }}" data-stok="{{ $stock->jumlah_stok }}">
                                                {{ $stock->pakan->nama_pakan }} (Sisa: {{ number_format($stock->jumlah_stok, 1, ',', '.') }} Kg)
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Rentang Tanggal</label>
                                <div class="flex items-center gap-2">
                                    <input type="date" x-model="startDate" @change="fetchPopulasi()" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium" required>
                                    <span class="text-gray-400">-</span>
                                    <input type="date" x-model="endDate" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" @click="generateTable()" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition flex items-center gap-2 shadow-md hover:-translate-y-0.5">
                                <i class="ph-bold ph-table"></i> Generate Baris Interaktif
                            </button>
                        </div>
                    </div>

                    <!-- Tahap 2: Tabel Input -->
                    <div x-show="rows.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-cloak>
                        
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 border-b border-gray-100 pb-4">
                            <div class="flex items-center gap-2 text-gold-600">
                                <div class="p-2 bg-gold-50 rounded-lg"><i class="ph-fill ph-grid-nine text-xl"></i></div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Langkah 2: Isi Data Terpusat</h3>
                                    <p class="text-xs text-gray-500">Populasi Awal diambil per tanggal: <strong x-text="populasi_awal.toLocaleString('id-ID')"></strong> Ekor</p>
                                </div>
                            </div>
                            
                            <!-- Indikator Total Pakan Berjalan -->
                            <div class="bg-gray-900 text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center gap-4 transition-colors duration-300" :class="total_pakan_input > stok_pakan_maksimal ? 'bg-red-600 animate-pulse' : ''">
                                <div>
                                    <div class="text-[10px] text-gray-300 uppercase tracking-widest mb-0.5" :class="total_pakan_input > stok_pakan_maksimal ? 'text-red-200' : ''">Total Pakan Digunakan</div>
                                    <div class="font-bold text-lg leading-none"><span x-text="total_pakan_input.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 1})"></span> <span class="text-xs font-normal">Kg</span></div>
                                </div>
                                <div class="h-8 w-px bg-white/20"></div>
                                <div>
                                    <div class="text-[10px] text-gray-300 uppercase tracking-widest mb-0.5" :class="total_pakan_input > stok_pakan_maksimal ? 'text-red-200' : ''">Sisa Stok Gudang</div>
                                    <div class="font-bold text-lg leading-none"><span x-text="stok_pakan_maksimal.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 1})"></span> <span class="text-xs font-normal">Kg</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- MAGIC PASTE BOX -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl relative overflow-hidden group">
                            <div class="absolute right-0 top-0 bottom-0 w-32 bg-linear-to-l from-blue-100 to-transparent pointer-events-none"></div>
                            <label class="block text-xs font-bold text-blue-800 uppercase mb-2 items-center gap-2">
                                <i class="ph-fill ph-magic-wand text-lg"></i> Magic Paste dari Excel
                            </label>
                            <p class="text-xs text-blue-600 mb-3">Blok 5 kolom berurutan di Excel <strong class="bg-blue-100 px-1 rounded">(Mati, Afkir, Telur Butir, Berat Kg, Pakan Kg)</strong>, tekan <kbd class="bg-white px-1 border border-blue-200 rounded">Ctrl+C</kbd>. Klik kotak di bawah dan tekan <kbd class="bg-white px-1 border border-blue-200 rounded">Ctrl+V</kbd>.</p>
                            <textarea x-model="pasteData" @input="handlePaste()" class="w-full h-12 p-3 bg-white border border-blue-300 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-500 shadow-inner" placeholder="Klik di sini lalu Paste (Ctrl+V)..."></textarea>
                        </div>

                        <!-- TABEL INTERAKTIF -->
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="p-3 border-b border-r border-gray-200 w-28 bg-gray-100">Tanggal</th>
                                        <th class="p-3 border-b border-r border-gray-200">Populasi & Kematian</th>
                                        <th class="p-3 border-b border-r border-gray-200">Produksi Telur</th>
                                        <th class="p-3 border-b border-r border-gray-200">Konsumsi Pakan</th>
                                        <th class="p-3 border-b border-gray-200 bg-blue-50/50">Analisa (Auto)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(row, index) in rows" :key="index">
                                        <tr class="hover:bg-white transition bg-gray-50/30 group">
                                            
                                            <!-- TANGGAL -->
                                            <td class="p-3 border-r border-gray-200 bg-gray-50 group-hover:bg-gray-100 transition-colors align-top">
                                                <div class="font-bold text-gray-800" x-text="row.displayDate"></div>
                                                <div class="text-[10px] text-gray-400 font-medium" x-text="'Baris ' + (index+1)"></div>
                                                <input type="hidden" :name="`data[${index}][tanggal]`" :value="row.date">
                                            </td>
                                            
                                            <!-- POPULASI & KEMATIAN -->
                                            <td class="p-3 border-r border-gray-200 align-top">
                                                <div class="flex justify-between items-center text-[10px] text-gray-500 font-bold mb-2 bg-gray-100 px-2 py-1 rounded">
                                                    <span>POP AWAL: <span class="text-gray-800" x-text="row.populasi_awal_hari.toLocaleString('id-ID')"></span></span>
                                                    <span class="text-blue-600">SISA: <span class="text-xl" x-text="row.populasi_sisa.toLocaleString('id-ID')"></span></span>
                                                </div>
                                                <div class="flex gap-2">
                                                    <div class="flex-1">
                                                        <label class="text-[10px] text-red-600 font-bold block mb-1">Mati (Ekor)</label>
                                                        <input type="number" :name="`data[${index}][mati]`" x-model="row.mati" @input="recalculateRows()" class="w-full p-2 border border-red-200 bg-white rounded-lg text-center outline-none focus:ring-2 focus:ring-red-500 font-bold text-red-700 shadow-sm" placeholder="0" required>
                                                    </div>
                                                    <div class="flex-1">
                                                        <label class="text-[10px] text-orange-600 font-bold block mb-1">Afkir (Ekor)</label>
                                                        <input type="number" :name="`data[${index}][afkir]`" x-model="row.afkir" @input="recalculateRows()" class="w-full p-2 border border-orange-200 bg-white rounded-lg text-center outline-none focus:ring-2 focus:ring-orange-500 font-bold text-orange-700 shadow-sm" placeholder="0" required>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- PRODUKSI TELUR -->
                                            <td class="p-3 border-r border-gray-200 align-top">
                                                <div class="flex justify-between items-center text-[10px] text-gray-500 font-bold mb-2 bg-gold-50/50 px-2 py-1 rounded">
                                                    <span>BERAT PER BUTIR:</span>
                                                    <span class="text-gold-700"><span class="text-sm" x-text="row.egg_weight"></span> gr</span>
                                                </div>
                                                <div class="flex gap-2">
                                                    <div class="flex-1">
                                                        <label class="text-[10px] text-gold-600 font-bold block mb-1">Total Butir</label>
                                                        <input type="number" :name="`data[${index}][telur_butir]`" x-model="row.telur_butir" @input="recalculateRows()" class="w-full p-2 border border-gold-200 bg-white rounded-lg text-center outline-none focus:ring-2 focus:ring-gold-500 font-bold text-gray-800 shadow-sm" placeholder="0" required>
                                                    </div>
                                                    <div class="flex-1">
                                                        <label class="text-[10px] text-gold-600 font-bold block mb-1">Total Kg</label>
                                                        <input type="number" step="0.01" :name="`data[${index}][telur_kg]`" x-model="row.telur_kg" @input="recalculateRows()" class="w-full p-2 border border-gold-200 bg-white rounded-lg text-center outline-none focus:ring-2 focus:ring-gold-500 font-bold text-gold-700 shadow-sm" placeholder="0.00" required>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- PAKAN -->
                                            <td class="p-3 border-r border-gray-200 align-top">
                                                <div class="flex justify-between items-center text-[10px] text-gray-500 font-bold mb-2 bg-yellow-50/50 px-2 py-1 rounded">
                                                    <span>KONSUMSI:</span>
                                                    <span class="text-yellow-700"><span class="text-sm" x-text="row.feed_intake"></span> gr/ekor</span>
                                                </div>
                                                <div>
                                                    <label class="text-[10px] text-yellow-600 font-bold block mb-1">Pakan (Kg)</label>
                                                    <input type="number" step="0.01" :name="`data[${index}][pakan_kg]`" x-model="row.pakan_kg" @input="recalculateRows()" class="w-full p-2 border border-yellow-200 bg-white rounded-lg text-center outline-none focus:ring-2 focus:ring-yellow-500 font-bold text-gray-800 shadow-sm" placeholder="0.00" required>
                                                </div>
                                            </td>

                                            <!-- ANALISA -->
                                            <td class="p-3 bg-blue-50/20 align-top">
                                                <div class="flex flex-col gap-2 justify-center h-full pt-6">
                                                    <!-- Box HD -->
                                                    <div class="flex justify-between items-center bg-white border px-3 py-1.5 rounded-lg shadow-sm" :class="row.hd < 80 ? 'border-red-200' : 'border-blue-100'">
                                                        <span class="text-[10px] font-bold text-blue-600" :class="row.hd < 80 ? 'text-red-500' : ''">HD%</span>
                                                        <span class="font-bold text-blue-800" :class="row.hd < 80 ? 'text-red-600' : ''" x-text="row.hd + '%'"></span>
                                                    </div>
                                                    
                                                    <!-- Box FCR -->
                                                    <div class="flex justify-between items-center bg-white border px-3 py-1.5 rounded-lg shadow-sm transition-colors" :class="row.fcr > 3 ? 'border-red-300 bg-red-50' : 'border-yellow-100'">
                                                        <span class="text-[10px] font-bold text-yellow-600" :class="row.fcr > 3 ? 'text-red-600' : ''">FCR</span>
                                                        <span class="font-bold text-yellow-800" :class="row.fcr > 3 ? 'text-red-800 animate-pulse' : ''" x-text="row.fcr"></span>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Warning Limit -->
                        <div x-show="total_pakan_input > stok_pakan_maksimal" class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-3 font-bold" x-cloak>
                            <i class="ph-fill ph-warning text-2xl"></i>
                            Peringatan: Total input pakan (<span x-text="total_pakan_input.toFixed(1)"></span> Kg) melebihi stok yang ada di gudang unit (<span x-text="stok_pakan_maksimal.toLocaleString('id-ID')"></span> Kg). Anda tidak bisa menyimpan laporan ini.
                        </div>

                        <!-- Footer Actions -->
                        <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                            <button type="button" @click="rows = []; total_pakan_input = 0" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition shadow-sm">
                                Batal / Reset
                            </button>
                            <button type="submit" :disabled="total_pakan_input > stok_pakan_maksimal || rows.length === 0" class="px-8 py-3 rounded-xl font-bold transition shadow-lg flex items-center gap-2 text-white" :class="total_pakan_input > stok_pakan_maksimal ? 'bg-gray-400 cursor-not-allowed' : 'bg-cemara-900 hover:bg-cemara-800 hover:-translate-y-0.5'">
                                <i class="ph-bold ph-paper-plane-right"></i>
                                Simpan Laporan Massal
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </main>
    </div>

    <!-- Script Alpine Logic -->
    <script>
        function massInputForm() {
            return {
                unit_id: '',
                kandang_id: '',
                pakan_id: '',
                startDate: '',
                endDate: '',
                
                kandangOptions: [],
                
                populasi_awal: 0,
                stok_pakan_maksimal: 0,
                total_pakan_input: 0,
                
                batch_name: '-',
                umur_teks: '-',
                
                rows: [],
                pasteData: '',

                // 1. Saat Unit Diganti
                updateKandangOptions() {
                    const select = document.getElementById('selectUnit');
                    const option = select.options[select.selectedIndex];
                    if(option && option.dataset.kandangs) {
                        this.kandangOptions = JSON.parse(option.dataset.kandangs);
                    } else {
                        this.kandangOptions = [];
                    }
                    this.kandang_id = '';
                    this.rows = [];
                    this.populasi_awal = 0;
                    this.batch_name = '-';
                    this.umur_teks = '-';
                },

                // 2. Ambil Stok Pakan Saat Pakan Diganti
                updatePakanStock() {
                    const select = document.getElementById('selectPakan');
                    const option = select.options[select.selectedIndex];
                    if(option && option.dataset.stok) {
                        this.stok_pakan_maksimal = parseFloat(option.dataset.stok);
                    } else {
                        this.stok_pakan_maksimal = 0;
                    }
                },

                // 3. Ambil Populasi Master Awal, Batch & Umur
                fetchPopulasi() {
                    if(!this.kandang_id || !this.startDate) return;
                    
                    this.umur_teks = 'Menghitung...';

                    fetch(`/admin/analytic/kandang-stats/${this.kandang_id}?tanggal=${this.startDate}`)
                        .then(res => res.json())
                        .then(data => {
                            this.populasi_awal = data.stok_saat_ini;
                            this.batch_name = data.batch_name || '-';
                            this.umur_teks = data.umur_teks || '-';

                            if(this.rows.length > 0) this.recalculateRows();
                        })
                        .catch(err => {
                            console.error("Gagal menarik data kandang", err);
                            this.umur_teks = 'Gagal memuat';
                        });
                },

                // 4. Generate Kerangka Tabel
                generateTable() {
                    if (!this.kandang_id || !this.pakan_id || !this.startDate || !this.endDate) {
                        alert("Mohon lengkapi Unit, Kandang, Pakan, dan Rentang Tanggal terlebih dahulu!");
                        return;
                    }

                    const start = new Date(this.startDate);
                    const end = new Date(this.endDate);
                    
                    if (start > end) {
                        alert("Tanggal Mulai tidak boleh lebih besar dari Tanggal Akhir.");
                        return;
                    }

                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                    if(diffDays > 31) {
                        alert("Maksimal input massal adalah 31 hari sekaligus agar sistem tidak kelebihan beban.");
                        return;
                    }

                    // Pastikan pakan stok sudah diset
                    this.updatePakanStock();
                    
                    // Fetch ulang untuk memastikan data akurat, setelah itu baru buat array
                    this.umur_teks = 'Menghitung...';
                    fetch(`/admin/analytic/kandang-stats/${this.kandang_id}?tanggal=${this.startDate}`)
                        .then(res => res.json())
                        .then(data => {
                            this.populasi_awal = data.stok_saat_ini;
                            this.batch_name = data.batch_name || '-';
                            this.umur_teks = data.umur_teks || '-';
                            
                            this.rows = [];
                            let currentDate = new Date(start);

                            while (currentDate <= end) {
                                let yyyy = currentDate.getFullYear();
                                let mm = String(currentDate.getMonth() + 1).padStart(2, '0');
                                let dd = String(currentDate.getDate()).padStart(2, '0');
                                let dbDate = `${yyyy}-${mm}-${dd}`;
                                
                                let displayOptions = { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' };
                                let displayStr = currentDate.toLocaleDateString('id-ID', displayOptions);

                                this.rows.push({
                                    date: dbDate,
                                    displayDate: displayStr,
                                    mati: '',
                                    afkir: '',
                                    telur_butir: '',
                                    telur_kg: '',
                                    pakan_kg: '',
                                    
                                    // Variabel Kalkulasi
                                    populasi_awal_hari: 0,
                                    populasi_sisa: 0,
                                    hd: '0.0',
                                    fcr: '0.00',
                                    feed_intake: '0.0',
                                    egg_weight: '0.0'
                                });

                                currentDate.setDate(currentDate.getDate() + 1);
                            }
                            
                            // Hitung untuk pertama kali
                            this.recalculateRows();
                        });
                },

                // 5. OTAK UTAMA: Perhitungan Berantai (Cascading Math)
                recalculateRows() {
                    let currentPop = this.populasi_awal;
                    let totalPakanAcc = 0;

                    this.rows.forEach(row => {
                        let mati = parseFloat(row.mati) || 0;
                        let afkir = parseFloat(row.afkir) || 0;
                        let telur_butir = parseFloat(row.telur_butir) || 0;
                        let telur_kg = parseFloat(row.telur_kg) || 0;
                        let pakan_kg = parseFloat(row.pakan_kg) || 0;

                        // Set populasi awal untuk baris ini
                        row.populasi_awal_hari = currentPop;
                        
                        // Hitung sisa setelah pengurangan
                        row.populasi_sisa = Math.max(0, currentPop - mati - afkir);
                        
                        // Kalkulasi Analisa
                        row.hd = (row.populasi_sisa > 0) ? ((telur_butir / row.populasi_sisa) * 100).toFixed(1) : '0.0';
                        row.fcr = (telur_kg > 0) ? (pakan_kg / telur_kg).toFixed(2) : '0.00';
                        row.feed_intake = (row.populasi_sisa > 0) ? ((pakan_kg * 1000) / row.populasi_sisa).toFixed(1) : '0.0';
                        row.egg_weight = (telur_butir > 0) ? ((telur_kg * 1000) / telur_butir).toFixed(1) : '0.0';

                        // Operkan sisa populasi ke hari selanjutnya
                        currentPop = row.populasi_sisa;
                        
                        totalPakanAcc += pakan_kg;
                    });

                    this.total_pakan_input = totalPakanAcc;
                },

                // 6. LOGIKA MAGIC PASTE EXCEL
                handlePaste() {
                    setTimeout(() => {
                        if(!this.pasteData) return;
                        
                        let lines = this.pasteData.split('\n');
                        let dataIndex = 0;
                        
                        for(let i=0; i<lines.length; i++) {
                            if(lines[i].trim() === '') continue;
                            
                            if(dataIndex < this.rows.length) {
                                let cols = lines[i].split('\t');
                                const cleanNum = (val) => val ? val.trim().replace(',', '.') : '0';

                                if(cols.length >= 1) this.rows[dataIndex].mati = cleanNum(cols[0]);
                                if(cols.length >= 2) this.rows[dataIndex].afkir = cleanNum(cols[1]);
                                if(cols.length >= 3) this.rows[dataIndex].telur_butir = cleanNum(cols[2]);
                                if(cols.length >= 4) this.rows[dataIndex].telur_kg = cleanNum(cols[3]);
                                if(cols.length >= 5) this.rows[dataIndex].pakan_kg = cleanNum(cols[4]);
                                
                                dataIndex++;
                            }
                        }
                        this.pasteData = '';
                        this.recalculateRows();
                    }, 100);
                }
            }
        }
    </script>
</body>
</html>