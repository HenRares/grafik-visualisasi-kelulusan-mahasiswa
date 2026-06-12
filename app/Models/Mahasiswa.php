<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'mahasiswa';

    // Primary Key
    protected $primaryKey = 'id';

    // Karena tabel tidak memiliki created_at dan updated_at
    public $timestamps = false;

    // Field yang boleh diisi mass assignment
    protected $fillable = [
        'ipk',
        'kehadiran',
        'sks_lulus',
        'status_kerja',
        'tepat_waktu'
    ];

    // Cast otomatis tipe data
    protected $casts = [
        'id' => 'integer',
        'ipk' => 'float',
        'kehadiran' => 'integer',
        'sks_lulus' => 'integer'
    ];
}

