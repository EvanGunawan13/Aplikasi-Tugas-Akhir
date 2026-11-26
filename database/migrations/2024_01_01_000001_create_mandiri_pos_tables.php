<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 2. categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. units
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // pcs, liter, dus, set, dll.
            $table->string('symbol', 20)->nullable();
            $table->timestamps();
        });

        // 6. racks
        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // misal: A-01-3
            $table->string('name');
            $table->string('zone')->nullable(); // Zona/Area Gudang
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 10. suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 11. partner_companies
        Schema::create('partner_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 4. products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode internal / barcode
            $table->string('part_number')->nullable()->index(); // Part number alat berat
            $table->string('name')->index();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->decimal('buy_price', 15, 2)->default(0);
            $table->decimal('sell_price', 15, 2)->default(0);
            $table->integer('stock')->default(0); // Single source of truth
            $table->integer('min_stock')->default(5);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // 5. product_photos
        Schema::create('product_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 7. product_racks
        Schema::create('product_racks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('rack_id')->constrained('racks')->cascadeOnDelete();
            $table->boolean('is_primary')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'rack_id']);
        });

        // 8. stock_movements (kartu stok)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['sale', 'purchase', 'partner_out', 'adjustment', 'void'])->index();
            $table->string('reference_type')->nullable(); // Polymorphic type
            $table->unsignedBigInteger('reference_id')->nullable(); // Polymorphic ID
            $table->integer('qty_change'); // +/- change
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['reference_type', 'reference_id']);
        });

        // 9. stock_adjustments (koreksi stok fisik)
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->integer('difference'); // selisih (+/-)
            $table->text('reason'); // Alasan wajib diisi
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 12. purchases (pembelian dari supplier)
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_no')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->date('purchase_date')->index();
            $table->string('supplier_doc_no')->nullable(); // Nomor Surat Jalan / Faktur Supplier
            $table->text('notes')->nullable();
            $table->string('validated_by_warehouse')->nullable(); // Nama/Petugas gudang verifikasi fisik
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 13. purchase_items
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('qty');
            $table->decimal('buy_price', 15, 2)->default(0);
            $table->timestamps();
        });

        // 14. sales (POS eceran B2C)
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // Format INV-YYYYMMDD-0001
            $table->dateTime('sale_date')->index();
            $table->string('customer_name')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('delivery_fee', 15, 2)->default(0); // Ongkir opsional
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->enum('payment_method', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->enum('status', ['completed', 'void'])->default('completed')->index();
            $table->text('void_reason')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->string('idempotency_key', 64)->unique(); // Cegah submit ganda
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 15. sale_items
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('product_code_snapshot');
            $table->string('product_name_snapshot');
            $table->integer('qty');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        // 16. partner_outputs (Barang Keluar Rekanan B2B tanpa nilai akuntansi)
        Schema::create('partner_outputs', function (Blueprint $table) {
            $table->id();
            $table->string('output_no')->unique();
            $table->foreignId('partner_company_id')->constrained('partner_companies')->cascadeOnDelete();
            $table->date('output_date')->index();
            $table->decimal('delivery_fee', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 17. partner_output_items
        Schema::create('partner_output_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_output_id')->constrained('partner_outputs')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('qty');
            $table->timestamps();
        });

        // 18. activity_logs (audit trail sistem)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // misal: login, create_sale, void_sale, adjust_stock
            $table->string('module'); // misal: pos, warehouse, purchase, partner_out, auth
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // 19. settings (konfigurasi nama toko, alamat, footer nota, dll)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('partner_output_items');
        Schema::dropIfExists('partner_outputs');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('product_racks');
        Schema::dropIfExists('product_photos');
        Schema::dropIfExists('products');
        Schema::dropIfExists('partner_companies');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('racks');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
    }
};