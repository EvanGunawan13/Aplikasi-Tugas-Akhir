<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Catat transaksi penjualan POS eceran (B2C)
     *
     * @param array $data ['customer_name', 'items', 'delivery_fee', 'paid_amount', 'payment_method', 'idempotency_key']
     * @throws Exception
     */
    public function createSale(array $data, int $cashierId): Sale
    {
        $idempotencyKey = $data['idempotency_key'] ?? null;
        if (! $idempotencyKey) {
            throw new Exception("Idempotency key wajib disertakan untuk mencegah transaksi ganda.");
        }

        // Cek apakah key sudah pernah digunakan
        $existing = Sale::where('idempotency_key', $idempotencyKey)->first();
        if ($existing) {
            return $existing;
        }

        if (empty($data['items'])) {
            throw new Exception("Keranjang belanja tidak boleh kosong.");
        }

        return DB::transaction(function () use ($data, $cashierId, $idempotencyKey) {
            $subtotal = 0;
            $itemsToProcess = [];

            // Validasi & kunci stok setiap barang terlebih dahulu
            foreach ($data['items'] as $item) {
                $productId = (int) $item['product_id'];
                $qty = (int) $item['qty'];

                if ($qty <= 0) {
                    throw new Exception("Kuantitas barang harus lebih dari 0.");
                }

                $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

                if ($product->stock < $qty) {
                    throw new Exception("Stok {$product->name} tidak mencukupi (tersedia {$product->stock}).");
                }

                $itemPrice = (float) $product->sell_price;
                $itemSubtotal = $itemPrice * $qty;
                $subtotal += $itemSubtotal;

                $itemsToProcess[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $deliveryFee = isset($data['delivery_fee']) ? max(0, (float) $data['delivery_fee']) : 0;
            $grandTotal = $subtotal + $deliveryFee;
            $paidAmount = isset($data['paid_amount']) ? (float) $data['paid_amount'] : $grandTotal;

            if ($paidAmount < $grandTotal) {
                throw new Exception("Uang yang dibayarkan kurang dari total belanja.");
            }

            $changeAmount = $paidAmount - $grandTotal;

            // Generate nomor invoice berurutan hari ini: INV-YYYYMMDD-0001
            $today = date('Ymd');
            $prefix = 'INV-' . $today . '-';
            $latestSale = Sale::where('invoice_no', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('invoice_no', 'desc')
                ->first();

            $nextNumber = 1;
            if ($latestSale) {
                $lastSeq = (int) substr($latestSale->invoice_no, -4);
                $nextNumber = $lastSeq + 1;
            }
            $invoiceNo = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'invoice_no' => $invoiceNo,
                'sale_date' => now(),
                'customer_name' => $data['customer_name'] ?? 'Pelanggan Umum',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $data['payment_method'] ?? 'tunai',
                'status' => 'completed',
                'idempotency_key' => $idempotencyKey,
                'cashier_id' => $cashierId,
            ]);

            foreach ($itemsToProcess as $processed) {
                /** @var Product $product */
                $product = $processed['product'];
                $qty = $processed['qty'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_code_snapshot' => $product->code,
                    'product_name_snapshot' => $product->name,
                    'qty' => $qty,
                    'price' => $processed['price'],
                    'subtotal' => $processed['subtotal'],
                ]);

                // Kurangi stok & catat mutasi
                $this->stockService->decreaseStock(
                    $product->id,
                    $qty,
                    'sale',
                    Sale::class,
                    $sale->id,
                    $cashierId
                );
            }

            ActivityLog::record('create_sale', 'pos', "Penjualan eceran #{$invoiceNo} sebesar Rp " . number_format($grandTotal, 0, ',', '.'));

            return $sale;
        });
    }

    /**
     * Batalkan transaksi penjualan (Void) oleh Admin
     *
     * @throws Exception
     */
    public function voidSale(int $saleId, string $reason, int $adminId): Sale
    {
        if (trim($reason) === '') {
            throw new Exception("Alasan pembatalan (void) wajib diisi.");
        }

        return DB::transaction(function () use ($saleId, $reason, $adminId) {
            $sale = Sale::with('items.product')->where('id', $saleId)->lockForUpdate()->firstOrFail();

            if ($sale->status === 'void') {
                throw new Exception("Transaksi ini sudah pernah dibatalkan (void).");
            }

            // Kembalikan stok untuk setiap item
            foreach ($sale->items as $item) {
                if ($item->product_id) {
                    $this->stockService->increaseStock(
                        $item->product_id,
                        $item->qty,
                        'void',
                        Sale::class,
                        $sale->id,
                        $adminId
                    );
                }
            }

            $sale->update([
                'status' => 'void',
                'void_reason' => $reason,
                'voided_by' => $adminId,
                'voided_at' => now(),
            ]);

            ActivityLog::record('void_sale', 'pos', "Void transaksi penjualan #{$sale->invoice_no}. Alasan: {$reason}");

            return $sale;
        });
    }
}
