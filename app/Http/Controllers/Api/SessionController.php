<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    // Menampilkan semua sesi
    public function index()
    {
        $sessions = Session::all();
        return response()->json($sessions);
    }

    // Menyimpan sesi baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ip_address' => 'nullable|string',
            'user_agent' => 'nullable|string',
            'payload' => 'required|string',
            'last_activity' => 'required|integer',
        ]);

        $session = Session::create([
            'user_id' => $request->user_id,
            'ip_address' => $request->ip_address,
            'user_agent' => $request->user_agent,
            'payload' => $request->payload,
            'last_activity' => $request->last_activity,
        ]);

        return response()->json($session, 201);
    }

    // Menghapus sesi berdasarkan ID
    public function destroy($id)
    {
        $session = Session::find($id);
        if (!$session) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        $session->delete();
        return response()->json(['message' => 'Session deleted successfully']);
    }
}
