<?php

namespace App\Services;

use App\Models\PartnerOutput;
use App\Models\PartnerOutputItem;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Exception;

class PartnerOutputService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Catat barang keluar rekanan (B2B) untuk sinkronisasi Accurate.
     * Tidak menyimpan nilai akuntansi barang, hanya kuantitas fisik dan biaya antar bila ada.
     *
     * @throws Exception
     */
    public function createPartnerOutput(array $data, int $adminId): PartnerOutput
    {
        if (empty($data['items'])) {
            throw new Exception("Daftar barang keluar rekanan tidak boleh kosong.");
        }

        return DB::transaction(function () use ($data, $adminId) {
            // Generate output_no: OUT-YYYYMM-XXXX
            $monthPrefix = 'OUT-' . date('Ym') . '-';
            $lastOutput = PartnerOutput::where('output_no', 'like', $monthPrefix . '%')
                ->lockForUpdate()
                ->orderBy('output_no', 'desc')
                ->first();

            $nextSeq = 1;
            if ($lastOutput) {
                $lastSeq = (int) substr($lastOutput->output_no, -4);
                $nextSeq = $lastSeq + 1;
            }
            $outputNo = $monthPrefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

            $partnerOutput = PartnerOutput::create([
                'output_no' => $outputNo,
                'partner_company_id' => $data['partner_company_id'],
                'output_date' => $data['output_date'] ?? now()->toDateString(),
                'delivery_fee' => isset($data['delivery_fee']) ? max(0, (float) $data['delivery_fee']) : 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => $adminId,
            ]);

            foreach ($data['items'] as $item) {
                $productId = (int) $item['product_id'];
                $qty = (int) $item['qty'];

                if ($qty <= 0) {
                    throw new Exception("Jumlah kuantitas barang keluar harus lebih dari 0.");
                }

                PartnerOutputItem::create([
                    'partner_output_id' => $partnerOutput->id,
                    'product_id' => $productId,
                    'qty' => $qty,
                ]);

                // Kurangi stok barang & catat mutasi fisik
                $this->stockService->decreaseStock(
                    $productId,
                    $qty,
                    'partner_out',
                    PartnerOutput::class,
                    $partnerOutput->id,
                    $adminId
                );
            }

            ActivityLog::record('create_partner_output', 'partner_out', "Pengeluaran barang rekanan B2B #{$outputNo}.");

            return $partnerOutput;
        });
    }
}
