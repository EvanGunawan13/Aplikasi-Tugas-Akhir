@extends('layouts.app')

@section('page_title', 'Edit Rak: ' . $rack->code)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="mb-5 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Edit Rak Gudang</h2>
                <p class="text-xs text-slate-500">Perbarui informasi lokasi rak {{ $rack->code }}</p>
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

        <form method="POST" action="{{ route('racks.update', $rack) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Rak (Unik) *</label>
                <input type="text" name="code" value="{{ old('code', $rack->code) }}" required 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-mono py-2.5 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Rak *</label>
                <input type="text" name="name" value="{{ old('name', $rack->name) }}" required 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Zona / Area Gudang</label>
                <input type="text" name="zone" value="{{ old('zone', $rack->zone) }}" 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan / Deskripsi</label>
                <textarea name="description" rows="3" 
                          class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">{{ old('description', $rack->description) }}</textarea>
            </div>

            <div class="flex items-center">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $rack->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="ms-2 text-xs font-medium text-slate-700">Status Rak Aktif</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition">
                    Hapus Rak
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('racks.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                        Perbarui Rak
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" method="POST" action="{{ route('racks.destroy', $rack) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus rak ini?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection
