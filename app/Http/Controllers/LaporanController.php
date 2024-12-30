<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);
        $data = Pembayaran::whereBetween('created_at', [$request->start, $request->end])->get();
        return Excel::download(new LaporanExport($data), 'laporan_pembayaran.xlsx');
    }
}
