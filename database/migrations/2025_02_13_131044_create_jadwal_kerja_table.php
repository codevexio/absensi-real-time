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
        Schema::create('jadwal_kerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id'); // kolom karyawan_id yang menjadi foreign key
            $table->unsignedBigInteger('shift_id'); // kolom karyawan_id yang menjadi foreign key
            $table->date('tanggalKerja');
            $table->enum('statusKerja', ['Kerja', 'Cuti']); // Status Kerja
            $table->timestamps();
            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shift')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_kerja');
    }
};
