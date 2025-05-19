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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama karyawan
            $table->string('username');
            $table->string('password');
            $table->enum('golongan', ['Direksi', 'Kepala Bagian', 'Kepala SubBagian', 'Asisten', 'Staff']); // Golongan
            $table->enum('divisi', ['Bag.Sekper', 'Bag.SPI', 'Bag.SDM', 'Bag.Tanaman', 'Bag.Teknik & Pengolahan', 
                                    'Bag.Keuangan', 'Bag.Pemasaran & P.Baku', 'Bag.Perencana Strategis', 
                                    'Bag.Hukum', 'Bag.Pengadaan & TI', 'Keamanan', 'Papam', 
                                    'Bag.Percepetan Transformasi Teknologi', 'Bag.Teknik & Pengolahan' ]); // Divisi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
