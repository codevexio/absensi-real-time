<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Karyawan;

class KaryawanTest extends TestCase
{
    public function test_karyawan_creation()
    {
        $karyawan = new Karyawan([
            'nama' => 'Budi',
            'golongan' => 'III',
            'divisi' => 'IT',
            'username' => 'budi123',
            'password' => bcrypt('password123')
        ]);

        $this->assertEquals('Budi', $karyawan->nama);
        $this->assertEquals('III', $karyawan->golongan);
    }
}
