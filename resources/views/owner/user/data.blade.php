<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User - Owner Panel</title>
    
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-poppins'],
                        poppins: ['"Playfair Display"', 'poppins'],
                    },
                    colors: {
                        cemara: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' }
                    }
                }
            }
        }
    </script>
    <style>
        .table-container::-webkit-scrollbar { height: 8px; }
        .table-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        th, td { white-space: nowrap; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans" x-data="{ showModalEdit: false, editData: {} }">

    {{-- LOGIKA PENGAMBILAN DATA --}}
    @php
        // Ambil data Unit untuk Dropdown Edit (agar dinamis)
        if(!isset($units)) {
             $units = \App\Models\Unit::all();
        }

        // Ambil data User dengan relasi Unit (jika ada relasi di model User)
        // Pastikan Model User punya method public function unit() { return $this->belongsTo(Unit::class); }
        if(!isset($users)) {
            $query = \App\Models\User::with('unit')->orderBy('created_at', 'desc');
            
            if(request('role') && request('role') != 'Semua Role') {
                $query->where('role', request('role'));
            }
            if(request('status') && request('status') != 'Semua Status') {
                $query->where('status', request('status'));
            }
            $users = $query->get();

            if(request('search')) {
                $search = strtolower(request('search'));
                $users = $users->filter(function($item) use ($search) {
                    return str_contains(strtolower($item->name), $search) || str_contains(strtolower($item->email), $search);
                });
            }
        }
    @endphp

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Overlay Mobile -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Sidebar Owner -->
        @include('owner.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col h-full bg-gray-50 overflow-y-auto w-full transition-all duration-300">
            
            <!-- Header -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                        <span>Pengguna</span>
                        <i class="ph-bold ph-caret-right"></i>
                        <span>Data User</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins">Daftar Pengguna Sistem</h2>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:text-cemara-900 rounded-lg">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                    <!-- Tombol Tambah (Create) -->
                    <a href="{{ route('owner.user.input') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-cemara-900 text-white rounded-lg text-sm font-semibold hover:bg-cemara-800 transition shadow-lg shadow-cemara-900/20">
                        <i class="ph-bold ph-user-plus"></i> Tambah User
                    </a>
                </div>
            </header>

            <!-- Content Body -->
            <div class="p-4 md:p-8 w-full">
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 font-bold flex items-center gap-2 shadow-sm">
                        <i class="ph-fill ph-check-circle text-xl"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 font-bold flex items-center gap-2 shadow-sm">
                        <i class="ph-fill ph-warning-circle text-xl"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Filter & Search Section -->
                <form action="{{ route('owner.user.data') }}" method="GET" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative group">
                            <select name="role" onchange="this.form.submit()" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-gold-500 outline-none cursor-pointer">
                                <option value="Semua Role">Semua Role</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="mandor" {{ request('role') == 'mandor' ? 'selected' : '' }}>Mandor</option>
                                <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                            </select>
                        </div>
                        <div class="relative group">
                            <select name="status" onchange="this.form.submit()" class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-gold-500 outline-none cursor-pointer">
                                <option value="Semua Status">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg text-sm font-semibold hover:bg-gold-600 transition shadow-md">
                            Cari
                        </button>
                    </div>
                    <div class="relative w-full md:w-64">
                        <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gold-500 outline-none">
                    </div>
                </form>

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">#</th>
                                    <th class="px-6 py-4">User Info</th>
                                    <th class="px-6 py-4">Role</th>
                                    <th class="px-6 py-4">Email</th>
                                    
                                    {{-- PERBAIKAN: Kolom dipisah agar lebih jelas --}}
                                    <th class="px-6 py-4">Unit Tugas</th>
                                    <th class="px-6 py-4">Lokasi</th>
                                    
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                
                                @forelse($users as $index => $user)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-center">{{ $loop->iteration }}</td>
                                        
                                        <!-- User Info -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @php
                                                    if ($user->avatar) {
                                                        $avatarUrl = asset('storage/' . $user->avatar);
                                                    } else {
                                                        if ($user->role == 'owner') {
                                                            $avatarUrl = 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
                                                        } elseif ($user->role == 'admin') {
                                                            $avatarUrl = 'https://cdn-icons-png.flaticon.com/512/2304/2304226.png';
                                                        } else { // Mandor
                                                            $avatarUrl = 'https://cdn-icons-png.flaticon.com/512/3866/3866122.png';
                                                        }
                                                    }
                                                @endphp
                                                <img src="{{ $avatarUrl }}" alt="{{ $user->role }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 bg-white p-0.5">
                                                <div>
                                                    <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                                    <p class="text-[10px] text-gray-400">Join: {{ $user->created_at->format('d M Y') }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Role -->
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize
                                                {{ $user->role == 'admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : 
                                                   ($user->role == 'mandor' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-gold-50 text-gold-700 border-gold-200') }}">
                                                {{ $user->role }}
                                            </span>
                                        </td>

                                        <!-- Email -->
                                        <td class="px-6 py-4 font-mono text-xs text-gray-600">
                                            {{ $user->email }}
                                        </td>

                                        <!-- Unit Tugas (KOLOM TERPISAH) -->
                                        <td class="px-6 py-4">
                                            @if($user->role == 'mandor')
                                                <span class="font-bold text-gray-700">
                                                    {{ optional($user->unit)->nama_unit ?? '-' }}
                                                </span>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>

                                        <!-- Lokasi (KOLOM TERPISAH) -->
                                        <td class="px-6 py-4">
                                            @if($user->role == 'mandor')
                                                <div class="flex items-center gap-1 text-gray-600">
                                                    <i class="ph-fill ph-map-pin text-gold-500"></i>
                                                    <span>{{ optional($user->unit)->lokasi ?? '-' }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button onclick="openEditModal({{ json_encode($user) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                                    <i class="ph-bold ph-pencil-simple text-lg"></i>
                                                </button>
                                                
                                                @if(auth()->id() !== $user->id && $user->role !== 'owner')
                                                    <button type="button" onclick="confirmDelete({{ $user->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                        <i class="ph-bold ph-trash text-lg"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $user->id }}" action="{{ route('owner.user.delete', $user->id) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @else
                                                    <span class="p-1.5 text-gray-300 cursor-not-allowed" title="User Dilindungi">
                                                        <i class="ph-bold ph-lock-key text-lg"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada data user. Silakan tambah user baru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Info -->
                <div class="p-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-sm text-gray-500">Menampilkan {{ isset($users) ? $users->count() : 0 }} data user</p>
                </div>

            </div>
        </main>

        <!-- MODAL EDIT USER -->
        <div x-show="showModalEdit" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModalEdit = false"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl relative z-50 transform transition-all"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center rounded-t-2xl">
                        <h3 class="font-bold text-lg text-gray-800">Edit Data User</h3>
                        <button @click="showModalEdit = false" class="text-gray-400 hover:text-red-500 transition"><i class="ph-bold ph-x text-xl"></i></button>
                    </div>

                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" name="name" x-model="editData.name" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" x-model="editData.email" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Password Baru (Opsional)</label>
                                    <input type="password" name="password" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-gold-500 outline-none" placeholder="Isi jika ingin ganti">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Role</label>
                                    <select name="role" x-model="editData.role" class="w-full px-4 py-3 bg-white border-2 border-gold-100 rounded-xl font-bold cursor-pointer text-gray-800">
                                        <option value="mandor">Mandor Lapangan</option>
                                        <option value="admin">Administrator</option>
                                        <option value="owner">Owner</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Unit (Hanya Mandor) -->
                            <div x-show="editData.role === 'mandor'" class="p-4 bg-gold-50 rounded-xl border border-gold-100">
                                <h4 class="text-xs font-bold text-gold-700 uppercase mb-3 flex items-center gap-2"><i class="ph-fill ph-map-pin"></i> Update Wilayah</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1">Unit & Lokasi</label>
                                        <div class="relative">
                                            <select name="unit_id" x-model="editData.unit_id" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gold-500 outline-none">
                                                <option value="">Pilih Unit...</option>
                                                @foreach($units as $u)
                                                    <option value="{{ $u->id }}">{{ $u->nama_unit }} ({{ $u->lokasi }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="showModalEdit = false" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-white transition text-sm">Batal</button>
                            <button type="submit" class="px-8 py-2.5 rounded-xl bg-cemara-900 text-white font-bold hover:bg-cemara-800 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 text-sm">
                                <i class="ph-bold ph-check-circle"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Script SweetAlert & Sidebar -->
    <script>
        function openEditModal(userData) {
            const alpineData = Alpine.$data(document.querySelector('[x-data]'));
            
            alpineData.editData = {
                id: userData.id,
                name: userData.name,
                email: userData.email,
                role: userData.role,
                unit_id: userData.unit_id || ''
            };

            const form = document.getElementById('editForm');
            form.action = "{{ url('/owner/user/update') }}/" + userData.id;

            alpineData.showModalEdit = true;
        }

        function confirmDelete(userId) {
            Swal.fire({
                title: 'Hapus User?',
                text: "User ini tidak akan bisa login lagi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + userId).submit();
                }
            })
        }
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) {
                if (overlay.classList.contains('hidden')) {
                    overlay.classList.remove('hidden');
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                } else {
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }
        }
    </script>
</body>
</html>