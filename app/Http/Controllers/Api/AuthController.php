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
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $karyawan = Karyawan::where('username', $request->username)->first();

            if (!$karyawan || !Hash::check($request->password, $karyawan->password)) {
                return response()->json(['message' => 'Username atau password salah'], 401);
            }

            $token = $karyawan->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'token' => $token,
                'karyawan' => [
                    'id' => $karyawan->id,
                    'nama' => $karyawan->nama,
                    'golongan' => $karyawan->golongan,
                    'divisi' => $karyawan->divisi,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan server'], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
