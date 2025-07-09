@extends('backend.v_layouts.app')

@section('title', 'Halaman Beranda')

@section('content')
<div class="container">
    <div class="alert alert-success">
        <strong>Selamat Datang, Administrator</strong><br>
        Ini halaman beranda Super Admin.
        <hr>
        <a href="#" class="text-primary">Belanja..? DISINI AJA !!!</a>
    </div>

    <!-- Chart -->
    <div class="card-body">
    <div style="max-width: 400px; margin: 0 auto;">
        <canvas id="produkChart" height="300"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('produkChart').getContext('2d');
    const produkChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Jumlah Produk',
                data: {!! json_encode($jumlah) !!},
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc',
                    '#f6c23e', '#e74a3b', '#858796'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            },
            // Fungsi interaktif ketika chart diklik
            onClick: (evt, activeElements) => {
                if (activeElements.length > 0) {
                    const index = activeElements[0].index;
                    const kategori = produkChart.data.labels[index];
                    // Redirect ke halaman daftar produk berdasarkan kategori
                    window.location.href = `/produk-per-kategori/${encodeURIComponent(kategori)}`;
                }
            }
        }
    });
</script>
@endsection
