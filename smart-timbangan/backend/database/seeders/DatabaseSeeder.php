<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Material;
use App\Models\Formula;
use App\Models\BomItem;
use App\Models\Device;
use App\Models\ProductionOrder;
use App\Models\ProductionCosting;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Setup User
        $user = User::query()->firstOrCreate(
            ['email' => 'admin@pabrik.com'],
            [
                'name' => 'Admin Pabrik',
                'email_verified_at' => now(),
                'password' => 'password123',
            ]
        );

        // Setup Device
        $device = Device::create([
            'device_id' => 'ESP32-SCALE-A',
            'name' => 'Timbangan Bahan Baku 1',
            'location' => 'Area Mixing A'
        ]);

        // Setup Materials (Sesuai dengan Screenshot List BOM Formula)
        $mat1 = Material::create(['kode_produk' => 'LB4221', 'nama_produk' => 'MINYAK GORENG SOVIA 18L', 'uom_dasar' => 'ML', 'standart_cost' => 0]);
        $mat2 = Material::create(['kode_produk' => 'LB4224', 'nama_produk' => 'MINYAK GORENG SOVIA 18L', 'uom_dasar' => 'ML', 'standart_cost' => 0]);

        // Setup Formula
        $formula = Formula::create([
            'formula_code' => 'PREMIX-BROWNIES',
            'formula_name' => 'PREMIX BROWNIES KUKUS',
            'mix_kategory' => 'PREMIX',
            'created_by' => $user->id
        ]);

        // Setup BOM Items
        $bom1 = BomItem::create([
            'formula_id' => $formula->id,
            'material_id' => $mat1->id,
            'bom_konversi_qty' => 1.00,
            'bom_konversi_uom' => 'PCS',
            'netto_target' => 315.000,
            'mix_id' => 'PREMIX',
            'is_optional' => false,
            'created_by' => $user->id
        ]);
        
        $bom2 = BomItem::create([
            'formula_id' => $formula->id,
            'material_id' => $mat2->id,
            'bom_konversi_qty' => 1.00,
            'bom_konversi_uom' => 'PCS',
            'netto_target' => 110.000,
            'mix_id' => 'PREMIX',
            'is_optional' => false,
            'created_by' => $user->id
        ]);

        // Setup Production Order & Costing (Sesuai dengan Screenshot Costing Produksi)
        $order = ProductionOrder::create([
            'order_number' => 'PO-001',
            'formula_id' => $formula->id,
            'qty_order' => 1,
            'start_date' => now(),
            'status' => 'In Progress',
            'operator_id' => $user->id
        ]);

        ProductionCosting::create([
            'production_order_id' => $order->id,
            'bom_item_id' => $bom1->id,
            'device_id' => $device->id,
            'netto_target' => 315.000,
            'netto_produksi' => null, // Belum ditimbang
            'price_bom' => 0,
            'status' => 'Pending'
        ]);

        ProductionCosting::create([
            'production_order_id' => $order->id,
            'bom_item_id' => $bom2->id,
            'device_id' => $device->id,
            'netto_target' => 110.000,
            'netto_produksi' => null, // Belum ditimbang
            'price_bom' => 0,
            'status' => 'Pending'
        ]);
    }
}
