@extends('layouts.app')

@section('page_title', 'Tambah Rak Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="mb-5 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Tambah Rak Gudang</h2>
                <p class="text-xs text-slate-500">Buat kode rak baru untuk memudahkan penataan suku cadang</p>
            </div>
            <a href="{{ route('racks.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
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

        <form method="POST" action="{{ route('racks.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Rak (Unik) *</label>
                <input type="text" name="code" value="{{ old('code') }}" required placeholder="Contoh: A-01-3, B-02-1" 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-mono py-2.5 px-3">
                <p class="text-[11px] text-slate-400 mt-1">Gunakan format standar zona-lorong-tingkat.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Rak *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Rak Baut M10 - M14" 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Zona / Area Gudang</label>
                <input type="text" name="zone" value="{{ old('zone') }}" placeholder="Contoh: Zona B (Fastener & Hardware)" 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan / Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Informasi tambahan mengenai kapasitas atau jenis barang..." 
                          class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="ms-2 text-xs font-medium text-slate-700">Status Rak Aktif</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('racks.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                    Simpan Rak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
