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
        Schema::create('tt_quotation_letter', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_letter_number');
            $table->string('recipient');
            $table->date('letter_date');
            $table->string('subject');
            $table->string('quotation_letter_file');
            $table->string('letter_status');
            $table->string('letter_type');

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
        Schema::table('tt_quotation_letter', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('tt_quotation_letter');
    }
};
