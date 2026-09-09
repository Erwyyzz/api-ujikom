
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Peminjam')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #F8F4F0;
        }
        .sidebar {
            transition: all 0.3s ease;
            background: #1E3A5F;
            color: white;
        }
        .sidebar-link {
            transition: all 0.2s ease;
            color: rgba(255, 255, 255, 0.6);
        }
        .sidebar-link:hover {
            transform: translateX(4px);
            color: white;
            background: rgba(224, 122, 95, 0.2);
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #E07A5F, #D96A4F);
            color: white;
            box-shadow: 0 4px 12px rgba(224, 122, 95, 0.3);
        }
        .card-hover {
            transition: all 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        .user-avatar {
            background: linear-gradient(135deg, #E07A5F, #F4A261);
        }
        .btn-primary {
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(224, 122, 95, 0.3);
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #E07A5F; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #D96A4F; }
    </style>
</head>
<body>
<div class="flex h-screen overflow-hidden">
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col shadow-sm hidden md:flex">
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-[#E07A5F] to-[#F4A261] rounded-xl flex items-center justify-center shadow-lg shadow-[#E07A5F]/30">
                <i class="fas fa-user text-white text-lg"></i>
            </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800 tracking-tight">AlatKu</h1>
                    <p class="text-xs text-gray-400">Sistem Peminjaman Alat</p>
                </div>
            </div>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('peminjam.dashboard') }}" 
            class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 {{ request()->routeIs('peminjam.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large w-5 text-center text-lg"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('peminjam.pengembalian.index') }}" 
            class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 {{ request()->routeIs('peminjam.pengembalian.*') ? 'active' : '' }}">
                <i class="fas fa-undo-alt w-5 text-center text-lg"></i>
                <span>Pengembalian Alat</span>
            </a>

            <a href="{{ route('peminjam.riwayat') }}" 
            class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 {{ request()->routeIs('peminjam.riwayat') ? 'active' : '' }}">
                <i class="fas fa-history w-5 text-center text-lg"></i>
                <span>Riwayat</span>
            </a>
        </nav>
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="user-avatar w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                    {{ substr(auth()->user()->name ?? 'P', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name ?? 'Peminjam' }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                <button type="button" onclick="showLogoutModal()" class="text-gray-400 hover:text-red-500 transition" title="Logout">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                </button>
                </form>
            </div>
        </div>
    </aside>
    <div class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-10">
            <div class="h-16 flex items-center justify-between px-6">
                <h2 class="text-lg font-semibold text-gray-800">@yield('header-title', 'Dashboard')</h2>
                <span class="text-sm text-gray-500 hidden sm:block">
                    <i class="far fa-calendar-alt mr-1"></i>
                    {{ date('d F Y') }}
                </span>
            </div>
        </header>
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>


<!-- Modal Konfirmasi Logout -->
<div id="logoutModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 animate-fadeIn">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Konfirmasi Logout</h3>
            <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin keluar dari sistem?</p>
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="closeLogoutModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                Batal
            </button>
            <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                    Ya, Logout
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function showLogoutModal() {
        document.getElementById('logoutModal').classList.remove('hidden');
        document.getElementById('logoutModal').classList.add('flex');
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').classList.add('hidden');
        document.getElementById('logoutModal').classList.remove('flex');
    }
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out;
    }
</style>
</body>
</html>