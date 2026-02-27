<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pakan - Admin Panel</title>
    
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
        
        <!-- Sidebar Admin -->
        @include('admin.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Manajemen Data</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Pakan</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Input Data</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Input Stok & Master Pakan</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Date Display -->
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-semibold border border-cemara-100">
                        <i class="ph-fill ph-calendar-check"></i>
                        <span id="headerDateDisplay"></span>
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full max-w-7xl mx-auto">
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex items-center gap-2 shadow-sm">
                        <i class="ph-fill ph-check-circle text-lg"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm">
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- FORM 1: MASTER PAKAN BARU (Kiri - Kecil) -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-fit">
                        <div class="p-6 border-b border-gray-100 flex items-center gap-3 bg-gray-50/50">
                            <div class="w-10 h-10 rounded-full bg-cemara-100 flex items-center justify-center text-cemara-600">
                                <i class="ph-fill ph-tag text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">Master Pakan Baru</h3>
                                <p class="text-xs text-gray-500">Daftarkan jenis pakan jika belum ada.</p>
                            </div>
                        </div>
                        
                        <!-- Route: admin.pakan.store -->
                        <form action="{{ route('admin.pakan.store') }}" method="POST" class="p-6 space-y-5">
                            @csrf
                            
                            <!-- Nama Pakan -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Pakan</label>
                                <input type="text" name="nama_pakan" placeholder="Contoh: Konsentrat 144" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium" required>
                            </div>

                            <!-- Kategori (Datalist) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kategori</label>
                                <div class="relative">
                                    <input list="kategoriList" type="text" name="jenis_pakan" placeholder="Pilih atau Ketik..." class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium" required>
                                    <datalist id="kategoriList">
                                        <option value="Konsentrat">
                                        <option value="Jagung">
                                        <option value="Dedak">
                                        <option value="Pakan Jadi">
                                        <option value="Mineral">
                                    </datalist>
                                </div>
                            </div>

                            <!-- Satuan & Min Stok -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Satuan</label>
                                    <select name="satuan" class="w-full px-3 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none font-medium text-gray-700">
                                        <option value="sak">Sak</option>
                                        <option value="kg">Kg</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Min. Stok</label>
                                    <input type="number" name="min_stok" placeholder="100" class="w-full px-3 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium">
                                </div>
                            </div>

                            <!-- Deskripsi Singkat -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Keterangan</label>
                                <textarea name="deskripsi" rows="2" placeholder="Deskripsi singkat..." class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-cemara-500 outline-none transition font-medium resize-none"></textarea>
                            </div>

                            <button type="submit" class="w-full py-3 bg-cemara-900 text-white font-bold rounded-xl hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="ph-bold ph-plus-circle"></i> Simpan Master
                            </button>
                        </form>

                        <!-- List Preview -->
                        <div class="px-6 pb-6 pt-2 border-t border-gray-100 bg-gray-50 rounded-b-2xl mt-auto">
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 pt-4">Pakan Tersedia</h4>
                            <div class="flex flex-wrap gap-2">
                                @if(isset($pakans) && count($pakans) > 0)
                                    @foreach($pakans->unique('nama_pakan')->take(8) as $p)
                                        <span class="px-3 py-1 bg-white text-gray-600 rounded-full text-xs font-semibold border border-gray-200 shadow-sm">
                                            {{ $p->nama_pakan }} ({{ $p->satuan }})
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum ada data master.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- FORM 2: INPUT STOK MASUK (Kanan - Besar) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-fit">
                        <div class="p-6 border-b border-gray-100 flex items-center gap-3 bg-gold-50/50">
                            <div class="w-10 h-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 shadow-sm">
                                <i class="ph-fill ph-package text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">Input Stok Masuk (Pusat)</h3>
                                <p class="text-xs text-gray-500">Catat pembelian atau stok pakan baru masuk ke gudang pusat.</p>
                            </div>
                        </div>

                        <!-- Form Update Stok -->
                        <!-- Action URL akan diupdate oleh JS berdasarkan pilihan pakan -->
                        <form id="formStokMasuk" method="POST" class="p-6 space-y-6">
                            @csrf
                            @method('PUT') 
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tanggal Input -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tanggal Masuk</label>
                                    <div class="relative">
                                        <input type="date" name="tgl_masuk" id="dateInput" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-medium text-gray-700">
                                        <i class="ph-bold ph-calendar-blank absolute left-3 top-3.5 text-gray-400"></i>
                                    </div>
                                </div>

                                <!-- Pilih Item Pakan (DINAMIS) -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pilih Item Pakan</label>
                                    <div class="relative">
                                        <select id="pakanSelect" onchange="updateFormAction(this)" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-gold-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                            <option value="" disabled selected>Pilih Item...</option>
                                            @if(isset($pakans))
                                                @foreach($pakans as $p)
                                                    <option value="{{ $p->id }}" data-satuan="{{ strtolower($p->satuan) }}">{{ $p->nama_pakan }} (Stok Pusat: {{ number_format($p->stok_pusat) }} {{ $p->satuan }})</option>
                                                @endforeach
                                            @else
                                                <option disabled>Data kosong</option>
                                            @endif
                                        </select>
                                        <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                    </div>
                                    <p class="text-[10px] text-red-500 mt-1 hidden" id="pakanError">*Wajib pilih item pakan.</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Sumber Pakan -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Sumber</label>
                                    <div class="relative">
                                        <select name="sumber" id="sumberSelect" onchange="toggleSupplierInput()" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-gold-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                            <option value="pembelian" selected>Pembelian Supplier</option>
                                            <option value="produksi_sendiri">Produksi Sendiri (Mixing)</option>
                                        </select>
                                        <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                
                                <!-- Supplier -->
                                <div id="supplierContainer">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Supplier</label>
                                    <input type="text" name="supplier" id="supplierInput" placeholder="Contoh: PT. Charoen" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-medium">
                                </div>
                            </div>

                            <!-- Area Hitung Berat -->
                            <div class="p-5 bg-gold-50 border border-gold-100 rounded-xl transition-all duration-300">
                                <h4 class="text-sm font-bold text-gold-800 mb-4 flex items-center gap-2">
                                    <i class="ph-fill ph-scales"></i> Detail Jumlah Masuk
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    
                                    <!-- INPUT 1: JUMLAH (Label Dinamis) -->
                                    <div>
                                        <label class="block text-[10px] font-bold text-gold-700 uppercase mb-1" id="labelInput1">Jml Sak / Karung</label>
                                        <div class="relative">
                                            <input type="number" name="jumlah_karung" id="input1" placeholder="0" class="w-full px-4 py-3 bg-white border border-gold-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-bold text-gray-800" oninput="calculateTotal()">
                                            <span class="absolute right-4 top-3.5 text-xs text-gray-400 font-bold" id="satuanInput1">Sak</span>
                                        </div>
                                    </div>

                                    <!-- INPUT 2: BERAT PER SAK (Hanya Muncul jika Satuan = Sak) -->
                                    <div id="containerInput2">
                                        <label class="block text-[10px] font-bold text-gold-700 uppercase mb-1">Berat per Sak (Standar Mix)</label>
                                        <div class="relative">
                                            <!-- [PERUBAHAN DI SINI] Default diubah menjadi 40 sesuai standar -->
                                            <input type="number" name="berat_per_karung" id="input2" value="40" step="0.1" class="w-full px-4 py-3 bg-white border border-gold-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-bold text-gray-800" oninput="calculateTotal()">
                                            <span class="absolute right-4 top-3.5 text-xs text-gray-400 font-bold">Kg</span>
                                        </div>
                                    </div>

                                    <!-- TOTAL BERAT (System) -->
                                    <div>
                                        <label class="block text-[10px] font-bold text-gold-700 uppercase mb-1" id="labelTotal">Total Masuk (Kg)</label>
                                        <div class="relative">
                                            <input type="number" name="tambah_stok" id="totalBeratInput" readonly class="w-full px-4 py-3 bg-gold-100 border border-gold-200 text-gold-900 rounded-xl outline-none font-bold cursor-not-allowed">
                                            <span class="absolute right-4 top-3.5 text-xs text-gold-700 font-bold" id="satuanTotal">Kg</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-2 italic" id="hintText">*Total Berat inilah yang akan ditambahkan ke stok gudang.</p>
                            </div>

                            <div class="pt-2 flex justify-end gap-3">
                                <button type="reset" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition">
                                    Reset
                                </button>
                                <button type="submit" onclick="return validateForm()" class="px-8 py-3 bg-gold-500 text-white font-bold rounded-xl hover:bg-gold-600 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <i class="ph-bold ph-check-circle"></i> Simpan Stok
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Scripts Logic Utama -->
    <script>
        let currentSatuan = 'sak'; // Default

        function calculateTotal() {
            const val1 = document.getElementById('input1').value || 0;
            const val2 = document.getElementById('input2').value || 0;
            let total = 0;

            if (currentSatuan === 'sak') {
                // Jika Sak: Total Kg = Jml Sak * Berat per Sak
                total = parseFloat(val1) * parseFloat(val2);
            } else {
                // Jika Kg: Total Kg = Input Langsung
                total = parseFloat(val1);
            }
            document.getElementById('totalBeratInput').value = total; 
        }

        // Logic Switch Satuan (Sak/Kg)
        function updateFormAction(select) {
            const id = select.value;
            const form = document.getElementById('formStokMasuk');
            const selectedOption = select.options[select.selectedIndex];
            const satuanRaw = selectedOption.getAttribute('data-satuan'); // 'sak' atau 'kg'
            
            // Update URL Route
            form.action = "{{ url('admin/pakan/update-stok') }}/" + id;
            document.getElementById('pakanError').style.display = 'none';

            // Element UI
            const containerInput2 = document.getElementById('containerInput2');
            const labelInput1 = document.getElementById('labelInput1');
            const satuanInput1 = document.getElementById('satuanInput1');
            const hintText = document.getElementById('hintText');

            if (satuanRaw === 'kg') {
                currentSatuan = 'kg';
                // Jika Kg: Sembunyikan Input Berat per Sak
                containerInput2.style.display = 'none';
                
                // Ubah Label Input 1 jadi "Jumlah Berat"
                labelInput1.innerText = "Jumlah Berat Masuk";
                satuanInput1.innerText = "Kg";
                
                hintText.innerText = "*Jumlah input langsung ditambahkan ke stok gudang (Kg).";
                
            } else {
                currentSatuan = 'sak'; // Default Sak
                // Jika Sak: Tampilkan Input Berat per Sak
                containerInput2.style.display = 'block';
                
                // Ubah Label Input 1 jadi "Jml Sak / Karung"
                labelInput1.innerText = "Jml Sak / Karung";
                satuanInput1.innerText = "Sak";
                
                hintText.innerText = "*Total Berat (Kg) hasil perkalian akan ditambahkan ke stok gudang.";
            }
            calculateTotal();
        }

        // Toggle tampilan input supplier
        function toggleSupplierInput() {
            const sumber = document.getElementById('sumberSelect').value;
            const supplierInput = document.getElementById('supplierInput');
            
            if (sumber === 'produksi_sendiri') {
                supplierInput.value = 'Internal Farm (Mixing)';
                supplierInput.readOnly = true;
                supplierInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                supplierInput.value = '';
                supplierInput.readOnly = false;
                supplierInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        }

        function validateForm() {
            const select = document.getElementById('pakanSelect');
            if (!select.value) {
                document.getElementById('pakanError').style.display = 'block';
                return false;
            }
            return true;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const dateInput = document.getElementById('dateInput');
            const displayDate = document.getElementById('headerDateDisplay');
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            if(dateInput) dateInput.value = `${yyyy}-${mm}-${dd}`;
            
            const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
            if(displayDate) displayDate.innerText = today.toLocaleDateString('id-ID', options);
        });
    </script>
</body>
</html>