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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade')->index();
            $table->foreignId('jadwal_kerja_id')->constrained('jadwal_kerja')->onDelete('cascade')->index();
            $table->date('tanggalPresensi');
            $table->time('waktuMasuk')->nullable();
            $table->enum('statusMasuk', ['Tepat Waktu', 'Terlambat', 'Cuti'])->default('Tepat Waktu');
            $table->time('waktuPulang')->nullable();
            $table->enum('statusPulang', ['Tepat Waktu', 'Tidak Presensi Pulang', 'Cuti'])->default('Tidak Presensi Pulang');
            $table->string('imageMasuk')->nullable();
            $table->string('imagePulang')->nullable();
            $table->json('lokasiMasuk')->nullable();
            $table->json('lokasiPulang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
