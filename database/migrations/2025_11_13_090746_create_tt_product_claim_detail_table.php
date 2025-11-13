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
        Schema::create('tt_product_claim_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_claim_id')->constrained('tt_product_claim')->onDelete('cascade');
            $table->string('invoice_id');
            $table->string('product_id');
            $table->integer('quantity');
            $table->date('order_date');
            $table->date('delivery_date');
            $table->string('return_reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tt_product_claim_detail', function (Blueprint $table) {
            $table->dropForeign(['product_claim_id']);
        });

        Schema::dropIfExists('tt_product_claim_detail');
    }
};
