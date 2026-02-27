<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Obat - CV Cemara Sewu</title>
    <style>
        body { font-family: sans-poppins; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { padding: 2px 5px; border-radius: 4px; font-size: 10px; background: #eee; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>LAPORAN {{ $tipeLaporan == 'stok' ? 'STOK MASUK' : 'PEMAKAIAN OBAT' }}</h1>
        <p>CV. Cemara Sewu - Peternakan Ayam</p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-center">Jumlah</th>
                <th>Satuan</th>
                @if($tipeLaporan == 'stok')
                    <th>No. Batch</th>
                    <th>Expired Date</th>
                @else
                    <th>Petugas</th>
                    <th>Keterangan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($tipeLaporan == 'stok' ? $item->tgl_masuk : $item->tgl_pakai)->format('d/m/Y') }}</td>
                    <td>
                        {{ $tipeLaporan == 'stok' ? $item->obat->nama_obat : $item->batch->obat->nama_obat }}
                    </td>
                    <td>
                        {{ $tipeLaporan == 'stok' ? $item->obat->jenis_obat : $item->batch->obat->jenis_obat }}
                    </td>
                    <td class="text-center">
                        {{ $tipeLaporan == 'stok' ? $item->stok_awal : $item->jumlah_pakai }}
                    </td>
                    <td>
                        {{ $tipeLaporan == 'stok' ? $item->obat->satuan : $item->batch->obat->satuan }}
                    </td>

                    @if($tipeLaporan == 'stok')
                        <td>{{ $item->kode_batch }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_kadaluarsa)->format('d M Y') }}</td>
                    @else
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->keterangan }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Dicetak pada: {{ date('d M Y H:i') }}</p>
        <br><br><br>
        <p>( __________________ )</p>
        <p>Admin Gudang</p>
    </div>

</body>
</html>