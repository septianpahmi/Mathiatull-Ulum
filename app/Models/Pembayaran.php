<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pembayaran',
        'nominal',
        'tgl_tagihan',
        'tgl_pembayaran',
        'status',
        'id_siswa',
    ];

    public function idSiswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
