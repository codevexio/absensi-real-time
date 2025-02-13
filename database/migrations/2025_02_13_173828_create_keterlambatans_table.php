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
        Schema::create('keterlambatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id'); // kolom karyawan_id yang menjadi foreign key
            $table->unsignedBigInteger('presensi_id'); // kolom karyawan_id yang menjadi foreign key
            $table->timestamps();
            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');
            $table->foreign('presensi_id')->references('id')->on('presensi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keterlambatan');
    }
};
