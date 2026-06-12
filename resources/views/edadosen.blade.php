<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Praktikum Data - Mudah Dipahami</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .metric-card { transition: all 0.3s ease; border: none; }
        .metric-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="bg-light py-4">
    <div class="container">
        
        <div class="text-center mb-4">
            <span class="badge bg-secondary px-3 py-2 mb-2">Modul Praktikum EDA</span>
            <h1 class="fw-black text-dark">Detektif Data: Menemukan Pola</h1>
            <p class="text-muted"></p>
        </div>

        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-secondary">Kesimpulan Detektif Data Hari Ini:</h5>
                @if($r > 0.7)
                    <div class="alert alert-success border-0 p-3 mb-0">
                        <strong>Hubungan Sangat Kuat!</strong> Data membuktikan bahwa semakin rajin mahasiswa belajar, nilai ujiannya terbukti **pasti semakin tinggi**. Polanya sangat jelas dan konsisten!
                    </div>
                @elseif($r > 0.4)
                    <div class="alert alert-warning border-0 p-3 mb-0">
                        <strong>Hubungan Sedang.</strong> Jam belajar ada pengaruhnya ke nilai, tapi ada faktor lain juga yang ikut menentukan (misal: nilai tugas atau kehadiran).
                    </div>
                @else
                    <div class="alert alert-danger border-0 p-3 mb-0">
                        <strong>Hubungan Rusak / Berantakan</strong> Secara matematika, jam belajar terlihat *tidak berpengaruh* ke nilai ujian (Skor Korelasi: <strong>{{ $r }}</strong>). <br>
                        <strong>Kenapa?</strong> Coba lihat grafik di bawah! Ada data "Ajaib" (Outlier) di sebelah kanan yang merusak rumus statistik kita.
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-md-6">
                <div class="card metric-card h-100 shadow-sm text-center">
                    <div class="card-body d-flex flex-column justify-content-center p-4">
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Skor Kekuatan Hubungan (r)</h6>
                        <h1 class="display-1 fw-bold {{ $r > 0.5 ? 'text-success' : 'text-danger' }}">{{ $r }}</h1>
                        <p class="small text-muted mt-2">Rentang skor: -1 sampai +1. <br>Makin dekat ke angka 1, makin kuat hubungannya.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card metric-card h-100 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase text-muted fw-bold mb-3">Detektor Data Ajaib (Outlier)</h6>
                        <div class="p-3 bg-light rounded mb-3">
                            <span class="text-muted d-block small">Batas Wajar Jam Belajar/Minggu:</span>
                            <h4 class="fw-bold text-dark">Maksimal {{ $upperBound }} Jam</h4>
                        </div>
                        
                        <p class="small text-muted mb-2">Mahasiswa di bawah ini belajarnya melebihi batas wajar, tapi nilainya tidak sesuai (merusak rumus):</p>
                        <div style="max-height: 120px; overflow-y: auto;" class="border rounded p-2 bg-white">
                            @forelse($outliers as $outlier)
                                <div class="text-danger small mb-1">
                                    <strong>{{ $outlier->id_mahasiswa }}</strong>: Belajar <strong>{{ $outlier->jam_belajar_per_minggu }} jam</strong> (Tapi Ujian Cuma: {{ $outlier->skor_ujian }})
                                </div>
                            @empty
                                <div class="text-success small">🎉 Bersih! Tidak ada data outlier yang merusak pola.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-dark text-white fw-bold py-3">
                Peta Sebaran Data (Scatter Plot)
            </div>
            <div class="card-body p-4">
                <p class="text-muted small"><strong>Cara Baca Grafik:</strong> Setiap titik melambangkan 1 mahasiswa. Jika titik-titik naik rapi dari kiri bawah ke kanan atas, artinya datanya normal. Perhatikan jika ada titik yang terisolasi sendirian di pojok kanan!</p>
                <div style="height: 400px; width: 100%;">
                    <canvas id="easyScatterChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <script>
        const dbData = @json($dataset);
        
        // Siapkan data koordinat
        const points = dbData.map(item => ({
            x: item.jam_belajar_per_minggu,
            y: item.skor_ujian,
            id: item.id_mahasiswa
        }));

        const ctx = document.getElementById('easyScatterChart').getContext('2d');
        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Posisi Mahasiswa',
                    data: points,
                    backgroundColor: function(context) {
                        const index = context.dataIndex;
                        const value = context.dataset.data[index];
                        // Jika jam belajarnya melebihi batas atas, warnai MERAH di grafik agar mahasiswa langsung ngeh
                        return value && value.x > {{ $upperBound }} ? '#ef4444' : '#3b82f6';
                    },
                    pointRadius: function(context) {
                        const index = context.dataIndex;
                        const value = context.dataset.data[index];
                        // Perbesar ukuran titik outlier agar mencolok
                        return value && value.x > {{ $upperBound }} ? 9 : 5;
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Jam Belajar / Minggu (Makin ke kanan makin rajin)' } },
                    y: { title: { display: true, text: 'Skor Ujian (Makin ke atas makin pintar)' }, min: 0, max: 105 }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                let p = ctx.raw;
                                return `ID: ${p.id} [${p.x} Jam Belajar -> Skor: ${p.y}]`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>