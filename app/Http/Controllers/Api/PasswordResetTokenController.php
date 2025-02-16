<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;

class PasswordResetTokenController extends Controller
{
    // Menampilkan semua token reset password
    public function index()
    {
        $tokens = PasswordResetToken::all();
        return response()->json($tokens);
    }

    // Menyimpan token reset password baru
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'token' => 'required|string',
        ]);

        $token = PasswordResetToken::create([
            'username' => $request->username,
            'token' => $request->token,
            'created_at' => now(),
        ]);

        return response()->json($token, 201);
    }

    // Menghapus token reset password berdasarkan username
    public function destroy($username)
    {
        $token = PasswordResetToken::find($username);
        if (!$token) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        $token->delete();
        return response()->json(['message' => 'Token deleted successfully']);
    }
}

