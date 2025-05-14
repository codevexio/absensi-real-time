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
        Schema::create('pengajuan_cuti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id'); // Foreign key ke karyawan
            $table->enum('jenisCuti', ['Cuti Panjang', 'Cuti Tahunan']);
            $table->date('tanggalMulai');
            $table->date('tanggalSelesai');
            $table->integer('jumlahHari');
            $table->enum('statusCuti', ['Diproses', 'Disetujui', 'Ditolak'])->default('Diproses'); 
            $table->string('alasanPenolakan')->nullable();
            $table->string('file_surat_cuti')->nullable(); // Kolom untuk menyimpan path PDF
            $table->timestamps();
        
            // Foreign key ke tabel karyawan
            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cuti');
    }
};
