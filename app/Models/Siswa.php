<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'tmp_lahir',
        'tgl_lahir',
        'gender',
        'id_user',
    ];

    public function idUser()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!$model->nama && $model->id_user) {
                $model->nama = $model->idUser->name; // Ambil nama dari relasi
            }
        });
    }
}
