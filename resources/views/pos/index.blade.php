@extends('layouts.app')

@section('page_title', 'Kasir: Point of Sale (POS)')

@section('content')
<div class="h-[calc(100vh-7.5rem)] flex flex-col" x-data="posApp()">
    <!-- Top Bar Quick Info & Keyboard Shortcuts -->
    <div class="bg-white px-4 py-2.5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2 text-xs font-semibold text-slate-600">
            <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-mono">F2</span>
            <span>Cari</span>
            <span class="text-slate-300">|</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-mono">F9</span>
            <span>Bayar</span>
            <span class="text-slate-300">|</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-mono">Esc</span>
            <span>Reset</span>
        </div>
        <div class="text-xs text-slate-500 font-medium">
            Kasir: <strong>{{ auth()->user()->name }}</strong> &bull; {{ date('d M Y') }}
        </div>
    </div>

    <!-- Layout 2 Kolom POS -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-5 min-h-0">
        <!-- Panel Kiri: Pencarian & Grid Barang (7 Kolom) -->
        <div class="lg:col-span-7 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-4">
            <!-- Input Cari Barang Instan -->
            <div class="relative mb-3">
                <input type="text" x-ref="searchInput" x-model="searchQuery" @input.debounce.300ms="searchItems()" 
                       placeholder="Cari suku cadang (Kode, Nama, atau Part Number)... [F2]" 
                       class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-medium">
                <div class="absolute left-3.5 top-3.5 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- List Hasil Pencarian / Katalog -->
            <div class="flex-1 overflow-y-auto pr-1 space-y-2.5">
                <template x-if="isLoading">
                    <div class="py-12 text-center text-slate-400">
                        <svg class="animate-spin h-6 w-6 mx-auto text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <p class="text-xs">Memuat katalog suku cadang...</p>
                    </div>
                </template>

                <template x-if="!isLoading && products.length === 0">
                    <div class="py-12 text-center text-slate-400">
                        <p class="text-sm font-semibold">Tidak ada suku cadang ditemukan.</p>
                        <p class="text-xs mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                    </div>
                </template>

                <template x-for="product in products" :key="product.id">
                    <div class="p-3 rounded-xl border border-slate-100 hover:border-blue-300 hover:bg-blue-50/40 transition flex items-center justify-between gap-3">
                        <!-- Thumbnail Foto & Modal Katalog -->
                        <div class="relative w-14 h-14 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 cursor-pointer group"
                             @click="openCatalogModal(product)">
                            <img :src="product.photo_url" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>

                        <!-- Info Barang & Rak -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-mono font-bold text-xs" x-text="'Rak: ' + product.rack_code"></span>
                                <span class="text-xs text-slate-400 font-mono" x-text="product.code"></span>
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm truncate mt-0.5" x-text="product.name"></h4>
                            <p class="text-[11px] text-slate-400 font-mono" x-text="'Part: ' + product.part_number"></p>
                        </div>

                        <!-- Stok & Harga & Tombol Tambah -->
                        <div class="flex items-center gap-4 text-right">
                            <div>
                                <span class="text-[11px] block font-semibold" :class="product.stock <= 0 ? 'text-rose-600' : 'text-slate-500'" x-text="'Stok: ' + product.stock + ' ' + product.unit"></span>
                                <span class="font-extrabold text-blue-700 text-sm" x-text="formatRupiah(product.price)"></span>
                            </div>
                            <button type="button" @click="addToCart(product)" :disabled="product.stock <= 0" 
                                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tambah</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Panel Kanan: Keranjang & Checkout (5 Kolom) -->
        <div class="lg:col-span-5 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Keranjang Belanja
                </h3>
                <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-600 hover:underline font-semibold">
                    Kosongkan
                </button>
            </div>

            <!-- Nama Pelanggan (Opsional) -->
            <div class="mb-3">
                <input type="text" x-model="customerName" placeholder="Nama Pelanggan (Opsional, cth: Pak Joko Bengkel)" 
                       class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
            </div>

            <!-- List Item Keranjang -->
            <div class="flex-1 overflow-y-auto space-y-2 pr-1 mb-3">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-slate-300 py-12">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="text-xs font-semibold text-slate-400">Keranjang masih kosong</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pilih barang di panel sebelah kiri</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3 text-xs">
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-slate-800 truncate" x-text="item.name"></h5>
                            <span class="text-[11px] text-blue-600 font-semibold" x-text="formatRupiah(item.price)"></span>
                        </div>

                        <!-- Pengatur Qty -->
                        <div class="flex items-center gap-1">
                            <button type="button" @click="decreaseQty(index)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 flex items-center justify-center font-bold text-slate-600">
                                -
                            </button>
                            <input type="number" x-model.number="item.qty" @change="validateQty(index)" min="1" :max="item.stock" 
                                   class="w-12 text-center text-xs py-1 px-1 rounded-lg border-slate-200 font-bold text-slate-800">
                            <button type="button" @click="increaseQty(index)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 flex items-center justify-center font-bold text-slate-600">
                                +
                            </button>
                        </div>

                        <div class="text-right min-w-[70px]">
                            <span class="font-extrabold text-slate-800 block" x-text="formatRupiah(item.price * item.qty)"></span>
                            <button type="button" @click="removeItem(index)" class="text-[10px] text-rose-500 hover:underline">Hapus</button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Bagian Biaya Pengantaran & Ringkasan -->
            <div class="pt-3 border-t border-slate-200 space-y-2 text-xs">
                <!-- Toggle Biaya Pengantaran -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="hasDeliveryFee" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="ms-2 font-semibold text-slate-700">Biaya Pengantaran (Ongkir)</span>
                    </label>
                    <input type="number" x-show="hasDeliveryFee" x-model.number="deliveryFee" min="0" step="1000" placeholder="Rp 0" 
                           class="w-28 text-xs py-1 px-2 rounded-lg border-slate-200 text-right font-bold text-slate-800">
                </div>

                <!-- Subtotal & Grand Total -->
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal Barang:</span>
                    <strong class="font-mono" x-text="formatRupiah(calculateSubtotal())"></strong>
                </div>
                <div class="flex justify-between text-slate-600" x-show="hasDeliveryFee && deliveryFee > 0">
                    <span>Ongkir Pengantaran:</span>
                    <strong class="font-mono" x-text="formatRupiah(deliveryFee)"></strong>
                </div>
                <div class="flex justify-between text-base font-extrabold text-blue-900 pt-1 border-t border-slate-200">
                    <span>TOTAL BAYAR:</span>
                    <span class="text-lg text-blue-600" x-text="formatRupiah(calculateGrandTotal())"></span>
                </div>
            </div>

            <!-- Tombol Bayar Sekarang -->
            <div class="pt-3">
                <button type="button" @click="openPaymentModal()" :disabled="cart.length === 0" 
                        class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>BAYAR SEKARANG [F9]</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran (F9) -->
    <div x-show="paymentModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="paymentModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-lg">Penyelesaian Pembayaran</h3>
                <button @click="paymentModalOpen = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>

            <div class="text-center p-4 rounded-xl bg-blue-50 border border-blue-100">
                <span class="text-xs text-blue-600 font-semibold uppercase tracking-wider block">Total Tagihan</span>
                <span class="text-3xl font-black text-blue-900 mt-1 block" x-text="formatRupiah(calculateGrandTotal())"></span>
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Metode Pembayaran</label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="paymentMethod = 'tunai'" :class="paymentMethod === 'tunai' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-700'" class="py-2 text-xs rounded-xl transition">
                        Tunai / Cash
                    </button>
                    <button type="button" @click="paymentMethod = 'transfer'" :class="paymentMethod === 'transfer' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-700'" class="py-2 text-xs rounded-xl transition">
                        Transfer Bank
                    </button>
                    <button type="button" @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-700'" class="py-2 text-xs rounded-xl transition">
                        QRIS
                    </button>
                </div>
            </div>

            <!-- Nominal Uang Diterima & Tombol Pintasan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal Uang Diterima (Rp)</label>
                <input type="number" x-ref="paidInput" x-model.number="paidAmount" min="0" step="1000" 
                       class="w-full text-lg font-bold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">

                <!-- Tombol Pintasan Uang Cepat -->
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <button type="button" @click="paidAmount = calculateGrandTotal()" class="py-1.5 px-2 bg-slate-100 hover:bg-blue-50 text-[11px] font-bold text-slate-700 rounded-lg">
                        Uang Pas
                    </button>
                    <button type="button" @click="paidAmount = 50000" class="py-1.5 px-2 bg-slate-100 hover:bg-blue-50 text-[11px] font-bold text-slate-700 rounded-lg">
                        Rp 50.000
                    </button>
                    <button type="button" @click="paidAmount = 100000" class="py-1.5 px-2 bg-slate-100 hover:bg-blue-50 text-[11px] font-bold text-slate-700 rounded-lg">
                        Rp 100.000
                    </button>
                </div>
            </div>

            <!-- Kembalian -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">Kembalian:</span>
                <span class="text-lg font-black" :class="calculateChange() < 0 ? 'text-rose-600' : 'text-emerald-700'" x-text="formatRupiah(calculateChange())"></span>
            </div>

            <!-- Tombol Proses Transaksi -->
            <div class="pt-2">
                <button type="button" @click="processPayment()" :disabled="isSubmitting || calculateChange() < 0" 
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    <span x-show="!isSubmitting">PROSES & CETAK NOTA</span>
                    <span x-show="isSubmitting">Memproses Transaksi...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Foto dan Katalog Suku Cadang -->
    <div x-show="catalogModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="catalogModalOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="px-2 py-0.5 text-xs font-mono font-bold rounded bg-blue-100 text-blue-800" x-text="'Rak: ' + selectedCatalogItem?.rack_code"></span>
                    <h3 class="font-bold text-slate-800 text-base mt-1" x-text="selectedCatalogItem?.name"></h3>
                </div>
                <button @click="catalogModalOpen = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>

            <div class="w-full h-64 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center border border-slate-200">
                <img :src="selectedCatalogItem?.photo_url" class="w-full h-full object-cover">
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-3 rounded-xl">
                <div><span class="text-slate-400 block">Kode Barang:</span> <strong class="font-mono" x-text="selectedCatalogItem?.code"></strong></div>
                <div><span class="text-slate-400 block">Part Number:</span> <strong class="font-mono" x-text="selectedCatalogItem?.part_number"></strong></div>
                <div><span class="text-slate-400 block">Stok Gudang:</span> <strong x-text="selectedCatalogItem?.stock + ' ' + selectedCatalogItem?.unit"></strong></div>
                <div><span class="text-slate-400 block">Harga Jual:</span> <strong class="text-blue-700" x-text="formatRupiah(selectedCatalogItem?.price)"></strong></div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="button" @click="catalogModalOpen = false" class="px-4 py-2 bg-slate-800 text-white font-semibold text-xs rounded-xl">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function posApp() {
        return {
            searchQuery: '',
            isLoading: false,
            products: [],
            cart: [],
            customerName: '',
            hasDeliveryFee: false,
            deliveryFee: 0,
            paymentModalOpen: false,
            catalogModalOpen: false,
            selectedCatalogItem: null,
            paymentMethod: 'tunai',
            paidAmount: 0,
            isSubmitting: false,
            idempotencyKey: '',

            init() {
                this.generateIdempotencyKey();
                this.searchItems();

                // Setup Keyboard Shortcuts F2, F9, Esc
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        this.$refs.searchInput.focus();
                    } else if (e.key === 'F9') {
                        e.preventDefault();
                        if (this.cart.length > 0) {
                            this.openPaymentModal();
                        }
                    } else if (e.key === 'Escape') {
                        this.paymentModalOpen = false;
                        this.catalogModalOpen = false;
                    }
                });
            },

            generateIdempotencyKey() {
                this.idempotencyKey = 'pos-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9);
            },

            searchItems() {
                this.isLoading = true;
                fetch(`/api/items/search?q=${encodeURIComponent(this.searchQuery)}`)
                    .then(res => res.json())
                    .then(data => {
                        this.products = data;
                        this.isLoading = false;
                    })
                    .catch(() => {
                        this.isLoading = false;
                    });
            },

            addToCart(product) {
                const existing = this.cart.find(i => i.id === product.id);
                if (existing) {
                    if (existing.qty < product.stock) {
                        existing.qty++;
                    } else {
                        alert(`Stok ${product.name} tidak mencukupi (tersedia ${product.stock}).`);
                    }
                } else {
                    this.cart.push({
                        id: product.id,
                        code: product.code,
                        name: product.name,
                        price: product.price,
                        stock: product.stock,
                        unit: product.unit,
                        qty: 1
                    });
                }
            },

            increaseQty(index) {
                const item = this.cart[index];
                if (item.qty < item.stock) {
                    item.qty++;
                } else {
                    alert(`Stok tidak mencukupi (tersedia ${item.stock}).`);
                }
            },

            decreaseQty(index) {
                if (this.cart[index].qty > 1) {
                    this.cart[index].qty--;
                } else {
                    this.removeItem(index);
                }
            },

            validateQty(index) {
                const item = this.cart[index];
                if (item.qty > item.stock) {
                    alert(`Stok ${item.name} hanya tersedia ${item.stock}.`);
                    item.qty = item.stock;
                }
                if (item.qty < 1 || isNaN(item.qty)) {
                    item.qty = 1;
                }
            },

            removeItem(index) {
                this.cart.splice(index, 1);
            },

            clearCart() {
                if (confirm('Kosongkan semua barang di keranjang?')) {
                    this.cart = [];
                }
            },

            calculateSubtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            },

            calculateGrandTotal() {
                const ongkir = this.hasDeliveryFee ? Number(this.deliveryFee || 0) : 0;
                return this.calculateSubtotal() + ongkir;
            },

            calculateChange() {
                return Number(this.paidAmount || 0) - this.calculateGrandTotal();
            },

            openPaymentModal() {
                this.paidAmount = this.calculateGrandTotal();
                this.paymentModalOpen = true;
                this.$nextTick(() => {
                    this.$refs.paidInput.select();
                });
            },

            openCatalogModal(product) {
                this.selectedCatalogItem = product;
                this.catalogModalOpen = true;
            },

            processPayment() {
                if (this.isSubmitting) return;
                this.isSubmitting = true;

                const payload = {
                    customer_name: this.customerName,
                    delivery_fee: this.hasDeliveryFee ? this.deliveryFee : 0,
                    paid_amount: this.paidAmount,
                    payment_method: this.paymentMethod,
                    idempotency_key: this.idempotencyKey,
                    items: this.cart.map(i => ({ product_id: i.id, qty: i.qty }))
                };

                fetch('{{ route("pos.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `/pos/success/${data.sale_id}`;
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat memproses penjualan.');
                        this.isSubmitting = false;
                    }
                })
                .catch(err => {
                    alert('Gagal memproses transaksi: ' + err.message);
                    this.isSubmitting = false;
                });
            },

            formatRupiah(num) {
                return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            }
        };
    }
</script>
@endpush
