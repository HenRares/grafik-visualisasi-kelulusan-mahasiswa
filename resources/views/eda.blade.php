
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Kelulusan Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .metric-card{
            transition: all .3s ease;
            border:none;
        }

        .metric-card:hover{
            transform: translateY(-5px);
        }
    </style>


</head>
<body class="bg-light py-4">

    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-primary px-3 py-2 mb-2">
                Grafik & Visualisasi Data
            </span>

            <h1 class="fw-bold">
                Analisis Pola Kelulusan Mahasiswa 
            </h1>

            <p class="text-muted mb-1">
                Visualisasi hubungan antara IPK, Kehadiran, dan Kelulusan Tepat Waktu
            </p>

            <p class="text-secondary small">
                Jumlah Data: <strong>{{ $totalMahasiswa }}</strong> Mahasiswa
            </p>
        </div>
    
        <div class="row mb-4">

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5>Mahasiswa Tepat Waktu</h5>

                        <h1 class="text-success">
                            {{ $tepatWaktu }}
                        </h1>

                        <small class="text-muted">
                            {{ $persenTepatWaktu }}%
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5>Tidak Tepat Waktu</h5>

                        <h1 class="text-danger">
                            {{ $tidakTepatWaktu }}
                        </h1>

                        <small class="text-muted">
                            {{ $persenTidakTepatWaktu }}%
                        </small>
                    </div>
                </div>
            </div>

        </div>
<!-- KESIMPULAN -->

        <div clas   s="card shadow-sm border-0 mb-4">
            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Kesimpulan Analisis
                </h5>

                @if($r > 0.7)

                    <div class="alert alert-success mb-0">
                        <strong>Hubungan Sangat Kuat!</strong><br>
                        Nilai korelasi menunjukkan bahwa mahasiswa dengan IPK tinggi cenderung memiliki tingkat kehadiran yang tinggi.
                    </div>

                @elseif($r > 0.4)

                    <div class="alert alert-warning mb-0">
                        <strong>Hubungan Sedang.</strong><br>
                        IPK memiliki hubungan dengan tingkat kehadiran, namun masih terdapat faktor lain yang mempengaruhi kelulusan mahasiswa.
                    </div>

                @else
                            
                    <div class="alert alert-danger mb-0">

                        <strong>Hubungan Sangat Lemah.</strong><br>

                        Nilai korelasi Pearson sebesar {{ $r }}
                        menunjukkan bahwa hubungan linear antara
                        IPK dan jumlah SKS lulus sangat lemah.

                        Scatter plot menunjukkan hubungan antara
                        IPK dan tingkat kehadiran mahasiswa.

                        Titik hijau menunjukkan mahasiswa yang
                        lulus tepat waktu, sedangkan titik merah
                        menunjukkan mahasiswa yang tidak lulus tepat waktu.

                        Visualisasi ini digunakan untuk mengamati
                        pola kelulusan mahasiswa berdasarkan kedua
                        variabel tersebut.

                    </div>

                @endif

            </div>
        </div>

<!-- METRIK -->

        <div class="row g-4 mb-4">

            <div class="col-md-6">

                <div class="card metric-card shadow-sm h-100">

                    <div class="card-body text-center">

                        <h6 class="text-uppercase text-muted">
                            KORELASI PEARSON (IPK VS SKS LULUS)
                        </h6>

                        <h1 class="display-1 fw-bold {{ $r > 0.5 ? 'text-success' : 'text-danger' }}">
                            {{ $r }}
                        </h1>

                        <p class="text-muted small">
                            Nilai berkisar antara -1 sampai +1.
                            Semakin mendekati +1 maka hubungan semakin kuat.
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card metric-card shadow-sm h-100">

                    <div class="card-body">

                        <h6 class="text-uppercase text-muted mb-3">
                            Deteksi Outlier SKS Lulus
                        </h6>

                        <div class="bg-light rounded p-3 mb-3">

                            <span class="small text-muted">
                                Batas Atas Outlier SKS Lulus (Metode IQR)
                            </span>

                            <h4 class="fw-bold">
                                {{ number_format($upperBound,2) }}
                            </h4>

                        </div>

                        <div style="max-height:150px; overflow-y:auto;">

                            @forelse($outliers as $outlier)

                                <div class="small text-danger mb-2">

                                    <strong>ID {{ $outlier->id }}</strong><br>

                                    SKS Lulus : {{ $outlier->sks_lulus }}
                                    IPK : {{ $outlier->ipk }}

                                </div>

                            @empty

                                <div class="text-success">
                                    Tidak ditemukan outlier pada data SKS Lulus.
                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

        </div>

<!-- SCATTER PLOT -->

    <div class="card shadow-sm border-0">

        <div class="card-header bg-dark text-white fw-bold">
            Scatter Plot Kelulusan Mahasiswa
        </div>

        <div class="card-body pb-0">
            <div class="mb-3">
                <span class="badge bg-success">
                    Lulus Tepat Waktu
                </span>
                <span class="badge bg-danger">
                    Tidak Tepat Waktu
                </span>
            </div>
        </div>

        <div class="card-body">

            <p class="small text-muted">
                Sumbu X menunjukkan IPK, sumbu Y menunjukkan tingkat kehadiran. Titik hijau menunjukkan mahasiswa yang lulus tepat waktu, sedangkan titik merah menunjukkan mahasiswa yang tidak lulus tepat waktu.
            </p>

            <div style="height:500px;">
                <canvas id="scatterChart"></canvas>
            </div>

        </div>

    </div>

</div>

<script>

    const dbData = @json($dataset);

    const points = dbData.map(item => ({
        x: parseFloat(item.ipk),
        y: parseInt(item.kehadiran),
        status: item.tepat_waktu,
        id: item.id
    }));

    const ctx = document.getElementById('scatterChart');

    new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [
                {
                    label: 'Mahasiswa',
                    data: points,
                    backgroundColor: function(context){
                        const point = context.raw;
                        return point.status === 'Ya'
                            ? '#22c55e'
                            : '#ef4444';
                    },
                    pointRadius: function(context){
                        const point = context.raw;
                        return point.x > {{ $upperBound }}
                            ? 8
                            : 5;
                    }
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'IPK'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Kehadiran (%)'
                    },
                    min: 50,
                    max: 100
                }
            },

            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context){
                            let p = context.raw;
                            return [
                                'ID : ' + p.id,
                                'IPK : ' + p.x,
                                'Kehadiran : ' + p.y + '%',
                                'Tepat Waktu : ' + p.status
                            ];
                        }
                    }
                }
            }
        }
    });

</script>

</body>
</html>

