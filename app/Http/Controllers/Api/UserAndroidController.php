<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAndroid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAndroidController extends Controller
{
    // Menampilkan semua data user_android
    public function index()
    {
        $users = UserAndroid::all();
        return response()->json($users);
    }

    // Menyimpan data user_android baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'karyawan_id' => 'required|exists:karyawan,id',
            'username' => 'required|string|max:255|unique:user_android,username',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Create new user
        $user = UserAndroid::create([
            'karyawan_id' => $request->karyawan_id,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    // Menampilkan data user_android berdasarkan ID
    public function show($id)
    {
        $user = UserAndroid::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    // Mengupdate data user_android
    public function update(Request $request, $id)
    {
        $user = UserAndroid::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate update request (optional, depending on your needs)
        $validator = Validator::make($request->all(), [
            'karyawan_id' => 'nullable|exists:karyawan,id',
            'username' => 'nullable|string|max:255|unique:user_android,username,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Update user data
        if ($request->has('karyawan_id')) {
            $user->karyawan_id = $request->karyawan_id;
        }
        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json($user);
    }

    // Menghapus data user_android
    public function destroy($id)
    {
        $user = UserAndroid::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
