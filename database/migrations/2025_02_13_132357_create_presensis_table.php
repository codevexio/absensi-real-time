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
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->foreignId('jadwal_kerja_id')->constrained('jadwal_kerja')->onDelete('cascade');
            $table->date('tanggalPresensi');
            $table->time('waktuMasuk');
            $table->enum('statusMasuk', ['Tepat Waktu', 'Terlambat', 'Cuti']);
            $table->time('waktuPulang')->nullable();
            $table->enum('statusPulang', ['Tepat Waktu', 'Tidak Presensi Pulang', 'Cuti']);
            $table->string('imageMasuk')->nullable();
            $table->string('imagePulang')->nullable();
            $table->json('lokasiMasuk');
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

