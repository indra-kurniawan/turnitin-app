<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengajuan extends Model
{
    protected $fillable = [
        'user_id',
        'judul_skripsi',
        'pembimbing_id',
        'file_skripsi',
        'no_hp',
        'prodi',
        'jenis_naskah',
        'status',
        'similarity_score',
        'surat_keterangan',
        'hasil_turnitin',
        'catatan_admin',
    ];

    protected $casts = [
        'similarity_score' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembimbing()
    {
        return $this->belongsTo(Pembimbing::class);
    }

    public function logs()
    {
        //return $this->hasMany(LogPengajuan::class, 'pengajuan_id');
    }
}
