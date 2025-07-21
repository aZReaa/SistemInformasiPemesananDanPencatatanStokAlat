<?php 
$title = 'Pesanan Saya - Dashboard Pelanggan';
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
            <li><a href="/hakikah/pelanggan/pesanan" class="active">Pesanan Saya</a></li>
            <li><a href="/hakikah/pelanggan/riwayat">Riwayat Transaksi</a></li>
            <li><a href="/hakikah/pelanggan/profil">Profil Saya</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Pesanan Saya</h1>
            <p style="color: var(--secondary-color);">Kelola dan pantau status pesanan alat pesta Anda</p>
        </div>

        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-body">
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <span style="font-weight: 600;">Filter Status:</span>
                    <a href="/hakikah/pelanggan/pesanan" 
                       class="btn <?= empty($_GET['status']) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                        Semua
                    </a>
                    <a href="/hakikah/pelanggan/pesanan?status=pending" 
                       class="btn <?= ($_GET['status'] ?? '') === 'pending' ? 'btn-warning' : 'btn-outline-primary' ?> btn-sm">
                        Pending
                    </a>
                    <a href="/hakikah/pelanggan/pesanan?status=approved" 
                       class="btn <?= ($_GET['status'] ?? '') === 'approved' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                        Approved
                    </a>
                    <a href="/hakikah/pelanggan/pesanan?status=ongoing" 
                       class="btn <?= ($_GET['status'] ?? '') === 'ongoing' ? 'btn-info' : 'btn-outline-primary' ?> btn-sm">
                        Ongoing
                    </a>
                    <a href="/hakikah/pelanggan/pesanan?status=completed" 
                       class="btn <?= ($_GET['status'] ?? '') === 'completed' ? 'btn-success' : 'btn-outline-primary' ?> btn-sm">
                        Completed
                    </a>
                </div>
            </div>
        </div>

        <?php if (empty($pesananList)): ?>
            <div class="card">
                <div class="card-body">
                    <div style="text-align: center; margin: 2rem 0;">
                        <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;">📦</div>
                        <h3 style="color: var(--secondary-color); margin-bottom: 1rem;">Belum Ada Pesanan</h3>
                        <p style="color: var(--secondary-color); margin-bottom: 2rem;">
                            Anda belum memiliki pesanan. Mulai menyewa alat pesta untuk acara spesial Anda!
                        </p>
                        <a href="/hakikah/pelanggan/alat" class="btn btn-primary">
                            Lihat Katalog Alat
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="display: grid; gap: 1.5rem;">
                <?php foreach ($pesananList as $pesanan): ?>
                    <div class="card">
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 1.5rem; align-items: start;">
                                <!-- Status Badge -->
                                <div style="text-align: center;">
                                    <?php
                                    $badgeClass = match($pesanan['status']) {
                                        'pending' => 'badge-warning',
                                        'approved' => 'badge-primary',
                                        'ongoing' => 'badge-info',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                        <?= ucfirst($pesanan['status']) ?>
                                    </span>
                                    <div style="font-size: 0.75rem; color: var(--secondary-color); margin-top: 0.5rem;">
                                        ID: #<?= $pesanan['id_transaksi'] ?>
                                    </div>
                                </div>

                                <!-- Detail Pesanan -->
                                <div>
                                    <div style="margin-bottom: 1rem;">
                                        <h4 style="margin: 0 0 0.5rem 0; color: var(--primary-color);">
                                            <?= htmlspecialchars($pesanan['nama_alat']) ?>
                                        </h4>
                                        <div style="font-size: 0.875rem; color: var(--secondary-color);">
                                            Dipesan pada: <?= date('d F Y, H:i', strtotime($pesanan['created_at'])) ?>
                                        </div>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                                        <div>
                                            <strong>Periode Sewa:</strong>
                                            <div style="color: var(--secondary-color);">
                                                <?= date('d/m/Y', strtotime($pesanan['tgl_sewa'])) ?> - 
                                                <?= date('d/m/Y', strtotime($pesanan['tgl_kembali'])) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>Jumlah:</strong>
                                            <div style="color: var(--secondary-color);">
                                                <?= $pesanan['jumlah_alat'] ?> unit
                                            </div>
                                        </div>
                                        <div>
                                            <strong>Total Biaya:</strong>
                                            <div style="font-weight: 700; color: var(--primary-color);">
                                                Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>Metode Pengambilan:</strong>
                                            <div style="color: var(--secondary-color);">
                                                <?php if ($pesanan['metode_pengambilan'] === 'pickup'): ?>
                                                    <span style="color: var(--primary-color);">
                                                        <i class="fas fa-store"></i> Pickup di Toko
                                                    </span>
                                                <?php elseif ($pesanan['metode_pengambilan'] === 'delivery_profile'): ?>
                                                    <span style="color: var(--success-color);">
                                                        <i class="fas fa-truck"></i> Delivery ke Alamat Profil
                                                    </span>
                                                <?php elseif ($pesanan['metode_pengambilan'] === 'delivery_custom'): ?>
                                                    <span style="color: var(--info-color);">
                                                        <i class="fas fa-map-marker-alt"></i> Delivery ke Alamat Custom
                                                    </span>
                                                <?php else: ?>
                                                    <!-- Fallback for old data -->
                                                    <?php if (!empty($pesanan['alamat_pengiriman'])): ?>
                                                        <span style="color: var(--success-color);">
                                                            <i class="fas fa-truck"></i> Delivery
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="color: var(--primary-color);">
                                                            <i class="fas fa-store"></i> Pickup di Toko
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>Status Pembayaran:</strong>
                                            <?php if ($pesanan['status_pembayaran']): ?>
                                                <?php
                                                $paymentBadge = match($pesanan['status_pembayaran']) {
                                                    'pending' => 'badge-warning',
                                                    'verified' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                    default => 'badge-secondary'
                                                };
                                                ?>
                                                <div>
                                                    <span class="badge <?= $paymentBadge ?>"><?= ucfirst($pesanan['status_pembayaran']) ?></span>
                                                </div>
                                            <?php else: ?>
                                                <div>
                                                    <span class="badge badge-secondary">Belum Bayar</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($pesanan['metode_pengambilan'] === 'delivery_profile' || $pesanan['metode_pengambilan'] === 'delivery_custom' || (!empty($pesanan['alamat_pengiriman']))): ?>
                                        <div style="margin-top: 1rem; padding: 1rem; background-color: var(--light-color); border-radius: 0.375rem; border-left: 4px solid var(--success-color);">
                                            <strong style="color: var(--success-color);">
                                                <i class="fas fa-map-marker-alt"></i> Alamat Pengiriman:
                                            </strong>
                                            <div style="margin-top: 0.5rem; color: var(--secondary-color);">
                                                <?php if ($pesanan['metode_pengambilan'] === 'delivery_custom' && !empty($pesanan['alamat_pengiriman'])): ?>
                                                    <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])) ?>
                                                    <br><small style="color: var(--info-color);"><i class="fas fa-info-circle"></i> Alamat Custom</small>
                                                <?php elseif ($pesanan['metode_pengambilan'] === 'delivery_profile'): ?>
                                                    <?php
                                                    // Get customer profile address
                                                    $db = Database::getInstance();
                                                    $pelanggan = $db->fetch("SELECT alamat FROM pelanggan WHERE id_pelanggan = ?", [$pesanan['id_pelanggan']]);
                                                    ?>
                                                    <?= nl2br(htmlspecialchars($pelanggan['alamat'] ?? 'Alamat profil tidak tersedia')) ?>
                                                    <br><small style="color: var(--success-color);"><i class="fas fa-user"></i> Alamat Profil</small>
                                                <?php else: ?>
                                                    <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Actions -->
                                <div class="btn-actions" style="flex-direction: column; align-items: stretch; min-width: 120px;">
                                    <a href="/hakikah/pelanggan/detail-pesanan/<?= $pesanan['id_transaksi'] ?>" 
                                       class="btn btn-outline-primary btn-cancel btn-sm">
                                        Detail
                                    </a>
                                    
                                    <?php if ($pesanan['status'] === 'pending' && !$pesanan['status_pembayaran']): ?>
                                        <a href="/hakikah/pelanggan/pembayaran/<?= $pesanan['id_transaksi'] ?>" 
                                           class="btn btn-primary btn-submit btn-sm">
                                            Bayar Sekarang
                                        </a>
                                        <form method="POST" action="/hakikah/pelanggan/batalkan-pesanan" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $pesanan['id_transaksi'] ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-cancel btn-sm" 
                                                    data-confirm="Yakin ingin membatalkan pesanan ini?">
                                                Batalkan
                                            </button>
                                        </form>
                                    <?php elseif ($pesanan['status'] === 'pending' && $pesanan['status_pembayaran'] === 'pending'): ?>
                                        <div style="text-align: center; font-size: 0.75rem; color: var(--secondary-color);">
                                            Menunggu verifikasi pembayaran
                                        </div>
                                    <?php elseif ($pesanan['status'] === 'approved'): ?>
                                        <div style="text-align: center; font-size: 0.75rem; color: var(--success-color);">
                                            ✓ Pesanan disetujui
                                        </div>
                                    <?php elseif ($pesanan['status'] === 'ongoing'): ?>
                                        <div style="text-align: center; font-size: 0.75rem; color: var(--info-color);">
                                            🚚 Alat sedang disewa
                                        </div>
                                    <?php elseif ($pesanan['status'] === 'completed'): ?>
                                        <div style="text-align: center; font-size: 0.75rem; color: var(--success-color);">
                                            ✅ Selesai
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <p style="color: var(--secondary-color);">
                    Total <?= count($pesananList) ?> pesanan ditemukan
                </p>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>