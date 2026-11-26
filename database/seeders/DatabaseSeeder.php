<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Rack;
use App\Models\Supplier;
use App\Models\PartnerCompany;
use App\Models\Product;
use App\Models\ProductRack;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PartnerOutput;
use App\Models\PartnerOutputItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Demo untuk 4 Role (Password: password)
        $pemilik = User::create([
            'name' => 'Bapak Hendra (Pemilik)',
            'username' => 'pemilik',
            'email' => 'pemilik@mandiridiesel.com',
            'password' => Hash::make('password'),
            'role' => 'pemilik',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Siti Rahma (Admin)',
            'username' => 'admin',
            'email' => 'admin@mandiridiesel.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $gudang = User::create([
            'name' => 'Budi Santoso (Petugas Gudang)',
            'username' => 'gudang',
            'email' => 'gudang@mandiridiesel.com',
            'password' => Hash::make('password'),
            'role' => 'petugas_gudang',
            'is_active' => true,
        ]);

        $kasir = User::create([
            'name' => 'Dewi Lestari (Kasir)',
            'username' => 'kasir',
            'email' => 'kasir@mandiridiesel.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        // 2. Settings (Identitas Toko & Nota)
        $settings = [
            'store_name' => 'CV MANDIRI DIESEL',
            'store_tagline' => 'Pusat Sparepart Alat Berat & Diesel Engine',
            'store_address' => 'Jl Raden Muhammad No.261, 24 Ilir, Kec. Bukit Kecil, Kota Palembang, Sumatera Selatan',
            'store_phone' => '+62 852-6897-9925',
            'store_email' => 'sales@mandiridiesel.com',
            'receipt_footer' => 'Terima kasih atas kunjungan Anda. Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa nota resmi.',
        ];
        foreach ($settings as $key => $val) {
            Setting::create(['key' => $key, 'value' => $val]);
        }

        // 3. Kategori (8 Kategori Realistis)
        $categoriesData = [
            ['name' => 'Pelumas & Oli', 'description' => 'Oli mesin diesel, transmisi, hidrolik, dan grease'],
            ['name' => 'Baut & Fastener', 'description' => 'Baut roda, track bolt, nut grade 10.9 & 12.9'],
            ['name' => 'Filter Alat Berat', 'description' => 'Filter oli, filter solar / fuel filter, filter udara'],
            ['name' => 'Seal & O-Ring Kit', 'description' => 'Main seal, O-ring box, boom/arm cylinder seal kit'],
            ['name' => 'Gasket & Paking', 'description' => 'Cylinder head gasket, full overhaul gasket kit'],
            ['name' => 'Selang & Hose Hidrolik', 'description' => 'High pressure hydraulic hose and fittings'],
            ['name' => 'Bearing & Bushing', 'description' => 'Taper roller bearing, track roller bushing'],
            ['name' => 'Sparepart Engine', 'description' => 'Piston, liner, ring set, connecting rod, nozzle injector'],
        ];
        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::create($cat);
        }

        // 4. Satuan (6 Satuan Realistis)
        $unitsData = [
            ['name' => 'Pieces / Buah', 'symbol' => 'Pcs'],
            ['name' => 'Liter / Drum', 'symbol' => 'Ltr'],
            ['name' => 'Dus / Kotak', 'symbol' => 'Dus'],
            ['name' => 'Set / Paket', 'symbol' => 'Set'],
            ['name' => 'Meter', 'symbol' => 'Mtr'],
            ['name' => 'Pail / Galon', 'symbol' => 'Pail'],
        ];
        $units = [];
        foreach ($unitsData as $u) {
            $units[$u['symbol']] = Unit::create($u);
        }

        // 5. Rak Gudang (15 Rak Realistis)
        $racksData = [
            ['code' => 'A-01-1', 'name' => 'Rak Oli & Drum Bawah', 'zone' => 'Zona A (Pelumas Berat)', 'description' => 'Penyimpanan pail oli 20L dan drum'],
            ['code' => 'A-01-2', 'name' => 'Rak Oli Botol & Grease', 'zone' => 'Zona A (Pelumas Berat)', 'description' => 'Oli botolan 1L, 4L, dan gemuk grease'],
            ['code' => 'B-01-1', 'name' => 'Laci Baut M10 - M14', 'zone' => 'Zona B (Fastener & Hardware)', 'description' => 'Baut baja keras 8.8 / 10.9 ukuran sedang'],
            ['code' => 'B-01-2', 'name' => 'Laci Baut M16 - M24', 'zone' => 'Zona B (Fastener & Hardware)', 'description' => 'Track shoe bolt & segment bolt'],
            ['code' => 'B-02-1', 'name' => 'Rak Washer & Nut', 'zone' => 'Zona B (Fastener & Hardware)', 'description' => 'Mur dan ring baja berbagai diameter'],
            ['code' => 'C-01-1', 'name' => 'Rak Filter Solar / Fuel', 'zone' => 'Zona C (Sistem Filtrasi)', 'description' => 'Fuel filter Donaldson & Sakura'],
            ['code' => 'C-01-2', 'name' => 'Rak Filter Oli Mesin', 'zone' => 'Zona C (Sistem Filtrasi)', 'description' => 'Oil filter Komatsu & CAT'],
            ['code' => 'C-02-1', 'name' => 'Rak Filter Udara Besar', 'zone' => 'Zona C (Sistem Filtrasi)', 'description' => 'Air cleaner element luar dan dalam'],
            ['code' => 'D-01-1', 'name' => 'Laci Box O-Ring NBR', 'zone' => 'Zona D (Sealing & Gasket)', 'description' => 'O-ring kit metric & inchi'],
            ['code' => 'D-01-2', 'name' => 'Rak Seal Kit Silinder Hidrolik', 'zone' => 'Zona D (Sealing & Gasket)', 'description' => 'Seal boom, arm, bucket excavator'],
            ['code' => 'D-02-1', 'name' => 'Rak Paking Head & Engine Gasket', 'zone' => 'Zona D (Sealing & Gasket)', 'description' => 'Paking tembaga dan asbes grafit'],
            ['code' => 'E-01-1', 'name' => 'Rak Selang Hidrolik Potongan', 'zone' => 'Zona E (Hydraulic Line)', 'description' => 'Hose R2, 4SP, fitting crimping'],
            ['code' => 'F-01-1', 'name' => 'Rak Bearing Roda & Transmisi', 'zone' => 'Zona F (Engine & Undercarriage)', 'description' => 'Bearing KOYO, NTN, NSK'],
            ['code' => 'F-02-1', 'name' => 'Rak Liner & Piston Mesin', 'zone' => 'Zona F (Engine & Undercarriage)', 'description' => 'Piston kit 6D16, S6D95, 3306'],
            ['code' => 'F-02-2', 'name' => 'Rak Nozzle & Injector Pump', 'zone' => 'Zona F (Engine & Undercarriage)', 'description' => 'Plunger, nozzle injector Bosch/Denso'],
        ];
        $racks = [];
        foreach ($racksData as $r) {
            $racks[$r['code']] = Rack::create($r);
        }

        // 6. Pemasok (3 Supplier Realistis)
        $suppliersData = [
            ['name' => 'PT United Tractors Tbk Parts Div', 'contact_person' => 'Agus Subagio', 'phone' => '021-4605980', 'email' => 'sales.part@unitedtractors.com', 'address' => 'Jl. Raya Bekasi Km 22, Cakung, Jakarta Timur'],
            ['name' => 'PT Trakindo Utama Parts Hub', 'contact_person' => 'Bambang Irawan', 'phone' => '021-7822373', 'email' => 'contact@trakindo.co.id', 'address' => 'Jl. Cilandak KKO No. 1, Jakarta Selatan'],
            ['name' => 'CV Sumber Makmur Diesel Glodok', 'contact_person' => 'Koh Apin', 'phone' => '021-6289945', 'email' => 'sumbermakmurdiesel@gmail.com', 'address' => 'Pertokoan Glodok Jaya Blok B No. 12, Jakarta Barat'],
        ];
        $suppliers = [];
        foreach ($suppliersData as $s) {
            $suppliers[] = Supplier::create($s);
        }

        // 7. Perusahaan Rekanan B2B (3 Partner Realistis)
        $partnersData = [
            ['name' => 'PT Jaya Tambang Perkasa (Site Kalsel)', 'contact_person' => 'Ir. Wahyu Pratama', 'phone' => '0511-7890123', 'address' => 'Jl. Ahmad Yani Km 71, Simpang Empat, Kalimantan Selatan'],
            ['name' => 'PT Surya Konstruksi Mandiri', 'contact_person' => 'Denny Setiawan, ST', 'phone' => '031-7548900', 'address' => 'Jl. Tanjung Perak Barat No. 88, Surabaya'],
            ['name' => 'CV Agro Nusantara Transport', 'contact_person' => 'H. Mulyono', 'phone' => '0813-8877-6655', 'address' => 'Jl. Raya Bypass Mojokerto No. 14, Jawa Timur'],
        ];
        $partners = [];
        foreach ($partnersData as $p) {
            $partners[] = PartnerCompany::create($p);
        }

        // 8. 40 Data Barang Suku Cadang Realistis
        $productsData = [
            // Pelumas & Oli
            ['code' => 'OLI-MED-15W40', 'part_number' => 'MED-15W40-20L', 'name' => 'Meditran SX SAE 15W-40 (Pail 20L)', 'category' => 'Pelumas & Oli', 'unit' => 'Pail', 'buy_price' => 780000, 'sell_price' => 880000, 'stock' => 25, 'min_stock' => 5, 'rack' => 'A-01-1'],
            ['code' => 'OLI-HYD-68', 'part_number' => 'SHELL-TELLUS-S2-20L', 'name' => 'Shell Tellus S2 MX 68 Oli Hidrolik (20L)', 'category' => 'Pelumas & Oli', 'unit' => 'Pail', 'buy_price' => 890000, 'sell_price' => 1020000, 'stock' => 18, 'min_stock' => 4, 'rack' => 'A-01-1'],
            ['code' => 'OLI-RORED-90', 'part_number' => 'RORED-HDA-90-4L', 'name' => 'Rored HDA SAE 90 Oli Gardan (Galon 4L)', 'category' => 'Pelumas & Oli', 'unit' => 'Pcs', 'buy_price' => 175000, 'sell_price' => 210000, 'stock' => 30, 'min_stock' => 8, 'rack' => 'A-01-2'],
            ['code' => 'GRS-CHASIS-EP2', 'part_number' => 'MOBILUX-EP2-16KG', 'name' => 'Mobilux EP 2 Grease / Gemuk Chassis (16 Kg)', 'category' => 'Pelumas & Oli', 'unit' => 'Pail', 'buy_price' => 720000, 'sell_price' => 840000, 'stock' => 12, 'min_stock' => 3, 'rack' => 'A-01-2'],
            ['code' => 'GRS-HITEMP-1KG', 'part_number' => 'TOP1-HI-TEMP-1KG', 'name' => 'TOP 1 Synthetic Hi-Temp Grease (1 Kg)', 'category' => 'Pelumas & Oli', 'unit' => 'Pcs', 'buy_price' => 75000, 'sell_price' => 95000, 'stock' => 45, 'min_stock' => 10, 'rack' => 'A-01-2'],

            // Baut & Fastener
            ['code' => 'BT-TRK-PC200', 'part_number' => '20Y-32-11210', 'name' => 'Track Shoe Bolt & Nut Komatsu PC200 M20x65', 'category' => 'Baut & Fastener', 'unit' => 'Set', 'buy_price' => 24000, 'sell_price' => 32000, 'stock' => 350, 'min_stock' => 50, 'rack' => 'B-01-2'],
            ['code' => 'BT-SEG-D85', 'part_number' => '01803-02228', 'name' => 'Segment Bolt & Nut Bulldozer D85 M22x75', 'category' => 'Baut & Fastener', 'unit' => 'Set', 'buy_price' => 38000, 'sell_price' => 49000, 'stock' => 180, 'min_stock' => 30, 'rack' => 'B-01-2'],
            ['code' => 'BT-PLW-CAT', 'part_number' => '1J-5607', 'name' => 'Plow Bolt Grader Blade CAT 5/8 x 2-1/4', 'category' => 'Baut & Fastener', 'unit' => 'Set', 'buy_price' => 21000, 'sell_price' => 28000, 'stock' => 220, 'min_stock' => 40, 'rack' => 'B-01-2'],
            ['code' => 'BT-HT-M12X40', 'part_number' => 'DIN933-10.9-M12', 'name' => 'Baut Baja Hex Grade 10.9 M12x40 Hitam', 'category' => 'Baut & Fastener', 'unit' => 'Pcs', 'buy_price' => 6500, 'sell_price' => 9500, 'stock' => 500, 'min_stock' => 100, 'rack' => 'B-01-1'],
            ['code' => 'BT-HT-M14X50', 'part_number' => 'DIN933-10.9-M14', 'name' => 'Baut Baja Hex Grade 10.9 M14x50 Hitam', 'category' => 'Baut & Fastener', 'unit' => 'Pcs', 'buy_price' => 9000, 'sell_price' => 13500, 'stock' => 400, 'min_stock' => 80, 'rack' => 'B-01-1'],
            ['code' => 'MUR-NYLOC-M20', 'part_number' => 'DIN985-GR8-M20', 'name' => 'Mur Pengunci Nyloc Nut M20 Grade 8', 'category' => 'Baut & Fastener', 'unit' => 'Pcs', 'buy_price' => 8500, 'sell_price' => 12000, 'stock' => 260, 'min_stock' => 50, 'rack' => 'B-02-1'],

            // Filter Alat Berat
            ['code' => 'FLT-FUEL-PC200-8', 'part_number' => '6754-71-6140', 'name' => 'Fuel Filter Utama Komatsu PC200-8 / PC200-8M0', 'category' => 'Filter Alat Berat', 'unit' => 'Pcs', 'buy_price' => 185000, 'sell_price' => 245000, 'stock' => 35, 'min_stock' => 8, 'rack' => 'C-01-1'],
            ['code' => 'FLT-WS-CAT320D', 'part_number' => '326-1644', 'name' => 'Water Separator Fuel Filter CAT 320D', 'category' => 'Filter Alat Berat', 'unit' => 'Pcs', 'buy_price' => 295000, 'sell_price' => 380000, 'stock' => 22, 'min_stock' => 5, 'rack' => 'C-01-1'],
            ['code' => 'FLT-OIL-PC200-8', 'part_number' => '6736-51-5142', 'name' => 'Oil Filter Mesin Komatsu SAA6D107E (PC200-8)', 'category' => 'Filter Alat Berat', 'unit' => 'Pcs', 'buy_price' => 165000, 'sell_price' => 215000, 'stock' => 40, 'min_stock' => 10, 'rack' => 'C-01-2'],
            ['code' => 'FLT-OIL-CAT320C', 'part_number' => '1R-0716', 'name' => 'Oil Filter Mesin CAT 1R-0716 Standard Efficiency', 'category' => 'Filter Alat Berat', 'unit' => 'Pcs', 'buy_price' => 210000, 'sell_price' => 275000, 'stock' => 28, 'min_stock' => 6, 'rack' => 'C-01-2'],
            ['code' => 'FLT-AIR-OUT-PC200', 'part_number' => '600-185-4100', 'name' => 'Air Cleaner Outer Element Komatsu PC200-7/8', 'category' => 'Filter Alat Berat', 'unit' => 'Pcs', 'buy_price' => 340000, 'sell_price' => 430000, 'stock' => 15, 'min_stock' => 4, 'rack' => 'C-02-1'],
            ['code' => 'FLT-AIR-IN-PC200', 'part_number' => '600-185-3100', 'name' => 'Air Cleaner Inner Safety Element Komatsu PC200', 'category' => 'Filter Alat Berat', 'unit' => 'Pcs', 'buy_price' => 195000, 'sell_price' => 255000, 'stock' => 14, 'min_stock' => 4, 'rack' => 'C-02-1'],
            ['code' => 'FLT-HYD-RET-SK200', 'part_number' => 'YN52V01016P1', 'name' => 'Hydraulic Return Filter Kobelco SK200-8', 'category' => 'Filter Alat Berat', 'unit' => 'Pcs', 'buy_price' => 320000, 'sell_price' => 410000, 'stock' => 16, 'min_stock' => 4, 'rack' => 'C-01-2'],

            // Seal & O-Ring Kit
            ['code' => 'SEAL-BOM-PC200-8', 'part_number' => '707-99-67210', 'name' => 'Boom Cylinder Seal Kit Komatsu PC200-8 Nok/Kayaba', 'category' => 'Seal & O-Ring Kit', 'unit' => 'Set', 'buy_price' => 1150000, 'sell_price' => 1450000, 'stock' => 8, 'min_stock' => 2, 'rack' => 'D-01-2'],
            ['code' => 'SEAL-ARM-PC200-8', 'part_number' => '707-99-68210', 'name' => 'Arm Cylinder Seal Kit Komatsu PC200-8', 'category' => 'Seal & O-Ring Kit', 'unit' => 'Set', 'buy_price' => 1250000, 'sell_price' => 1580000, 'stock' => 7, 'min_stock' => 2, 'rack' => 'D-01-2'],
            ['code' => 'SEAL-BCK-PC200-8', 'part_number' => '707-99-57220', 'name' => 'Bucket Cylinder Seal Kit Komatsu PC200-8', 'category' => 'Seal & O-Ring Kit', 'unit' => 'Set', 'buy_price' => 890000, 'sell_price' => 1120000, 'stock' => 9, 'min_stock' => 2, 'rack' => 'D-01-2'],
            ['code' => 'BOX-ORING-CAT', 'part_number' => '4C-4782', 'name' => 'O-Ring Kit Box Caterpillar Yellow Metric & SAE', 'category' => 'Seal & O-Ring Kit', 'unit' => 'Dus', 'buy_price' => 420000, 'sell_price' => 550000, 'stock' => 18, 'min_stock' => 4, 'rack' => 'D-01-1'],
            ['code' => 'BOX-ORING-KOM', 'part_number' => '07000-KIT-NBR', 'name' => 'O-Ring Service Box Komatsu NBR 90 Shore', 'category' => 'Seal & O-Ring Kit', 'unit' => 'Dus', 'buy_price' => 380000, 'sell_price' => 490000, 'stock' => 20, 'min_stock' => 5, 'rack' => 'D-01-1'],
            ['code' => 'SEAL-CRK-FR-6D16', 'part_number' => 'ME013674', 'name' => 'Crankshaft Front Oil Seal Mitsubishi 6D16', 'category' => 'Seal & O-Ring Kit', 'unit' => 'Pcs', 'buy_price' => 95000, 'sell_price' => 135000, 'stock' => 24, 'min_stock' => 5, 'rack' => 'D-01-2'],
            ['code' => 'SEAL-CRK-RR-6D16', 'part_number' => 'ME013679', 'name' => 'Crankshaft Rear Oil Seal Mitsubishi 6D16 Viton', 'category' => 'Seal & O-Ring Kit', 'unit' => 'Pcs', 'buy_price' => 140000, 'sell_price' => 190000, 'stock' => 21, 'min_stock' => 5, 'rack' => 'D-01-2'],

            // Gasket & Paking
            ['code' => 'GSK-HED-6D16T', 'part_number' => 'ME071313-MET', 'name' => 'Gasket Cylinder Head Mitsubishi 6D16T Plat Besi', 'category' => 'Gasket & Paking', 'unit' => 'Pcs', 'buy_price' => 410000, 'sell_price' => 530000, 'stock' => 11, 'min_stock' => 3, 'rack' => 'D-02-1'],
            ['code' => 'GSK-HED-S6D102', 'part_number' => '6735-11-1810', 'name' => 'Head Gasket Komatsu S6D102 / Cummins 6BT', 'category' => 'Gasket & Paking', 'unit' => 'Pcs', 'buy_price' => 480000, 'sell_price' => 620000, 'stock' => 10, 'min_stock' => 3, 'rack' => 'D-02-1'],
            ['code' => 'GSK-OVH-PC200-7', 'part_number' => '6738-K1-9901', 'name' => 'Full Engine Overhaul Gasket Kit Komatsu PC200-7', 'category' => 'Gasket & Paking', 'unit' => 'Set', 'buy_price' => 1650000, 'sell_price' => 210000, 'stock' => 4, 'min_stock' => 2, 'rack' => 'D-02-1'],
            ['code' => 'LEM-TRI-BOND-1104', 'part_number' => 'TB-1104-GRAY', 'name' => 'ThreeBond 1104 Rubber Gasket Sealer (100g)', 'category' => 'Gasket & Paking', 'unit' => 'Pcs', 'buy_price' => 48000, 'sell_price' => 65000, 'stock' => 55, 'min_stock' => 15, 'rack' => 'D-02-1'],

            // Selang & Hose Hidrolik
            ['code' => 'HOS-1/2-4SP-2M', 'part_number' => 'AERO-4SP-08-2000', 'name' => 'Hydraulic Hose 1/2" 4SP Wire Spiral Panjang 2 Meter', 'category' => 'Selang & Hose Hidrolik', 'unit' => 'Pcs', 'buy_price' => 310000, 'sell_price' => 420000, 'stock' => 14, 'min_stock' => 3, 'rack' => 'E-01-1'],
            ['code' => 'HOS-3/4-4SP-2M', 'part_number' => 'AERO-4SP-12-2000', 'name' => 'Hydraulic Hose 3/4" 4SP Wire Spiral Panjang 2.2 Meter', 'category' => 'Selang & Hose Hidrolik', 'unit' => 'Pcs', 'buy_price' => 460000, 'sell_price' => 610000, 'stock' => 10, 'min_stock' => 3, 'rack' => 'E-01-1'],
            ['code' => 'HOS-MTR-R2-3/8', 'part_number' => 'BRIDG-R2-06', 'name' => 'Selang Hidrolik Bridgestone 3/8 R2 (Per Meter)', 'category' => 'Selang & Hose Hidrolik', 'unit' => 'Mtr', 'buy_price' => 65000, 'sell_price' => 90000, 'stock' => 120, 'min_stock' => 20, 'rack' => 'E-01-1'],

            // Bearing & Bushing
            ['code' => 'BRG-30212-KOYO', 'part_number' => '30212JR-KOYO', 'name' => 'Tapered Roller Bearing 30212 Koyo Japan', 'category' => 'Bearing & Bushing', 'unit' => 'Pcs', 'buy_price' => 185000, 'sell_price' => 245000, 'stock' => 19, 'min_stock' => 5, 'rack' => 'F-01-1'],
            ['code' => 'BRG-6208-2RS-NTN', 'part_number' => '6208LLU-NTN', 'name' => 'Deep Groove Ball Bearing 6208 2RS NTN Japan', 'category' => 'Bearing & Bushing', 'unit' => 'Pcs', 'buy_price' => 68000, 'sell_price' => 95000, 'stock' => 32, 'min_stock' => 8, 'rack' => 'F-01-1'],
            ['code' => 'BSH-PIN-BK-PC200', 'part_number' => '20Y-70-11160', 'name' => 'Bucket Pin Bushing Komatsu PC200-8 (80x95x80)', 'category' => 'Bearing & Bushing', 'unit' => 'Pcs', 'buy_price' => 280000, 'sell_price' => 360000, 'stock' => 16, 'min_stock' => 4, 'rack' => 'F-01-1'],

            // Sparepart Engine
            ['code' => 'PST-KIT-6D16', 'part_number' => 'ME072061-KIT', 'name' => 'Piston Kit + Pin + Clip Mitsubishi Fuso 6D16', 'category' => 'Sparepart Engine', 'unit' => 'Set', 'buy_price' => 2450000, 'sell_price' => 3100000, 'stock' => 6, 'min_stock' => 2, 'rack' => 'F-02-1'],
            ['code' => 'RNG-PST-6D16', 'part_number' => 'ME996380-RIKEN', 'name' => 'Ring Piston Riken Japan 6D16 Standard', 'category' => 'Sparepart Engine', 'unit' => 'Set', 'buy_price' => 620000, 'sell_price' => 790000, 'stock' => 11, 'min_stock' => 3, 'rack' => 'F-02-1'],
            ['code' => 'LNR-SLV-6D16', 'part_number' => 'ME071220-IZUMI', 'name' => 'Liner Cylinder Sleeve Izumi 6D16 (6 Buah/Set)', 'category' => 'Sparepart Engine', 'unit' => 'Set', 'buy_price' => 1800000, 'sell_price' => 2300000, 'stock' => 5, 'min_stock' => 2, 'rack' => 'F-02-1'],
            ['code' => 'NZL-INJ-PC200-8', 'part_number' => '095000-5471', 'name' => 'Nozzle Common Rail Injector Denso PC200-8', 'category' => 'Sparepart Engine', 'unit' => 'Pcs', 'buy_price' => 1950000, 'sell_price' => 2450000, 'stock' => 12, 'min_stock' => 4, 'rack' => 'F-02-2'],
            ['code' => 'PLG-BOSCH-6D16', 'part_number' => '9411-140-520', 'name' => 'Plunger Bosch Pompa Injeksi Fuso 6D16', 'category' => 'Sparepart Engine', 'unit' => 'Pcs', 'buy_price' => 240000, 'sell_price' => 320000, 'stock' => 18, 'min_stock' => 6, 'rack' => 'F-02-2'],
        ];

        $createdProducts = [];
        foreach ($productsData as $item) {
            $cat = $categories[$item['category']];
            $unit = $units[$item['unit']];
            $rack = $racks[$item['rack']];

            $product = Product::create([
                'code' => $item['code'],
                'part_number' => $item['part_number'],
                'name' => $item['name'],
                'category_id' => $cat->id,
                'unit_id' => $unit->id,
                'buy_price' => $item['buy_price'],
                'sell_price' => $item['sell_price'],
                'stock' => $item['stock'],
                'min_stock' => $item['min_stock'],
                'description' => 'Suku cadang ' . $item['name'] . ' berkualitas tinggi untuk alat berat.',
                'is_active' => true,
            ]);

            // Penempatan Rak Utama
            ProductRack::create([
                'product_id' => $product->id,
                'rack_id' => $rack->id,
                'is_primary' => true,
                'note' => 'Penempatan utama rak ' . $rack->code,
            ]);

            // Catat saldo awal di stock_movements
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'purchase',
                'reference_type' => null,
                'reference_id' => null,
                'qty_change' => $item['stock'],
                'stock_before' => 0,
                'stock_after' => $item['stock'],
                'created_by' => $gudang->id,
                'created_at' => now()->subDays(15),
            ]);

            $createdProducts[$product->code] = $product;
        }

        // 9. Transaksi Sampel Pembelian (Pemasok)
        $purchase = Purchase::create([
            'purchase_no' => 'PO-202611-001',
            'supplier_id' => $suppliers[0]->id,
            'purchase_date' => now()->subDays(5)->toDateString(),
            'supplier_doc_no' => 'SJ-UT-99231',
            'notes' => 'Pengadaan filter dan baut rutin bulanan armada',
            'validated_by_warehouse' => $gudang->name,
            'created_by' => $admin->id,
        ]);

        $pItem1 = $createdProducts['FLT-FUEL-PC200-8'];
        $pItem2 = $createdProducts['BT-TRK-PC200'];

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $pItem1->id,
            'qty' => 10,
            'buy_price' => $pItem1->buy_price,
        ]);
        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $pItem2->id,
            'qty' => 50,
            'buy_price' => $pItem2->buy_price,
        ]);

        // 10. Transaksi Sampel Penjualan Eceran (POS B2C)
        $sale1Product = $createdProducts['OLI-MED-15W40'];
        $sale1Qty = 2;
        $sale1Subtotal = $sale1Product->sell_price * $sale1Qty;
        $sale1Delivery = 25000;
        $sale1Grand = $sale1Subtotal + $sale1Delivery;

        $sale1 = Sale::create([
            'invoice_no' => 'INV-' . date('Ymd') . '-0001',
            'sale_date' => now()->subHours(4),
            'customer_name' => 'Pak Joko (Bengkel Berkah)',
            'subtotal' => $sale1Subtotal,
            'delivery_fee' => $sale1Delivery,
            'grand_total' => $sale1Grand,
            'paid_amount' => 1800000,
            'change_amount' => 1800000 - $sale1Grand,
            'payment_method' => 'tunai',
            'status' => 'completed',
            'idempotency_key' => 'seed-pos-demo-inv-001',
            'cashier_id' => $kasir->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $sale1Product->id,
            'product_code_snapshot' => $sale1Product->code,
            'product_name_snapshot' => $sale1Product->name,
            'qty' => $sale1Qty,
            'price' => $sale1Product->sell_price,
            'subtotal' => $sale1Subtotal,
        ]);

        StockMovement::create([
            'product_id' => $sale1Product->id,
            'type' => 'sale',
            'reference_type' => Sale::class,
            'reference_id' => $sale1->id,
            'qty_change' => -$sale1Qty,
            'stock_before' => $sale1Product->stock + $sale1Qty,
            'stock_after' => $sale1Product->stock,
            'created_by' => $kasir->id,
            'created_at' => now()->subHours(4),
        ]);

        // 11. Transaksi Sampel Barang Keluar Rekanan (B2B tanpa nilai akuntansi uang barang)
        $partnerOut1 = PartnerOutput::create([
            'output_no' => 'OUT-202611-0001',
            'partner_company_id' => $partners[0]->id,
            'output_date' => now()->subDays(2)->toDateString(),
            'delivery_fee' => 50000,
            'notes' => 'Pengiriman suku cadang rutin Site Kalsel - Cocokkan dengan DO Accurate No. DO-JTP-114',
            'created_by' => $admin->id,
        ]);

        $b2bProduct1 = $createdProducts['FLT-OIL-PC200-8'];
        $b2bQty1 = 5;

        PartnerOutputItem::create([
            'partner_output_id' => $partnerOut1->id,
            'product_id' => $b2bProduct1->id,
            'qty' => $b2bQty1,
        ]);

        StockMovement::create([
            'product_id' => $b2bProduct1->id,
            'type' => 'partner_out',
            'reference_type' => PartnerOutput::class,
            'reference_id' => $partnerOut1->id,
            'qty_change' => -$b2bQty1,
            'stock_before' => $b2bProduct1->stock + $b2bQty1,
            'stock_after' => $b2bProduct1->stock,
            'created_by' => $admin->id,
            'created_at' => now()->subDays(2),
        ]);
    }
}
