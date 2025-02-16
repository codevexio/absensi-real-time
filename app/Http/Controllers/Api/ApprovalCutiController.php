<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\ApprovalCuti;
use App\Models\Karyawan;

class ApprovalCutiController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function approve($id)
    {
        $approval = ApprovalCuti::findOrFail($id);

        if ($approval->status !== 'Menunggu') {
            return response()->json(['message' => 'Cuti sudah diproses'], 400);
        }

        // Ambil golongan karyawan yang menyetujui
        $karyawan = Karyawan::findOrFail($approval->approver_id);
        $nextGolongan = $this->getNextGolongan($karyawan->golongan);

        if ($nextGolongan) {
            // Buat approval untuk golongan berikutnya
            ApprovalCuti::create([
                'pengajuan_cuti_id' => $approval->pengajuan_cuti_id,
                'approver_id' => Karyawan::where('golongan', $nextGolongan)->first()->id,
                'status' => 'Menunggu',
            ]);
        }

        $approval->update(['status' => 'Disetujui']);
        return response()->json(['message' => 'Approval berhasil'], 200);
    }

    public function reject($id, Request $request)
    {
        $approval = ApprovalCuti::findOrFail($id);
        $approval->update(['status' => 'Ditolak']);

        return response()->json(['message' => 'Approval ditolak'], 200);
    }

    private function getNextGolongan($golongan)
    {
        $order = ['E', 'D', 'C', 'B', 'A'];
        $index = array_search($golongan, $order);
        return $index > 0 ? $order[$index - 1] : null;
    }

    
}
