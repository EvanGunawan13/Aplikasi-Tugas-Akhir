<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PartnerCompany;
use App\Models\Sale;
use App\Models\StockAdjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class MandiriPosBusinessLogicTest extends TestCase
{
    // Tidak memakai RefreshDatabase langsung agar tetap menggunakan seeder yang terisi
    
    protected User $pemilik;
    protected User $admin;
    protected User $gudang;
    protected User $kasir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemilik = User::where('username', 'pemilik')->first();
        $this->admin = User::where('username', 'admin')->first();
        $this->gudang = User::where('username', 'gudang')->first();
        $this->kasir = User::where('username', 'kasir')->first();
    }

    /** 1. Uji Pembatasan Akses Role (RBAC) */
    public function test_role_access_restriction(): void
    {
        // Kasir tidak boleh akses kelola akun pengguna (403)
        $response = $this->actingAs($this->kasir)->get(route('users.index'));
        $response->assertStatus(403);

        // Kasir tidak boleh akses kelola data rak (403)
        $response = $this->actingAs($this->kasir)->get(route('racks.index'));
        $response->assertStatus(403);

        // Petugas gudang boleh akses cek stok (200)
        $response = $this->actingAs($this->gudang)->get(route('stock.index'));
        $response->assertStatus(200);

        // Pemilik boleh akses kelola akun pengguna (200)
        $response = $this->actingAs($this->pemilik)->get(route('users.index'));
        $response->assertStatus(200);
    }

    /** 2. Uji Penjualan POS Memotong Stok Otomatis */
    public function test_pos_sale_decreases_stock_and_creates_movement(): void
    {
        $product = Product::where('code', 'OLI-MED-15W40')->first();
        $initialStock = $product->stock;
        $qtyToBuy = 2;

        $key = 'test-idemp-' . Str::random(10);
        $payload = [
            'customer_name' => 'Test Bengkel',
            'delivery_fee' => 15000,
            'paid_amount' => 2000000,
            'payment_method' => 'tunai',
            'idempotency_key' => $key,
            'items' => [
                ['product_id' => $product->id, 'qty' => $qtyToBuy],
            ],
        ];

        $response = $this->actingAs($this->kasir)->post(route('pos.store'), $payload);
        $response->assertRedirect();

        $product->refresh();
        $this->assertEquals($initialStock - $qtyToBuy, $product->stock);

        $this->assertDatabaseHas('sales', [
            'idempotency_key' => $key,
            'customer_name' => 'Test Bengkel',
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'sale',
            'qty_change' => -$qtyToBuy,
        ]);
    }

    /** 3. Uji Penjualan Ditolak Jika Stok Tidak Mencukupi */
    public function test_pos_sale_rejected_when_stock_insufficient(): void
    {
        $product = Product::where('code', 'PST-KIT-6D16')->first();
        $excessiveQty = $product->stock + 100;

        $payload = [
            'customer_name' => 'Test Pembeli',
            'delivery_fee' => 0,
            'paid_amount' => 50000000,
            'payment_method' => 'tunai',
            'idempotency_key' => 'test-insufficient-' . Str::random(10),
            'items' => [
                ['product_id' => $product->id, 'qty' => $excessiveQty],
            ],
        ];

        $response = $this->actingAs($this->kasir)->post(route('pos.store'), $payload);
        $response->assertSessionHas('error');

        $product->refresh();
        $this->assertGreaterThan(0, $product->stock);
    }

    /** 4. Uji Idempotency: Submit Ganda Tidak Memotong Stok Dua Kali */
    public function test_pos_idempotency_prevents_double_stock_deduction(): void
    {
        $product = Product::where('code', 'GRS-HITEMP-1KG')->first();
        $initialStock = $product->stock;
        $idempotencyKey = 'idemp-duplicate-' . Str::random(10);

        $payload = [
            'customer_name' => 'Pelanggan Tetap',
            'delivery_fee' => 0,
            'paid_amount' => 200000,
            'payment_method' => 'tunai',
            'idempotency_key' => $idempotencyKey,
            'items' => [
                ['product_id' => $product->id, 'qty' => 1],
            ],
        ];

        // Request pertama
        $res1 = $this->actingAs($this->kasir)->post(route('pos.store'), $payload);
        $res1->assertRedirect();

        $product->refresh();
        $stockAfterFirst = $product->stock;
        $this->assertEquals($initialStock - 1, $stockAfterFirst);

        // Request kedua dengan idempotency key yang sama persis
        $res2 = $this->actingAs($this->kasir)->post(route('pos.store'), $payload);

        $product->refresh();
        // Stok TIDAK boleh berkurang lagi
        $this->assertEquals($stockAfterFirst, $product->stock);
    }

    /** 5. Uji Pembelian Menambah Stok Otomatis */
    public function test_purchase_increases_stock(): void
    {
        $supplier = Supplier::first();
        $product = Product::where('code', 'FLT-WS-CAT320D')->first();
        $initialStock = $product->stock;
        $qtyPurchased = 5;

        $payload = [
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'supplier_doc_no' => 'SJ-TEST-1001',
            'validated_by_warehouse' => 'Budi Santoso (Gudang)',
            'items' => [
                ['product_id' => $product->id, 'qty' => $qtyPurchased, 'buy_price' => $product->buy_price],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('purchases.store'), $payload);
        $response->assertRedirect();

        $product->refresh();
        $this->assertEquals($initialStock + $qtyPurchased, $product->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'purchase',
            'qty_change' => $qtyPurchased,
        ]);
    }

    /** 6. Uji Barang Keluar Rekanan (B2B) Mengurangi Stok Otomatis */
    public function test_partner_output_decreases_stock(): void
    {
        $partner = PartnerCompany::first();
        $product = Product::where('code', 'BT-HT-M12X40')->first();
        $initialStock = $product->stock;
        $qtyOut = 10;

        $payload = [
            'partner_company_id' => $partner->id,
            'output_date' => now()->toDateString(),
            'delivery_fee' => 20000,
            'notes' => 'DO Accurate Ref #DO-ACC-99',
            'items' => [
                ['product_id' => $product->id, 'qty' => $qtyOut],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('partner-outputs.store'), $payload);
        $response->assertRedirect();

        $product->refresh();
        $this->assertEquals($initialStock - $qtyOut, $product->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'partner_out',
            'qty_change' => -$qtyOut,
        ]);
    }

    /** 7. Uji Koreksi Stok Wajib Alasan dan Mencatat Riwayat */
    public function test_stock_adjustment_requires_reason_and_logs_history(): void
    {
        $product = Product::where('code', 'BOX-ORING-CAT')->first();
        $oldStock = $product->stock;
        $newStock = $oldStock + 3;

        // Coba submit tanpa alasan (harus gagal validasi)
        $invalidResponse = $this->actingAs($this->gudang)->post(route('stock.adjustment.store'), [
            'product_id' => $product->id,
            'stock_after' => $newStock,
            'reason' => '',
        ]);
        $invalidResponse->assertSessionHasErrors('reason');

        // Submit dengan alasan valid
        $validResponse = $this->actingAs($this->gudang)->post(route('stock.adjustment.store'), [
            'product_id' => $product->id,
            'stock_after' => $newStock,
            'reason' => 'Ditemukan kelebihan 3 box saat opname fisik rak D-01-1',
        ]);
        $validResponse->assertRedirect(route('stock.adjustment'));

        $product->refresh();
        $this->assertEquals($newStock, $product->stock);

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'stock_before' => $oldStock,
            'stock_after' => $newStock,
            'difference' => 3,
        ]);
    }

    /** 8. Uji Rekap Transaksi Menghasilkan Data Benar dan PDF Terunduh */
    public function test_transaction_reports_and_pdf_download(): void
    {
        // Akses halaman rekapitulasi (Pemilik)
        $response = $this->actingAs($this->pemilik)->get(route('reports.index', [
            'preset' => 'this_month',
            'type' => 'all',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Arus Transaksi', false);

        // Unduh PDF Rekapitulasi (Admin)
        $pdfResponse = $this->actingAs($this->admin)->get(route('reports.pdf', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'type' => 'all',
        ]));

        $pdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('content-type'));
    }
}
