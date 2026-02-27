<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pakan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-poppins; font-size: 11px; color: #333; margin: 0; padding: 15px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; font-weight: bold; }
        .header h2 { margin: 5px 0; font-size: 12px; font-weight: normal; }
        
        .meta-table { width: 100%; margin-bottom: 15px; font-size: 11px; }
        .meta-table td { padding: 2px; }
        
        h3 { font-size: 13px; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-top: 20px; text-transform: uppercase; font-weight: bold; }
        
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data th, table.data td { border: 1px solid #666; padding: 4px 6px; text-align: left; vertical-align: top; }
        table.data th { background-color: #f0f0f0; font-weight: bold; text-align: center; vertical-align: middle; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-red { color: #c00; }
        .text-green { color: #080; }
        .text-orange { color: #d67d00; }
        .text-muted { color: #666; font-size: 9px; font-weight: normal; }
        
        .summary-box { float: right; width: 45%; border: 1px solid #000; padding: 8px; margin-top: 15px; background: #fff; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-weight: bold; border-bottom: 1px dashed #ccc; padding-bottom: 3px; }
        .summary-row:last-child { border-bottom: none; }
        
        .footer { margin-top: 40px; width: 100%; display: inline-block;}
        .signature { float: right; width: 200px; text-align: center; }
        
        .page-break { page-break-before: always; }
        
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="setTimeout(window.print, 1000)">

    @php
        $start = isset($startDate) ? $startDate : (request('start_date') ? \Carbon\Carbon::parse(request('start_date')) : \Carbon\Carbon::now()->startOfMonth());
        $end = isset($endDate) ? $endDate : (request('end_date') ? \Carbon\Carbon::parse(request('end_date')) : \Carbon\Carbon::now());
        
        $masuk = isset($dataMasuk) ? $dataMasuk : collect([]);
        $keluar = isset($dataKeluar) ? $dataKeluar : collect([]);
        $distribusi = isset($dataDistribusi) ? $dataDistribusi : collect([]);
    @endphp

    <div class="header">
        <h1>FARM MANAJEMEN SYSTEM</h1>
        <h2>LAPORAN MUTASI & PENGGUNAAN PAKAN</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td width="15%"><strong>Periode</strong></td>
            <td width="2%">:</td>
            <td>{{ $start->translatedFormat('d M Y') }} s/d {{ $end->translatedFormat('d M Y') }}</td>
            <td width="15%" class="text-right"><strong>Dicetak Oleh</strong></td>
            <td width="2%">:</td>
            <td width="20%">{{ Auth::user()->name ?? 'Admin' }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>:</td>
            <td>{{ date('d F Y, H:i') }} WIB</td>
            <td colspan="3"></td>
        </tr>
    </table>

    <!-- BAGIAN 1: REKAPITULASI -->
    @if(isset($rekapStok) && count($rekapStok) > 0)
    <h3>I. Rekapitulasi Stok Gudang Pusat</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Nama Pakan</th>
                <th class="text-right">Stok Awal</th>
                <th class="text-right">Masuk</th>
                <th class="text-right">Keluar (Dist)</th>
                <th class="text-right">Stok Akhir</th>
                <th class="text-center">Total Konsumsi (Unit)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapStok as $item)
            <tr>
                <td>{{ $item['nama_pakan'] }}</td>
                <td class="text-right text-gray-500">
                    {{ number_format($item['stok_awal_pusat'], 1) }} Kg<br>
                    <span class="text-muted">({{ number_format($item['stok_awal_pusat'] / 40, 1) }} Sak)</span>
                </td>
                <td class="text-right text-green">
                    +{{ number_format($item['masuk_pusat'], 1) }} Kg<br>
                    <span class="text-muted">({{ number_format($item['masuk_pusat'] / 40, 1) }} Sak)</span>
                </td>
                <td class="text-right text-orange">
                    -{{ number_format($item['keluar_pusat'], 1) }} Kg<br>
                    <span class="text-muted">({{ number_format($item['keluar_pusat'] / 40, 1) }} Sak)</span>
                </td>
                <td class="text-right font-bold">
                    {{ number_format($item['stok_akhir_pusat'], 1) }} Kg<br>
                    <span class="text-muted">({{ number_format($item['stok_akhir_pusat'] / 40, 1) }} Sak)</span>
                </td>
                <td class="text-center">
                    {{ number_format($item['total_pemakaian'], 1) }} Kg<br>
                    <span class="text-muted">({{ number_format($item['total_pemakaian'] / 40, 1) }} Sak)</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- BAGIAN 2: STOK MASUK -->
    <h3>{{ (isset($rekapStok) && count($rekapStok) > 0) ? 'II' : 'I' }}. Stok Masuk (Restock Pusat)</h3>
    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="28%">Nama Pakan</th>
                <th width="20%">Sumber / Supplier</th>
                <th width="20%">Detail Kemasan</th>
                <th width="15%">Jumlah Masuk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($masuk as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td><strong>{{ $item->pakan->nama_pakan ?? '-' }}</strong></td>
                <td>
                    @if(isset($item->jenis_mutasi) && $item->jenis_mutasi == 'produksi')
                        Produksi Sendiri
                    @else
                        {{ Str::contains($item->keterangan, 'Supplier:') ? Str::between($item->keterangan, 'Supplier: ', '.') : ($item->supplier ?? 'Pembelian Luar') }}
                    @endif
                </td>
                <td class="text-center">
                    @if(str_contains($item->keterangan, 'Sak'))
                        {{ Str::after($item->keterangan, 'Detail: ') }}
                    @else - @endif
                </td>
                <td class="text-right text-green">
                    <strong>+{{ number_format($item->jumlah) }} Kg</strong><br>
                    <span class="text-muted">({{ number_format($item->jumlah / 40, 1) }} Sak)</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data stok masuk.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f9f9f9;">
                <td colspan="5" class="text-right"><strong>TOTAL MASUK</strong></td>
                <td class="text-right text-green">
                    <strong>+{{ number_format($masuk->sum('jumlah')) }} Kg</strong><br>
                    <span class="text-muted">({{ number_format($masuk->sum('jumlah') / 40, 1) }} Sak)</span>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- BAGIAN 3: DISTRIBUSI -->
    <h3>{{ (isset($rekapStok) && count($rekapStok) > 0) ? 'III' : 'II' }}. Distribusi (Admin -> Unit)</h3>
    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="25%">Lokasi Tujuan (Unit)</th>
                <th width="28%">Nama Pakan</th>
                <th width="15%">Status</th>
                <th width="15%">Jumlah Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($distribusi as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td>
                    {{-- Pastikan relasi 'unitTujuan' ada di Model PakanMutation.php --}}
                    <strong>{{ $item->unitTujuan->nama_unit ?? '-' }}</strong>
                </td>
                <td>{{ $item->pakan->nama_pakan ?? '-' }}</td>
                <td class="text-center">{{ ucfirst($item->status) }}</td>
                <td class="text-right text-orange">
                    <strong>-{{ number_format($item->jumlah) }} Kg</strong><br>
                    <span class="text-muted">({{ number_format($item->jumlah / 40, 1) }} Sak)</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data distribusi.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f9f9f9;">
                <td colspan="5" class="text-right"><strong>TOTAL DISTRIBUSI</strong></td>
                <td class="text-right text-orange">
                    <strong>-{{ number_format($distribusi->sum('jumlah')) }} Kg</strong><br>
                    <span class="text-muted">({{ number_format($distribusi->sum('jumlah') / 40, 1) }} Sak)</span>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- BAGIAN 4: STOK KELUAR -->
    <h3>{{ (isset($rekapStok) && count($rekapStok) > 0) ? 'IV' : 'III' }}. Pemakaian Pakan (Kandang)</h3>
    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="25%">Lokasi (Unit - Kandang)</th> 
                <th width="30%">Nama Pakan</th>
                <th width="13%">Pemakaian</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($keluar as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td class="text-left">
                    <strong>{{ $item->dariUnit->nama_unit ?? $item->kandang->unit->nama_unit ?? '-' }}</strong>
                    <br>
                    <span style="font-size: 10px; color: #555;">({{ $item->kandang->nama_kandang ?? 'Kandang Hapus' }})</span>
                </td>
                <td>{{ $item->pakan->nama_pakan ?? '-' }}</td>
                <td class="text-right text-red">
                    <strong>-{{ number_format($item->jumlah) }} Kg</strong><br>
                    <span class="text-muted">({{ number_format($item->jumlah / 40, 1) }} Sak)</span>
                </td>
                <td class="text-center" style="font-size: 10px;">
                    @if(str_contains($item->keterangan, '[OVER LIMIT]'))
                        <span style="color:red; font-weight:bold;">Over Limit</span>
                    @else
                        Normal
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data pemakaian.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f9f9f9;">
                <td colspan="4" class="text-right"><strong>TOTAL PEMAKAIAN</strong></td>
                <td colspan="2" class="text-left text-red">
                    <strong>-{{ number_format($keluar->sum('jumlah')) }} Kg</strong>
                    <span class="text-muted" style="margin-left: 5px;">({{ number_format($keluar->sum('jumlah') / 40, 1) }} Sak)</span>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- RINGKASAN -->
    <div style="clear: both;"></div>
    <div class="summary-box">
        <div style="text-align: center; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom:5px; font-weight:bold;">RINGKASAN MUTASI</div>
        <div class="summary-row">
            <span>Total Masuk (Pusat):</span>
            <span class="text-green">+{{ number_format($masuk->sum('jumlah')) }} Kg <span class="text-muted">({{ number_format($masuk->sum('jumlah') / 40, 1) }} Sak)</span></span>
        </div>
        <div class="summary-row">
            <span>Total Distribusi (Pusat):</span>
            <span class="text-orange">-{{ number_format($distribusi->sum('jumlah')) }} Kg <span class="text-muted">({{ number_format($distribusi->sum('jumlah') / 40, 1) }} Sak)</span></span>
        </div>
        <div class="summary-row">
            <span>Total Konsumsi (Kandang):</span>
            <span class="text-red">-{{ number_format($keluar->sum('jumlah')) }} Kg <span class="text-muted">({{ number_format($keluar->sum('jumlah') / 40, 1) }} Sak)</span></span>
        </div>
        <div class="summary-row" style="border-top: 1px solid #000; margin-top:5px; padding-top:5px;">
            <span>Stok Akhir Pusat:</span>
            <span>
                {{ isset($rekapStok) ? number_format($rekapStok->sum('stok_akhir_pusat')) : '-' }} Kg 
                @if(isset($rekapStok))
                    <span class="text-muted">({{ number_format($rekapStok->sum('stok_akhir_pusat') / 40, 1) }} Sak)</span>
                @endif
            </span>
        </div>
    </div>

    <!-- GRAFIK -->
    @if(request('include_chart') == '1' && $keluar->count() > 0)
        <div class="page-break"></div>
        <h3 style="text-align: center; border: none;">GRAFIK TREN PEMAKAIAN HARIAN</h3>
        <div style="width: 100%; height: 350px; border: 1px solid #ccc; padding: 10px;">
            <canvas id="pdfChart"></canvas>
        </div>
        <script>
            // Data JSON dari Controller
            const rawData = {!! json_encode($keluar) !!};
            
            // Grouping data by date
            const grouped = rawData.reduce((acc, curr) => {
                const rawDate = curr.tanggal;
                if (rawDate) {
                    const date = rawDate.split(' ')[0];
                    if(!acc[date]) acc[date] = 0;
                    acc[date] += parseFloat(curr.jumlah);
                }
                return acc;
            }, {});

            const sortedKeys = Object.keys(grouped).sort();
            const labels = sortedKeys.map(d => {
                const dateObj = new Date(d);
                return dateObj.getDate() + '/' + (dateObj.getMonth() + 1);
            });
            const values = sortedKeys.map(d => grouped[d]);

            const ctx = document.getElementById('pdfChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pemakaian (Kg)',
                        data: values,
                        backgroundColor: '#cc0000',
                        borderColor: '#990000',
                        borderWidth: 1
                    }]
                },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'bottom' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        </script>
    @endif

    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <br><br><br>
            <p style="border-top: 1px solid #000;">Owner / Manager</p>
        </div>
    </div>

</body>
</html>