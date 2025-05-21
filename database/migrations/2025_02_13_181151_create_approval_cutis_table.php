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
            $table->unsignedBigInteger('pengajuan_cuti_id'); // FK ke pengajuan cuti
            $table->unsignedBigInteger('approver_id'); // ID karyawan yang approve
            $table->string('approver_golongan'); // Golongan approver (Asisten, Kepala SubBagian, dll)
            $table->enum('status', ['Disetujui', 'Ditolak']);
            $table->text('catatan')->nullable(); // Catatan jika ditolak / disetujui
            $table->timestamps();

            // Relasi
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
