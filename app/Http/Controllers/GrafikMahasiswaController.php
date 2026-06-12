<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;

class GrafikMahasiswaController extends Controller
{
    public function index()
    {
        $dataset = Mahasiswa::all();
        $totalMahasiswa = $dataset->count();

        if ($dataset->isEmpty()) {
            return "Data mahasiswa masih kosong.";
        }

        /*
        |--------------------------------------------------------------------------
        | OUTLIER BERDASARKAN SKS LULUS (IQR)
        |--------------------------------------------------------------------------
        */

        $sksSorted = $dataset->pluck('sks_lulus')->sort()->values();

        $count = $sksSorted->count();

        $q1Index = (int) floor(($count - 1) * 0.25);
        $q3Index = (int) floor(($count - 1) * 0.75);

        $q1 = $sksSorted[$q1Index];
        $q3 = $sksSorted[$q3Index];

        $iqr = $q3 - $q1;

        $lowerBound = $q1 - (1.5 * $iqr);
        $upperBound = $q3 + (1.5 * $iqr);

        $outliers = $dataset->filter(function ($item) use ($lowerBound, $upperBound) {
            return $item->sks_lulus < $lowerBound
                || $item->sks_lulus > $upperBound;
        });

        $tepatWaktu = $dataset->where('tepat_waktu', 'Ya')->count();
        $tidakTepatWaktu = $dataset->where('tepat_waktu', 'Tidak')->count();
        
        $persenTepatWaktu = round(($tepatWaktu / $totalMahasiswa) * 100, 1);
        $persenTidakTepatWaktu = round(($tidakTepatWaktu / $totalMahasiswa) * 100, 1);
        

        /*
        |--------------------------------------------------------------------------
        | KORELASI PEARSON
        | IPK ↔ SKS LULUS
        |--------------------------------------------------------------------------
        */

        $r = $this->calculateCorrelation($dataset);

        return view('eda', compact(
            'dataset',
            'q1',
            'q3',
            'iqr',
            'lowerBound',
            'upperBound',
            'outliers',
            'r',
            'tepatWaktu',
            'tidakTepatWaktu',
            'totalMahasiswa',
            'persenTepatWaktu',
            'persenTidakTepatWaktu'
        ));
    }

    private function calculateCorrelation($collection)
    {
        $n = $collection->count();

        if ($n == 0) {
            return 0;
        }

        $sumX = $collection->sum('ipk');
        $sumY = $collection->sum('sks_lulus');

        $sumXY = $collection->reduce(function ($carry, $item) {
            return $carry + ($item->ipk * $item->sks_lulus);
        }, 0);

        $sumX2 = $collection->reduce(function ($carry, $item) {
            return $carry + pow($item->ipk, 2);
        }, 0);

        $sumY2 = $collection->reduce(function ($carry, $item) {
            return $carry + pow($item->sks_lulus, 2);
        }, 0);

        $numerator = ($n * $sumXY) - ($sumX * $sumY);

        $denominator = sqrt(
            (($n * $sumX2) - pow($sumX, 2))
            *
            (($n * $sumY2) - pow($sumY, 2))
        );

        return $denominator == 0
            ? 0
            : round($numerator / $denominator, 4);

        
    }
}