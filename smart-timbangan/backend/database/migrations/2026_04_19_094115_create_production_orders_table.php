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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('formula_id')->constrained('formulas')->restrictOnDelete();
            $table->integer('qty_order')->default(1)->comment('Berapa batch yang akan dibuat');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['Draft', 'In Progress', 'Completed', 'Cancelled'])->default('Draft');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
