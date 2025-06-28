<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Handle login request and return token.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $karyawan = Karyawan::where('username', $request->username)->first();

            if (!$karyawan || !Hash::check($request->password, $karyawan->password)) {
                return response()->json([
                    'message' => 'Username atau password salah'
                ], 401); // 401 Unauthorized
            }

            $token = $karyawan->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'token' => $token,
                'karyawan' => [
                    'id' => $karyawan->id,
                    'nama' => $karyawan->nama,
                    'username' => $karyawan->username,
                    'golongan' => $karyawan->golongan,
                    'divisi' => $karyawan->divisi,
                ]
            ], 200); // 200 OK
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage() // bisa dihapus di produksi
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Handle logout and revoke tokens.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Logout berhasil'
            ], 200); // 200 OK
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Gagal logout',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }
}
