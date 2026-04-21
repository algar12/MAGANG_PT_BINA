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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->decimal('bom_konversi_qty', 8, 2)->default(1.00)->comment('Misal 1.00');
            $table->string('bom_konversi_uom', 20)->comment('Misal PCS atau KG');
            $table->decimal('netto_target', 10, 3)->comment('Target berat/volume dalam UOM dasar');
            $table->string('mix_id', 50)->nullable()->comment('Kategori mix dalam BOM');
            $table->boolean('is_optional')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
