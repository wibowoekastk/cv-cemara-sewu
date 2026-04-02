<?php

namespace App\Http\Controllers; 

use App\Http\Controllers\Controller;
#use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductionTarget;
// use App\Models\DailyReport; // Nonaktifkan Model DailyReport sementara
use Carbon\Carbon;

class DashboardMandorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $unitId = $user->unit_id;

        // 1. DATA TARGET
        // Mengambil target aktif dari database berdasarkan Unit Mandor
        $target = ProductionTarget::getActiveTarget($unitId);

        // Jika belum ada target yang diset admin, gunakan nilai default 0
        if (!$target) {
            $target = new ProductionTarget([
                'hd' => 0, 'egg_weight' => 0, 'fcr' => 0, 'bw' => 0, 'mortality' => 0,
                'start_date' => null, 'end_date' => null
            ]);
        }

        // 2. REALISASI HARI INI (DUMMY 0)
        // Karena database daily_reports belum ada, kita set manual ke 0
        // Nanti logika query database akan ditaruh di sini
        $realisasi = [
            'hd' => 0,
            'telur_kg' => 0,
            'bw' => 0,
            'egg_weight' => 0,
            'fcr' => 0,
            'mortality' => 0
        ];

        // 3. GRAFIK (HANYA FORMAT TANGGAL)
        $dates = [];
        $chartSeries = [
            'hd' => [], 'egg_weight' => [], 'fcr' => [], 'bw' => [], 'mortality' => []
        ];

        // Loop 7 hari ke belakang untuk sumbu X grafik
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates[] = $date->format('D, d M'); 

            // Isi data realisasi dengan null (kosong) agar grafik realisasi tidak muncul
            // Hanya grafik target (garis lurus) yang akan muncul di View
            $chartSeries['hd'][] = null;
            $chartSeries['egg_weight'][] = null;
            $chartSeries['fcr'][] = null;
            $chartSeries['bw'][] = null;
            $chartSeries['mortality'][] = null;
        }

        return view('mandor.dashboard', compact('target', 'realisasi', 'chartSeries', 'dates'));
    }
}