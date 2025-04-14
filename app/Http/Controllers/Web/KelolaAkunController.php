<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KelolaAkunController extends Controller
{
    public function index()
    {
        $akun = Karyawan::paginate(10); // Ambil semua data karyawan dengan paginasi
        return view('KelolaAkun', compact('akun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'golongan' => 'required',
            'divisi' => 'required',
            'username' => 'required|unique:karyawan',
            'password' => 'required|min:6',
        ]);

        Karyawan::create([
            'nama' => $request->nama,
            'golongan' => $request->golongan,
            'divisi' => $request->divisi,
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('web/kelola-akun')->with('success', 'Akun berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'golongan' => 'required',
            'divisi' => 'required',
            'username' => 'required|unique:karyawan,username,' . $id,
            'password' => 'nullable|min:6',
        ]);

        $data = $request->only(['nama', 'golongan', 'divisi', 'username']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $karyawan->update($data);

        return redirect()->route('web/kelola-akun')->with('success', 'Akun berhasil diperbarui');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('web/kelola-akun')->with('success', 'Akun berhasil dihapus');
    }

    public function search(Request $request)
    {
        $query = $request->get('query', ''); // Ambil query pencarian dari request

        $akun = Karyawan::where('nama', 'like', "%{$query}%")
                        ->orWhere('golongan', 'like', "%{$query}%")
                        ->orWhere('divisi', 'like', "%{$query}%")
                        ->orWhere('username', 'like', "%{$query}%")
                        ->get();

        return response()->json($akun);
    }
}
