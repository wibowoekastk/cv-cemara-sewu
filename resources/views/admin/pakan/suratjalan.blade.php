<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - SJ/{{ $mutasi->id }}/{{ date('Y') }}</title>
    <style>
        body { font-family: Arial, sans-poppins; font-size: 14px; color: #333; line-height: 1.4; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .sub-logo { font-size: 12px; margin-top: 5px; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; padding: 5px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 10px; text-align: left; }
        .data-table th { background-color: #f0f0f0; text-align: center; }
        
        .signature { width: 100%; margin-top: 50px; text-align: center; }
        .signature td { width: 33%; padding-top: 60px; }
        
        @media print {
            @page { margin: 1cm; }
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            .container { border: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container">
        <div class="header">
            <div class="logo">CV. CEMARA SEWU</div>
            <div class="sub-logo">Farm Management & Production System</div>
            <div class="sub-logo">Jl. Raya Peternakan No. 12, Jawa Tengah</div>
        </div>

        <div class="title">SURAT JALAN PENGIRIMAN</div>

        <table class="info-table">
            <tr>
                <td width="15%"><strong>No. Dokumen</strong></td>
                <td width="2%">:</td>
                <td width="33%">SJ/PKN/{{ date('Y') }}/{{ str_pad($mutasi->id, 5, '0', STR_PAD_LEFT) }}</td>
                
                <td width="15%"><strong>Tanggal Kirim</strong></td>
                <td width="2%">:</td>
                <td width="33%">{{ \Carbon\Carbon::parse($mutasi->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Pengirim</strong></td>
                <td>:</td>
                <td>Gudang Pusat (Admin: {{ $mutasi->user->name ?? 'Admin' }})</td>
                
                <td><strong>Tujuan</strong></td>
                <td>:</td>
                <td>
                    <strong>{{ $mutasi->unitTujuan->nama_unit ?? 'Unit ???' }}</strong><br>
                    <small>Lokasi: {{ $mutasi->unitTujuan->lokasi ?? '-' }}</small>
                </td>
            </tr>
            <tr>
                <td><strong>No. Kendaraan</strong></td>
                <td>:</td>
                <td>__________________</td> <!-- Diisi manual saat print -->
                
                <td><strong>Status</strong></td>
                <td>:</td>
                <td>{{ strtoupper($mutasi->status == 'pending_terima' ? 'Dalam Pengiriman' : 'Diterima') }}</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 40%">Nama Barang / Pakan</th>
                    <th style="width: 20%">Kategori</th>
                    <!-- Judul kolom disesuaikan -->
                    <th style="width: 15%">Jumlah</th>
                    <th style="width: 20%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>
                        <strong>{{ $mutasi->pakan->nama_pakan }}</strong>
                    </td>
                    <td style="text-align: center;">{{ $mutasi->pakan->jenis_pakan }}</td>
                    
                    <!-- [UPDATE] Tampilan Jumlah ditambahkan Sak -->
                    <td style="text-align: center;">
                        <strong style="font-size: 14px;">{{ number_format($mutasi->jumlah, 1) }} Kg</strong><br>
                        <span style="font-size: 11px; color: #555;">({{ number_format($mutasi->jumlah / 40, 1) }} Sak)</span>
                    </td>
                    
                    <td>{{ $mutasi->keterangan ?? '-' }}</td>
                </tr>
                <!-- Jika sistem mendukung multi-item dalam satu kiriman, loop di sini. 
                     Saat ini sistem per 1 jenis pakan -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total Pengiriman</td>
                    <td style="text-align: center; background-color: #f9f9f9;">
                        <!-- [UPDATE] Tampilan Total ditambahkan Sak -->
                        <strong style="font-size: 14px;">{{ number_format($mutasi->jumlah, 1) }} Kg</strong><br>
                        <span style="font-size: 11px; color: #555;">({{ number_format($mutasi->jumlah / 40, 1) }} Sak)</span>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div style="font-size: 12px; margin-bottom: 20px;">
            <strong>Catatan:</strong><br>
            1. Harap barang dicek kembali saat penerimaan. Pastikan jumlah fisik (Sak) sesuai dengan surat jalan.<br>
            2. Surat jalan ini merupakan bukti resmi perpindahan stok antar gudang.
        </div>

        <table class="signature">
            <tr>
                <td>
                    <p>Dibuat Oleh,</p>
                    <br><br><br>
                    <strong>( {{ $mutasi->user->name ?? 'Admin Logistik' }} )</strong><br>
                    Gudang Pusat
                </td>
                <td>
                    <p>Supir / Pengirim,</p>
                    <br><br><br>
                    <strong>( ____________________ )</strong><br>
                    Ekspedisi
                </td>
                <td>
                    <p>Diterima Oleh,</p>
                    <br><br><br>
                    <strong>( Mandor Unit )</strong><br>
                    Gudang Unit
                </td>
            </tr>
        </table>
    </div>

</body>
</html>