<?php 
$title = 'Dashboard Admin - Sistem Penyewaan Alat Pesta Haqiqah';
include __DIR__ . '/../../layouts/header.php';

// Data sudah disediakan oleh AdminController melalui Dashboard class
// Variable $dashboardData sudah berisi semua data yang diperlukan
?>

<div class="dashboard">
    <aside class="sidebar">
        <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
            <h3 style="color: var(--primary-color); margin: 0;">Admin Panel</h3>
            <p style="margin: 0.5rem 0 0 0; color: var(--secondary-color);">
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </p>
        </div>
        <ul class="sidebar-nav">
            <li><a href="/hakikah/dashboard" class="active">Dashboard</a></li>
            <li><a href="/hakikah/admin/pelanggan">Kelola Pelanggan</a></li>
            <li><a href="/hakikah/admin/alat">Kelola Alat</a></li>
            <li><a href="/hakikah/admin/kategori">Kelola Kategori</a></li>
            <li><a href="/hakikah/admin/transaksi">Transaksi</a></li>
            <li><a href="/hakikah/admin/pembayaran">Pembayaran</a></li>
            <li><a href="/hakikah/admin/pengembalian">Pengembalian</a></li>
            <li><a href="/hakikah/admin/laporan">Laporan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Dashboard Admin</h1>
            <p style="color: var(--secondary-color);">Selamat datang di panel administrasi sistem penyewaan alat pesta</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= number_format($dashboardData['total_transaksi'] ?? 0) ?></h3>
                <p>Total Transaksi</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format($dashboardData['transaksi_hari_ini'] ?? 0) ?></h3>
                <p>Transaksi Hari Ini</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format($dashboardData['total_pelanggan'] ?? 0) ?></h3>
                <p>Total Pelanggan</p>
            </div>
            <div class="stat-card">
                <h3>Rp <?= number_format($dashboardData['total_pendapatan'] ?? 0, 0, ',', '.') ?></h3>
                <p>Total Pendapatan</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Transaksi Terbaru</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($dashboardData['transaksi_terbaru'])): ?>
                        <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                            Belum ada transaksi
                        </p>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Pelanggan</th>
                                        <th>Alat</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dashboardData['transaksi_terbaru'] as $t): ?>
                                        <tr>
                                            <td><?= $t['id_transaksi'] ?></td>
                                            <td><?= htmlspecialchars($t['nama_pelanggan']) ?></td>
                                            <td><?= htmlspecialchars($t['nama_alat']) ?></td>
                                            <td>Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></td>
                                            <td>
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
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="/hakikah/admin/transaksi" class="btn btn-outline-primary">Lihat Semua Transaksi</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Alat Terdaftar</h3>
                </div>
                <div class="card-body">
                    <div style="text-align: center; padding: 2rem 0;">
                        <h2 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                            <?= number_format($dashboardData['total_alat'] ?? 0) ?>
                        </h2>
                        <p style="color: var(--secondary-color); margin: 0;">Total Alat Terdaftar</p>
                        <div style="margin-top: 1rem;">
                            <a href="/hakikah/admin/alat" class="btn btn-outline-primary btn-sm">Kelola Alat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>