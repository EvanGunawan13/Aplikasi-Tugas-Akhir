@extends('layouts.app')

@section('page_title', 'Edit Pemasok')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="mb-5 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Edit Pemasok</h2>
                <p class="text-xs text-slate-500">Perbarui informasi {{ $supplier->name }}</p>
            </div>
            <a href="{{ route('suppliers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
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

        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Perusahaan Pemasok *</label>
                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required 
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kontak Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email) }}" 
                           class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat</label>
                <textarea name="address" rows="3" 
                          class="w-full text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">{{ old('address', $supplier->address) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition">
                    Hapus Pemasok
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('suppliers.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                        Perbarui Pemasok
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus pemasok ini?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endsection
