<?php

namespace App\Exports;

use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengajuanExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        $query = Pengajuan::query()->with(['user', 'pembimbing']);
        if (Auth::user()->role === 'mahasiswa') {
            $query->where('user_id', Auth::id());
        }
        return $query;
    }

    public function map($row): array
    {
        return [
            "'" . ($row->user?->custom_fields['nim'] ?? '-'),
            $row->user?->name,
            $row->prodi,
            $row->judul_skripsi,
            $row->pembimbing?->nama,
            $row->status,
            $row->jenis_naskah,
            $row->similarity_score,
            $row->created_at,
            $row->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Mahasiswa',
            'Program Studi',
            'Judul Skripsi',
            'Dosen Pembimbing',
            'Status Pengajuan',
            'Jenis Naskah',
            'Similarity (%)',
            'Tanggal Pengajuan',
            'Tanggal Selesai',
        ];
    }
}
