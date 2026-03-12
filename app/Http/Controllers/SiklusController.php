<?php

namespace App\Http\Controllers;

use App\Models\Kandang;
use App\Models\Siklus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiklusController extends Controller
{
    /**
     * Handle CHICK-IN (Mulai Siklus Baru)
     * Digunakan pada tombol "+ Chick In" di Tabel Data Kandang
     */
    public function store(Request $request, $kandangId)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal_chick_in' => 'required|date',
            'populasi_awal' => 'required|integer|min:1',
            'umur_awal_minggu' => 'required|integer|min:0',
            // [BARU] Wajib pilih Batch dari dropdown
            'batch_id' => 'required|exists:batches,id', 
            'jenis_ayam' => 'nullable|string',
        ]);

        $kandang = Kandang::findOrFail($kandangId);

        // 2. Cek apakah kandang masih ada siklus aktif?
        if ($kandang->siklusAktif) {
            return back()->with('error', 'Gagal Chick-In! Kandang ini masih memiliki siklus aktif. Harap lakukan Afkir/Tutup Siklus terlebih dahulu.');
        }

        DB::transaction(function () use ($request, $kandang) {
            // A. Buat Data Siklus Baru (Terhubung ke Batch)
            Siklus::create([
                'kandang_id' => $kandang->id,
                'batch_id' => $request->batch_id, // Simpan ID Batch
                'tanggal_chick_in' => $request->tanggal_chick_in,
                'jenis_ayam' => $request->jenis_ayam ?? 'Layer',
                'populasi_awal' => $request->populasi_awal,
                'umur_awal_minggu' => $request->umur_awal_minggu,
                'status' => 'Aktif',
            ]);

            // B. Update Data Master Kandang (Sinkronisasi agar Dashboard Realtime)
            $kandang->update([
                'stok_awal' => $request->populasi_awal,
                'stok_saat_ini' => $request->populasi_awal,
                'tgl_masuk' => $request->tanggal_chick_in,
                'umur_awal' => $request->umur_awal_minggu,
                'status' => 'aktif' // Pastikan status string kecil semua biar konsisten
            ]);
        });

        return back()->with('success', 'Chick-In Berhasil! Siklus baru telah dimulai.');
    }

    /**
     * Handle AFKIR (Tutup Siklus / Kosongkan Kandang)
     */
    public function afkir(Request $request, $kandangId)
    {
        $kandang = Kandang::findOrFail($kandangId);
        $siklus = $kandang->siklusAktif;

        if (!$siklus) {
            return back()->with('error', 'Tidak ada siklus aktif di kandang ini.');
        }

        DB::transaction(function () use ($kandang, $siklus) {
            // A. Tutup Siklus (Arsipkan)
            $siklus->update([
                'status' => 'Selesai',
                'tanggal_selesai' => now(),
                'total_afkir' => $kandang->stok_saat_ini, // Sisa ayam dianggap afkir semua
            ]);

            // B. Kosongkan Kandang Fisik
            $kandang->update([
                'stok_saat_ini' => 0,
                'status' => 'kosong'
            ]);
        });

        return back()->with('success', 'Siklus ditutup. Kandang kini status KOSONG.');
    }
}