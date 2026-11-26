@extends('layouts.app')

@section('page_title', 'Kelola Data Pemasok')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Pemasok (Suppliers)</h2>
            <p class="text-xs text-slate-500">Kelola informasi vendor distributor suku cadang alat berat</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Pemasok Baru
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pemasok, kontak person, telepon..." 
                   class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl">
                Cari
            </button>
        </form>
    </div>

    <!-- Tabel Pemasok -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase border-b border-slate-200">
                        <th class="py-3.5 px-4">Nama Perusahaan Pemasok</th>
                        <th class="py-3.5 px-4">Kontak Person</th>
                        <th class="py-3.5 px-4">No. Telepon / HP</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Alamat</th>
                        <th class="py-3.5 px-4 text-center">Riwayat Pembelian</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($suppliers as $s)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-800">{{ $s->name }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $s->contact_person ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-600">{{ $s->phone ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">{{ $s->email ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-xs text-slate-500 max-w-xs truncate">{{ $s->address ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-700">
                                    {{ $s->purchases_count }} Faktur
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('suppliers.edit', $s) }}" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                Belum ada data pemasok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection
