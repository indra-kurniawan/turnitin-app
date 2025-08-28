<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembimbing extends Model
{
    protected $fillable = [
        'nip',
        'nama',
        'email',
        'no_hp',
        'prodi',
        'jabatan',
        'status',
    ];

    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class);
    }
}
