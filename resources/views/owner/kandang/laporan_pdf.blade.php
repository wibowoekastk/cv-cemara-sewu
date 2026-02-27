<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kandang - Owner Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-poppins'],
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
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Manajemen Kandang</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Laporan</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Pusat Download Laporan</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
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
            <div class="p-4 md:p-8 w-full max-w-6xl mx-auto" 
                 x-data="{ 
                    isLoading: false,
                    searchLaporan() {
                        this.isLoading = true;
                        // Submit form secara manual agar efek loading terlihat
                        document.getElementById('reportForm').submit();
                    }
                 }">
                
                <form action="{{ route('owner.kandang.laporan-pdf') }}" method="GET" id="reportForm">
                    <input type="hidden" name="filter" value="true">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- LEFT COLUMN: Filter & Settings -->
                        <div class="md:col-span-2 space-y-6">
                            
                            <!-- Card Filter -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <i class="ph-fill ph-faders text-cemara-600 text-xl"></i> Filter Data
                                </h3>

                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Lokasi -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Lokasi Farm</label>
                                            <div class="relative">
                                                <select name="lokasi" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                    <option value="all">Semua Lokasi</option>
                                                    @foreach($lokasis as $loc)
                                                        <option value="{{ $loc }}" {{ request('lokasi') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                                    @endforeach
                                                </select>
                                                <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                            </div>
                                        </div>

                                        <!-- Unit -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Unit</label>
                                            <div class="relative">
                                                <select name="unit_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-cemara-500 outline-none transition cursor-pointer font-medium text-gray-700">
                                                    <option value="all">Semua Unit</option>
                                                    @foreach($units as $u)
                                                        <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                                                    @endforeach
                                                </select>
                                                <i class="ph-bold ph-caret-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status (Optional) -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Status Kandang</label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="status" value="all" class="text-cemara-600 focus:ring-cemara-500" {{ request('status', 'all') == 'all' ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-700">Semua</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="status" value="aktif" class="text-cemara-600 focus:ring-cemara-500" {{ request('status') == 'aktif' ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-700">Aktif</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="status" value="kosong" class="text-cemara-600 focus:ring-cemara-500" {{ request('status') == 'kosong' ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-700">Kosong</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tombol Cari -->
                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-cemara-900 text-white font-bold rounded-xl hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 w-full md:w-auto justify-center">
                                        <i class="ph-bold ph-magnifying-glass"></i> Cari & Preview
                                    </button>
                                </div>
                            </div>

                            <!-- Result Preview (Tabel) -->
                            @if(isset($data) && $data->count() > 0)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                                    <div>
                                        <h4 class="font-bold text-gray-800">Pratinjau Data</h4>
                                        <p class="text-xs text-gray-500">{{ $data->count() }} Data ditemukan</p>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded border border-green-200">Data Siap</span>
                                </div>
                                
                                <div class="p-0 overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-white text-gray-500 font-semibold border-b border-gray-100">
                                            <tr>
                                                <th class="px-4 py-3">Unit</th>
                                                <th class="px-4 py-3">Kandang</th>
                                                <th class="px-4 py-3 text-right">Kapasitas</th>
                                                <th class="px-4 py-3 text-right">Populasi</th>
                                                <th class="px-4 py-3 text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($data as $kandang)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 font-medium text-gray-800">{{ $kandang->unit->nama_unit ?? '-' }}</td>
                                                <td class="px-4 py-3 text-gray-600">{{ $kandang->nama_kandang }}</td>
                                                <td class="px-4 py-3 text-right">{{ number_format($kandang->kapasitas) }}</td>
                                                <td class="px-4 py-3 text-right font-bold {{ $kandang->stok_saat_ini < 100 ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ number_format($kandang->stok_saat_ini) }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $kandang->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                                        {{ ucfirst($kandang->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @elseif(request('filter'))
                            <div class="bg-red-50 p-4 rounded-xl border border-red-200 text-center text-red-600">
                                <p class="text-sm">Tidak ada data kandang yang sesuai filter.</p>
                            </div>
                            @endif

                        </div>

                        <!-- RIGHT COLUMN: Opsi Cetak & Download -->
                        <div class="space-y-6">
                            
                            <!-- Panel Opsi -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide border-b border-gray-100 pb-2">Opsi Dokumen</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition group select-none">
                                        <input type="checkbox" checked class="w-5 h-5 text-cemara-600 rounded border-gray-300 focus:ring-cemara-500">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-700">Ringkasan Total</span>
                                            <span class="text-xs text-gray-400">Total populasi per unit</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="bg-cemara-900 rounded-2xl p-6 text-white text-center shadow-lg shadow-cemara-900/20 sticky top-24">
                                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                                    <i class="ph-fill ph-file-pdf text-3xl text-white"></i>
                                </div>
                                <h3 class="font-bold text-lg mb-1 font-poppins">Siap Mengunduh</h3>
                                <p class="text-cemara-200 text-xs mb-6 px-2 leading-relaxed">
                                    Data kandang akan diexport ke PDF.
                                </p>
                                
                                <button type="submit" name="download_pdf" value="true" 
                                        {{ !(isset($data) && count($data) > 0) ? 'disabled' : '' }}
                                        class="w-full py-3.5 rounded-xl font-bold transition shadow-md flex items-center justify-center gap-2 group 
                                        {{ !(isset($data) && count($data) > 0) ? 'bg-gray-600 cursor-not-allowed opacity-50' : 'bg-white text-cemara-900 hover:bg-gray-100' }}">
                                    <i class="ph-bold ph-download-simple group-hover:scale-110 transition-transform"></i>
                                    Download PDF
                                </button>
                            </div>

                        </div>

                    </div>
                </form>
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
        
        document.addEventListener('DOMContentLoaded', () => {
            const displayDate = document.getElementById('headerDateDisplay');
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
            if(displayDate) displayDate.innerText = today.toLocaleDateString('id-ID', options);
        });
    </script>
</body>
</html>