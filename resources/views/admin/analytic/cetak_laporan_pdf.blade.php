<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian Kandang</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 10px; margin: 0; padding: 10px; color: #000; }
        
        .header-container { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .header-subtitle { font-size: 12px; margin: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #000; padding: 4px; }
        
        /* Header Tabel Gaya Laporan */
        th { background-color: #e0e0e0; text-align: center; vertical-align: middle; font-weight: bold; font-size: 9px; }
        
        /* Kolom Data */
        td { font-size: 9px; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        /* Warna Baris Ganjil/Genap */
        tr:nth-child(even) { background-color: #f9f9f9; }

        /* Tanda Tangan */
        .footer-signature { margin-top: 30px; width: 100%; display: table; }
        .signature-box { display: table-cell; width: 33%; text-align: center; vertical-align: top; }
        .signature-space { height: 50px; }
        
        /* Kotak Info Manual */
        .manual-info-box { margin-top: 20px; width: 40%; border: 1px solid #000; padding: 10px; float: left; background-color: #fff; }
        .manual-info-box h4 { margin: 0 0 8px 0; font-size: 11px; text-transform: uppercase; border-bottom: 1px dashed #ccc; padding-bottom: 4px; }
        .manual-info-table { width: 100%; border: none; margin-top: 0; }
        .manual-info-table tr { background-color: transparent !important; }
        .manual-info-table td { border: none; padding: 3px 0; font-size: 10px; }
        
        .clear-both { clear: both; }

        @media print {
            @page { size: landscape; margin: 10mm; } /* Cetak Landscape agar muat banyak kolom */
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header-container">
        <h1 class="header-title">LAPORAN HARIAN KANDANG</h1>
        <p class="header-subtitle">
            Periode: {{ $request->dateStart ? \Carbon\Carbon::parse($request->dateStart)->translatedFormat('d F Y') : '-' }} 
            s/d 
            {{ $request->dateEnd ? \Carbon\Carbon::parse($request->dateEnd)->translatedFormat('d F Y') : '-' }}
        </p>
    </div>

    <!-- PENGAMBILAN NAMA FILTER YANG TEPAT UNTUK KOP SURAT -->
    @php
        $lokasiTeks = ($request->lokasi && $request->lokasi != 'all') ? ucfirst($request->lokasi) : 'Semua Lokasi';
        
        $unitTeks = 'Semua Unit';
        if ($request->unit && $request->unit != 'all') {
            $u = \App\Models\Unit::find($request->unit);
            $unitTeks = $u ? $u->nama_unit : 'ID: ' . $request->unit;
        }

        $kandangTeks = 'Semua Kandang';
        if ($request->kandang && $request->kandang != 'all') {
            $k = \App\Models\Kandang::find($request->kandang);
            $kandangTeks = $k ? $k->nama_kandang : 'ID: ' . $request->kandang;
        }

        $batchTeks = 'Semua Batch';
        if ($request->batch_id && $request->batch_id != 'all') {
            $b = \App\Models\Batch::find($request->batch_id);
            $batchTeks = $b ? $b->nama_batch : 'ID: ' . $request->batch_id;
        }
    @endphp

    <!-- Info Filter -->
    <div style="margin-bottom: 10px; font-size: 11px;">
        <span style="margin-right: 20px;"><strong>Lokasi:</strong> {{ $lokasiTeks }}</span>
        <span style="margin-right: 20px;"><strong>Unit:</strong> {{ $unitTeks }}</span>
        <span style="margin-right: 20px;"><strong>Kandang:</strong> {{ $kandangTeks }}</span>
        <span><strong>Batch:</strong> {{ $batchTeks }}</span>
    </div>

    <table>
        <thead>
            <!-- Baris 1 Header (Pengelompokan) -->
            <tr>
                <th rowspan="2" width="3%">No</th>
                <th rowspan="2" width="7%">Tanggal</th>
                <th rowspan="2" width="9%">Kandang</th>
                
                <!-- Kolom Batch -->
                <th rowspan="2" width="7%">Batch</th>
                
                <!-- Group Populasi -->
                <th colspan="4">Populasi Ayam</th>
                
                <!-- Group Telur -->
                <th colspan="3">Produksi Telur</th>
                
                <!-- Group Pakan -->
                <th colspan="3">Pakan</th>
                
                <!-- Group Analisa -->
                <th colspan="4">Performance</th>
            </tr>
            
            <!-- Baris 2 Header (Detail Kolom) -->
            <tr>
                <!-- Populasi -->
                <th width="5%">Awal</th>
                <th width="4%">Mati</th>
                <th width="4%">Afkir</th>
                <th width="5%">Akhir</th>
                
                <!-- Telur -->
                <th width="5%">Utuh (Btr)</th>
                <th width="6%">Berat (Kg)</th>
                <th width="5%">Avg (gr)</th>
                
                <!-- Pakan -->
                <th width="8%">Jenis</th>
                <th width="6%">Total (Kg)</th>
                <th width="5%">gr/Ekor</th>
                
                <!-- Performance -->
                <th width="5%">HD %</th>
                <th width="5%">HH (Btr)</th>
                <th width="5%">HH (Kg)</th>
                <th width="5%">FCR</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotalTelurButir = 0;
                $grandTotalTelurKg = 0;
                $grandTotalPakan = 0;
                $grandTotalMati = 0;
                $grandTotalAfkir = 0;
                $grandTotalHhButir = 0;
                $grandTotalHhKg = 0;
            @endphp

            @forelse($laporanData as $index => $row)
            @php
                $populasiAkhir = $row->populasi_awal - $row->mati - $row->afkir;
                $avgBeratTelur = $row->telur_butir > 0 ? ($row->telur_kg * 1000) / $row->telur_butir : 0;
                $konsumsiPerEkor = $populasiAkhir > 0 ? ($row->pakan_kg * 1000) / $populasiAkhir : 0;
                
                // Hitung HH Harian (Kontribusi)
                $stokAwal = $row->kandang->stok_awal ?? 1;
                $hhButir = $stokAwal > 0 ? $row->telur_butir / $stokAwal : 0;
                $hhKg = $stokAwal > 0 ? $row->telur_kg / $stokAwal : 0;
                
                // Akumulasi Total
                $grandTotalTelurButir += $row->telur_butir;
                $grandTotalTelurKg += $row->telur_kg;
                $grandTotalPakan += $row->pakan_kg;
                $grandTotalMati += $row->mati;
                $grandTotalAfkir += $row->afkir;
                $grandTotalHhButir += $hhButir;
                $grandTotalHhKg += $hhKg;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td class="text-left">
                    <strong>{{ $row->kandang->nama_kandang ?? '-' }}</strong><br>
                    <span style="font-size: 8px;">{{ $row->kandang->unit->nama_unit ?? '' }}</span>
                </td>
                
                <!-- Isi Kolom Batch -->
                <td class="text-center" style="font-size: 8px;">
                    @if($row->siklus)
                        {{ $row->siklus->tanggal_chick_in->format('Y') }}<br>
                        ({{ $row->siklus->jenis_ayam }})
                    @else
                        -
                    @endif
                </td>
                
                <!-- Populasi -->
                <td class="text-right">{{ number_format($row->populasi_awal) }}</td>
                <td class="text-center text-red-600 font-bold">{{ $row->mati > 0 ? $row->mati : '-' }}</td>
                <td class="text-center">{{ $row->afkir > 0 ? $row->afkir : '-' }}</td>
                <td class="text-right font-bold">{{ number_format($populasiAkhir) }}</td>
                
                <!-- Telur -->
                <td class="text-right">{{ number_format($row->telur_butir) }}</td>
                <td class="text-right font-bold">{{ number_format($row->telur_kg, 2) }}</td>
                <td class="text-right">{{ number_format($avgBeratTelur, 1) }}</td>
                
                <!-- Pakan -->
                <td class="text-center" style="font-size: 8px;">{{ $row->pakan->nama_pakan ?? '-' }}</td>
                <td class="text-right font-bold">{{ number_format($row->pakan_kg, 1) }}</td>
                <td class="text-right">{{ number_format($konsumsiPerEkor, 1) }}</td>
                
                <!-- Performance -->
                <td class="text-center font-bold">{{ number_format($row->hdp, 1) }}%</td>
                <td class="text-center">{{ number_format($hhButir, 3) }}</td>
                <td class="text-center">{{ number_format($hhKg, 3) }}</td>
                <td class="text-center font-bold">{{ number_format($row->fcr, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="18" class="text-center" style="padding: 20px;">Tidak ada data laporan pada periode dan filter ini.</td>
            </tr>
            @endforelse
        </tbody>
        
        <!-- Footer Total (Hanya muncul jika ada data) -->
        @if(count($laporanData) > 0)
        <tfoot>
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL / RATA-RATA</td>
                
                <!-- Populasi -->
                <td></td>
                <td class="text-center text-red-600">{{ number_format($grandTotalMati) }}</td>
                <td class="text-center">{{ number_format($grandTotalAfkir) }}</td>
                <td></td>
                
                <!-- Telur -->
                <td class="text-right">{{ number_format($grandTotalTelurButir) }}</td>
                <td class="text-right">{{ number_format($grandTotalTelurKg, 2) }}</td>
                <td class="text-right">{{ $grandTotalTelurButir > 0 ? number_format(($grandTotalTelurKg * 1000) / $grandTotalTelurButir, 1) : 0 }}</td>
                
                <!-- Pakan -->
                <td></td>
                <td class="text-right">{{ number_format($grandTotalPakan, 1) }}</td>
                <td></td>
                
                <!-- Performance -->
                <td class="text-center">{{ number_format($laporanData->avg('hdp'), 1) }}%</td>
                <td class="text-center text-blue-600">{{ number_format($grandTotalHhButir, 3) }}</td>
                <td class="text-center text-blue-600">{{ number_format($grandTotalHhKg, 3) }}</td>
                <td class="text-center font-bold">{{ $grandTotalTelurKg > 0 ? number_format($grandTotalPakan / $grandTotalTelurKg, 2) : 0 }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- KOTAK DATA TAMBAHAN MANUAL -->
    @if(request('manual_peti') || request('manual_pecah') || request('manual_konsumsi'))
    <div class="manual-info-box">
        <h4>Catatan Tambahan (Manual)</h4>
        <table class="manual-info-table">
            <tr>
                <td width="55%">Total Peti</td>
                <td width="5%">:</td>
                <td width="40%" class="font-bold">{{ request('manual_peti') ?: '0' }} Peti</td>
            </tr>
            <tr>
                <td>Total Telur Pecah</td>
                <td>:</td>
                <td class="font-bold">{{ request('manual_pecah') ?: '0' }} Kg</td>
            </tr>
            <tr>
                <td>Total Konsumsi Pegawai</td>
                <td>:</td>
                <td class="font-bold">{{ request('manual_konsumsi') ?: '0' }} Kg</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="clear-both"></div>

    <!-- Tanda Tangan -->
    <div class="footer-signature">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-space"></div>
            <p><strong>( Admin / Mandor )</strong></p>
        </div>
        <div class="signature-box">
            <p>Diperiksa Oleh,</p>
            <div class="signature-space"></div>
            <p><strong>( Manager Farm )</strong></p>
        </div>
        <div class="signature-box">
            <p>Disetujui Oleh,</p>
            <div class="signature-space"></div>
            <p><strong>( Owner )</strong></p>
        </div>
    </div>

</body>
</html>