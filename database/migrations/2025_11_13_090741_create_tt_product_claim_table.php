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
        Schema::create('tt_product_claim', function (Blueprint $table) {
            $table->id();
            $table->string('company_type');
            $table->integer('sales_id');
            $table->integer('sales_head_id');
            $table->integer('checker_id');
            $table->string('retail_name');
            $table->date('claim_date');
            $table->date('verification_date')->nullable();
            $table->text('verification_result')->nullable();
            $table->string('checker_signature_path')->nullable();
            $table->string('sales_signature_path')->nullable();
            $table->string('sales_head_signature_path')->nullable();

            $table->integer('created_by');
            $table->integer('updated_by')->nullable();

            $table->foreign('sales_id')->references('ID')->on('users')->onDelete('cascade');
            $table->foreign('sales_head_id')->references('ID')->on('users')->onDelete('cascade');
            $table->foreign('checker_id')->references('ID')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('ID')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('ID')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tt_product_claim', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->dropForeign(['sales_head_id']);
            $table->dropForeign(['checker_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('tt_product_claim');
    }
};
