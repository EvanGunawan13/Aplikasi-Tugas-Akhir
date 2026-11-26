@extends('layouts.app')

@section('page_title', 'Kelola Akun Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Pengguna & Hak Akses</h2>
            <p class="text-xs text-slate-500">Kelola akun pegawai, penetapan role (Pemilik, Admin, Petugas Gudang, Kasir), dan status aktif</p>
        </div>
        <a href="{{ route('users.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Tambah Pengguna Baru
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, username, atau email..." 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>
            <div class="flex gap-2">
                <select name="role" class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">Semua Role</option>
                    <option value="pemilik" {{ $role == 'pemilik' ? 'selected' : '' }}>Pemilik</option>
                    <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="petugas_gudang" {{ $role == 'petugas_gudang' ? 'selected' : '' }}>Petugas Gudang</option>
                    <option value="kasir" {{ $role == 'kasir' ? 'selected' : '' }}>Kasir</option>
                </select>
                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-b border-slate-200">
                        <th class="py-3.5 px-4">Nama Lengkap</th>
                        <th class="py-3.5 px-4">Username</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $u)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $u->name }}</span>
                                @if ($u->id === auth()->id())
                                    <span class="text-[10px] text-blue-600 font-semibold">(Akun Anda)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-600">{{ $u->username }}</td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">{{ $u->email }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg uppercase tracking-wide
                                    {{ $u->role === 'pemilik' ? 'bg-purple-100 text-purple-800' : ($u->role === 'admin' ? 'bg-blue-100 text-blue-800' : ($u->role === 'petugas_gudang' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800')) }}">
                                    {{ $u->role_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $u->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('users.edit', $u) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
