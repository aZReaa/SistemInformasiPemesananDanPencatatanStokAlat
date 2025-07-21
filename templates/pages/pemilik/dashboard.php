<?php 
$title = 'Dashboard Pemilik - Sistem Penyewaan Alat Pesta Haqiqah';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard">
    <aside class="sidebar">
        <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
            <h3 style="color: var(--primary-color); margin: 0;">Panel Pemilik</h3>
            <p style="margin: 0.5rem 0 0 0; color: var(--secondary-color);">
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </p>
        </div>
        <ul class="sidebar-nav">
            <li><a href="/hakikah/dashboard" class="active">Laporan Keuangan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Laporan Keuangan dan Transaksi</h1>
            <p style="color: var(--secondary-color);">Sesuai UML: Data pendapatan, total penyewaan, dan laporan pelanggan</p>
        </div>

        <!-- Summary Statistics sesuai UML Activity Diagram -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= number_format($laporanKeuangan['summary']['total_transaksi']) ?></h3>
                <p>Total Penyewaan</p>
            </div>
            <div class="stat-card">
                <h3>Rp <?= number_format($laporanKeuangan['summary']['total_pendapatan'], 0, ',', '.') ?></h3>
                <p>Total Pendapatan</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format($laporanKeuangan['summary']['total_pelanggan']) ?></h3>
                <p>Total Pelanggan</p>
            </div>
            <div class="stat-card">
                <h3>Rp <?= number_format($laporanKeuangan['summary']['rata_rata_transaksi'], 0, ',', '.') ?></h3>
                <p>Rata-rata per Transaksi</p>
            </div>
        </div>

        <!-- Detail Transaksi sesuai UML -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;">Detail Laporan Transaksi</h3>
            </div>
            <div class="card-body">
                <?php if (empty($laporanKeuangan['transactions'])): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Belum ada data transaksi
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Pelanggan</th>
                                    <th>Alat</th>
                                    <th>Periode Sewa</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laporanKeuangan['transactions'] as $transaksi): ?>
                                    <tr>
                                        <td><?= $transaksi['id_transaksi'] ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($transaksi['nama_pelanggan']) ?></td>
                                        <td><?= htmlspecialchars($transaksi['nama_alat']) ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($transaksi['tgl_sewa'])) ?><br>
                                            <small style="color: var(--secondary-color);">
                                                s/d <?= date('d/m/Y', strtotime($transaksi['tgl_kembali'])) ?>
                                            </small>
                                        </td>
                                        <td style="color: var(--success-color); font-weight: 600;">
                                            Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = match($transaksi['status']) {
                                                'pending' => 'badge-warning',
                                                'approved' => 'badge-primary',
                                                'confirmed' => 'badge-primary',
                                                'ongoing' => 'badge-info',
                                                'completed' => 'badge-success',
                                                'cancelled' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($transaksi['status']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($transaksi['status_pembayaran']): ?>
                                                <?php
                                                $paymentBadge = match($transaksi['status_pembayaran']) {
                                                    'pending' => 'badge-warning',
                                                    'verified' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                    default => 'badge-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $paymentBadge ?>"><?= ucfirst($transaksi['status_pembayaran']) ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Belum bayar</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>