<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-500/30 mb-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">MANDIRI POS</h2>
        <p class="text-xs text-gray-500 mt-1">CV Mandiri Diesel &bull; Sistem POS & Stok Berbasis Rak Gudang</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Username / Email -->
        <div>
            <label for="login" class="block text-sm font-semibold text-gray-700 mb-1">Username atau Email</label>
            <input id="login" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3" 
                   type="text" name="login" :value="old('login')" required autofocus autocomplete="username" 
                   placeholder="Contoh: pemilik, admin, gudang, kasir" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi</label>
            <input id="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3"
                   type="password"
                   name="password"
                   required autocomplete="current-password" 
                   placeholder="Masukkan kata sandi..." />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-xs text-gray-600">Ingat saya</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                Masuk ke Sistem
            </button>
        </div>
    </form>

    <!-- Quick Role Login Helper (Demo) -->
    <div class="mt-6 pt-5 border-t border-gray-100">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 text-center">Akun Demo (Password: password)</p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button type="button" onclick="fillLogin('pemilik')" class="p-2 border rounded bg-slate-50 hover:bg-blue-50 text-left hover:border-blue-300 transition">
                <span class="font-bold text-gray-800 block">Pemilik</span>
                <span class="text-gray-500">user: pemilik</span>
            </button>
            <button type="button" onclick="fillLogin('admin')" class="p-2 border rounded bg-slate-50 hover:bg-blue-50 text-left hover:border-blue-300 transition">
                <span class="font-bold text-gray-800 block">Admin</span>
                <span class="text-gray-500">user: admin</span>
            </button>
            <button type="button" onclick="fillLogin('gudang')" class="p-2 border rounded bg-slate-50 hover:bg-blue-50 text-left hover:border-blue-300 transition">
                <span class="font-bold text-gray-800 block">Gudang</span>
                <span class="text-gray-500">user: gudang</span>
            </button>
            <button type="button" onclick="fillLogin('kasir')" class="p-2 border rounded bg-slate-50 hover:bg-blue-50 text-left hover:border-blue-300 transition">
                <span class="font-bold text-gray-800 block">Kasir</span>
                <span class="text-gray-500">user: kasir</span>
            </button>
        </div>
    </div>

    <script>
        function fillLogin(role) {
            document.getElementById('login').value = role;
            document.getElementById('password').value = 'password';
        }
    </script>
</x-guest-layout>
