<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_berhasil()
    {
        $karyawan = Karyawan::factory()->create([
            'username' => 'karyawan_test',
            // password sudah di-hash di factory
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'karyawan_test',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'karyawan' => [
                    'id',
                    'nama',
                    'golongan',
                    'divisi'
                ]
            ]
        ]);
    }

}
