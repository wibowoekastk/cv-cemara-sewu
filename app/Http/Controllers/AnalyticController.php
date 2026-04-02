<?php

namespace App\Http\Controllers;

use App\Models\DailyRecord;
use App\Models\Kandang;
use App\Models\Unit;
use App\Models\UnitPakanStock;
use App\Models\PakanMutation;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AnalyticController extends Controller
{
    // ================================================================
    // 1. DASHBOARD ANALYTIC
    // ================================================================
    public function dashboard(Request $request) {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $totalKandang = Kandang::count();
        $inputToday = DailyRecord::whereDate('tanggal', $today)->count();
        $inputPercentage = $totalKandang > 0 ? ($inputToday / $totalKandang) * 100 : 0;

        $productionDropCount = 0;
        $dropKandangNames = [];
        $recordsToday = DailyRecord::whereDate('tanggal', $today)->get()->keyBy('kandang_id');
        $recordsYest = DailyRecord::whereDate('tanggal', $yesterday)->get()->keyBy('kandang_id');

        foreach ($recordsToday as $kandangId => $recToday) {
            if (isset($recordsYest[$kandangId])) {
                if ($recToday->hdp < ($recordsYest[$kandangId]->hdp - 1)) {
                    $productionDropCount++;
                    if (count($dropKandangNames) < 3) {
                        $dropKandangNames[] = $recToday->kandang->nama_kandang ?? 'Kandang ' . $kandangId;
                    }
                }
            }
        }

        $avgFcr = $recordsToday->avg('fcr') ?? 0;

        $startOfMonth = Carbon::now()->startOfMonth();
        $totalMatiMonth = DailyRecord::whereBetween('tanggal', [$startOfMonth, $today])->sum('mati');
        $currentPopulasi = Kandang::sum('stok_saat_ini');
        $totalAfkirMonth = DailyRecord::whereBetween('tanggal', [$startOfMonth, $today])->sum('afkir');
        $initialPopulasi = $currentPopulasi + $totalMatiMonth + $totalAfkirMonth;
        $mortalityRate = $initialPopulasi > 0 ? ($totalMatiMonth / $initialPopulasi) * 100 : 0;

        $units = Unit::with('kandangs')->get();
        $lokasis = Unit::select('lokasi')->distinct()->pluck('lokasi'); 
        
        $firstKandang = Kandang::first();
        $initialChartData = $firstKandang ? $this->getChartDataForKandang($firstKandang->id, 'weekly') : null;

        return view('admin.analytic.dashboard', compact(
            'totalKandang', 'inputToday', 'inputPercentage',
            'productionDropCount', 'dropKandangNames',
            'avgFcr', 'mortalityRate',
            'units', 'firstKandang', 'initialChartData', 'lokasis' 
        ));
    }

    public function getChartData(Request $request) 
    {
        $mode = $request->input('mode', 'weekly'); 
        return response()->json($this->getChartDataForKandang($request->kandang_id, $mode));
    }

    private function getChartDataForKandang($kandangId, $mode = 'weekly') 
    {
        $kandang = Kandang::with('siklusAktif')->findOrFail($kandangId);
        $siklus = $kandang->siklusAktif;

        // Ambil patokan tanggal dan umur dari siklus/kandang
        if ($siklus) {
            $tglMasuk = Carbon::parse($siklus->tanggal_chick_in);
            $umurAwalMinggu = $siklus->umur_awal_minggu ?? 0;
        } else {
            $tglMasuk = $kandang->tgl_masuk ? Carbon::parse($kandang->tgl_masuk) : null;
            $umurAwalMinggu = $kandang->umur_awal ?? 0;
        }

        $stokAwalPopulasi = $kandang->stok_awal > 0 ? $kandang->stok_awal : 1; 

        $query = DailyRecord::where('kandang_id', $kandangId);
        
        // Filter by siklus jika ada, agar datanya relevan
        if ($siklus) {
            $query->where('siklus_id', $siklus->id);
        }
        
        if ($mode == 'daily') { 
            // Harian: Ambil 30 hari TERAKHIR agar grafik selalu memunculkan data ter-update
            $records = $query->orderBy('tanggal', 'desc')->limit(30)->get()->reverse(); 
        } else { 
            // MINGGUAN: Ambil SEMUA data dalam siklus ini (tanpa dibatasi limit 90 hari)
            $records = $query->orderBy('tanggal', 'asc')->get();
        }

        if ($records->isEmpty()) {
            return [ 'labels' => [], 'deplesi' => [], 'produksi' => [], 'feed_intake' => [], 'total_pakan' => [], 'berat_telur' => [], 'fcr' => [], 'body_weight' => [], 'daya_hidup' => [] ];
        }

        // Jika tglMasuk masih kosong, gunakan tanggal dari data paling awal yang diinput
        if (!$tglMasuk) {
            $tglMasuk = Carbon::parse($records->first()->tanggal);
        }

        $groupedData = [];
        foreach ($records as $record) {
            $tglRecord = Carbon::parse($record->tanggal);
            
            // Hitung Umur Berdasarkan Selisih Hari Sebenarnya dari waktu ayam masuk
            $selisihHari = $tglMasuk->startOfDay()->diffInDays($tglRecord->startOfDay(), false);
            
            // Umur dalam minggu (dibulatkan ke bawah ke minggu terdekat)
            $umurSaatIni = $umurAwalMinggu + floor($selisihHari / 7);
            
            if ($mode == 'daily') {
                $key = $tglRecord->format('d M'); 
            } elseif ($mode == 'monthly') {
                $key = $tglRecord->format('M Y'); 
            } else {
                // MINGGUAN: Tampilkan Umur secara jujur (tanpa pencegahan min 0 agar titik chart tidak menumpuk)
                $key = "Umur " . $umurSaatIni . " Mgg"; 
            }

            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [ 
                    'count' => 0, 
                    'tgl_akhir' => $record->tanggal, 
                    'populasi_sum' => 0, 
                    'mati_sum' => 0, 
                    'afkir_sum' => 0, 
                    'hdp_sum' => 0, 
                    'pakan_sum' => 0, 
                    'telur_kg_sum' => 0, 
                    'telur_butir_sum' => 0, 
                    'fcr_sum' => 0 
                ];
            }
            $item = &$groupedData[$key];
            $item['count']++;
            $item['tgl_akhir'] = $record->tanggal;
            
            $populasiHariIni = $record->populasi_awal - $record->mati - $record->afkir;
            
            $item['populasi_sum'] += $populasiHariIni; 
            $item['mati_sum'] += $record->mati;
            $item['afkir_sum'] += $record->afkir;
            $item['hdp_sum'] += $record->hdp;
            $item['pakan_sum'] += $record->pakan_kg;
            $item['telur_kg_sum'] += $record->telur_kg;
            $item['telur_butir_sum'] += $record->telur_butir;
            $item['fcr_sum'] += $record->fcr;
        }

        $labels = []; $deplesi = []; $produksi = []; $feed_intake = []; $total_pakan = []; $berat_telur = []; $fcr = []; $body_weight = []; $daya_hidup = [];

        $weighingsInRange = collect([]);
        if (Schema::hasTable('timbangans')) {
            $startTgl = $records->first()->tanggal;
            $endTgl = $records->last()->tanggal;
            $weighingsInRange = DB::table('timbangans')->where('kandang_id', $kandangId)->whereBetween('tanggal_timbang', [$startTgl, $endTgl])->pluck('berat_rata', 'tanggal_timbang');
        }
        
        $currentWeight = 0;
        if (Schema::hasTable('timbangans')) {
             $lastWeight = DB::table('timbangans')->where('kandang_id', $kandangId)->where('tanggal_timbang', '<', $records->first()->tanggal)->orderBy('tanggal_timbang', 'desc')->value('berat_rata');
             $currentWeight = $lastWeight ? ($lastWeight / 1000) : 0;
        }

        foreach ($groupedData as $key => $data) {
            $count = $data['count'];
            $avgPopulasiHidup = $data['populasi_sum'] / $count;

            $labels[] = $key;
            
            // Produksi HDP (Rata-rata Seminggu)
            $produksi[] = round($data['hdp_sum'] / $count, 2);
            
            // [LOGIKA TITIPAN MANDOR: DEPLESI MINGGUAN]
            if ($mode == 'weekly' || $mode == 'monthly') {
                $totalMatiSeminggu = $data['mati_sum'] + $data['afkir_sum'];
                // (Mati Seminggu / Populasi Awal) x 100%
                $deplesi[] = round(($totalMatiSeminggu / $stokAwalPopulasi) * 100, 3); // 3 desimal agar titiknya terlihat di grafik
            } else {
                $deplesi[] = $avgPopulasiHidup > 0 ? round((($data['mati_sum'] + $data['afkir_sum']) / $avgPopulasiHidup) * 100, 2) : 0;
            }

            $daya_hidup[] = round(($avgPopulasiHidup / $stokAwalPopulasi) * 100, 2);

            $feed_intake[] = $avgPopulasiHidup > 0 ? round((($data['pakan_sum'] / $count) * 1000) / $avgPopulasiHidup, 1) : 0;
            $total_pakan[] = round($data['pakan_sum'], 1);
            $berat_telur[] = $data['telur_butir_sum'] > 0 ? round(($data['telur_kg_sum'] * 1000) / $data['telur_butir_sum'], 1) : 0;
            $fcr[] = $data['telur_kg_sum'] > 0 ? round($data['pakan_sum'] / $data['telur_kg_sum'], 2) : 0;
            
            $tglAkhirMinggu = Carbon::parse($data['tgl_akhir']);
            $tglStr = $tglAkhirMinggu->format('Y-m-d');
            if (isset($weighingsInRange[$tglStr])) { $currentWeight = $weighingsInRange[$tglStr] / 1000; }
            $body_weight[] = $currentWeight;
        }

        return [ 
            'labels' => $labels, 'deplesi' => $deplesi, 'produksi' => $produksi, 
            'feed_intake' => $feed_intake, 'total_pakan' => $total_pakan, 
            'berat_telur' => $berat_telur, 'fcr' => $fcr, 'body_weight' => $body_weight, 
            'daya_hidup' => $daya_hidup 
        ];
    }

    // =========================================================================
    // 2. API UNTUK FORM INPUT (MENGHITUNG HEN HOUSE / HH & INFO UMUR DENGAN BACKDATE)
    // =========================================================================
    public function getKandangStats(Request $request, $id)
    {
        // Load relasi batch dari siklusAktif untuk ditampilkan
        $kandang = Kandang::with(['siklusAktif.batch'])->findOrFail($id);
        $siklus = $kandang->siklusAktif;

        // [UPDATE] Tangkap Tanggal Laporan dari Request agar Umur Menyesuaikan Mundur/Maju
        $tglLaporan = $request->query('tanggal') ? Carbon::parse($request->query('tanggal')) : Carbon::today();

        $batchName = "Tidak Ada Batch Aktif";
        $siklusId = null;
        $umurTeks = "-";

        if ($siklus) {
            $siklusId = $siklus->id;
            
            // 1. Ambil Nama Batch
            if ($siklus->batch) {
                $batchName = $siklus->batch->nama_batch; 
            } else {
                $batchName = "Siklus " . $siklus->tanggal_chick_in->format('d/m/Y');
            }

            // 2. Logika Perhitungan Umur Berdasarkan Tanggal Input (Backdate Support)
            $tglChickIn = Carbon::parse($siklus->tanggal_chick_in);
            
            // Konversi umur saat awal masuk ke total hari
            $umurAwalHari = ($siklus->umur_awal_minggu ?? 0) * 7;
            
            // Hitung selisih hari antara Chick-in dan Tanggal Laporan yang dipilih
            $selisihHariLaporan = $tglChickIn->startOfDay()->diffInDays($tglLaporan->startOfDay(), false); 
            
            $totalHariUmur = $umurAwalHari + $selisihHariLaporan;

            if ($totalHariUmur >= 0) {
                $minggu = floor($totalHariUmur / 7);
                $sisaHari = $totalHariUmur % 7;
                $umurTeks = $minggu . " mng" . ($sisaHari > 0 ? " " . $sisaHari . " hr" : "");
            } else {
                $umurTeks = "Belum Chick-in";
            }
        }

        // 3. Hitung total produksi SEBELUMNYA (Strictly data sebelum tanggal laporan ini)
        $history = DailyRecord::where('kandang_id', $id)
            ->where('siklus_id', $siklusId)
            ->where('tanggal', '<', $tglLaporan->format('Y-m-d'))
            ->selectRaw('SUM(telur_butir) as total_butir, SUM(telur_kg) as total_kg')
            ->first();

        return response()->json([
            'stok_awal' => $kandang->stok_awal,
            'stok_saat_ini' => $kandang->stok_saat_ini,
            'cum_butir_sebelumnya' => floatval($history->total_butir ?? 0),
            'cum_kg_sebelumnya' => floatval($history->total_kg ?? 0),
            
            // Data untuk Tampilan View Input
            'batch_name' => $batchName,
            'umur_teks' => $umurTeks, // Mengirimkan teks umur yang dinamis
            'is_active' => $siklus ? true : false
        ]);
    }

    // ================================================================
    // 3. INPUT, STORE, UPDATE, DELETE (CRUD)
    // ================================================================
    
    public function input(Request $request)
    {
        $units = Unit::with(['kandangs', 'pakanStocks.pakan'])->get();
        return view('admin.analytic.input_harian', compact('units'));
    }
    
    public function mandorInput(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 'mandor' && $user->unit_id) {
            $units = Unit::with('kandangs')->where('id', $user->unit_id)->get();
            $unitId = $user->unit_id;
        } else {
            $units = Unit::with('kandangs')->get();
            $unitId = $units->first()->id ?? 0;
        }
        $stokPakanUnit = UnitPakanStock::with('pakan')->where('unit_id', $unitId)->where('jumlah_stok', '>', 0)->get();
        return view('mandor.produksi.input', compact('units', 'stokPakanUnit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandangs,id',
            'tanggal' => 'required|date',
            'pakan_id' => 'required|exists:pakans,id',
            'pakan_kg' => 'required|numeric|min:0',
            'telur_butir' => 'required|numeric|min:0',
            'telur_kg' => 'required|numeric|min:0',
            'mati' => 'required|numeric|min:0',
            'afkir' => 'required|numeric|min:0',
            'ket_mati' => 'nullable|string|max:255', 
        ]);

        $kandang = Kandang::findOrFail($request->kandang_id);
        $user = Auth::user();
        
        $siklusAktif = $kandang->siklusAktif; 
        $siklusId = $siklusAktif ? $siklusAktif->id : null;

        $stokUnit = UnitPakanStock::where('unit_id', $kandang->unit_id)
                                  ->where('pakan_id', $request->pakan_id)
                                  ->first();

        if ($request->pakan_kg > 0) {
            if (!$stokUnit || $stokUnit->jumlah_stok < $request->pakan_kg) {
                return back()->with('error', 'Gagal! Stok pakan di Gudang Unit tidak cukup. Sisa: ' . ($stokUnit->jumlah_stok ?? 0) . ' Kg');
            }
        }

        if(DailyRecord::where('kandang_id', $kandang->id)->whereDate('tanggal', $request->tanggal)->exists()) {
            return back()->with('error', 'Data untuk tanggal ini sudah ada.');
        }

        DB::transaction(function () use ($request, $kandang, $user, $stokUnit, $siklusId) {
            $populasi = $kandang->stok_saat_ini;
            $hdp = ($populasi > 0) ? ($request->telur_butir / $populasi) * 100 : 0;
            $fcr = ($request->telur_kg > 0) ? ($request->pakan_kg / $request->telur_kg) : 0;

            DailyRecord::create([
                'siklus_id' => $siklusId, 
                'unit_id' => $kandang->unit_id,
                'kandang_id' => $kandang->id,
                'user_id' => $user->id,
                'tanggal' => $request->tanggal,
                'populasi_awal' => $populasi,
                'mati' => $request->mati,
                'afkir' => $request->afkir,
                'ket_mati' => $request->ket_mati, 
                'telur_butir' => $request->telur_butir,
                'telur_kg' => $request->telur_kg,
                'pakan_id' => $request->pakan_id,
                'pakan_kg' => $request->pakan_kg,
                'fcr' => $fcr,
                'hdp' => $hdp,
            ]);

            $kandang->decrement('stok_saat_ini', $request->mati + $request->afkir);

            if($request->pakan_kg > 0) {
                $stokUnit->decrement('jumlah_stok', $request->pakan_kg);
                PakanMutation::create([
                    'pakan_id' => $request->pakan_id, 'user_id' => $user->id, 'dari_unit_id' => $kandang->unit_id, 'kandang_id' => $kandang->id, 
                    'tanggal' => $request->tanggal, 'jenis_mutasi' => 'pemakaian', 'jumlah' => $request->pakan_kg, 'status' => 'selesai', 'keterangan' => "Input Harian"
                ]);
            }
        });

        return back()->with('success', 'Laporan Harian berhasil disimpan.');
    }

    // =========================================================================
    // FITUR BARU: INPUT MASSAL (MAGIC PASTE EXCEL)
    // =========================================================================

    /**
     * Menampilkan Halaman Input Massal
     */
    public function createMassal(Request $request)
    {
        $user = Auth::user();
        
        // Membedakan akses jika user adalah mandor (hanya unitnya sendiri) atau admin (semua unit)
        if ($user->role == 'mandor' && $user->unit_id) {
            $units = Unit::with('kandangs')->where('id', $user->unit_id)->get();
            $stokPakanUnit = UnitPakanStock::with('pakan')->where('unit_id', $user->unit_id)->where('jumlah_stok', '>', 0)->get();
            $view = 'mandor.produksi.input_massal'; // Asumsi view untuk mandor
        } else {
            $units = Unit::with('kandangs')->get(); 
            $stokPakanUnit = UnitPakanStock::with('pakan')->where('jumlah_stok', '>', 0)->get();
            $view = 'admin.analytic.input_massal'; // View admin seperti di contoh
        }

        // Cek apakah file view tersedia untuk return, pakai fallback yang ada jika perlu
        if (view()->exists($view)) {
            return view($view, compact('units', 'stokPakanUnit'));
        }
        
        // Jika hanya 1 view yang dibikin (misal di admin), otomatis pakai itu
        return view('admin.analytic.input_massal', compact('units', 'stokPakanUnit'));
    }

    /**
     * Memproses Data Input Massal
     */
    public function storeMassal(Request $request)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandangs,id',
            'pakan_id' => 'required|exists:pakans,id',
            'data' => 'required|array', 
            'data.*.tanggal' => 'required|date',
            'data.*.mati' => 'required|numeric|min:0',
            'data.*.afkir' => 'required|numeric|min:0',
            'data.*.pakan_kg' => 'required|numeric|min:0',
            'data.*.telur_butir' => 'required|numeric|min:0',
            'data.*.telur_kg' => 'required|numeric|min:0',
        ]);

        $kandang = Kandang::findOrFail($request->kandang_id);
        $user = Auth::user();
        $siklusAktif = $kandang->siklusAktif; 
        $siklusId = $siklusAktif ? $siklusAktif->id : null;
        
        $stokUnit = UnitPakanStock::where('unit_id', $kandang->unit_id)
                                  ->where('pakan_id', $request->pakan_id)
                                  ->first();

        $berhasil = 0;
        $gagal = 0;

        DB::transaction(function () use ($request, $kandang, $user, $stokUnit, $siklusId, &$berhasil, &$gagal) {
            
            foreach ($request->data as $row) {
                // Cek data ganda (mencegah double input)
                if(DailyRecord::where('kandang_id', $kandang->id)->whereDate('tanggal', $row['tanggal'])->exists()) {
                    $gagal++;
                    continue; 
                }

                // Cek ketersediaan stok pakan (Akan melempar error dan cancel transaksi jika pakan kurang)
                if ($row['pakan_kg'] > 0) {
                    if (!$stokUnit || $stokUnit->jumlah_stok < $row['pakan_kg']) {
                        throw new \Exception("Stok Pakan tidak cukup untuk pemakaian di tanggal " . Carbon::parse($row['tanggal'])->format('d M Y'));
                    }
                }

                $populasi = $kandang->stok_saat_ini;
                
                $hdp = ($populasi > 0) ? ($row['telur_butir'] / $populasi) * 100 : 0;
                $fcr = ($row['telur_kg'] > 0) ? ($row['pakan_kg'] / $row['telur_kg']) : 0;

                DailyRecord::create([
                    'siklus_id' => $siklusId, 
                    'unit_id' => $kandang->unit_id,
                    'kandang_id' => $kandang->id,
                    'user_id' => $user->id,
                    'tanggal' => $row['tanggal'],
                    'populasi_awal' => $populasi,
                    'mati' => $row['mati'],
                    'afkir' => $row['afkir'],
                    'ket_mati' => null, 
                    'telur_butir' => $row['telur_butir'],
                    'telur_kg' => $row['telur_kg'],
                    'pakan_id' => $request->pakan_id, 
                    'pakan_kg' => $row['pakan_kg'],
                    'fcr' => $fcr,
                    'hdp' => $hdp,
                ]);

                $kandang->decrement('stok_saat_ini', $row['mati'] + $row['afkir']);

                if($row['pakan_kg'] > 0) {
                    $stokUnit->decrement('jumlah_stok', $row['pakan_kg']);
                    
                    PakanMutation::create([
                        'pakan_id' => $request->pakan_id, 
                        'user_id' => $user->id, 
                        'dari_unit_id' => $kandang->unit_id, 
                        'kandang_id' => $kandang->id, 
                        'tanggal' => $row['tanggal'], 
                        'jenis_mutasi' => 'pemakaian', 
                        'jumlah' => $row['pakan_kg'], 
                        'status' => 'selesai', 
                        'keterangan' => "Input Harian Massal (Auto)"
                    ]);
                }
                
                $berhasil++;
            }
        });

        return back()->with('success', "Input Massal Selesai! Berhasil menyimpan $berhasil hari. (Di-skip karena data sudah ada: $gagal hari).");
    }

    public function update(Request $request, $id) {
        $record = DailyRecord::with('kandang')->findOrFail($id);
        $request->validate([
            'tanggal'     => 'required|date', 
            'pakan_id'    => 'required|exists:pakans,id', 
            'pakan_kg'    => 'required|numeric|min:0', 
            'telur_butir' => 'required|numeric|min:0', 
            'telur_kg'    => 'required|numeric|min:0', 
            'mati'        => 'required|numeric|min:0', 
            'afkir'       => 'required|numeric|min:0',
            'ket_mati'    => 'nullable|string|max:255' 
        ]);

        if (DailyRecord::where('kandang_id', $record->kandang_id)
            ->whereDate('tanggal', $request->tanggal)
            ->where('id', '!=', $id) 
            ->exists()) {
            return back()->with('error', 'Gagal Edit! Data pada tanggal ' . Carbon::parse($request->tanggal)->format('d/m/Y') . ' sudah ada di kandang ini.');
        }

        DB::transaction(function () use ($request, $record) {
            $oldPengurangan = $record->mati + $record->afkir;
            if ($oldPengurangan > 0) { $record->kandang->increment('stok_saat_ini', $oldPengurangan); }
            
            if ($record->pakan_kg > 0) {
                $stokUnit = UnitPakanStock::where('unit_id', $record->unit_id)->where('pakan_id', $record->pakan_id)->first();
                if ($stokUnit) { $stokUnit->increment('jumlah_stok', $record->pakan_kg); }
                PakanMutation::where('kandang_id', $record->kandang_id)->where('tanggal', $record->tanggal)->where('jenis_mutasi', 'pemakaian')->where('jumlah', $record->pakan_kg)->limit(1)->delete();
            }

            $populasi = $record->populasi_awal;
            $hdp = ($populasi > 0) ? ($request->telur_butir / $populasi) * 100 : 0;
            $fcr = ($request->telur_kg > 0) ? ($request->pakan_kg / $request->telur_kg) : 0;
            
            $record->update([
                'tanggal'     => $request->tanggal, 
                'pakan_id'    => $request->pakan_id,
                'mati'        => $request->mati, 
                'afkir'       => $request->afkir, 
                'ket_mati'    => $request->ket_mati, 
                'telur_butir' => $request->telur_butir, 
                'telur_kg'    => $request->telur_kg, 
                'pakan_kg'    => $request->pakan_kg, 
                'fcr'         => $fcr, 
                'hdp'         => $hdp
            ]);
            
            $newPengurangan = $request->mati + $request->afkir;
            if ($newPengurangan > 0) { $record->kandang->decrement('stok_saat_ini', $newPengurangan); }
            
            if ($request->pakan_kg > 0) {
                $stokUnit = UnitPakanStock::firstOrCreate(['unit_id' => $record->unit_id, 'pakan_id' => $record->pakan_id], ['jumlah_stok' => 0]);
                $stokUnit->decrement('jumlah_stok', $request->pakan_kg);
                PakanMutation::create(['pakan_id' => $record->pakan_id, 'user_id' => Auth::id(), 'dari_unit_id' => $record->unit_id, 'kandang_id' => $record->kandang_id, 'tanggal' => $request->tanggal, 'jenis_mutasi' => 'pemakaian', 'jumlah' => $request->pakan_kg, 'status' => 'selesai', 'keterangan' => "Koreksi Harian"]);
            }
        });
        return back()->with('success', 'Data diperbarui.');
    }

    public function destroy($id) { 
        $record = DailyRecord::with('kandang')->findOrFail($id);
        DB::transaction(function () use ($record) {
            $pengurangan = $record->mati + $record->afkir;
            if ($pengurangan > 0) { $record->kandang->increment('stok_saat_ini', $pengurangan); }
            if ($record->pakan_kg > 0) {
                $stokUnit = UnitPakanStock::where('unit_id', $record->unit_id)->where('pakan_id', $record->pakan_id)->first();
                if ($stokUnit) { $stokUnit->increment('jumlah_stok', $record->pakan_kg); }
                PakanMutation::where('kandang_id', $record->kandang_id)->where('tanggal', $record->tanggal)->where('jenis_mutasi', 'pemakaian')->where('jumlah', $record->pakan_kg)->delete();
            }
            $record->delete();
        });
        return back()->with('success', 'Data dihapus.'); 
    }

    // ================================================================
    // 4. DATA VIEWERS (TABLES)
    // ================================================================
    
    public function data(Request $request) { 
        $data = $this->getDailyRecords($request); 
        $units = Unit::all(); 
        return view('admin.analytic.data_input_harian', compact('data', 'units')); 
    }

    public function riwayat(Request $request) { 
        $data = $this->getDailyRecords($request); 
        $units = Unit::all(); 
        return view('admin.analytic.riwayat_input_harian', compact('data', 'units')); 
    }

    private function getDailyRecords(Request $request) {
        $query = DailyRecord::with(['kandang.unit', 'user', 'pakan', 'siklus.batch'])->latest('tanggal');
        
        if($request->filled('unit_id')) { 
            $query->where('unit_id', $request->unit_id); 
        }
        
        if($request->filled('kandang_id')) { 
            $query->where('kandang_id', $request->kandang_id); 
        }

        if($request->filled('pakan_id')) { 
            $query->where('pakan_id', $request->pakan_id); 
        }

        if($request->filled('start_date') && $request->filled('end_date')) { 
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]); 
        }
        return $query->paginate(15);
    }

    // ================================================================
    // 5. LAPORAN & PDF (ADMIN & OWNER)
    // ================================================================
    public function laporan(Request $request) { 
        $units = Unit::with('kandangs')->get();
        $laporanData = collect([]);
        $summary = ['total_telur_kg' => 0, 'total_pakan_kg' => 0, 'avg_fcr' => 0, 'avg_hdp' => 0];
        $batches = Batch::orderBy('created_at', 'desc')->get(); 

        if ($request->has('filter')) {
            $query = DailyRecord::with(['kandang.unit', 'pakan', 'siklus.batch'])->orderBy('tanggal', 'asc');
            
            if ($request->dateStart && $request->dateEnd) { 
                $query->whereBetween('tanggal', [$request->dateStart, $request->dateEnd]); 
            } elseif ($request->dateStart) { 
                $query->whereDate('tanggal', $request->dateStart); 
            }
            
            if ($request->unit && $request->unit != 'all') { $query->where('unit_id', $request->unit); }
            if ($request->kandang && $request->kandang != 'all') { $query->where('kandang_id', $request->kandang); }
            
            if ($request->batch_id && $request->batch_id != 'all') {
                $query->whereHas('siklus', function($q) use ($request) {
                    $q->where('batch_id', $request->batch_id);
                });
            }
            
            $laporanData = $query->get();

            if ($laporanData->count() > 0) {
                $summary['total_telur_kg'] = $laporanData->sum('telur_kg');
                $summary['total_pakan_kg'] = $laporanData->sum('pakan_kg');
                $summary['avg_fcr'] = $summary['total_telur_kg'] > 0 ? $summary['total_pakan_kg'] / $summary['total_telur_kg'] : 0;
                $summary['avg_hdp'] = $laporanData->avg('hdp');
            }
        }
        
        if ($request->has('download_pdf')) { 
            return view('admin.analytic.cetak_laporan_pdf', compact('laporanData', 'summary', 'request')); 
        }
        
        return view('admin.analytic.laporan_pdf', compact('units', 'laporanData', 'summary', 'batches'));
    }

    public function mandorData(Request $request) {
        $user = Auth::user();
        $query = DailyRecord::with(['kandang.unit', 'pakan', 'siklus.batch'])->where('user_id', $user->id)->latest('tanggal');
        if ($request->filled('date')) { $query->whereDate('tanggal', $request->date); }
        if ($request->filled('kandang_id')) { $query->where('kandang_id', $request->kandang_id); }
        $data = $query->paginate(10);
        $units = Unit::with('kandangs')->where('id', $user->unit_id)->get();
        return view('mandor.produksi.data', compact('data', 'units'));
    }

    public function ownerData(Request $request) { 
        $data = $this->getDailyRecords($request); 
        $units = Unit::all(); 
        return view('owner.analytic.data_input', compact('data', 'units')); 
    }
    
    public function ownerLaporan(Request $request) { 
        $units = Unit::with('kandangs')->get();
        $laporanData = collect([]);
        $summary = ['total_telur_kg' => 0, 'total_pakan_kg' => 0, 'avg_fcr' => 0, 'avg_hdp' => 0];
        $batches = Batch::orderBy('created_at', 'desc')->get(); 

        if ($request->has('filter')) {
            $query = DailyRecord::with(['kandang.unit', 'pakan', 'siklus.batch'])->orderBy('tanggal', 'asc');
            
            if ($request->dateStart && $request->dateEnd) { 
                $query->whereBetween('tanggal', [$request->dateStart, $request->dateEnd]); 
            } elseif ($request->dateStart) { 
                $query->whereDate('tanggal', $request->dateStart); 
            }
            
            if ($request->unit && $request->unit != 'all') { $query->where('unit_id', $request->unit); }
            if ($request->kandang && $request->kandang != 'all') { $query->where('kandang_id', $request->kandang); }
            
            if ($request->batch_id && $request->batch_id != 'all') {
                $query->whereHas('siklus', function($q) use ($request) {
                    $q->where('batch_id', $request->batch_id);
                });
            }
            
            $laporanData = $query->get();

            if ($laporanData->count() > 0) {
                $summary['total_telur_kg'] = $laporanData->sum('telur_kg');
                $summary['total_pakan_kg'] = $laporanData->sum('pakan_kg');
                $summary['avg_fcr'] = $summary['total_telur_kg'] > 0 ? $summary['total_pakan_kg'] / $summary['total_telur_kg'] : 0;
                $summary['avg_hdp'] = $laporanData->avg('hdp');
            }
        }

        if ($request->has('download_pdf')) { 
            return view('admin.analytic.cetak_laporan_pdf', compact('laporanData', 'summary', 'request')); 
        }

        return view('owner.analytic.laporan_pdf', compact('units', 'laporanData', 'summary', 'batches')); 
    }
}