<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toko Online')</title>

    <!-- Bootstrap CDN (atau sesuaikan jika pakai asset sendiri) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    
    <!-- Konten utama -->
    @yield('content')

    <!-- Script tambahan -->
    @yield('scripts')

</body>
</html>
