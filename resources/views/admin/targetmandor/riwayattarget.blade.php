<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Target - Admin Panel</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-poppins'], poppins: ['"Playfair Display"', 'poppins'] },
                    colors: { 
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' }, 
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' } 
                    }
                }
            }
        }
    </script>
    <style>
        .table-container::-webkit-scrollbar { height: 6px; }
        .table-container::-webkit-scrollbar-track { background: #f9fafb; }
        .table-container::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        .table-container::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    {{-- Fallback data jika variabel belum dikirim controller (untuk preview statis) --}}
    @php
        if(!isset($units)) {
            $units = \App\Models\Unit::all();
        }
    @endphp

    <div class="flex h-screen overflow-hidden relative">
        @include('admin.sidebar')

        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm/50 backdrop-blur-md">
                <div>
                    <div class="flex items-center gap-2 text-gray-400 text-xs font-bold mb-0.5 uppercase tracking-wider">
                        <span>Laporan</span><i class="ph-bold ph-caret-right text-gray-300"></i><span>Evaluasi</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 font-poppins tracking-tight">Riwayat Target & Mandor</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg"><i class="ph-bold ph-list text-2xl"></i></button>
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-600">
                        <i class="ph-fill ph-calendar-blank text-gold-500"></i> {{ date('d M Y') }}
                    </div>
                </div>
            </header>

            <div class="p-6 md:p-10 w-full max-w-7xl mx-auto min-h-screen">
                
                <!-- Ringkasan Kartu -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                    <!-- Card Active -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shadow-inner">
                            <i class="ph-fill ph-target"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Target Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalActive }}</p>
                        </div>
                    </div>
                    
                    <!-- Card History -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center text-2xl shadow-inner">
                            <i class="ph-fill ph-archive-box"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Arsip Selesai</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalHistory }}</p>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div x-data="{ showFilter: true }" class="mb-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 px-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 cursor-pointer hover:bg-gray-50 transition" @click="showFilter = !showFilter">
                            <h3 class="font-bold text-gray-700 text-sm flex items-center gap-2">
                                <span class="bg-cemara-100 text-cemara-700 p-1 rounded-md"><i class="ph-bold ph-funnel"></i></span> Filter Data
                            </h3>
                            <i class="ph-bold ph-caret-down text-gray-400 transition-transform duration-300" :class="showFilter ? 'rotate-180' : ''"></i>
                        </div>
                        
                        <div x-show="showFilter" x-transition.opacity class="p-6">
                            <form action="{{ request()->url() }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                
                                <!-- Filter Unit (DINAMIS) -->
                                <div class="md:col-span-3">
                                    <label class="text-xs font-bold text-gray-500 mb-1.5 block uppercase tracking-wide">Pilih Unit</label>
                                    <div class="relative">
                                        <select name="unit_id" class="w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-cemara-500 focus:border-cemara-500 outline-none appearance-none cursor-pointer transition shadow-sm">
                                            <option value="Semua Unit">Semua Unit</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->nama_unit }} ({{ $unit->lokasi }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="ph-bold ph-caret-down absolute right-3.5 top-3.5 text-gray-400 pointer-events-none"></i>
                                    </div>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="text-xs font-bold text-gray-500 mb-1.5 block uppercase tracking-wide">Dari Tanggal</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cemara-500 focus:border-cemara-500 outline-none shadow-sm text-gray-600">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="text-xs font-bold text-gray-500 mb-1.5 block uppercase tracking-wide">Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cemara-500 focus:border-cemara-500 outline-none shadow-sm text-gray-600">
                                </div>
                                <div class="md:col-span-3">
                                    <button type="submit" class="w-full px-6 py-2.5 bg-cemara-900 text-white rounded-xl text-sm font-bold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20 flex items-center justify-center gap-2 transform active:scale-95">
                                        <i class="ph-bold ph-magnifying-glass"></i> Tampilkan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabel Riwayat & Evaluasi -->
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 overflow-hidden">
                    
                    <!-- TAB 1: ACTIVE TARGETS -->
                    <div class="p-4 border-b border-gray-100 bg-gray-50/30">
                        <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide flex items-center gap-2">
                            <i class="ph-fill ph-lightning text-amber-500"></i> Target Sedang Berjalan
                        </h3>
                    </div>
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                                <tr>
                                    <th class="px-6 py-4">Periode & Unit</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center w-40 bg-blue-50/50 text-blue-700 border-r border-dashed border-blue-200">Target</th>
                                    <th class="px-6 py-4 text-center w-40 bg-gray-50">Realisasi (Rata²)</th>
                                    <th class="px-6 py-4 text-left">Evaluasi Kinerja</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($activeTargets as $t)
                                    @php
                                        // LOGIKA EVALUASI (Sama seperti sebelumnya)
                                        $real_hd = 0; $real_fcr = 0; $has_data = false;
                                        $evalClass = 'bg-blue-50 border-blue-100 text-blue-600';
                                        $evalIcon = 'ph-hourglass-medium';
                                        $evalTitle = 'Sedang Berjalan';
                                        $evalDesc = 'Target masih aktif dipantau.';
                                    @endphp

                                    <tr class="hover:bg-gray-50/80 transition duration-150 group">
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <p class="font-bold text-gray-900 text-sm mb-0.5">{{ $t->unit->nama_unit ?? 'Unit Hapus' }}</p>
                                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                                    <span class="font-medium text-cemara-600 bg-cemara-50 px-1.5 py-0.5 rounded">{{ $t->unit->lokasi ?? '-' }}</span>
                                                    <span class="text-[10px] text-gray-400">•</span>
                                                    <span class="font-mono text-[10px]">{{ \Carbon\Carbon::parse($t->start_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($t->end_date)->format('d/m') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Status -->
                                        <td class="px-6 py-5 text-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-blue-50 text-blue-600 border-blue-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Aktif
                                            </span>
                                        </td>
                                        
                                        <!-- Target -->
                                        <td class="px-6 py-5 text-center bg-blue-50/20 border-r border-dashed border-blue-100">
                                            <div class="flex flex-col gap-1 items-center">
                                                <div class="flex justify-between w-24 text-xs"><span class="text-gray-400">HD</span><span class="font-bold text-blue-700">{{ $t->hd }}%</span></div>
                                                <div class="flex justify-between w-24 text-xs"><span class="text-gray-400">FCR</span><span class="font-bold text-purple-700">{{ $t->fcr }}</span></div>
                                            </div>
                                        </td>

                                        <!-- Realisasi -->
                                        <td class="px-6 py-5 text-center">
                                            <div class="text-center"><span class="text-gray-300 text-xs italic">- Belum ada data -</span></div>
                                        </td>

                                        <!-- Evaluasi -->
                                        <td class="px-6 py-5">
                                            <div class="flex items-start gap-3 p-3 rounded-xl border {{ $evalClass }} transition-colors">
                                                <div class="mt-0.5 text-lg"><i class="ph-fill {{ $evalIcon }}"></i></div>
                                                <div>
                                                    <p class="text-xs font-bold leading-tight mb-0.5">{{ $evalTitle }}</p>
                                                    <p class="text-[10px] opacity-80 leading-tight">{{ $evalDesc }}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Belum ada target aktif.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- TAB 2: HISTORY TARGETS (ARSIP) -->
                    @if($historyTargets->count() > 0)
                    <div class="p-4 border-t border-b border-gray-100 bg-gray-50/30 mt-4">
                        <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide flex items-center gap-2">
                            <i class="ph-fill ph-clock-counter-clockwise text-gray-400"></i> Arsip Target Selesai
                        </h3>
                    </div>
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <tbody class="divide-y divide-gray-50">
                                @foreach($historyTargets as $t)
                                    <!-- (Copy logika row yang sama, beda status & evaluasi mungkin sudah final) -->
                                    <tr class="hover:bg-gray-50/80 transition duration-150 group opacity-75">
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <p class="font-bold text-gray-700 text-sm mb-0.5">{{ $t->unit->nama_unit ?? 'Unit Hapus' }}</p>
                                                <span class="text-xs font-medium text-gray-500">{{ $t->unit->lokasi ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-gray-100 text-gray-500 border-gray-200">
                                                Selesai
                                            </span>
                                        </td>
                                        <!-- ... kolom target/realisasi sama ... -->
                                        <td colspan="3" class="px-6 py-5 text-center text-xs text-gray-400">
                                            Data Arsip (Klik untuk detail)
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>
            </div>
        </main>
    </div>
</body>
</html>