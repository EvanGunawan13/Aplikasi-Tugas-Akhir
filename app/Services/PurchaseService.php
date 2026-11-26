<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Catat pembelian dari pemasok dan tambahkan stok otomatis.
     * Wajib divalidasi fisik oleh petugas gudang.
     *
     * @throws Exception
     */
    public function createPurchase(array $data, int $adminId): Purchase
    {
        if (empty($data['items'])) {
            throw new Exception("Daftar barang pembelian tidak boleh kosong.");
        }

        if (empty($data['validated_by_warehouse'])) {
            throw new Exception("Fisik barang wajib divalidasi oleh petugas gudang sebelum dicatat.");
        }

        return DB::transaction(function () use ($data, $adminId) {
            // Generate purchase_no: PO-YYYYMM-XXXX
            $monthPrefix = 'PO-' . date('Ym') . '-';
            $lastPurchase = Purchase::where('purchase_no', 'like', $monthPrefix . '%')
                ->lockForUpdate()
                ->orderBy('purchase_no', 'desc')
                ->first();

            $nextSeq = 1;
            if ($lastPurchase) {
                $lastSeq = (int) substr($lastPurchase->purchase_no, -4);
                $nextSeq = $lastSeq + 1;
            }
            $purchaseNo = $monthPrefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

            $purchase = Purchase::create([
                'purchase_no' => $purchaseNo,
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'] ?? now()->toDateString(),
                'supplier_doc_no' => $data['supplier_doc_no'] ?? null,
                'notes' => $data['notes'] ?? null,
                'validated_by_warehouse' => $data['validated_by_warehouse'],
                'created_by' => $adminId,
            ]);

            foreach ($data['items'] as $item) {
                $productId = (int) $item['product_id'];
                $qty = (int) $item['qty'];
                $buyPrice = (float) ($item['buy_price'] ?? 0);

                if ($qty <= 0) {
                    throw new Exception("Jumlah kuantitas barang pembelian harus lebih dari 0.");
                }

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'qty' => $qty,
                    'buy_price' => $buyPrice,
                ]);

                // Update harga beli produk terakhir bila diisi
                if ($buyPrice > 0) {
                    Product::where('id', $productId)->update(['buy_price' => $buyPrice]);
                }

                // Tambahkan stok produk & catat mutasi
                $this->stockService->increaseStock(
                    $productId,
                    $qty,
                    'purchase',
                    Purchase::class,
                    $purchase->id,
                    $adminId
                );
            }

            ActivityLog::record('create_purchase', 'purchase', "Pencatatan pembelian barang #{$purchaseNo} dari pemasok.");

            return $purchase;
        });
    }
}
