<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    // Fungsi Index (Opsional jika butuh halaman khusus list batch)
    public function index()
    {
        $batches = Batch::withCount(['siklus' => function($q) {
            $q->where('status', 'Aktif');
        }])->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.batch.index', compact('batches'));
    }

    // Fungsi Simpan Batch Baru
    public function store(Request $request)
    {
        // [FIXED] Tambahkan validasi 'unique'
        $request->validate([
            'nama_batch' => 'required|string|max:255|unique:batches,nama_batch',
            'tanggal_mulai' => 'nullable|date',
        ], [
            // Pesan error kustom bahasa Indonesia
            'nama_batch.unique' => 'Gagal! Nama Batch/Siklus ini sudah ada. Gunakan nama lain.',
            'nama_batch.required' => 'Nama Batch wajib diisi.'
        ]);

        Batch::create([
            'nama_batch' => $request->nama_batch,
            'tanggal_mulai' => $request->tanggal_mulai ?? now(),
            'is_active' => true
        ]);

        return back()->with('success', 'Master Batch berhasil dibuat! Silakan pilih di form kanan.');
    }

    // Fungsi Hapus Batch
    public function destroy($id)
    {
        $batch = Batch::findOrFail($id);
        
        // Cek apakah batch sudah dipakai di kandang manapun?
        if($batch->siklus()->exists()) {
            return back()->with('error', 'Gagal hapus! Batch ini sedang digunakan oleh kandang yang aktif.');
        }
        
        $batch->delete();
        return back()->with('success', 'Batch berhasil dihapus.');
    }
}