<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - {{ ucfirst($tipe) }}</title>
    
    <!-- CDN Tailwind (Hanya untuk utility dasar, styling print utama pakai CSS native agar lebih stabil) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js untuk Grafik Timbang -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        @media print {
            @page { margin: 1cm; size: landscape; } /* Landscape agar tabel muat */
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: white; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        
        body { 
            font-family: 'Times New Roman', Times, poppins; 
            font-size: 12px; 
            color: black;
        }

        /* Styling Tabel Standar Dokumen */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 11px; }
        
        /* Helper Class */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header-laporan { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header-laporan h1 { font-size: 18px; margin: 0; }
        .header-laporan h2 { font-size: 14px; margin: 5px 0 0 0; }
        .meta-info { font-size: 10px; margin-top: 5px; }

        .signature { margin-top: 50px; float: right; width: 200px; text-align: center; }
        .signature p { margin-bottom: 60px; }
        .signature .line { border-bottom: 1px solid #000; display: block; margin-top: 10px; }
    </style>
</head>
<body>

    <!-- KOP LAPORAN -->
    <div class="header-laporan">
        <h1>FARM MANAJEMEN SYSTEM</h1>
        <h2>LAPORAN DATA {{ strtoupper($tipe) }}</h2>
        <div class="meta-info">
            Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB <br>
            Oleh: {{ Auth::user()->name ?? 'Administrator' }}
        </div>
    </div>

    <!-- LOGIKA TAMPILAN BERDASARKAN TIPE -->
    @if($tipe == 'timbang')
        
        <!-- === BAGIAN 1: TABEL DATA TIMBANG === -->
        <h3 style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">A. Tabel Rekapitulasi Berat Badan</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 12%">Tanggal</th>
                    <th style="width: 15%">Unit Farm</th>
                    <th style="width: 15%">Kandang</th>
                    <th style="width: 10%">Umur</th>
                    <th style="width: 15%">Berat Rata-rata</th>
                    <th style="width: 10%">Uniformity</th>
                    <th style="width: 18%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_timbang)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $item->kandang->unit->nama_unit ?? '-' }}</td>
                        <td class="text-center">{{ $item->kandang->nama_kandang ?? '-' }}</td>
                        <td class="text-center">{{ $item->umur_minggu }} Minggu</td>
                        <td class="text-center font-bold">{{ number_format($item->berat_rata) }} gr</td>
                        <td class="text-center">{{ $item->uniformity }}%</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data penimbangan untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- === BAGIAN 2: GRAFIK TIMBANG (Jika Data Ada) === -->
        @if(count($data) > 0)
            <div style="page-break-inside: avoid;">
                <h3 style="font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-top: 20px;">B. Grafik Perkembangan Berat Badan</h3>
                
                <!-- Container Grafik Fixed Height untuk Cetak -->
                <div style="width: 100%; height: 350px; border: 1px solid #ccc; padding: 10px;">
                    <canvas id="timbangChart"></canvas>
                </div>

                <script>
                    const chartData = {!! json_encode($data->sortBy('umur_minggu')->values()) !!};
                    
                    const labels = chartData.map(item => 'Minggu ' + item.umur_minggu);
                    const dataBerat = chartData.map(item => item.berat_rata);

                    const ctx = document.getElementById('timbangChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Berat Rata-rata (Gram)',
                                data: dataBerat,
                                borderColor: 'black',       // Hitam agar jelas saat print hitam putih
                                backgroundColor: 'rgba(0,0,0,0.1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'black',
                                pointRadius: 4,
                                tension: 0.1, // Garis lurus antar titik
                                fill: false
                            }]
                        },
                        options: {
                            animation: false, // Matikan animasi agar tercetak
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    title: { display: true, text: 'Berat (Gram)' }
                                },
                                x: {
                                    title: { display: true, text: 'Umur Ayam' }
                                }
                            },
                            plugins: {
                                legend: { display: true, position: 'bottom' }
                            }
                        }
                    });
                </script>
            </div>
        @endif

    @else
        <!-- === BAGIAN UTAMA: TABEL DATA KANDANG (POPULASI) === -->
        <h3 style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">Data Populasi Kandang</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 20%">Nama Unit</th>
                    <th style="width: 20%">Lokasi</th>
                    <th style="width: 15%">Kode Kandang</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 15%">Kapasitas</th>
                    <th style="width: 15%">Populasi Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->unit->nama_unit ?? '-' }}</td>
                        <td>{{ $item->unit->lokasi ?? '-' }}</td>
                        <td class="text-center font-bold">{{ $item->nama_kandang }}</td>
                        <td class="text-center uppercase">{{ $item->status }}</td>
                        <td class="text-right">{{ number_format($item->kapasitas) }}</td>
                        <td class="text-right font-bold">{{ number_format($item->stok_saat_ini) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 20px;">Data kandang tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($data) > 0)
            <tfoot>
                <tr style="background-color: #f3f4f6;">
                    <td colspan="5" class="text-right font-bold">TOTAL POPULASI FARM</td>
                    <td class="text-right font-bold">{{ number_format($data->sum('kapasitas')) }}</td>
                    <td class="text-right font-bold">{{ number_format($data->sum('stok_saat_ini')) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    @endif

    <!-- Tanda Tangan -->
    <div class="signature">
        <p>Mengetahui,</p>
        <span class="line">Owner / Manager</span>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            // Beri jeda sedikit agar Chart.js selesai render
            setTimeout(() => {
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>