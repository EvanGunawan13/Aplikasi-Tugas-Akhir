<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak - 403 Forbidden</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full border border-slate-200 shadow-xl text-center space-y-5">
        <div class="w-20 h-20 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>

        <div>
            <h1 class="text-3xl font-black text-slate-800">403</h1>
            <h2 class="text-lg font-bold text-slate-700 mt-1">Akses Halaman Dibatasi</h2>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Mohon maaf, peran akun Anda tidak memiliki izin untuk mengakses halaman ini. Sistem membatasi fitur berdasarkan kewenangan tugas masing-masing.
            </p>
        </div>

        <div class="pt-2">
            <a href="{{ auth()->check() && auth()->user()->role === 'kasir' ? route('pos.index') : route('dashboard') }}" 
               class="inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</body>
</html>
