<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah karyawan ada
        $karyawan = Karyawan::where('username', $request->username)->first();

        // Validasi username dan password
        if (!$karyawan || !Hash::check($request->password, $karyawan->password)) {
            return $this->responseError('Username atau password salah', 401);
        }

        // Buat token login
        $token = $karyawan->createToken('auth_token')->plainTextToken;

        // Menggunakan responseSuccess dari base controller
        return $this->responseSuccess([
            'token' => $token,
            'karyawan' => [
                'id' => $karyawan->id,
                'nama' => $karyawan->nama,
                'golongan' => $karyawan->golongan,
                'divisi' => $karyawan->divisi,
            ]
        ], 'Login berhasil');
    }

    public function logout(Request $request)
    {
        // Hapus semua token pengguna saat logout
        $request->user()->tokens()->delete();

        // Menggunakan responseSuccess dari base controller
        return $this->responseSuccess([], 'Logout berhasil');
    }
}
