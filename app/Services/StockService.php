<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Exception;

class StockService
{
    /**
     * Kurangi stok produk secara aman dengan lockForUpdate.
     * Digunakan untuk POS dan Barang Keluar Rekanan.
     *
     * @throws Exception
     */
    public function decreaseStock(
        int $productId,
        int $qty,
        string $type,
        ?string $refType = null,
        ?int $refId = null,
        ?int $userId = null
    ): StockMovement {
        if ($qty <= 0) {
            throw new Exception("Jumlah kuantitas harus lebih dari 0.");
        }

        // Kunci baris produk untuk mencegah race condition
        $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

        if ($product->stock < $qty) {
            throw new Exception("Stok {$product->name} tidak mencukupi (tersedia {$product->stock}).");
        }

        $stockBefore = $product->stock;
        $stockAfter = $stockBefore - $qty;

        $product->update(['stock' => $stockAfter]);

        return StockMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'qty_change' => -$qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'created_by' => $userId ?? auth()->id(),
            'created_at' => now(),
        ]);
    }

    /**
     * Tambah stok produk secara aman dengan lockForUpdate.
     * Digunakan untuk Pembelian Pemasok dan Void Transaksi.
     *
     * @throws Exception
     */
    public function increaseStock(
        int $productId,
        int $qty,
        string $type,
        ?string $refType = null,
        ?int $refId = null,
        ?int $userId = null
    ): StockMovement {
        if ($qty <= 0) {
            throw new Exception("Jumlah kuantitas harus lebih dari 0.");
        }

        $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

        $stockBefore = $product->stock;
        $stockAfter = $stockBefore + $qty;

        $product->update(['stock' => $stockAfter]);

        return StockMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'qty_change' => $qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'created_by' => $userId ?? auth()->id(),
            'created_at' => now(),
        ]);
    }

    /**
     * Koreksi stok fisik oleh Petugas Gudang dengan alasan wajib.
     *
     * @throws Exception
     */
    public function adjustStock(
        int $productId,
        int $newStock,
        string $reason,
        ?int $userId = null
    ): StockAdjustment {
        if ($newStock < 0) {
            throw new Exception("Stok fisik tidak boleh bernilai negatif.");
        }

        if (trim($reason) === '') {
            throw new Exception("Alasan koreksi stok wajib diisi.");
        }

        return DB::transaction(function () use ($productId, $newStock, $reason, $userId) {
            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            $stockBefore = $product->stock;
            $stockAfter = $newStock;
            $difference = $stockAfter - $stockBefore;

            $product->update(['stock' => $stockAfter]);

            $adjustment = StockAdjustment::create([
                'product_id' => $product->id,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'difference' => $difference,
                'reason' => $reason,
                'created_by' => $userId ?? auth()->id(),
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'reference_type' => StockAdjustment::class,
                'reference_id' => $adjustment->id,
                'qty_change' => $difference,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'created_by' => $userId ?? auth()->id(),
                'created_at' => now(),
            ]);

            return $adjustment;
        });
    }
}
