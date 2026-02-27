<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pakan - Admin Panel</title>
    
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

    @php
        $start = isset($startDate) ? $startDate : (request('start_date') ? \Carbon\Carbon::parse(request('start_date')) : \Carbon\Carbon::now()->startOfMonth());
        $end = isset($endDate) ? $endDate : (request('end_date') ? \Carbon\Carbon::parse(request('end_date')) : \Carbon\Carbon::now());
        
        $hasData = (isset($rekapStok) && count($rekapStok) > 0) || 
                   (isset($dataMasuk) && count($dataMasuk) > 0) || 
                   (isset($dataKeluar) && count($dataKeluar) > 0);

        // [BARU] Filter Data Opname dari detailMutasi yang dikirim Controller
        $dataOpname = isset($detailMutasi) ? $detailMutasi->filter(function($item) {
            return in_array($item->jenis_mutasi, ['opname', 'opname_pusat', 'opname_unit', 'penyesuaian', 'pakan_rusak', 'koreksi'])
                || stripos($item->keterangan, 'Opname') !== false
                || stripos($item->keterangan, 'Rusak') !== false
                || stripos($item->keterangan, 'ilang') !== false
                || stripos($item->keterangan, 'Selisih') !== false;
        }) : collect([]);
    @endphp

    <div class="flex h-screen overflow-hidden relative">
        @include('admin.sidebar')
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Laporan Stok & Pakan</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg"><i class="ph-bold ph-list text-2xl"></i></button>
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-50 text-cemara-700 rounded-lg text-sm font-semibold border border-cemara-100">
                        <i class="ph-fill ph-calendar-check"></i> <span>{{ date('d M Y') }}</span>
                    </div>
                </div>
            </header>

            <div class="p-4 md:p-8 w-full max-w-6xl mx-auto" x-data="{ includeChart: false }">
                
                <!-- Filter Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8">
                    <form action="{{ route('admin.pakan.laporan') }}" method="GET">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                            <div class="w-10 h-10 bg-gold-100 rounded-full flex items-center justify-center text-gold-600"><i class="ph-fill ph-files text-xl"></i></div>
                            <div><h3 class="font-bold text-gray-800">Filter Laporan</h3><p class="text-xs text-gray-500">Pilih periode laporan yang ingin dicetak.</p></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Periode Tanggal</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-medium text-gray-700">
                                    <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none transition font-medium text-gray-700">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4">
                            <label class="flex items-center gap-2 cursor-pointer group select-none">
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="include_chart" value="1" x-model="includeChart" class="peer sr-only">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded peer-checked:bg-gold-500 peer-checked:border-gold-500 transition"></div>
                                    <i class="ph-bold ph-check text-white text-xs absolute left-0.5 top-0.5 opacity-0 peer-checked:opacity-100 transition"></i>
                                </div>
                                <span class="text-sm text-gray-600 font-medium group-hover:text-gold-600 transition">Sertakan Grafik Analisis (PDF)</span>
                            </label>

                            <button type="submit" class="px-8 py-3 bg-gold-500 text-white font-bold rounded-xl hover:bg-gold-600 transition shadow-lg flex items-center gap-2 w-full md:w-auto justify-center">
                                <i class="ph-bold ph-magnifying-glass"></i> Tampilkan Data
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Result Section -->
                @if($hasData)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">Pratinjau Laporan</h4>
                            <p class="text-sm text-gray-500">Periode: {{ $start->format('d M Y') }} s/d {{ $end->format('d M Y') }}</p>
                        </div>
                        <!-- Tombol Download -->
                        <a href="{{ request()->fullUrlWithQuery(['print' => 'true']) }}" target="_blank" class="px-6 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition shadow-md flex items-center gap-2">
                            <i class="ph-bold ph-file-pdf"></i> Download PDF
                        </a>
                    </div>
                    
                    <div class="p-6">
                        <!-- 1. REKAPITULASI -->
                        @if(isset($rekapStok) && count($rekapStok) > 0)
                        <h5 class="font-bold text-gray-700 mb-3 border-l-4 border-indigo-500 pl-3">I. Rekapitulasi Stok Gudang Pusat</h5>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 mb-8">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3">Nama Pakan</th>
                                        <th class="px-4 py-3 text-right">Stok Awal</th>
                                        <th class="px-4 py-3 text-right text-green-600">Masuk</th>
                                        <th class="px-4 py-3 text-right text-orange-600">Keluar (Dist)</th>
                                        <th class="px-4 py-3 text-right">Stok Akhir</th>
                                        <th class="px-4 py-3 text-center border-l">Konsumsi Kandang</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($rekapStok as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $item['nama_pakan'] }}</td>
                                        
                                        <!-- Info Sak -->
                                        <td class="px-4 py-2 text-right text-gray-500">
                                            <div>{{ number_format($item['stok_awal_pusat'], 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-400">({{ number_format($item['stok_awal_pusat'] / 40, 1) }} Sak)</div>
                                        </td>
                                        
                                        <td class="px-4 py-2 text-right">
                                            <div class="text-green-600 font-bold">+{{ number_format($item['masuk_pusat'], 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-400">({{ number_format($item['masuk_pusat'] / 40, 1) }} Sak)</div>
                                        </td>
                                        
                                        <td class="px-4 py-2 text-right">
                                            <div class="text-orange-600 font-bold">-{{ number_format($item['keluar_pusat'], 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-400">({{ number_format($item['keluar_pusat'] / 40, 1) }} Sak)</div>
                                        </td>
                                        
                                        <td class="px-4 py-2 text-right bg-gray-50">
                                            <div class="font-bold text-gray-800">{{ number_format($item['stok_akhir_pusat'], 1) }} {{ $item['satuan'] }}</div>
                                            <div class="text-[10px] text-gray-500 font-medium">({{ number_format($item['stok_akhir_pusat'] / 40, 1) }} Sak)</div>
                                        </td>
                                        
                                        <td class="px-4 py-2 text-center border-l">
                                            <div class="text-red-600 font-bold">{{ number_format($item['total_pemakaian'], 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-400">({{ number_format($item['total_pemakaian'] / 40, 1) }} Sak)</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        <!-- 2. Detail Masuk -->
                        @if(isset($dataMasuk))
                        <h5 class="font-bold text-gray-700 mb-3 border-l-4 border-green-500 pl-3">II. Riwayat Stok Masuk (Pusat)</h5>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 mb-8">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Sumber</th>
                                        <th class="px-4 py-3">Jenis Pakan</th>
                                        <th class="px-4 py-3 text-right">Jumlah (Kg)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($dataMasuk->take(5) as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">
                                            @if($item->jenis_mutasi == 'produksi')
                                                <span class="inline-flex items-center gap-1 text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-xs font-semibold">Prod. Sendiri</span>
                                            @else
                                                <span class="text-gray-600">{{ Str::contains($item->keterangan, 'Supplier:') ? Str::between($item->keterangan, 'Supplier: ', '.') : ($item->supplier ?? 'Pembelian') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 font-bold">{{ $item->pakan->nama_pakan ?? '-' }}</td>
                                        
                                        <td class="px-4 py-2 text-right">
                                            <div class="font-bold text-green-600">+{{ number_format($item->jumlah, 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-500 font-medium">({{ number_format($item->jumlah / 40, 1) }} Sak)</div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="px-4 py-3 text-center text-gray-400 italic">Tidak ada data masuk periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if($dataMasuk->count() > 5) <div class="px-4 py-2 text-xs text-center text-gray-400 bg-gray-50 border-t">... dan {{ $dataMasuk->count() - 5 }} data lainnya (Lihat PDF Lengkap)</div> @endif
                        </div>
                        @endif

                        <!-- 3. Detail Distribusi -->
                        @if(isset($dataDistribusi))
                        <h5 class="font-bold text-gray-700 mb-3 border-l-4 border-orange-500 pl-3">III. Riwayat Distribusi (Admin -> Unit)</h5>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 mb-8">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Lokasi Tujuan (Unit)</th>
                                        <th class="px-4 py-3">Jenis Pakan</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-right">Jumlah (Kg)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($dataDistribusi->take(5) as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">
                                            <span class="font-bold text-gray-800">
                                                {{ $item->unitTujuan->nama_unit ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">{{ $item->pakan->nama_pakan ?? '-' }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($item->status == 'selesai')
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Diterima</span>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">Pending</span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-4 py-2 text-right">
                                            <div class="font-bold text-orange-600">-{{ number_format($item->jumlah, 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-500 font-medium">({{ number_format($item->jumlah / 40, 1) }} Sak)</div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="px-4 py-3 text-center text-gray-400 italic">Tidak ada data distribusi periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if($dataDistribusi->count() > 5) <div class="px-4 py-2 text-xs text-center text-gray-400 bg-gray-50 border-t">... dan {{ $dataDistribusi->count() - 5 }} data lainnya (Lihat PDF Lengkap)</div> @endif
                        </div>
                        @endif

                        <!-- 4. Detail Pemakaian -->
                        @if(isset($dataKeluar))
                        <h5 class="font-bold text-gray-700 mb-3 border-l-4 border-red-500 pl-3">IV. Riwayat Pemakaian Pakan (Kandang)</h5>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 mb-8">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Lokasi (Unit - Kandang)</th>
                                        <th class="px-4 py-3">Jenis Pakan</th>
                                        <th class="px-4 py-3 text-right">Jumlah (Kg)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($dataKeluar->take(5) as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-800">
                                                    {{ $item->dariUnit->nama_unit ?? $item->kandang->unit->nama_unit ?? '-' }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $item->kandang->nama_kandang ?? 'Kandang Hapus' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">{{ $item->pakan->nama_pakan ?? '-' }}</td>
                                        
                                        <td class="px-4 py-2 text-right">
                                            <div class="font-bold text-red-600">-{{ number_format($item->jumlah, 1) }} Kg</div>
                                            <div class="text-[10px] text-gray-500 font-medium">({{ number_format($item->jumlah / 40, 1) }} Sak)</div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="px-4 py-3 text-center text-gray-400 italic">Tidak ada data pemakaian periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if($dataKeluar->count() > 5) <div class="px-4 py-2 text-xs text-center text-gray-400 bg-gray-50 border-t">... dan {{ $dataKeluar->count() - 5 }} data lainnya (Lihat PDF Lengkap)</div> @endif
                        </div>
                        @endif

                        <!-- [BARU] 5. Detail Opname & Koreksi Fisik -->
                        @if($dataOpname->count() > 0)
                        <h5 class="font-bold text-gray-700 mb-3 border-l-4 border-blue-500 pl-3">V. Riwayat Opname & Koreksi Fisik</h5>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Lokasi Gudang</th>
                                        <th class="px-4 py-3">Jenis Pakan</th>
                                        <th class="px-4 py-3 text-center">Selisih</th>
                                        <th class="px-4 py-3">Detail Pemeriksaan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($dataOpname->take(5) as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 font-bold text-gray-800">
                                            {{ $item->jenis_mutasi == 'opname_pusat' ? 'Gudang Pusat (Admin)' : ($item->dariUnit->nama_unit ?? 'Gudang Unit') }}
                                        </td>
                                        <td class="px-4 py-2">{{ $item->pakan->nama_pakan ?? '-' }}</td>
                                        
                                        <td class="px-4 py-2 text-center">
                                            @if($item->jumlah < 0)
                                                <div class="font-bold text-red-600">-{{ number_format(abs($item->jumlah), 1) }} Kg</div>
                                                <div class="text-[10px] text-gray-500 font-medium">({{ number_format(abs($item->jumlah) / 40, 1) }} Sak)</div>
                                            @elseif($item->jumlah > 0)
                                                <div class="font-bold text-green-600">+{{ number_format($item->jumlah, 1) }} Kg</div>
                                                <div class="text-[10px] text-gray-500 font-medium">({{ number_format($item->jumlah / 40, 1) }} Sak)</div>
                                            @else
                                                <div class="font-bold text-gray-500">0 Kg</div>
                                            @endif
                                        </td>

                                        <td class="px-4 py-2">
                                            @php
                                                $keteranganAsli = $item->keterangan;
                                                $isOpnameFormat = strpos($keteranganAsli, 'Sistem Seharusnya:') !== false;
                                                
                                                $sistemStr = '-';
                                                $fisikStr = '-';
                                                $alasanStr = $keteranganAsli;

                                                if ($isOpnameFormat) {
                                                    // Parse keterangan format "Sistem Seharusnya: X Sak. Fisik Diinput: Y Sak. Alasan Mandor: Z"
                                                    if (preg_match('/Sistem Seharusnya:\s*(.*?)\.\s*Fisik Diinput:\s*(.*?)\.\s*Alasan Mandor:\s*(.*)/i', $keteranganAsli, $matches)) {
                                                        $sistemStr = $matches[1];
                                                        $fisikStr = $matches[2];
                                                        $alasanStr = $matches[3];
                                                    } else {
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
                                                <div class="mb-1 text-xs text-gray-700 bg-gray-50 p-1.5 rounded border border-gray-100">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500 font-medium">Sistem:</span>
                                                        <span class="font-bold">{{ $sistemStr }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500 font-medium">Fisik:</span>
                                                        <span class="font-bold">{{ $fisikStr }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-[10px] text-gray-600 font-medium italic">Keterangan: {{ $alasanStr }}</div>
                                            @else
                                                <div class="text-xs text-gray-700">{{ $alasanStr }}</div>
                                            @endif
                                            
                                            <!-- Penanda Alert jika pakan rusak -->
                                            @if($item->jenis_mutasi == 'pakan_rusak' || stripos($item->keterangan, 'rusak') !== false || stripos($item->keterangan, 'ilang') !== false)
                                                <span class="inline-block mt-1 px-2 py-0.5 bg-red-50 text-red-600 text-[9px] rounded-full border border-red-200 font-bold uppercase">Pakan Rusak</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            @if($dataOpname->count() > 5) <div class="px-4 py-2 text-xs text-center text-gray-400 bg-gray-50 border-t">... dan {{ $dataOpname->count() - 5 }} data lainnya (Lihat PDF Lengkap)</div> @endif
                        </div>
                        @endif

                    </div>
                </div>
                @else
                <div class="mt-8 text-center py-12 bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400"><i class="ph-fill ph-magnifying-glass text-3xl"></i></div>
                    <h3 class="text-gray-800 font-bold">Data Belum Ditampilkan</h3>
                    <p class="text-gray-500 text-sm">Silakan pilih tanggal dan klik "Tampilkan Data".</p>
                </div>
                @endif

            </div>
        </main>
    </div>
</body>
</html>