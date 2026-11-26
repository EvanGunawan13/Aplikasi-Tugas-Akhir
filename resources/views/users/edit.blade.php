@extends('layouts.app')

@section('page_title', 'Edit Pengguna: ' . $user->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="mb-5 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Edit Akun Pengguna</h2>
                <p class="text-xs text-slate-500">Perbarui hak akses atau ganti password untuk {{ $user->username }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username (Unik) *</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-mono py-2.5 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="Password baru..." 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Peran (Role) *</label>
                    <select name="role" required class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="kasir" {{ old('role', $user->role) == 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="petugas_gudang" {{ old('role', $user->role) == 'petugas_gudang' ? 'selected' : '' }}>Petugas Gudang</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pemilik" {{ old('role', $user->role) == 'pemilik' ? 'selected' : '' }}>Pemilik</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="ms-2 text-xs font-semibold text-slate-700">Status Akun Aktif</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                @if ($user->id !== auth()->id())
                    <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition">
                        Hapus Akun
                    </button>
                @else
                    <div></div>
                @endif
                <div class="flex gap-3">
                    <a href="{{ route('users.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                        Perbarui Pengguna
                    </button>
                </div>
            </div>
        </form>

        @if ($user->id !== auth()->id())
            <form id="delete-form" method="POST" action="{{ route('users.destroy', $user) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection
