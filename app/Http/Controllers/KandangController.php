<?php

namespace App\Http\Controllers;

use App\Models\Kandang;
use App\Models\Unit;
use App\Models\Timbangan; 
use App\Models\Siklus;
use App\Models\Batch; // [BARU] Import Model Batch
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KandangController extends Controller
{
    // ================================================================
    // 1. DASHBOARD
    // ================================================================
    public function dashboard()
    {
        // A. Statistik Card
        $totalUnit = Unit::count();
        $totalKandang = Kandang::count();
        $kandangAktif = Kandang::where('status', 'aktif')->count();
        
        // Populasi (Stok Ayam Saat Ini)
        $populasiEfektif = Kandang::sum('stok_saat_ini');
        
        // Kandang dengan Warning (Logika dummy: Kematian > 5%)
        $kandangWarning = Kandang::where('status', 'aktif')
                            ->whereRaw('(stok_awal - stok_saat_ini) / NULLIF(stok_awal, 0) > 0.05') 
                            ->count();

        // B. Data Grafik Line (Performa Harian/Mingguan)
        $grafikBobot = Timbangan::select('umur_minggu', DB::raw('AVG(berat_rata) as rata_berat'))
                        ->groupBy('umur_minggu')
                        ->orderBy('umur_minggu')
                        ->get();
        
        $chartLabels = $grafikBobot->pluck('umur_minggu')->map(fn($m) => "Minggu $m");
        $chartBobotData = $grafikBobot->pluck('rata_berat');

        // C. Data Tabel Preview (Top Kandang Aktif)
        $topKandangs = Kandang::with('unit')
                        ->where('status', 'aktif')
                        ->orderByDesc('stok_saat_ini')
                        ->take(5)
                        ->get();

        return view('admin.kandang.dashboard', compact(
            'totalUnit', 'totalKandang', 'kandangAktif', 'populasiEfektif', 
            'kandangWarning', 'chartLabels', 'chartBobotData', 'topKandangs'
        ));
    }

    // ================================================================
    // 2. MANAJEMEN UNIT & KANDANG (CRUD)
    // ================================================================
    
    // Halaman Input (Form Tambah Unit & Kandang)
    public function input()
    {
        $units = Unit::all();
        
        // [UPDATE] Ambil data batch aktif untuk dropdown di Form Input Kandang
        $batches = Batch::where('is_active', true)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.kandang.input', compact('units', 'batches'));
    }

    // Simpan Unit Baru
    public function storeUnit(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'lokasi'    => 'required|string|max:255',
        ]);

        Unit::create($request->all());
        return redirect()->back()->with('success', 'Unit baru berhasil ditambahkan.');
    }

    // Update Unit (Fitur Edit)
    public function updateUnit(Request $request, $id)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'lokasi'    => 'required|string|max:255',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->update([
            'nama_unit' => $request->nama_unit,
            'lokasi'    => $request->lokasi
        ]);

        return redirect()->back()->with('success', 'Data Unit berhasil diperbarui.');
    }

    // Hapus Unit
    public function destroyUnit($id)
    {
        Unit::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data unit dihapus.');
    }

    // Simpan Kandang Baru (Dan Otomatis Chick-In Batch Pertama)
    public function storeKandang(Request $request)
    {
        $request->validate([
            'unit_id'       => 'required|exists:units,id',
            'nama_kandang'  => 'required|string|max:255',
            'kapasitas'     => 'required|integer|min:1',
            'tgl_masuk'     => 'nullable|date',      
            'umur_awal'     => 'nullable|integer',   
            'stok_awal'     => 'nullable|integer',
            'batch_id'      => 'nullable|exists:batches,id', // [BARU] Validasi Batch
        ]);

        DB::transaction(function () use ($request) {
            // A. Simpan Data Fisik Kandang
            $status = ($request->stok_awal > 0) ? 'aktif' : 'kosong';
            
            $kandang = Kandang::create([
                'unit_id'       => $request->unit_id,
                'nama_kandang'  => $request->nama_kandang,
                'kapasitas'     => $request->kapasitas,
                'tgl_masuk'     => $request->tgl_masuk,      
                'umur_awal'     => $request->umur_awal ?? 0, 
                'stok_awal'     => $request->stok_awal ?? 0,
                'stok_saat_ini' => $request->stok_awal ?? 0,
                'status'        => $status
            ]);

            // B. [UPDATE] Jika ada populasi awal, buat data SIKLUS otomatis
            if ($request->stok_awal > 0) {
                Siklus::create([
                    'kandang_id' => $kandang->id,
                    'batch_id' => $request->batch_id, // Simpan ID Batch/Siklus
                    'tanggal_chick_in' => $request->tgl_masuk ?? now(),
                    'populasi_awal' => $request->stok_awal,
                    'umur_awal_minggu' => $request->umur_awal ?? 18,
                    'jenis_ayam' => 'Pullet', // Default
                    'status' => 'Aktif',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Kandang baru berhasil ditambahkan.');
    }

    // Update Kandang (Fitur Edit Data Fisik)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kandang' => 'required|string|max:255',
            'kapasitas'    => 'required|integer',
            'status'       => 'required|string',
            'tgl_masuk'    => 'nullable|date',      
            'umur_awal'    => 'nullable|integer',   
        ]);

        $kandang = Kandang::findOrFail($id);
        
        $kandang->update([
            'nama_kandang' => $request->nama_kandang,
            'kapasitas'    => $request->kapasitas,
            'status'       => $request->status,
            'tgl_masuk'    => $request->tgl_masuk,  
            'umur_awal'    => $request->umur_awal   
        ]);

        return redirect()->back()->with('success', 'Data Kandang berhasil diperbarui.');
    }

    // Hapus Kandang
    public function destroyKandang($id)
    {
        Kandang::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data kandang dihapus.');
    }

    // Halaman Data List Unit & Kandang
    public function data(Request $request)
    {
        $units = Unit::withCount('kandangs');
        // Load relasi siklusAktif dan batch-nya untuk ditampilkan di tabel
        $kandangs = Kandang::with(['unit', 'siklusAktif.batch']); 

        // Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $units->where('nama_unit', 'like', "%$search%");
            $kandangs->where('nama_kandang', 'like', "%$search%");
        }

        // Filter Lokasi
        if ($request->has('lokasi') && $request->lokasi != 'Semua Lokasi') {
            $kandangs->whereHas('unit', function($q) use ($request) {
                $q->where('lokasi', $request->lokasi);
            });
        }

        $units = $units->paginate(5, ['*'], 'units_page');
        $kandangs = $kandangs->paginate(10, ['*'], 'kandangs_page');

        // [UPDATE] Ambil data batches untuk modal Chick-In di halaman Data
        $batches = Batch::where('is_active', true)->get();

        return view('admin.kandang.data_input_unit', compact('units', 'kandangs', 'batches'));
    }


    // ================================================================
    // 3. MANAJEMEN TIMBANG
    // ================================================================

    // Halaman Input Timbang
    public function inputTimbang()
    {
        // Gunakan Eager Loading agar di view bisa loop unit->kandangs
        $units = Unit::with('kandangs')->get();
        return view('admin.kandang.input_timbang', compact('units'));
    }

    // Simpan Data Timbang
    public function storeTimbang(Request $request)
    {
        $request->validate([
            'kandang_id'    => 'required|exists:kandangs,id',
            'tgl_timbang'   => 'required|date',
            'umur_minggu'   => 'required|integer', 
            'berat_rata'    => 'required|numeric',
            'uniformity'    => 'required|numeric',
        ]);

        Timbangan::create([
            'kandang_id'      => $request->kandang_id,
            // 'user_id'      => Auth::id(), 
            'tanggal_timbang' => $request->tgl_timbang,
            'umur_minggu'     => $request->umur_minggu, 
            'berat_rata'      => $request->berat_rata,
            'uniformity'      => $request->uniformity,
            'keterangan'      => $request->keterangan ?? 'Input Rutin'
        ]);

        return redirect()->route('admin.kandang.data_timbang')->with('success', 'Data timbang berhasil disimpan.');
    }

    // Halaman Data List Timbang
    public function dataTimbang(Request $request)
    {
        $query = Timbangan::with(['kandang.unit']);

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_timbang', $request->tanggal);
        }

        if ($request->filled('unit_id')) {
            $query->whereHas('kandang', function($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            });
        }

        $timbangans = $query->latest('tanggal_timbang')->paginate(10);
        $units = Unit::all(); 

        return view('admin.kandang.data_timbang', compact('timbangans', 'units'));
    }


    // ================================================================
    // 4. RIWAYAT (LOGS)
    // ================================================================
    
    public function riwayatUnit()
    {
        $riwayatUnits = Unit::withTrashed()->latest()->paginate(10);
        return view('admin.kandang.riwayat_unit', compact('riwayatUnits'));
    }

  

    public function riwayatKandang(Request $request)
    {
        // [UPDATE] Menggunakan Siklus dan load Batch
        $query = Siklus::with(['kandang.unit', 'batch'])->orderBy('tanggal_chick_in', 'desc');

        // Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_chick_in', [$request->start_date, $request->end_date]);
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('kandang', function($q) use ($search) {
                $q->where('nama_kandang', 'like', "%{$search}%");
            });
        }

        // Gunakan variable $riwayatSiklus agar sesuai dengan View
        $riwayatSiklus = $query->paginate(10);

        return view('admin.kandang.riwayat_kandang', compact('riwayatSiklus'));
    }

    public function riwayatTimbang()
    {
        $riwayatTimbang = Timbangan::with('kandang.unit')->latest()->paginate(10);
        return view('admin.kandang.riwayat_timbang', compact('riwayatTimbang'));
    }


    // ================================================================
    // 5. LAPORAN & PDF (ADMIN)
    // ================================================================
    public function laporan(Request $request)
    {
        $tipe = $request->input('tipe', 'kandang'); 
        
        $data = collect();
        $units = Unit::with('kandangs')->get(); 
        
        if ($tipe == 'timbang') {
            $query = Timbangan::with('kandang.unit');
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_timbang', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('unit_id')) {
                $query->whereHas('kandang', function($q) use ($request) {
                    $q->where('unit_id', $request->unit_id);
                });
            }
            if ($request->filled('kandang_id')) {
                $query->where('kandang_id', $request->kandang_id);
            }
            $data = $query->get();
        } else {
            // Laporan Kandang (Populasi)
            $query = Kandang::with('unit');
            if ($request->filled('unit_id')) {
                $query->where('unit_id', $request->unit_id);
            }
            if ($request->filled('kandang_id')) {
                $query->where('id', $request->kandang_id);
            }
            $data = $query->get();
        }

        if ($request->has('print') && $request->print == 'true') {
            return view('admin.kandang.cetak_laporan', compact('data', 'tipe'));
        }

        return view('admin.kandang.laporan', compact('data', 'tipe', 'units'));
    }

    // ================================================================
    // 6. KHUSUS OWNER (READ ONLY)
    // ================================================================
    public function ownerData(Request $request)
    {
        $units = Unit::withCount('kandangs');
        $kandangs = Kandang::with('unit');

        // Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $units->where('nama_unit', 'like', "%$search%");
            $kandangs->where('nama_kandang', 'like', "%$search%");
        }

        // Filter Lokasi
        if ($request->has('lokasi') && $request->lokasi != 'Semua Lokasi' && $request->lokasi != '') {
            $kandangs->whereHas('unit', function($q) use ($request) {
                $q->where('lokasi', $request->lokasi);
            });
        }

        $units = $units->paginate(5, ['*'], 'units_page');
        $kandangs = $kandangs->paginate(10, ['*'], 'kandangs_page');
        
        // Ambil list lokasi unik untuk dropdown filter
        $lokasis = Unit::select('lokasi')->distinct()->pluck('lokasi');

        return view('owner.kandang.data_input', compact('units', 'kandangs', 'lokasis'));
    }

    // ================================================================
    // LAPORAN PDF OWNER (KANDANG)
    // ================================================================
    public function ownerLaporan(Request $request)
    {
        // 1. Data untuk Dropdown Filter
        $units = Unit::all();
        $lokasis = Unit::select('lokasi')->distinct()->pluck('lokasi');

        $data = collect([]);

        // 2. Logika Filter Data
        if ($request->has('filter')) {
            $query = Kandang::with('unit');

            if ($request->lokasi && $request->lokasi != 'all') {
                $query->whereHas('unit', function($q) use ($request) {
                    $q->where('lokasi', $request->lokasi);
                });
            }

            if ($request->unit_id && $request->unit_id != 'all') {
                $query->where('unit_id', $request->unit_id);
            }

            if ($request->status && $request->status != 'all') {
                $query->where('status', $request->status);
            }

            $data = $query->get();
        }

        // 3. Jika Download PDF
        if ($request->has('download_pdf')) {
            // [FIX] Tambahkan variabel $tipe = 'kandang' agar view cetak tidak error
            $tipe = 'kandang'; 
            
            // Gunakan view cetak yang sama dengan admin
            return view('admin.kandang.cetak_laporan', compact('data', 'tipe')); 
        }

        // 4. Return View Laporan Owner
        return view('owner.kandang.laporan_pdf', compact('units', 'lokasis', 'data'));
    }
}