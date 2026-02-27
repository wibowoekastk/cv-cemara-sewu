<?php

namespace App\Http\Controllers;

use App\Models\Pakan;
use App\Models\PakanMutation; 
use App\Models\UnitPakanStock;
use App\Models\DailyRecord; 
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PakanController extends Controller
{
    // ================================================================
    // 1. DASHBOARD PAKAN (ADMIN)
    // ================================================================
    public function dashboard()
    {
        $today = Carbon::today();

        // Statistik Utama
        $pemakaianHarian = DailyRecord::whereDate('tanggal', $today)->sum('pakan_kg');
        $stokGudangPusat = Pakan::sum('stok_pusat');
        $stokDiUnit = UnitPakanStock::sum('jumlah_stok');
        $totalStokAvailable = $stokGudangPusat + $stokDiUnit;

        // Estimasi Hari
        $avgDailyUsage = DailyRecord::where('tanggal', '>=', Carbon::now()->subDays(7))
                                    ->groupBy('tanggal')
                                    ->selectRaw('sum(pakan_kg) as total_per_hari')
                                    ->get()
                                    ->avg('total_per_hari');
        
        $estimasiHari = ($avgDailyUsage > 0) ? round($totalStokAvailable / $avgDailyUsage) : 0;

        // Pakan Terbaik (FCR Terendah)
        $bestPakan = DB::table('daily_records')
                        ->join('pakans', 'daily_records.pakan_id', '=', 'pakans.id')
                        ->select('pakans.nama_pakan', DB::raw('AVG(daily_records.fcr) as avg_fcr'))
                        ->whereMonth('daily_records.tanggal', Carbon::now()->month)
                        ->where('daily_records.fcr', '>', 0)
                        ->groupBy('daily_records.pakan_id', 'pakans.nama_pakan')
                        ->orderBy('avg_fcr', 'asc')
                        ->first();

        // Grafik 7 Hari
        $chartData = DailyRecord::select('tanggal')
                        ->selectRaw('SUM(pakan_kg) as total_pakan')
                        ->selectRaw('AVG(hdp) as avg_hdp')
                        ->where('tanggal', '>=', Carbon::now()->subDays(7))
                        ->groupBy('tanggal')
                        ->orderBy('tanggal', 'asc')
                        ->get();

        $chartLabels = $chartData->map(fn($d) => Carbon::parse($d->tanggal)->format('d M'));
        $chartPakan = $chartData->pluck('total_pakan');
        $chartHDP = $chartData->pluck('avg_hdp');

        $topStok = Pakan::orderByDesc('stok_pusat')->take(5)->get();

        return view('admin.pakan.dashboard', compact(
            'pemakaianHarian', 'stokGudangPusat', 'stokDiUnit', 'estimasiHari', 'bestPakan',
            'chartLabels', 'chartPakan', 'chartHDP', 'topStok'
        ));
    }

    // ================================================================
    // 2. INPUT DATA ADMIN (MASTER & STOK PUSAT)
    // ================================================================
    
    public function input()
    {
        $pakans = Pakan::orderBy('nama_pakan')->get();
        // Alias stok_gudang untuk view lama jika ada
        $pakans->each(function($p) { $p->stok_gudang = $p->stok_pusat; });
        
        $units = Unit::all();

        return view('admin.pakan.input', compact('pakans', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pakan' => 'required|string|max:255',
            'jenis_pakan' => 'required|string',
            'satuan' => 'required|string',
            'min_stok' => 'required|integer',
        ]);

        $kategori = $request->kategori_bahan ?? $request->jenis_pakan;

        Pakan::create([
            'nama_pakan' => $request->nama_pakan,
            'jenis_pakan' => $kategori,
            'satuan' => $request->satuan,
            'min_stok' => $request->min_stok,
            'stok_pusat' => 0, 
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Master pakan berhasil ditambahkan.');
    }

    // Tambah Stok Pusat (Pembelian)
    public function updateStok(Request $request, $id)
    {
        $request->validate(['tambah_stok' => 'required|numeric|min:0.1', 'tgl_masuk' => 'required|date']);
        $pakan = Pakan::findOrFail($id);
        
        $detailSak = ($request->jumlah_karung ?? 0) . ' Sak @ ' . ($request->berat_per_karung ?? 0) . ' Kg';
        $supplier = $request->supplier ?? '-';

        DB::transaction(function () use ($request, $pakan, $detailSak, $supplier) {
            $pakan->increment('stok_pusat', $request->tambah_stok);
            PakanMutation::create([
                'pakan_id' => $pakan->id, 'user_id' => Auth::id(), 'tanggal' => $request->tgl_masuk,
                'jenis_mutasi' => 'masuk_pusat', 'jumlah' => $request->tambah_stok, 'status' => 'selesai',
                'keterangan' => "Supplier: $supplier. Detail: $detailSak"
            ]);
        });
        return redirect()->back()->with('success', 'Stok berhasil ditambahkan ke Gudang Pusat.');
    }

    // Produksi Sendiri (Mixing)
    public function storeProduksi(Request $request)
    {
        $request->validate(['nama_pakan_jadi' => 'required|string', 'total_berat_produksi' => 'required|numeric|min:1', 'tgl_produksi' => 'required|date']);
        
        DB::transaction(function () use ($request) {
            $pakanJadi = Pakan::firstOrCreate(
                ['nama_pakan' => $request->nama_pakan_jadi],
                ['jenis_pakan' => 'Pakan Jadi', 'satuan' => 'kg', 'min_stok' => 100, 'stok_pusat' => 0, 'deskripsi' => 'Hasil Produksi Sendiri']
            );
            $pakanJadi->increment('stok_pusat', $request->total_berat_produksi);
            
            $detail = 'Produksi Internal. Batch: ' . ($request->kode_batch ?? '-') . '. ' . ($request->jumlah_karung ? 'Jml: ' . $request->jumlah_karung . ' Sak' : '');
            
            PakanMutation::create([
                'pakan_id' => $pakanJadi->id, 'user_id' => Auth::id(), 'tanggal' => $request->tgl_produksi,
                'jenis_mutasi' => 'produksi', 'jumlah' => $request->total_berat_produksi, 'status' => 'selesai', 'keterangan' => $detail
            ]);
        });
        return redirect()->back()->with('success', 'Hasil produksi berhasil dicatat.');
    }

    // [BARU] Opname Koreksi Stok Pusat (Oleh Admin)
    public function opnamePusat(Request $request)
    {
        $request->validate([
            'pakan_id' => 'required|exists:pakans,id',
            'stok_fisik' => 'required|numeric|min:0',
            'keterangan' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $pakan = Pakan::findOrFail($request->pakan_id);
            
            $stokSistem = $pakan->stok_pusat; 
            $stokFisik = $request->stok_fisik;
            
            // Hitung selisih
            $selisih = $stokFisik - $stokSistem;

            if ($selisih != 0) {
                // Update master pakan
                $pakan->update(['stok_pusat' => $stokFisik]);

                // Simpan ke mutasi agar terbaca di riwayat
                PakanMutation::create([
                    'pakan_id' => $pakan->id,
                    'user_id' => Auth::id(),
                    'tanggal' => now(),
                    'jenis_mutasi' => 'opname_pusat',
                    'jumlah' => $selisih, // Min atau Plus
                    'status' => 'selesai',
                    'keterangan' => 'Opname Pusat: ' . $request->keterangan
                ]);
            }
        });

        return back()->with('success', 'Koreksi stok Gudang Pusat berhasil disimpan!');
    }

    // Distribusi ke Unit
    public function storeDistribution(Request $request)
    {
        $request->validate([
            'pakan_id' => 'required|exists:pakans,id', 
            'unit_id' => 'required|exists:units,id', 
            'jumlah' => 'required|numeric|min:0.1', 
            'tanggal' => 'required|date'
        ]);
        
        $pakan = Pakan::findOrFail($request->pakan_id);
        if ($pakan->stok_pusat < $request->jumlah) {
            return back()->with('error', 'Stok Pusat kurang! Sisa: ' . number_format($pakan->stok_pusat) . ' Kg');
        }

        DB::transaction(function () use ($request, $pakan) {
            $pakan->decrement('stok_pusat', $request->jumlah);
            PakanMutation::create([
                'pakan_id' => $request->pakan_id, 'user_id' => Auth::id(), 'ke_unit_id' => $request->unit_id,
                'tanggal' => $request->tanggal, 'jenis_mutasi' => 'distribusi', 'jumlah' => $request->jumlah,
                'status' => 'pending_terima', 'keterangan' => $request->keterangan ?? 'Pengiriman Admin ke Unit'
            ]);
        });
        return back()->with('success', 'Pakan dikirim ke Unit. Stok Pusat berkurang.');
    }

    // ================================================================
    // 3. LOGIKA MANDOR (TERIMA, PAKAI, & OPNAME DI UNIT)
    // ================================================================

    public function inputMandor()
    {
        $user = Auth::user();
        
        // Validasi: Mandor harus punya unit
        if (!$user->unit_id) {
            return redirect()->route('mandor.dashboard')->with('error', 'Akun Anda belum terhubung dengan Unit Farm.');
        }

        $unitId = $user->unit_id;
        
        // 1. Data Master Pakan
        $pakans = Pakan::orderBy('nama_pakan')->get();

        // 2. Kiriman Pending (Untuk Tab Terima)
        $pendingMutations = PakanMutation::with(['pakan', 'dariUnit'])
            ->where('ke_unit_id', $unitId)
            ->where('jenis_mutasi', 'distribusi')
            ->where('status', 'pending_terima')
            ->orderBy('tanggal', 'desc')
            ->get();

        // 3. Stok Saat Ini (Untuk Tab Opname & Pakai)
        $stokSaatIni = UnitPakanStock::with('pakan')
            ->where('unit_id', $unitId)
            ->where('jumlah_stok', '>', 0) // Filter yang ada stok saja
            ->get();

        // 4. Riwayat
        $riwayatTerima = PakanMutation::with('pakan')
            ->where('ke_unit_id', $unitId)
            ->whereIn('jenis_mutasi', ['terima_unit', 'distribusi'])
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $riwayatPakai = PakanMutation::with(['pakan', 'kandang'])
            ->where('dari_unit_id', $unitId)
            ->whereIn('jenis_mutasi', ['pemakaian', 'pakan_rusak']) // Tampilkan pakan rusak juga
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $riwayatOpname = PakanMutation::with('pakan')
            ->where('dari_unit_id', $unitId)
            ->whereIn('jenis_mutasi', ['penyesuaian', 'opname_unit']) // Tangkap history opname
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $tanggalWajibIsi = Carbon::yesterday()->format('Y-m-d');
        $statusInput = 'aman'; 

        return view('mandor.pakan.input', compact(
            'pakans', 
            'pendingMutations', 
            'stokSaatIni', 
            'riwayatTerima', 
            'riwayatPakai', 
            'riwayatOpname', 
            'tanggalWajibIsi', 
            'statusInput'
        ));
    }

    public function terimaStokMandorById($id)
    {
        $mutasi = PakanMutation::where('id', $id)->where('status', 'pending_terima')->firstOrFail();
        DB::transaction(function () use ($mutasi) {
            $mutasi->update(['status' => 'selesai', 'diterima_oleh' => Auth::id(), 'updated_at' => now()]);
            $stokUnit = UnitPakanStock::firstOrCreate(
                ['unit_id' => $mutasi->ke_unit_id, 'pakan_id' => $mutasi->pakan_id], 
                ['jumlah_stok' => 0]
            );
            $stokUnit->increment('jumlah_stok', $mutasi->jumlah);
        });
        return back()->with('success', 'Stok pakan berhasil diterima.');
    }

    // [UPDATE] Penyesuaian Label Opname Unit
    public function stockOpnameMandor(Request $request)
    {
        $request->validate(['pakan_id' => 'required', 'stok_fisik' => 'required|numeric|min:0', 'keterangan' => 'required']);
        $user = Auth::user();
        
        $stokSystem = UnitPakanStock::firstOrCreate(
            ['unit_id' => $user->unit_id, 'pakan_id' => $request->pakan_id], 
            ['jumlah_stok' => 0]
        );
        
        $selisih = $request->stok_fisik - $stokSystem->jumlah_stok;

        if ($selisih == 0) return back()->with('warning', 'Jumlah fisik sama dengan sistem. Tidak ada perubahan.');

        DB::transaction(function () use ($request, $user, $selisih, $stokSystem) {
            $stokSystem->update(['jumlah_stok' => $request->stok_fisik]);
            PakanMutation::create([
                'pakan_id' => $request->pakan_id, 'user_id' => $user->id, 'dari_unit_id' => $user->unit_id,
                'tanggal' => now(), 
                'jenis_mutasi' => 'opname_unit', // [UPDATE] Ubah dari 'penyesuaian' jadi 'opname_unit' agar standar
                'jumlah' => $selisih, 
                'status' => 'selesai', 'keterangan' => "Opname: " . $request->keterangan
            ]);
        });
        return back()->with('success', 'Stok opname berhasil disimpan.');
    }

    public function pakaiStokMandor(Request $request)
    {
        return $this->storeUsage($request); 
    }

    // [UPDATE] Penyesuaian Pemakaian Biasa vs Pakan Rusak
    public function storeUsage(Request $request) {
        $request->validate(['pakan_id' => 'required', 'unit_id' => 'nullable', 'kandang_id' => 'nullable', 'jumlah' => 'required|numeric|min:0.1', 'tanggal' => 'required|date']);
        $user = Auth::user();
        $unitId = $request->unit_id ?? $user->unit_id; 
        
        $stokUnit = UnitPakanStock::where('unit_id', $unitId)->where('pakan_id', $request->pakan_id)->first();
        if (!$stokUnit || $stokUnit->jumlah_stok < $request->jumlah) return back()->with('error', 'Stok Unit Kurang! Sisa: ' . ($stokUnit->jumlah_stok ?? 0));

        DB::transaction(function () use ($request, $user, $unitId, $stokUnit) {
            $stokUnit->decrement('jumlah_stok', $request->jumlah);
            
            // Deteksi flag pakan rusak dari view
            $isDamaged = $request->has('is_damaged');
            $jenisMutasi = $isDamaged ? 'pakan_rusak' : 'pemakaian';
            $keterangan = $isDamaged ? $request->keterangan : ($request->keterangan ?? 'Pemakaian Manual');

            PakanMutation::create([
                'pakan_id' => $request->pakan_id, 'user_id' => $user->id, 'dari_unit_id' => $unitId,
                'kandang_id' => $request->kandang_id, 'tanggal' => $request->tanggal, 'jenis_mutasi' => $jenisMutasi,
                'jumlah' => $request->jumlah, 'status' => 'selesai', 'keterangan' => $keterangan
            ]);
        });
        
        $msg = $request->has('is_damaged') ? 'Laporan pakan rusak berhasil dicatat.' : 'Pemakaian tercatat.';
        return back()->with('success', $msg);
    }

    // ================================================================
    // 4. ADMIN & OWNER DATA VIEWERS
    // ================================================================
    
    public function data(Request $request)
    {
        $query = Pakan::query(); 
        if ($request->filled('search')) {
            $query->where('nama_pakan', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_pakan', $request->jenis);
        }
        $pakans = $query->orderBy('nama_pakan')->paginate(10);
        $pakans->getCollection()->transform(function ($item) { $item->stok_gudang = $item->stok_pusat; return $item; });
        return view('admin.pakan.data_input', compact('pakans'));
    }

    public function update(Request $request, $id) {
        $pakan = Pakan::findOrFail($id); $pakan->update($request->all()); return back()->with('success', 'Data diperbarui.');
    }

    public function destroy($id) {
        Pakan::findOrFail($id)->delete(); return back()->with('success', 'Data dihapus.');
    }

    public function monitoring(Request $request) {
        $units = Unit::with(['pakanStocks.pakan', 'kandangs'])->get();
        if ($request->filled('unit_id')) $units = $units->where('id', $request->unit_id);
        $pusatPakans = Pakan::orderBy('nama_pakan')->get();
        
        // [BARU] Filter cerdas ditarik ke Controller agar lebih rapi
        // Filter ini akan menangkap data mutasi lama (penyesuaian) dan baru (opname)
        $riwayatOpnameMonitoring = PakanMutation::with(['pakan', 'user', 'dariUnit'])
            ->where(function ($q) {
                $q->whereIn('jenis_mutasi', ['opname', 'opname_pusat', 'opname_unit', 'stock_opname', 'pakan_rusak', 'penyesuaian', 'koreksi'])
                  ->orWhere('keterangan', 'LIKE', '%Opname%')
                  ->orWhere('keterangan', 'LIKE', '%Rusak%')
                  ->orWhere('keterangan', 'LIKE', '%ilang%')
                  ->orWhere('keterangan', 'LIKE', '%Selisih%');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.pakan.monitoring', compact('units', 'pusatPakans', 'riwayatOpnameMonitoring'));
    }

    public function riwayat(Request $request)
    {
        $pakans = Pakan::all(); $units = Unit::with('kandangs')->get(); 
        $query = PakanMutation::with(['pakan', 'kandang.unit', 'user', 'unitTujuan', 'unitAsal']);
        if ($request->filled('start_date') && $request->filled('end_date')) $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);

        $riwayatPakai = (clone $query)->whereIn('jenis_mutasi', ['pemakaian', 'penyesuaian'])->latest('tanggal')->paginate(10, ['*'], 'pakai_page');
        $riwayatPakai->getCollection()->transform(function ($item) { $item->tgl_pakan = $item->tanggal; $item->jumlah_pakai = $item->jumlah; return $item; });

        $riwayatDistribusi = (clone $query)->whereIn('jenis_mutasi', ['distribusi', 'terima_unit'])->latest('tanggal')->paginate(10, ['*'], 'dist_page');
        
        $riwayatMasuk = (clone $query)->whereIn('jenis_mutasi', ['masuk_pusat', 'produksi'])->latest('tanggal')->paginate(10, ['*'], 'masuk_page');
        $riwayatMasuk->getCollection()->transform(function ($item) { $item->tgl_masuk = $item->tanggal; $item->jumlah_masuk = $item->jumlah; if ($item->jenis_mutasi == 'produksi') $item->supplier = 'Produksi Sendiri'; return $item; });

        return view('admin.pakan.riwayat_input', compact('riwayatPakai', 'riwayatMasuk', 'riwayatDistribusi', 'pakans', 'units'));
    }

    public function destroyUsage($id) {
        $m = PakanMutation::findOrFail($id);
        if($m->jenis_mutasi == 'distribusi') { $pakan = Pakan::find($m->pakan_id); if($pakan) $pakan->increment('stok_pusat', $m->jumlah); }
        elseif($m->jenis_mutasi == 'pemakaian' && $m->dari_unit_id) { $stokUnit = UnitPakanStock::where('unit_id', $m->dari_unit_id)->where('pakan_id', $m->pakan_id)->first(); if($stokUnit) $stokUnit->increment('jumlah_stok', $m->jumlah); }
        $m->delete(); return back()->with('success', 'Riwayat dihapus.');
    }
    
    public function destroyRestock($id) {
        $m = PakanMutation::findOrFail($id);
        if($m->jenis_mutasi == 'masuk_pusat') { $pakan = Pakan::find($m->pakan_id); if($pakan) $pakan->decrement('stok_pusat', $m->jumlah); }
        $m->delete(); return back()->with('success', 'Riwayat masuk dihapus.');
    }

    public function cetakSuratJalan($id) {
        $mutasi = PakanMutation::with(['pakan', 'unitTujuan', 'user'])->findOrFail($id);
        return view('admin.pakan.suratjalan', compact('mutasi'));
    }

    // ================================================================
    // 5. LAPORAN ADMIN
    // ================================================================
    public function laporan(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $pakans = Pakan::orderBy('nama_pakan')->get();

        // Hitung Rekap Stok
        $rekapStok = $pakans->map(function($pakan) use ($startDate, $endDate) {
            $stokAwalPusat = PakanMutation::where('pakan_id', $pakan->id)->where('tanggal', '<', $startDate)->whereIn('jenis_mutasi', ['masuk_pusat', 'produksi', 'distribusi'])->sum(DB::raw("CASE WHEN jenis_mutasi IN ('masuk_pusat', 'produksi') THEN jumlah WHEN jenis_mutasi = 'distribusi' THEN -jumlah ELSE 0 END"));
            $masukPusat = PakanMutation::where('pakan_id', $pakan->id)->whereBetween('tanggal', [$startDate, $endDate])->whereIn('jenis_mutasi', ['masuk_pusat', 'produksi'])->sum('jumlah');
            $keluarPusat = PakanMutation::where('pakan_id', $pakan->id)->whereBetween('tanggal', [$startDate, $endDate])->where('jenis_mutasi', 'distribusi')->sum('jumlah');
            $stokAkhirPusat = $stokAwalPusat + $masukPusat - $keluarPusat;
            $pemakaianKandang = PakanMutation::where('pakan_id', $pakan->id)->whereBetween('tanggal', [$startDate, $endDate])->where('jenis_mutasi', 'pemakaian')->sum('jumlah');
            return [ 'nama_pakan' => $pakan->nama_pakan, 'satuan' => $pakan->satuan, 'stok_awal_pusat' => $stokAwalPusat, 'masuk_pusat' => $masukPusat, 'keluar_pusat' => $keluarPusat, 'stok_akhir_pusat' => $stokAkhirPusat, 'total_pemakaian' => $pemakaianKandang ];
        });

        // Ambil Data Detail
        $detailMutasi = PakanMutation::with(['pakan', 'unitTujuan', 'dariUnit', 'kandang'])->whereBetween('tanggal', [$startDate, $endDate])->orderBy('tanggal', 'desc')->get();
        $dataMasuk = $detailMutasi->whereIn('jenis_mutasi', ['masuk_pusat', 'produksi']);
        $dataKeluar = $detailMutasi->where('jenis_mutasi', 'pemakaian');
        $dataDistribusi = $detailMutasi->where('jenis_mutasi', 'distribusi');

        // Logic Cetak PDF ADMIN
        if ($request->has('print') || $request->has('download_pdf')) {
             return view('admin.pakan.cetak_pdf', compact('rekapStok', 'dataMasuk', 'dataKeluar', 'dataDistribusi', 'startDate', 'endDate'));
        }

        return view('admin.pakan.laporan_pdf', compact('rekapStok', 'detailMutasi', 'dataMasuk', 'dataKeluar', 'dataDistribusi', 'startDate', 'endDate'));
    }

    // ================================================================
    // 6. KHUSUS OWNER (DATA & LAPORAN)
    // ================================================================

    public function ownerData(Request $request) {
        $lokasis = Unit::select('lokasi')->distinct()->pluck('lokasi');
        $units = Unit::all();
        
        // [UPDATE] Owner juga bisa melihat riwayat opname dan pakan rusak
        $query = PakanMutation::with(['pakan', 'unitTujuan', 'dariUnit', 'kandang'])
            ->whereIn('jenis_mutasi', ['masuk_pusat', 'produksi', 'terima_unit', 'distribusi', 'pemakaian', 'opname_pusat', 'opname_unit', 'pakan_rusak', 'penyesuaian']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('unit_id') && $request->unit_id != 'all') {
            $query->where(function($q) use ($request) {
                $q->where('ke_unit_id', $request->unit_id)->orWhere('dari_unit_id', $request->unit_id);
            });
        } elseif ($request->filled('lokasi') && $request->lokasi != 'all') {
            $unitIds = Unit::where('lokasi', $request->lokasi)->pluck('id');
            $query->whereIn('ke_unit_id', $unitIds)->orWhereIn('dari_unit_id', $unitIds);
        }
        
        $data = $query->latest('tanggal')->paginate(10);
        
        $data->getCollection()->transform(function ($item) {
            $item->jml_karung = '-'; $item->berat_sak = '-';
            if (preg_match('/(\d+)\s*Sak/', $item->keterangan, $matches)) { $item->jml_karung = $matches[1] . ' Sak'; }
            if (preg_match('/@\s*(\d+)\s*Kg/', $item->keterangan, $matches)) { $item->berat_sak = $matches[1] . ' Kg'; }
            return $item;
        });

        return view('owner.pakan.data_input', compact('data', 'units', 'lokasis'));
    }

    public function ownerLaporan(Request $request) 
    { 
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        
        $pakans = Pakan::orderBy('nama_pakan')->get();
        $units = Unit::all();
        $lokasis = Unit::select('lokasi')->distinct()->pluck('lokasi');

        $rekapStok = $pakans->map(function($pakan) use ($startDate, $endDate) {
            $stokAwalPusat = PakanMutation::where('pakan_id', $pakan->id)->where('tanggal', '<', $startDate)->whereIn('jenis_mutasi', ['masuk_pusat', 'produksi', 'distribusi'])->sum(DB::raw("CASE WHEN jenis_mutasi IN ('masuk_pusat', 'produksi') THEN jumlah WHEN jenis_mutasi = 'distribusi' THEN -jumlah ELSE 0 END"));
            $masukPusat = PakanMutation::where('pakan_id', $pakan->id)->whereBetween('tanggal', [$startDate, $endDate])->whereIn('jenis_mutasi', ['masuk_pusat', 'produksi'])->sum('jumlah');
            $keluarPusat = PakanMutation::where('pakan_id', $pakan->id)->whereBetween('tanggal', [$startDate, $endDate])->where('jenis_mutasi', 'distribusi')->sum('jumlah');
            $stokAkhirPusat = $stokAwalPusat + $masukPusat - $keluarPusat;
            $pemakaianKandang = PakanMutation::where('pakan_id', $pakan->id)->whereBetween('tanggal', [$startDate, $endDate])->where('jenis_mutasi', 'pemakaian')->sum('jumlah');
            return [ 'nama_pakan' => $pakan->nama_pakan, 'satuan' => $pakan->satuan, 'stok_awal_pusat' => $stokAwalPusat, 'masuk_pusat' => $masukPusat, 'keluar_pusat' => $keluarPusat, 'stok_akhir_pusat' => $stokAkhirPusat, 'total_pemakaian' => $pemakaianKandang ];
        });

        $query = PakanMutation::with(['pakan', 'unitTujuan', 'dariUnit', 'kandang'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc');

        if ($request->pakan_id && $request->pakan_id != 'all') { $query->where('pakan_id', $request->pakan_id); }
        if ($request->unit_id && $request->unit_id != 'all') {
            $query->where(function($q) use ($request) {
                $q->where('ke_unit_id', $request->unit_id)->orWhere('dari_unit_id', $request->unit_id);
            });
        }

        $detailMutasi = $query->get();

        $dataMasuk = $detailMutasi->whereIn('jenis_mutasi', ['masuk_pusat', 'produksi']);
        $dataKeluar = $detailMutasi->where('jenis_mutasi', 'pemakaian');
        $dataDistribusi = $detailMutasi->where('jenis_mutasi', 'distribusi');

        if ($request->has('download_pdf')) {
            return view('admin.pakan.cetak_pdf', compact('rekapStok', 'dataMasuk', 'dataKeluar', 'dataDistribusi', 'startDate', 'endDate'));
        }

        return view('owner.pakan.laporan_pdf', compact(
            'rekapStok', 'detailMutasi', 'dataMasuk', 'dataKeluar', 'dataDistribusi', 'startDate', 'endDate', 
            'pakans', 'units', 'lokasis' 
        ));
    }
}