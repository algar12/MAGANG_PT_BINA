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
        Schema::create('production_costings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->cascadeOnDelete();
            $table->foreignId('bom_item_id')->constrained('bom_items')->restrictOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete()->comment('Timbangan mana yang digunakan');
            $table->decimal('netto_target', 10, 3)->comment('Didapat dari BOM x Qty Order');
            $table->decimal('netto_produksi', 10, 3)->nullable()->comment('Berat AKTUAL dari Timbangan IoT');
            $table->decimal('price_bom', 12, 2)->comment('Harga standar per UOM');
            $table->decimal('sub_price', 12, 2)->nullable()->comment('netto_target x price_bom');
            $table->decimal('sub_cost_price', 12, 2)->nullable()->comment('netto_produksi x price_bom');
            $table->enum('status', ['Pending', 'Weighed', 'Approved'])->default('Pending');
            $table->timestamp('weighed_at')->nullable()->comment('Waktu penimbangan berhasil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_costings');
    }
};
