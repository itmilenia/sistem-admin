<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('ID')->autoIncrement()->primary();
            $table->string('Nama', 100);
            $table->string('JK', 1)->nullable();
            $table->string('TmpLahir', 100)->nullable();
            $table->date('TglLahir')->nullable();
            $table->string('Agama', 50)->nullable();
            $table->string('Pendidikan', 100)->nullable();
            $table->string('Alamat', 250)->nullable();
            $table->string('Alamat_dom', 250);
            $table->string('Kota', 100)->nullable();
            $table->string('KodePos', 10)->nullable();
            $table->string('Telpon', 15)->nullable();
            $table->string('KTP', 45)->nullable();
            $table->string('Status', 30)->nullable();
            $table->integer('JA')->default(0);
            $table->date('TglMasuk')->nullable();
            $table->date('TglLulus')->nullable();
            $table->timestamp('TglUpdate')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrent()->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('Aktif');
            $table->date('TglKeluar')->nullable();
            $table->string('Jabatan', 20)->nullable();
            $table->string('Divisi', 50)->nullable();
            $table->string('Dept', 110)->nullable();
            $table->string('Cabang', 30)->nullable();
            $table->string('Golongan', 20)->nullable();
            $table->string('jeniskar', 50)->nullable();
            $table->string('statuskar', 10)->nullable();
            $table->string('no_bpjs_tk', 50)->nullable();
            $table->string('no_bpjs_kes', 50)->nullable();
            $table->date('tgl_keper_bpjs')->nullable();
            $table->integer('statBpjs')->nullable();
            $table->integer('Atasan')->nullable();
            $table->string('JamKerja', 3)->nullable();
            $table->time('total_telat')->nullable();
            $table->string('NoSuratKerja', 50)->nullable();
            $table->string('NoSuratKerja2', 50)->nullable();
            $table->date('MasaBerlaku')->nullable();
            $table->date('MasaBerlaku2')->nullable();
            $table->string('TjMakan', 2)->nullable();
            $table->integer('stat_makan')->nullable();
            $table->integer('NilaiTjMakan')->default(0);
            $table->string('TjBBM', 2)->nullable();
            $table->integer('NilaiTjBBM')->default(0);
            $table->string('stat_BBM', 30)->nullable();
            $table->string('TjAsuransi', 2)->nullable();
            $table->date('TjAssEff')->nullable();
            $table->string('TjAssPolis', 30)->nullable();
            $table->integer('TjPengobatan')->default(0);
            $table->integer('TjKerajinan')->default(0);
            $table->integer('TjLembur')->default(0);
            $table->integer('SaldoTjPengobatan')->default(0);
            $table->integer('TjUmObMinggu')->default(0);
            $table->integer('IDMesin')->nullable();
            $table->integer('StatusNoPrick')->default(0);
            $table->integer('Pajak')->default(0);
            $table->string('Npwp', 40)->nullable();
            $table->tinyInteger('hak_cuti')->nullable();
            $table->integer('jml_cuti')->nullable();
            $table->integer('jml_off')->nullable();
            $table->string('nokk', 45)->nullable();
            $table->string('email_karyawan', 100)->nullable();
            $table->string('email_atasan', 100)->nullable();
            $table->string('uname', 255);
            $table->string('pwd', 255);
            $table->integer('lvl')->comment('ID hak akses');
            $table->integer('abs')->comment('ID Jam kerja');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
