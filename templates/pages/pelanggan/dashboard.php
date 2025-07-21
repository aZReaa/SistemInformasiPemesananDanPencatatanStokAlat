<?php 
$title = 'Dashboard Pelanggan - Sistem Penyewaan Alat Pesta Haqiqah';
include __DIR__ . '/../../layouts/header.php';

$pelanggan = new Pelanggan();
$transaksi = new Transaksi();

$pelanggan->setId($_SESSION['pelanggan_id']);
$dataPelanggan = $pelanggan->getById($_SESSION['pelanggan_id']);
$alatTersedia = $pelanggan->getAllAlat();
$riwayatTransaksi = $transaksi->getByPelanggan($_SESSION['pelanggan_id']);
?>

<div class="dashboard">
    <aside class="sidebar">
        <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
            <h3 style="color: var(--primary-color); margin: 0;">Panel Pelanggan</h3>
            <p style="margin: 0.5rem 0 0 0; color: var(--secondary-color);">
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </p>
        </div>
        <ul class="sidebar-nav">
            <li><a href="/hakikah/dashboard" class="active">Dashboard</a></li>
            <li><a href="/hakikah/pelanggan/alat">Katalog Alat</a></li>
            <li><a href="/hakikah/pelanggan/pesanan">Pesanan Saya</a></li>
            <li><a href="/hakikah/pelanggan/riwayat">Riwayat Transaksi</a></li>
            <li><a href="/hakikah/pelanggan/profil">Profil Saya</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Dashboard Pelanggan</h1>
            <p style="color: var(--secondary-color);">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= count($riwayatTransaksi) ?></h3>
                <p>Total Pesanan</p>
            </div>
            <div class="stat-card">
                <h3><?= count(array_filter($riwayatTransaksi, fn($t) => $t['status'] === 'ongoing')) ?></h3>
                <p>Sedang Berjalan</p>
            </div>
            <div class="stat-card">
                <h3><?= count(array_filter($riwayatTransaksi, fn($t) => $t['status'] === 'completed')) ?></h3>
                <p>Selesai</p>
            </div>
            <div class="stat-card">
                <h3><?= count($alatTersedia) ?></h3>
                <p>Alat Tersedia</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 style="margin: 0;">Alat Pesta Tersedia</h3>
                    <a href="/hakikah/pelanggan/alat" class="btn btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (empty($alatTersedia)): ?>
                        <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                            Belum ada alat tersedia
                        </p>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <?php foreach (array_slice($alatTersedia, 0, 4) as $alat): ?>
                                <div style="border: 1px solid var(--border-color); border-radius: 0.375rem; padding: 1rem; text-align: center;">
                                    <h4 style="margin: 0 0 0.5rem 0; color: var(--primary-color);">
                                        <?= htmlspecialchars($alat['nama_alat']) ?>
                                    </h4>
                                    <p style="margin: 0 0 0.5rem 0; color: var(--secondary-color); font-size: 0.875rem;">
                                        <?= htmlspecialchars($alat['kategori']) ?>
                                    </p>
                                    <p style="margin: 0 0 0.5rem 0; font-weight: 600; color: var(--success-color);">
                                        Rp <?= number_format($alat['harga'], 0, ',', '.') ?>
                                    </p>
                                    <p style="margin: 0 0 1rem 0; font-size: 0.875rem;">
                                        Stok: <?= $alat['stok'] ?>
                                    </p>
                                    <a href="/hakikah/pelanggan/pesan/<?= $alat['id_alat'] ?>" class="btn btn-primary btn-sm">
                                        Pesan Sekarang
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Pesanan Terbaru</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($riwayatTransaksi)): ?>
                        <p style="text-align: center; color: var(--secondary-color); margin: 1rem 0;">
                            Belum ada pesanan
                        </p>
                        <div style="text-align: center;">
                            <a href="/hakikah/pelanggan/alat" class="btn btn-primary">Mulai Pesan</a>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($riwayatTransaksi, 0, 3) as $t): ?>
                            <div style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem; margin-bottom: 0.75rem;">
                                <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                    <?= htmlspecialchars($t['nama_alat']) ?>
                                </div>
                                <div style="font-size: 0.875rem; color: var(--secondary-color); margin-bottom: 0.25rem;">
                                    <?= date('d/m/Y', strtotime($t['tgl_sewa'])) ?> - <?= date('d/m/Y', strtotime($t['tgl_kembali'])) ?>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-weight: 600; color: var(--primary-color);">
                                        Rp <?= number_format($t['total_harga'], 0, ',', '.') ?>
                                    </span>
                                    <?php
                                    $badgeClass = match($t['status']) {
                                        'pending' => 'badge-warning',
                                        'approved' => 'badge-primary',
                                        'ongoing' => 'badge-primary',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($t['status']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="/hakikah/pelanggan/pesanan" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>