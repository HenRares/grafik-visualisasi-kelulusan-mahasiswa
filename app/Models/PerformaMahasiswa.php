<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformaMahasiswa extends Model
{
    use HasFactory;

    // Menentukan nama tabel di database
    protected $table = 'performa_mahasiswa';

    // Menentukan primary key tabel
    protected $primaryKey = 'id_mahasiswa';

    // Karena primary key kita bukan berjenis auto-incrementing integer
    public $incrementing = false;

    // Menentukan tipe data dari primary key
    protected $keyType = 'string';

    // Menonaktifkan fitur timestamp (created_at & updated_at) jika tabel Anda tidak memilikinya
    public $timestamps = false;

    // Kolom yang diizinkan untuk pengisian massal (mass assignment)
    protected $fillable = [
        'id_mahasiswa',
        'jam_belajar_per_minggu',
        'skor_ujian',
        'kehadiran_persen',
        'nilai_tugas',
        'kategori'
    ];
}