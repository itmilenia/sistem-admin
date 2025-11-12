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
        Schema::create('tt_promotion_program_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_program_id')->constrained('tt_promotion_program')->onDelete('cascade');
            $table->string('item_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tt_promotion_program_detail', function (Blueprint $table) {
            $table->dropForeign(['promotion_program_id']);
        });

        Schema::dropIfExists('tt_promotion_program_detail');
    }
};
