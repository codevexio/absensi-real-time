<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Tabel cuti
    public function up()
    {
        Schema::create('cuti', function (Blueprint $table) {
            $table->id(); // kolom id untuk cuti
            $table->unsignedBigInteger('karyawan_id'); // kolom karyawan_id yang menjadi foreign key
            $table->integer('cutiTahun')->default(12); // jumlah cuti tahun yang diberikan
            $table->timestamp('expiredTahun')->nullable(); // masa expired cuti tahunan
            $table->integer('cutiPanjang')->default(60); // jumlah cuti panjang yang diberikan
            $table->timestamp('expiredPanjang')->nullable(); // masa expired cuti panjang
            $table->timestamps(); // created_at dan updated_at

            // Membuat foreign key pada karyawan_id
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('cuti');
    }
};