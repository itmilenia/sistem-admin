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
        Schema::create('tt_promotion_program', function (Blueprint $table) {
            $table->id();
            $table->string('program_name');
            $table->string('program_description');
            $table->string('customer_type');
            $table->string('company_type');
            $table->date('effective_start_date');
            $table->date('effective_end_date');
            $table->string('program_file');
            $table->boolean('is_active');

            $table->integer('created_by');
            $table->integer('updated_by')->nullable();

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
        Schema::table('tt_promotion_program', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('tt_promotion_program');
    }
};
