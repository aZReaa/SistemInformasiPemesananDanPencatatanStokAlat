<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistem Penyewaan Alat Pesta Haqiqah' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/hakikah/public/css/style.css">
</head>
<body>
    <?php if (!isset($hideHeader) || !$hideHeader): ?>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="/hakikah/" class="navbar-brand">Haqiqah Rental</a>
                
                <!-- Mobile menu toggle -->
                <button class="navbar-toggle d-md-none" type="button" onclick="toggleMobileMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <!-- Navigation Menu -->
                <div class="navbar-menu" id="navbarMenu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <ul class="navbar-nav">
                            <?php if ($_SESSION['role'] === 'pelanggan'): ?>
                                <li><a href="/hakikah/dashboard" class="nav-link">Home</a></li>
                                <li><a href="/hakikah/pelanggan/alat" class="nav-link">Katalog</a></li>
                                <li><a href="/hakikah/pelanggan/pesanan" class="nav-link">Pesanan</a></li>
                            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                                <li><a href="/hakikah/dashboard" class="nav-link">Dashboard</a></li>
                                <li><a href="/hakikah/admin/alat" class="nav-link">Alat</a></li>
                                <li><a href="/hakikah/admin/transaksi" class="nav-link">Transaksi</a></li>
                            <?php elseif ($_SESSION['role'] === 'pemilik'): ?>
                                <li><a href="/hakikah/dashboard" class="nav-link">Dashboard</a></li>
                                <li><a href="/hakikah/pemilik/laporan-bulanan" class="nav-link">Laporan</a></li>
                                <li><a href="/hakikah/pemilik/analisis" class="nav-link">Analisis</a></li>
                            <?php endif; ?>
                             <!-- User Info & Actions -->
                             <li class="navbar-actions">
                                <div class="user-info">
                                    <span class="user-greeting"><?= htmlspecialchars($_SESSION['nama']) ?></span>
                                </div>
                                <a href="/hakikah/logout" class="btn btn-outline-primary btn-logout">Logout</a>
                            </li>
                        </ul>
                        
                       
                    <?php else: ?>
                    <ul class="navbar-nav">
                            <li><a href="/hakikah/" class="nav-link">Beranda</a></li>
                            <li><a href="/hakikah/login" class="nav-link">Login</a></li>
                            <li class="navbar-actions">
                                <a href="/hakikah/register" class="btn btn-primary">Daftar</a>
                            </li>
                    </ul>
                   
                        <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="container mt-3">
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
        <div class="container mt-3">
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>