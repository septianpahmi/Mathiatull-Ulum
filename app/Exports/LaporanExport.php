<?php

namespace App\Exports;

use App\Models\Pembayaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanExport implements FromCollection, WithHeadings
{
    protected $data;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'no_pembayaran' => $item->no_pembayaran,
                'nis' => $item->idSiswa->nis,
                'nama_siswa' => $item->idSiswa->nama,
                'kelas' => $item->idSiswa->kelas,
                'tgl_tagihan' => $item->tgl_tagihan,
                'tgl_pembayaran' => $item->tgl_pembayaran,
                'nominal' => $item->nominal,
                'status' => $item->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No Pembayaran',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Tanggal Tagihan',
            'Tanggal Pembayaran',
            'Nominal',
            'Status',
        ];
    }
}
