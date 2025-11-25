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
        Schema::create('tt_quotation_letter_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_letter_id')->constrained('tt_quotation_letter')->onDelete('cascade');
            $table->string('item_id');
            $table->string('item_type')->nullable();
            $table->string('sku_number')->nullable();
            $table->string('warranty_period')->nullable();
            $table->string('size_number')->nullable();
            $table->decimal('unit_price', 18, 2);
            $table->decimal('discount_percentage', 5, 2);
            $table->decimal('total_price', 18, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tt_quotation_letter_detail');
    }
};
