<?php 
$title = 'Riwayat Transaksi - Dashboard Pelanggan';
include __DIR__ . '/../../layouts/header.php';
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
            <li><a href="/hakikah/dashboard">Dashboard</a></li>
            <li><a href="/hakikah/pelanggan/alat">Katalog Alat</a></li>
            <li><a href="/hakikah/pelanggan/pesanan">Pesanan Saya</a></li>
            <li><a href="/hakikah/pelanggan/riwayat" class="active">Riwayat Transaksi</a></li>
            <li><a href="/hakikah/pelanggan/profil">Profil Saya</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Riwayat Transaksi</h1>
            <p style="color: var(--secondary-color);">Lihat riwayat transaksi penyewaan alat yang telah selesai</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;">Transaksi Selesai</h3>
            </div>
            <div class="card-body">
                <?php if (empty($riwayat)): ?>
                    <div style="text-align: center; padding: 3rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📜</div>
                        <h3 style="color: var(--secondary-color); margin-bottom: 0.5rem;">Belum Ada Riwayat</h3>
                        <p style="color: var(--secondary-color); margin-bottom: 1.5rem;">
                            Anda belum memiliki transaksi yang selesai
                        </p>
                        <a href="/hakikah/pelanggan/alat" class="btn btn-primary">
                            Mulai Sewa Alat
                        </a>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Alat</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($riwayat as $transaksi): ?>
                                    <tr>
                                        <td style="font-weight: 600;">#<?= $transaksi['id_transaksi'] ?></td>
                                        <td><?= htmlspecialchars($transaksi['nama_alat']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($transaksi['tgl_sewa'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($transaksi['tgl_kembali'])) ?></td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?= $transaksi['jumlah_alat'] ?> unit
                                            </span>
                                        </td>
                                        <td style="font-weight: 600; color: var(--primary-color);">
                                            Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">Selesai</span>
                                        </td>
                                        <td>
                                            <a href="/hakikah/pelanggan/detail-pesanan/<?= $transaksi['id_transaksi'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 2rem; padding: 1rem; background-color: var(--light-color); border-radius: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h4 style="margin: 0; color: var(--primary-color);">Ringkasan Riwayat</h4>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div style="text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 600; color: var(--primary-color);">
                                    <?= count($riwayat) ?>
                                </div>
                                <div style="color: var(--secondary-color); font-size: 0.875rem;">
                                    Total Transaksi
                                </div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 600; color: var(--success-color);">
                                    Rp <?= number_format(array_sum(array_column($riwayat, 'total_harga')), 0, ',', '.') ?>
                                </div>
                                <div style="color: var(--secondary-color); font-size: 0.875rem;">
                                    Total Pengeluaran
                                </div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 600; color: var(--info-color);">
                                    <?= array_sum(array_column($riwayat, 'jumlah_alat')) ?>
                                </div>
                                <div style="color: var(--secondary-color); font-size: 0.875rem;">
                                    Total Unit Disewa
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="/hakikah/pelanggan/pesanan" class="btn btn-outline-primary" style="margin-right: 0.5rem;">
                ← Kembali ke Pesanan
            </a>
            <a href="/hakikah/pelanggan/alat" class="btn btn-primary">
                Sewa Alat Lagi
            </a>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>