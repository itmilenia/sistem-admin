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
            $table->date('letter_date');
            $table->string('subject');

            $table->string('recipient_company_name');
            $table->string('recipient_attention_to');
            $table->string('recipient_address_line1')->nullable();
            $table->string('recipient_address_line2')->nullable();
            $table->string('recipient_city');
            $table->string('recipient_province');
            $table->string('recipient_postal_code');

            $table->string('letter_type');
            $table->text('letter_opening');
            $table->text('letter_note');
            $table->text('letter_ending');

            $table->integer('signature_id');
            $table->string('signature_path')->nullable();

            $table->integer('created_by');
            $table->integer('updated_by')->nullable();

            $table->foreign('signature_id')->references('ID')->on('users')->onDelete('cascade');
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
            $table->dropForeign(['signature_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('tt_quotation_letter');
    }
};
