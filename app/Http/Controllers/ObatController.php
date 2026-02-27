<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\ObatUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ObatController extends Controller
{
    // 1. DASHBOARD
    public function dashboard()
    {
        // --- A. Statistik Card Atas ---
        $semuaObat = Obat::all(); 
        
        $totalObat = $semuaObat->count();
        $stokKritis = $semuaObat->filter(fn($o) => $o->total_stok <= $o->min_stok)->count();
        
        $nearExpired = ObatBatch::where('status', 'active')
                        ->where('stok_saat_ini', '>', 0)
                        ->whereDate('tgl_kadaluarsa', '<=', Carbon::now()->addDays(30))
                        ->count();

        // Top 5 Stok
        $topStok = $semuaObat->sortByDesc('total_stok')->take(5);

        // --- B. Data Grafik Line ---
        $chartLabels = [];
        $chartUsageData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $formattedDate = $date->format('Y-m-d');
            
            $chartLabels[] = $date->translatedFormat('d M'); 
            $sumUsage = ObatUsage::whereDate('tgl_pakai', $formattedDate)->sum('jumlah_pakai');
            $chartUsageData[] = $sumUsage;
        }

        // --- C. Data Grafik Pie ---
        $kategoriList = ['Vitamin', 'Antibiotik', 'Vaksin', 'Disinfektan'];
        $chartPieData = [];

        foreach ($kategoriList as $kategori) {
            $totalStokPerKategori = ObatBatch::whereHas('obat', function($q) use ($kategori) {
                $q->where('jenis_obat', $kategori);
            })
            ->where('status', 'active') 
            ->sum('stok_saat_ini');     
            
            $chartPieData[] = (int) $totalStokPerKategori; 
        }

        return view('admin.obat.dashboard', compact(
            'totalObat', 'stokKritis', 'nearExpired', 'topStok',
            'chartLabels', 'chartUsageData', 'chartPieData', 'kategoriList'
        ));
    }

    // 2. INPUT
    public function input()
    {
        $obats = Obat::orderBy('nama_obat')->get();
        $recents = ObatBatch::with('obat')
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->get();
        return view('admin.obat.input', compact('obats', 'recents'));
    }

    // 3. DATA OBAT (ADMIN)
    public function data(Request $request)
    {
        $query = Obat::with(['batches' => function($q) {
            $q->where('stok_saat_ini', '>', 0)->orderBy('tgl_kadaluarsa', 'asc');
        }]);

        if($request->has('search')) {
            $query->where('nama_obat', 'like', '%'.$request->search.'%');
        }

        $obats = $query->paginate(10);
        return view('admin.obat.data_input', compact('obats'));
    }

    public function riwayat()
    {
        $obats = Obat::orderBy('nama_obat')->get();
        $units = \App\Models\Unit::with('kandangs')->get(); 

        $riwayatPakai = ObatUsage::with(['batch.obat', 'user'])->latest()->paginate(10);
        $riwayatMasuk = ObatBatch::with('obat')->latest()->paginate(10);

        return view('admin.obat.riwayat_input', compact('riwayatPakai', 'riwayatMasuk', 'obats', 'units'));
    }

    // --- LOGIC PENYIMPANAN ---
    public function store(Request $request) 
    {
        $request->validate([
            'nama_obat' => 'required', 
            'jenis_obat' => 'required', // Update validasi
            'satuan' => 'required'
        ]);
        Obat::create($request->all());
        return redirect()->back()->with('success', 'Master obat baru berhasil dibuat.');
    }

    public function storeStok(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string', 
            'stok_awal' => 'required|integer|min:1',
            'tgl_masuk' => 'required|date',
            'tgl_kadaluarsa' => 'required|date|after:tgl_masuk',
        ]);

        $obat = \App\Models\Obat::firstOrCreate(
            ['nama_obat' => $request->nama_obat],
            [
                'jenis_obat' => $request->jenis_obat,
                'satuan' => $request->satuan,
                'min_stok' => 5 
            ]
        );

        \App\Models\ObatBatch::create([
            'obat_id' => $obat->id,
            'kode_batch' => $request->kode_batch ?? 'BATCH-' . date('ymdHis'),
            'tgl_masuk' => $request->tgl_masuk,
            'tgl_kadaluarsa' => $request->tgl_kadaluarsa,
            'stok_awal' => $request->stok_awal,
            'stok_saat_ini' => $request->stok_awal,
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', 'Stok obat berhasil ditambahkan.');
    }

    public function storeUsage(Request $request)
    {
        $request->validate(['obat_id' => 'required', 'jumlah_pakai' => 'required']);
        $obatId = $request->obat_id;
        $jumlahMinta = $request->jumlah_pakai;
        
        $obat = Obat::find($obatId);
        if ($obat->total_stok < $jumlahMinta) return back()->with('error', 'Stok kurang!');

        $batches = ObatBatch::where('obat_id', $obatId)->where('stok_saat_ini', '>', 0)->where('status', 'active')->orderBy('tgl_kadaluarsa', 'asc')->get();

        DB::transaction(function () use ($batches, $jumlahMinta, $request) {
            $sisa = $jumlahMinta;
            foreach ($batches as $batch) {
                if ($sisa <= 0) break;
                $ambil = min($batch->stok_saat_ini, $sisa);
                $batch->stok_saat_ini -= $ambil;
                $sisa -= $ambil;
                if($batch->stok_saat_ini == 0) $batch->status = 'empty';
                $batch->save();

                ObatUsage::create([
                    'user_id' => Auth::id(),
                    'obat_batch_id' => $batch->id,
                    'jumlah_pakai' => $ambil,
                    'tgl_pakai' => $request->tgl_pakai ?? now(),
                    'keterangan' => $request->keterangan
                ]);
            }
        });

        return redirect()->back()->with('success', 'Pemakaian tercatat (FEFO).');
    }

    public function update(Request $request, $id) {
        $obat = Obat::findOrFail($id);
        $obat->update($request->all());
        return redirect()->back()->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy($id) {
        Obat::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data obat dihapus.');
    }
    
    // --- METODE LAPORAN & CETAK PDF ---
    public function laporan(Request $request)
    {
        $tipeLaporan = $request->input('report_type', 'stok'); 
        $startDate   = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate     = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $kategori    = $request->input('kategori', 'semua');

        $data = collect(); 

        if ($tipeLaporan == 'pakai') {
            $query = ObatUsage::with(['batch.obat', 'user'])
                        ->whereBetween('tgl_pakai', [$startDate, $endDate]);

            if ($kategori !== 'semua') {
                $query->whereHas('batch.obat', function($q) use ($kategori) {
                    $q->where('jenis_obat', $kategori); 
                });
            }
            $data = $query->orderBy('tgl_pakai', 'desc')->get();

        } else {
            $query = ObatBatch::with('obat');
            $query->whereBetween('tgl_masuk', [$startDate, $endDate]);

            if ($kategori !== 'semua') {
                $query->whereHas('obat', function($q) use ($kategori) {
                    $q->where('jenis_obat', $kategori);
                });
            }
            $data = $query->orderBy('tgl_masuk', 'desc')->get();
        }

        if ($request->has('print') && $request->print == 'true') {
            return view('admin.obat.cetak_pdf', compact('tipeLaporan', 'kategori', 'data', 'startDate', 'endDate'));
        }

        return view('admin.obat.laporan_pdf', compact('tipeLaporan', 'kategori', 'data', 'startDate', 'endDate'));
    }
  
    public function index(Request $request)
    {
        $query = Obat::query();
        if ($request->search) {
            $query->where('nama_obat', 'like', '%'.$request->search.'%');
        }
        $obats = $query->paginate(10);
        return view('admin.obat.data_input', compact('obats'));
    }

    // ================================================================
    // KHUSUS OWNER (READ ONLY)
    // ================================================================
    
    // 1. Data Stok Obat Owner
    public function ownerData(Request $request)
    {
        $query = Obat::query();

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->search . '%')
                  ->orWhere('jenis_obat', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Filter Kategori
        if ($request->filled('jenis_obat') && $request->jenis_obat != 'Semua Kategori') {
            $query->where('jenis_obat', $request->jenis_obat);
        }
        
        $data = $query->orderBy('nama_obat')->paginate(10);
        $kategoris = Obat::select('jenis_obat')->distinct()->pluck('jenis_obat'); 

        return view('owner.obat.data_input', compact('data', 'kategoris'));
    }

    // 2. Laporan PDF Obat Owner (FIXED DATE OBJECT)
    public function ownerLaporan(Request $request)
    {
        $tipeLaporan = $request->input('report_type', 'stok'); 
        
        // FIX: Pastikan ini menjadi Objek Carbon, bukan String
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        
        $kategori = $request->input('jenis_obat') ?? $request->input('kategori') ?? 'semua';

        $data = collect(); 

        if ($tipeLaporan == 'pakai') {
            $query = ObatUsage::with(['batch.obat', 'user'])
                        ->whereBetween('tgl_pakai', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($kategori !== 'semua' && $kategori !== 'Semua Kategori') {
                $query->whereHas('batch.obat', function($q) use ($kategori) {
                    $q->where('jenis_obat', $kategori); 
                });
            }
            $data = $query->orderBy('tgl_pakai', 'desc')->get();

        } else {
            // Laporan Stok Masuk
            $query = ObatBatch::with('obat');
            $query->whereBetween('tgl_masuk', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($kategori !== 'semua' && $kategori !== 'Semua Kategori') {
                $query->whereHas('obat', function($q) use ($kategori) {
                    $q->where('jenis_obat', $kategori);
                });
            }
            $data = $query->orderBy('tgl_masuk', 'desc')->get();
        }

        $kategoris = Obat::select('jenis_obat')->distinct()->pluck('jenis_obat');

        if ($request->has('download_pdf')) {
            // Ubah ke string untuk view cetak jika diperlukan, atau biarkan object
            // View cetak biasanya mengharapkan string di judul
            return view('admin.obat.cetak_pdf', compact('tipeLaporan', 'kategori', 'data', 'startDate', 'endDate'));
        }

        return view('owner.obat.laporan_pdf', compact('tipeLaporan', 'kategori', 'data', 'startDate', 'endDate', 'kategoris'));
    }
}