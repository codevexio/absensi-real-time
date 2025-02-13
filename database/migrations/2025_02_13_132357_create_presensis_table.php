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
            $table->unsignedBigInteger('karyawan_id'); // kolom karyawan_id yang menjadi foreign key
            $table->unsignedBigInteger('jadwal_kerja_id'); // kolom karyawan_id yang menjadi foreign key
            $table->date('tanggalPresensi');
            $table->time('waktuMasuk');
            $table->enum('statusMasuk', ['Tepat Waktu', 'Terlambat','Cuti']);
            $table->time('waktuPulang');
            $table->enum('statusPulang', ['Tepat Waktu', 'Tidak Presensi Pulang','Cuti']);
            $table->string('imageMasuk');
            $table->string('imagePulang');
            $table->string('lokasiMasuk');
            $table->string('lokasiPulang');
            $table->timestamps();
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
