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
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('jadwal_kerja_id');
            $table->date('tanggalPresensi');
            $table->time('waktuMasuk')->nullable();
            $table->enum('statusMasuk', ['Tepat Waktu', 'Terlambat', 'Cuti','Tidak Presensi Masuk'])->default('Tidak Presensi Masuk');
            $table->time('waktuPulang')->nullable();
            $table->enum('statusPulang', ['Tepat Waktu', 'Tidak Presensi Pulang', 'Cuti'])->default('Tidak Presensi Pulang');
            $table->string('imageMasuk')->nullable();
            $table->string('imagePulang')->nullable();
            $table->json('lokasiMasuk')->nullable();
            $table->json('lokasiPulang')->nullable();
            $table->timestamps();

            // Membuat foreign key pada karyawan_id
            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');
            $table->foreign('jadwal_kerja_id')->references('id')->on('jadwal_kerja')->onDelete('cascade');
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
