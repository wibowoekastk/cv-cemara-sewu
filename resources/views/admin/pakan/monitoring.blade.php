<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Stok - Admin Panel</title>
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
        @include('admin.sidebar')
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Pakan</span><i class="ph-bold ph-caret-right"></i><span>Monitoring</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Monitoring Sebaran Stok</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg"><i class="ph-bold ph-list text-2xl"></i></button>
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-semibold border border-cemara-100">
                        <i class="ph-fill ph-eye"></i> Real-time Data
                    </div>
                </div>
            </header>

            <div class="p-4 md:p-8 w-full">

                <!-- BAGIAN 1: GUDANG PUSAT (ADMIN) -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="ph-fill ph-warehouse text-gold-500"></i> Gudang Pusat (Belum Didistribusikan)
                    </h3>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gold-50 text-gold-800 font-bold border-b border-gold-100">
                                    <tr>
                                        <th class="px-6 py-3">Nama Pakan</th>
                                        <th class="px-6 py-3">Kategori</th>
                                        <th class="px-6 py-3 text-center">Stok Pusat</th>
                                        <th class="px-6 py-3 text-center">Status</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                 
                                    @foreach($pusatPakans as $p)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3 font-bold text-gray-800">{{ $p->nama_pakan }}</td>
                                        <td class="px-6 py-3">{{ $p->jenis_pakan }}</td>
                                        
                                        <td class="px-6 py-3 text-center">
                                            <span class="font-bold text-lg text-gold-600">{{ number_format($p->stok_pusat, 1) }} Kg</span>
                                            <span class="block text-xs text-gray-400 font-medium">({{ number_format($p->stok_pusat / 40, 1) }} Sak)</span>
                                        </td>

                                        <td class="px-6 py-3 text-center">
                                            @if($p->stok_pusat <= $p->min_stok)
                                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-bold animate-pulse">Menipis</span>
                                            @else
                                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-bold">Aman</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <a href="{{ route('admin.pakan.riwayat') }}" class="text-xs text-blue-600 hover:underline font-bold">Kirim</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 2: GUDANG UNIT / LOKASI (MANDOR) -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="ph-fill ph-map-pin text-cemara-600"></i> Stok di Lokasi (Gudang Unit)
                        </h3>
                        
                        <!-- Filter Unit -->
                        <form method="GET" class="flex gap-2">
                            <select name="unit_id" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cemara-500 outline-none">
                                <option value="">Semua Unit</option>
                                @foreach($units as $u_opt)
                                    <option value="{{ $u_opt->id }}" {{ request('unit_id') == $u_opt->id ? 'selected' : '' }}>{{ $u_opt->nama_unit }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($units as $unit)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                                <div class="p-4 bg-cemara-50 border-b border-cemara-100 flex justify-between items-center">
                                    <div>
                                        <h4 class="font-bold text-cemara-800 text-lg">{{ $unit->nama_unit }}</h4>
                                        <span class="text-xs text-cemara-600 font-medium">{{ $unit->lokasi }}</span>
                                    </div>
                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-cemara-600 shadow-sm">
                                        <i class="ph-bold ph-house"></i>
                                    </div>
                                </div>
                                
                                <div class="p-0 flex-1">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                            <tr>
                                                <th class="px-4 py-2">Item</th>
                                                <th class="px-4 py-2 text-right">Stok Gudang</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @forelse($unit->pakanStocks as $stock)
                                                @if($stock->jumlah_stok > 0)
                                                <tr>
                                                    <td class="px-4 py-3 font-medium text-gray-700">{{ $stock->pakan->nama_pakan }}</td>
                                                    
                                                    <td class="px-4 py-3 text-right">
                                                        <div class="font-bold text-gray-900">{{ number_format($stock->jumlah_stok, 1) }} Kg</div>
                                                        <div class="text-[10px] text-gray-500 font-medium">({{ number_format($stock->jumlah_stok / 40, 1) }} Sak)</div>
                                                    </td>
                                                </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="px-4 py-6 text-center text-gray-400 italic text-xs">Gudang unit kosong.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                                    <a href="{{ route('admin.pakan.riwayat', ['unit_id' => $unit->id]) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center justify-center gap-1">
                                        Lihat Riwayat Pemakaian <i class="ph-bold ph-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-12">
                                <p class="text-gray-400">Tidak ada unit yang ditemukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- BAGIAN 3: RIWAYAT OPNAME & KOREKSI STOK -->
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="ph-fill ph-clipboard-text text-orange-500"></i> Riwayat Opname & Koreksi Stok Terakhir
                    </h3>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-orange-50 text-orange-800 font-bold border-b border-orange-100">
                                    <tr>
                                        <th class="px-6 py-3">Tanggal Lapor</th>
                                        <th class="px-6 py-3">Lokasi / Penginput</th>
                                        <th class="px-6 py-3">Jenis Pakan</th>
                                        <th class="px-6 py-3 text-center">Selisih Koreksi</th>
                                        <th class="px-6 py-3" style="min-width: 250px;">Detail Pemeriksaan Fisik</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($riwayatOpnameMonitoring as $log)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-3">
                                            <div class="font-bold text-gray-800">
                                                {{ $log->jenis_mutasi == 'opname_pusat' ? 'Gudang Pusat (Admin)' : ($log->dariUnit->nama_unit ?? 'Gudang Unit') }}
                                            </div>
                                            <div class="text-xs text-gray-500">Oleh: {{ $log->user->name ?? 'Sistem' }}</div>
                                        </td>
                                        <td class="px-6 py-3 font-bold">{{ $log->pakan->nama_pakan }}</td>
                                        
                                        <!-- Tampilan Selisih Lebih Jelas (Highlight Minus) -->
                                        <td class="px-6 py-3 text-center">
                                            @if($log->jumlah < 0)
                                                <div class="font-bold text-red-700 bg-red-50 px-2 py-1 rounded-md inline-block border border-red-200">
                                                    Susut / Hilang: {{ number_format(abs($log->jumlah), 1) }} Kg
                                                </div>
                                                <div class="text-[10px] text-red-500 font-bold mt-1 ml-1">
                                                    Setara: {{ number_format(abs($log->jumlah) / 40, 1) }} Sak
                                                </div>
                                            @elseif($log->jumlah > 0)
                                                <div class="font-bold text-green-700 bg-green-50 px-2 py-1 rounded-md inline-block border border-green-200">
                                                    Kelebihan: +{{ number_format($log->jumlah, 1) }} Kg
                                                </div>
                                                <div class="text-[10px] text-green-600 font-bold mt-1 ml-1">
                                                    Setara: {{ number_format($log->jumlah / 40, 1) }} Sak
                                                </div>
                                            @else
                                                <div class="font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-md inline-block">
                                                    Sesuai (0 Kg)
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <!-- [UPDATE] Parsing Keterangan untuk Menampilkan Sistem vs Fisik -->
                                        <td class="px-6 py-3">
                                            @php
                                                $keteranganAsli = $log->keterangan;
                                                $isOpnameFormat = strpos($keteranganAsli, 'Sistem Seharusnya:') !== false;
                                                
                                                $sistemStr = '-';
                                                $fisikStr = '-';
                                                $alasanStr = $keteranganAsli;

                                                if ($isOpnameFormat) {
                                                    // Ekstrak data dari string "Sistem Seharusnya: X Sak. Fisik Diinput: Y Sak. Alasan Mandor: Z"
                                                    if (preg_match('/Sistem Seharusnya:\s*(.*?)\.\s*Fisik Diinput:\s*(.*?)\.\s*Alasan Mandor:\s*(.*)/i', $keteranganAsli, $matches)) {
                                                        $sistemStr = $matches[1];
                                                        $fisikStr = $matches[2];
                                                        $alasanStr = $matches[3];
                                                    } else {
                                                        // Fallback jika format regex meleset
                                                        $parts = explode('. ', $keteranganAsli);
                                                        if(count($parts) >= 3) {
                                                            $sistemStr = str_replace('Sistem Seharusnya: ', '', $parts[0]);
                                                            $fisikStr = str_replace('Fisik Diinput: ', '', $parts[1]);
                                                            $alasanStr = str_replace('Alasan Mandor: ', '', $parts[2]);
                                                        }
                                                    }
                                                }
                                            @endphp

                                            @if($isOpnameFormat)
                                                <!-- UI Jika Mengandung Sistem vs Fisik -->
                                                <div class="mb-2 bg-blue-50/40 border border-blue-100 rounded-lg p-2.5 text-xs shadow-sm w-full max-w-xs">
                                                    <div class="flex justify-between border-b border-blue-100/50 pb-1 mb-1">
                                                        <span class="text-gray-600 font-medium">Sistem (Seharusnya)</span>
                                                        <span class="font-bold text-gray-800">{{ $sistemStr }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 font-medium">Fisik (Gudang)</span>
                                                        <span class="font-bold text-gray-800">{{ $fisikStr }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-sm text-gray-800 font-medium wrap-break-word leading-relaxed max-w-xs">
                                                    <span class="text-[10px] text-gray-500 block mb-0.5 uppercase tracking-wider font-bold">Catatan Mandor:</span>
                                                    {{ $alasanStr }}
                                                </div>
                                            @else
                                                <!-- UI Jika Teks Biasa -->
                                                <div class="text-sm text-gray-800 font-medium wrap-break-word leading-relaxed max-w-xs">
                                                    {{ $alasanStr }}
                                                </div>
                                            @endif
                                            
                                            <!-- Penanda Visual Cerdas -->
                                            @if($log->jenis_mutasi == 'pakan_rusak' || stripos($log->keterangan, 'rusak') !== false || stripos($log->keterangan, 'ilang') !== false)
                                                <span class="inline-block mt-2 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] rounded-full font-bold border border-red-200"><i class="ph-fill ph-warning"></i> Pakan Rusak/Hilang</span>
                                            @else
                                                <span class="inline-block mt-2 px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] rounded-full font-bold border border-blue-200"><i class="ph-fill ph-info"></i> Koreksi Fisik</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data koreksi/opname.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 text-center bg-gray-50">
                            <!-- Link ini akan membawa Admin langsung ke halaman Riwayat -->
                            <a href="{{ route('admin.pakan.riwayat') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 flex items-center justify-center gap-1">
                                Lihat Seluruh Histori <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>