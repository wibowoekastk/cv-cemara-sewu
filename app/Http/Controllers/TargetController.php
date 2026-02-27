<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductionTarget;
use App\Models\Unit; // Pastikan Model Unit di-import
use Carbon\Carbon;

class TargetController extends Controller
{
    /**
     * 1. HALAMAN INPUT TARGET
     */
    public function create()
    {
        $units = Unit::all(); 
        $targets = ProductionTarget::with('unit')->get(); 
        
        return view('admin.targetmandor.inputtarget', compact('units', 'targets'));
    }

    /**
     * 2. HALAMAN RIWAYAT & EVALUASI
     */
    public function riwayat(Request $request)
    {
        $query = ProductionTarget::query();

        // Logic Filter
        if ($request->filled('unit_id') && $request->unit_id != 'Semua Unit') {
            $query->where('unit_id', $request->unit_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $allTargets = $query->orderBy('created_at', 'desc')->get();

        // Pisahkan Active vs History
        $activeTargets = $allTargets->where('status', 'active');
        $historyTargets = $allTargets->where('status', 'history');

        $totalActive = $activeTargets->count();
        $totalHistory = $historyTargets->count();

        // Ambil units untuk filter dropdown
        $units = Unit::all(); 

        return view('admin.targetmandor.riwayattarget', compact(
            'activeTargets', 
            'historyTargets', 
            'totalActive', 
            'totalHistory',
            'units'
        ));
    }

    /**
     * STORE: Simpan Data Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'lokasi_id' => 'required', // Bisa string atau int dari form
            'unit_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_hd' => 'required|numeric',
            'target_egg_weight' => 'required|numeric',
            'target_fcr' => 'required|numeric', 
            'target_bw' => 'required|numeric',
            'target_mortality_percent' => 'required|numeric',
        ]);

        // --- PERBAIKAN UTAMA DI SINI ---
        // Konversi Nama Lokasi menjadi ID (Integer)
        $lokasiId = 1; // Default 1 (Kalirambut)
        $inputLokasi = $request->lokasi_id;

        if ($inputLokasi == 'Sokawangi' || $inputLokasi == 2) {
            $lokasiId = 2;
        }
        // Jika form mengirim "Kalirambut" atau 1, tetap jadi 1.
        // -------------------------------

        // Nonaktifkan target lama di unit yang sama
        ProductionTarget::where('lokasi_id', $lokasiId) // Gunakan ID hasil konversi
            ->where('unit_id', $request->unit_id)
            ->where('status', 'active')
            ->update(['status' => 'history']);

        // Simpan
        ProductionTarget::create([
            'lokasi_id' => $lokasiId, // Gunakan ID hasil konversi
            'unit_id' => $request->unit_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'hd' => $request->target_hd,
            'egg_weight' => $request->target_egg_weight,
            'fcr' => $request->target_fcr, 
            'bw' => $request->target_bw,
            'mortality' => $request->target_mortality_percent,
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Target produksi berhasil disimpan!');
    }

    /**
     * UPDATE: Edit Data
     */
    public function update(Request $request, $id)
    {
        $target = ProductionTarget::findOrFail($id);
        
        $request->validate([
            'start_date' => 'required|date',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        $target->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'hd' => $request->target_hd,
            'egg_weight' => $request->target_egg_weight,
            'fcr' => $request->target_fcr,
            'bw' => $request->target_bw,
            'mortality' => $request->target_mortality_percent,
        ]);

        return redirect()->back()->with('success', 'Target berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $target = ProductionTarget::findOrFail($id);
        $target->delete();
        return redirect()->back()->with('success', 'Target dihapus.');
    }
}