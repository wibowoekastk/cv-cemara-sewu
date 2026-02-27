<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // PENTING: Untuk menangani file upload

class UserController extends Controller
{
    // ================================================================
    // [BARU] METHOD UNTUK HALAMAN DASHBOARD STATISTIK USER
    // ================================================================
    public function dashboardUser()
    {
        // 1. Hitung Statistik (Langsung query count agar ringan)
        $totalUser   = User::count();
        $adminCount  = User::where('role', 'admin')->count();
        $mandorCount = User::where('role', 'mandor')->count();
        $ownerCount  = User::where('role', 'owner')->count();

        // 2. Ambil 5 User Terbaru untuk tabel preview "Aktivitas Terakhir"
        $recentUsers = User::with('unit')->latest()->take(5)->get();

        // 3. Ambil semua users (jika view butuh looping total, opsional)
        $users = User::all();

        // 4. Return ke View Dashboard User (File yang ada grafiknya/kartu)
        // Pastikan nama filenya: resources/views/owner/user/dashboard.blade.php
        return view('owner.user.dashboarduser', compact(
            'users', 
            'totalUser', 
            'adminCount', 
            'mandorCount', 
            'ownerCount', 
            'recentUsers'
        ));
    }

    /**
     * Menyimpan User Baru (Create User dari Owner/Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,owner,mandor',
            'unit_id' => 'nullable|integer',
            'lokasi_id' => 'nullable|integer', // Validasi untuk lokasi_id
        ]);

        // Tentukan Unit & Lokasi (Hanya untuk Mandor)
        $unit_id = ($request->role === 'mandor') ? $request->unit_id : null;
        // Asumsi lokasi_id diambil dari input atau relasi unit
        $lokasi_id = ($request->role === 'mandor') ? $request->lokasi_id : null;
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active', // Default status active
            'unit_id' => $unit_id,
            'lokasi_id' => $lokasi_id, // Pastikan kolom ini ada di tabel users
        ]);

        return redirect()->route('owner.user.data')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Menampilkan Data User (Dengan Fitur Search & Filter)
     * Digunakan di halaman Data User
     */
    public function data(Request $request)
    {
        // Menggunakan with('unit') agar query lebih cepat (Eager Loading)
        $query = User::with('unit')->orderBy('created_at', 'desc');

        // Search Logic
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter Role
        if ($request->has('role') && $request->role != 'Semua Role' && $request->role != null) {
            $query->where('role', strtolower($request->role));
        }

        // Filter Status
        if ($request->has('status') && $request->status != 'Semua Status' && $request->status != null) {
             $query->where('status', $request->status);
        }

        $users = $query->paginate(10);
        
        // Ambil data unit untuk dropdown di modal edit
        $units = \App\Models\Unit::all();

        // Tentukan view berdasarkan role yang login atau route prefix
        if (request()->is('owner/*')) {
            return view('owner.user.data', compact('users', 'units'));
        } else {
            return view('admin.user.data', compact('users', 'units'));
        }
    }

    /**
     * Menampilkan Form Edit User
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $units = \App\Models\Unit::all(); 
        return view('owner.user.edit', compact('user', 'units'));
    }

    /**
     * Memproses Update User (Data user lain)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,owner,mandor',
            'unit_id' => 'nullable|integer',
            'lokasi_id' => 'nullable|integer',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Logika penugasan wilayah
        if ($request->role == 'mandor') {
            $user->unit_id = $request->unit_id;
            $user->lokasi_id = $request->lokasi_id; 
        } else {
            $user->unit_id = null;
            $user->lokasi_id = null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('owner.user.data')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Hapus User
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Update Profil Diri Sendiri
     */
    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Logika Upload Foto
        if ($request->hasFile('photo')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('photo')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);
        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update Password Diri Sendiri
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::find(Auth::id());
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah!']);
        }
        
        $user->update(['password' => Hash::make($request->new_password)]);
        
        return redirect()->back()->with('success', 'Password berhasil diubah!');
    }
}