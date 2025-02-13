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
        Schema::create('approval_cuti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengajuan_cuti_id'); // Foreign key ke pengajuan cuti
            $table->unsignedBigInteger('approver_id'); // Karyawan yang harus menyetujui
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->timestamps();

            // Foreign key
            $table->foreign('pengajuan_cuti_id')->references('id')->on('pengajuan_cuti')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('karyawan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_cuti');
    }
};
