<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformaMahasiswa; // Import model yang baru dibuat

class GrafikMahasiswaController extends Controller
{
    public function index()
    {
        // 1. AMBIL DATA LANGSUNG DARI DATABASE MENGGUNAKAN ELOUENT
        $dataset = PerformaMahasiswa::all();

        // Antisipasi jika database masih kosong agar tidak error divisi dengan nol
        if ($dataset->isEmpty()) {
            return "Dataset di database kosong. Sila isi tabel performa_mahasiswa terlebih dahulu.";
        }

        // 2. HITUNG OUTLIER MENGGUNAKAN METODE IQR (Berdasarkan Jam Belajar)
        $jamBelajarSorted = $dataset->pluck('jam_belajar_per_minggu')->sort()->values();
        $count = $jamBelajarSorted->count();

        // Mencari posisi Kuartil 1 (Q1) dan Kuartil 3 (Q3)
        $q1Index = floor(($count - 1) * 0.25);
        $q3Index = floor(($count - 1) * 0.75);
        
        $q1Index = (int) floor(($count - 1) * 0.25);
        $q3Index = (int) floor(($count - 1) * 0.75);

        $q1 = $jamBelajarSorted[$q1Index];
        $q3 = $jamBelajarSorted[$q3Index];
        $iqr = $q3 - $q1;

        // Menentukan Batas Atas dan Batas Bawah Outlier
        $lowerBound = $q1 - (1.5 * $iqr);
        $upperBound = $q3 + (1.5 * $iqr);

        // Filter data dari database yang nilainya di luar batas (Outlier)
        $outliers = $dataset->filter(function ($item) use ($lowerBound, $upperBound) {
            return $item->jam_belajar_per_minggu < $lowerBound || $item->jam_belajar_per_minggu > $upperBound;
        });

        // 3. HITUNG KORELASI PEARSON (r)
        $r = $this->calculateCorrelation($dataset);

        // Mengirimkan data ke Blade View
        return view('eda', compact('dataset', 'q1', 'q3', 'iqr', 'upperBound', 'outliers', 'r'));
    }

    // Fungsi matematika hitung Korelasi Pearson berdasarkan data Eloquent
    private function calculateCorrelation($collection)
    {
        $n = $collection->count();
        if ($n === 0) return 0;

        $sumX = $collection->sum('jam_belajar_per_minggu');
        $sumY = $collection->sum('skor_ujian');
        
        $sumXY = $collection->reduce(function ($carry, $item) {
            return $carry + ($item->jam_belajar_per_minggu * $item->skor_ujian);
        }, 0);

        $sumX2 = $collection->reduce(function ($carry, $item) {
            return $carry + pow($item->jam_belajar_per_minggu, 2);
        }, 0);

        $sumY2 = $collection->reduce(function ($carry, $item) {
            return $carry + pow($item->skor_ujian, 2);
        }, 0);

        // Rumus Koefisien Korelasi
        $numerator = ($n * $sumXY) - ($sumX * $sumY);
        $denominator = sqrt((($n * $sumX2) - pow($sumX, 2)) * (($n * $sumY2) - pow($sumY, 2)));

        return $denominator == 0 ? 0 : round($numerator / $denominator, 4);
    }
}