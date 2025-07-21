<?php 
$title = 'Detail Pesanan #' . $pesanan['id_transaksi'] . ' - Dashboard Pelanggan';
include __DIR__ . '/../../layouts/header.php';

// Get return information if exists
$db = Database::getInstance();
$pengembalian = $db->fetch("SELECT * FROM pengembalian WHERE id_transaksi = ?", [$pesanan['id_transaksi']]);
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
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <a href="/hakikah/pelanggan/pesanan" class="btn btn-outline-primary" style="padding: 0.5rem 1rem;">
                    ← Kembali
                </a>
                <h1 style="margin: 0;">Detail Pesanan #<?= $pesanan['id_transaksi'] ?></h1>
            </div>
            <p style="color: var(--secondary-color);">
                Informasi lengkap pesanan dan status transaksi
            </p>
        </div>

        <!-- Status Progress -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Status Pesanan</h3>
            </div>
            <div class="card-body">
                <?php
                $statusSteps = [
                    'pending' => ['label' => 'Menunggu Pembayaran', 'color' => 'warning', 'icon' => '⏳'],
                    'approved' => ['label' => 'Disetujui/Sedang Berlangsung', 'color' => 'primary', 'icon' => '✅'],
                    'ongoing' => ['label' => 'Sedang Berlangsung', 'color' => 'info', 'icon' => '🔄'],
                    'completed' => ['label' => 'Selesai', 'color' => 'success', 'icon' => '✅'],
                    'cancelled' => ['label' => 'Dibatalkan', 'color' => 'danger', 'icon' => '❌']
                ];
                
                $currentStatus = $pesanan['status'];
                $currentStep = $statusSteps[$currentStatus];
                ?>
                
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="display: inline-flex; align-items: center; gap: 1rem; padding: 1rem 2rem; background: var(--light-bg); border-radius: 12px; border: 2px solid var(--<?= $currentStep['color'] ?>-color);">
                        <span style="font-size: 2rem;"><?= $currentStep['icon'] ?></span>
                        <div>
                            <div style="font-size: 1.2rem; font-weight: bold; color: var(--<?= $currentStep['color'] ?>-color);">
                                <?= $currentStep['label'] ?>
                            </div>
                            <div style="color: var(--secondary-color); font-size: 0.9rem;">
                                Diperbarui: <?= date('d/m/Y H:i', strtotime($pesanan['updated_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($currentStatus === 'pending'): ?>
                    <div style="text-align: center;">
                        <a href="/hakikah/pelanggan/pembayaran/<?= $pesanan['id_transaksi'] ?>" 
                           class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">
                            Lakukan Pembayaran
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Detail Pesanan -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Informasi Pesanan</h3>
                </div>
                <div class="card-body">
                    <!-- Alat Information -->
                    <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; background: var(--light-bg);">
                        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 1rem;">
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 0.5rem 0; color: var(--primary-color);">
                                    <?= htmlspecialchars($pesanan['nama_alat']) ?>
                                </h4>
                                <span class="badge badge-secondary"><?= htmlspecialchars($pesanan['kategori']) ?></span>
                            </div>
                            <?php if (!empty($pesanan['gambar'])): ?>
                                <div style="width: 100px; height: 80px; background: white; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
                                    <img src="/hakikah/<?= htmlspecialchars($pesanan['gambar']) ?>" 
                                         alt="<?= htmlspecialchars($pesanan['nama_alat']) ?>"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($pesanan['deskripsi'])): ?>
                            <div style="color: var(--secondary-color); margin-bottom: 1rem;">
                                <?= htmlspecialchars($pesanan['deskripsi']) ?>
                            </div>
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                            <div>
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">Jumlah</div>
                                <div style="font-weight: 600; font-size: 1.1rem;"><?= $pesanan['jumlah_alat'] ?> unit</div>
                            </div>
                            <div>
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">Harga Satuan</div>
                                <div style="font-weight: 600; font-size: 1.1rem;">Rp <?= number_format($pesanan['harga'], 0, ',', '.') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Period -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; text-align: center;">
                            <div style="color: var(--secondary-color); font-size: 0.9rem; margin-bottom: 0.5rem;">Tanggal Sewa</div>
                            <div style="font-weight: 600; font-size: 1.1rem; color: var(--primary-color);">
                                <?= date('d/m/Y', strtotime($pesanan['tgl_sewa'])) ?>
                            </div>
                        </div>
                        <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; text-align: center;">
                            <div style="color: var(--secondary-color); font-size: 0.9rem; margin-bottom: 0.5rem;">Tanggal Kembali</div>
                            <div style="font-weight: 600; font-size: 1.1rem; color: var(--danger-color);">
                                <?= date('d/m/Y', strtotime($pesanan['tgl_kembali'])) ?>
                            </div>
                        </div>
                    </div>

                    <?php 
                    $durasi = (strtotime($pesanan['tgl_kembali']) - strtotime($pesanan['tgl_sewa'])) / (60 * 60 * 24) + 1;
                    ?>
                    <div style="background: var(--light-bg); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: between; margin-bottom: 0.5rem;">
                            <span>Durasi Sewa:</span>
                            <span style="font-weight: 600;"><?= $durasi ?> hari</span>
                        </div>
                        <div style="display: flex; justify-content: between; margin-bottom: 0.5rem;">
                            <span>Subtotal:</span>
                            <span>Rp <?= number_format($pesanan['harga'] * $pesanan['jumlah_alat'], 0, ',', '.') ?></span>
                        </div>
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.5rem; margin-top: 0.5rem;">
                            <div style="display: flex; justify-content: between; font-size: 1.2rem; font-weight: 700; color: var(--primary-color);">
                                <span>Total Pembayaran:</span>
                                <span>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        <h4 style="margin: 0 0 1rem 0;">Timeline Pesanan</h4>
                        <div style="position: relative; padding-left: 2rem;">
                            <div style="position: absolute; left: 0.5rem; top: 0; bottom: 0; width: 2px; background: var(--border-color);"></div>
                            
                            <div style="position: relative; margin-bottom: 1rem;">
                                <div style="position: absolute; left: -1.5rem; width: 12px; height: 12px; background: var(--primary-color); border-radius: 50%;"></div>
                                <div style="font-weight: 600;">Pesanan Dibuat</div>
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">
                                    <?= date('d/m/Y H:i', strtotime($pesanan['created_at'])) ?>
                                </div>
                            </div>

                            <?php if ($pembayaran): ?>
                                <div style="position: relative; margin-bottom: 1rem;">
                                    <div style="position: absolute; left: -1.5rem; width: 12px; height: 12px; background: var(--warning-color); border-radius: 50%;"></div>
                                    <div style="font-weight: 600;">Pembayaran Dikonfirmasi</div>
                                    <div style="color: var(--secondary-color); font-size: 0.9rem;">
                                        <?= date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])) ?>
                                    </div>
                                    <div style="color: var(--secondary-color); font-size: 0.8rem;">
                                        Status: <span class="badge badge-<?= $pembayaran['status_pembayaran'] === 'verified' ? 'success' : ($pembayaran['status_pembayaran'] === 'pending' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($pembayaran['status_pembayaran']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($pesanan['status'] === 'completed' && $pengembalian): ?>
                                <div style="position: relative; margin-bottom: 1rem;">
                                    <div style="position: absolute; left: -1.5rem; width: 12px; height: 12px; background: var(--success-color); border-radius: 50%;"></div>
                                    <div style="font-weight: 600;">Alat Dikembalikan</div>
                                    <div style="color: var(--secondary-color); font-size: 0.9rem;">
                                        <?= date('d/m/Y', strtotime($pengembalian['tanggal'])) ?>
                                    </div>
                                    <div style="color: var(--secondary-color); font-size: 0.8rem;">
                                        Kondisi: <span class="badge badge-<?= $pengembalian['kondisi_alat'] === 'baik' ? 'success' : ($pengembalian['kondisi_alat'] === 'rusak' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($pengembalian['kondisi_alat']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Actions -->
            <div>
                <!-- Payment Information -->
                <?php if ($pembayaran): ?>
                    <div class="card" style="margin-bottom: 1rem;">
                        <div class="card-header">
                            <h4 style="margin: 0;">Informasi Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <div style="margin-bottom: 1rem;">
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">Jumlah Dibayar</div>
                                <div style="font-weight: 600; font-size: 1.1rem;">
                                    Rp <?= number_format($pembayaran['jumlah'], 0, ',', '.') ?>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 1rem;">
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">Status</div>
                                <span class="badge badge-<?= $pembayaran['status_pembayaran'] === 'verified' ? 'success' : ($pembayaran['status_pembayaran'] === 'pending' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($pembayaran['status_pembayaran']) ?>
                                </span>
                            </div>

                            <?php if ($pembayaran['bukti_transfer']): ?>
                                <div style="margin-bottom: 1rem;">
                                    <div style="color: var(--secondary-color); font-size: 0.9rem; margin-bottom: 0.5rem;">Bukti Transfer</div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewBuktiTransfer('<?= htmlspecialchars($pembayaran['bukti_transfer']) ?>')">
                                        Lihat Bukti
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if ($pembayaran['verified_at']): ?>
                                <div style="font-size: 0.8rem; color: var(--secondary-color); margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                                    Diverifikasi: <?= date('d/m/Y H:i', strtotime($pembayaran['verified_at'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Return Information -->
                <?php if ($pengembalian): ?>
                    <div class="card" style="margin-bottom: 1rem;">
                        <div class="card-header">
                            <h4 style="margin: 0;">Informasi Pengembalian</h4>
                        </div>
                        <div class="card-body">
                            <div style="margin-bottom: 1rem;">
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">Tanggal Kembali</div>
                                <div style="font-weight: 600;">
                                    <?= date('d/m/Y', strtotime($pengembalian['tanggal'])) ?>
                                </div>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">Kondisi Alat</div>
                                <span class="badge badge-<?= $pengembalian['kondisi_alat'] === 'baik' ? 'success' : ($pengembalian['kondisi_alat'] === 'rusak' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($pengembalian['kondisi_alat']) ?>
                                </span>
                            </div>

                            <?php if ($pengembalian['denda'] > 0): ?>
                                <div style="margin-bottom: 1rem;">
                                    <div style="color: var(--secondary-color); font-size: 0.9rem;">Denda</div>
                                    <div style="font-weight: 600; color: var(--danger-color);">
                                        Rp <?= number_format($pengembalian['denda'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($pengembalian['catatan']): ?>
                                <div style="margin-bottom: 1rem;">
                                    <div style="color: var(--secondary-color); font-size: 0.9rem;">Catatan</div>
                                    <div style="font-size: 0.9rem;"><?= htmlspecialchars($pengembalian['catatan']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h4 style="margin: 0;">Aksi</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($pesanan['status'] === 'pending'): ?>
                            <a href="/hakikah/pelanggan/pembayaran/<?= $pesanan['id_transaksi'] ?>" 
                               class="btn btn-primary btn-submit" style="width: 100%; margin-bottom: 0.5rem;">
                                Bayar Sekarang
                            </a>
                            <form method="POST" action="/hakikah/pelanggan/batalkan-pesanan" style="width: 100%;">
                                <input type="hidden" name="id_transaksi" value="<?= $pesanan['id_transaksi'] ?>">
                                <button type="submit" class="btn btn-outline-primary btn-cancel" style="width: 100%;" 
                                        data-confirm="Yakin ingin membatalkan pesanan ini?">
                                    Batalkan Pesanan
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-outline-primary btn-cancel" style="width: 100%; margin-bottom: 0.5rem;" 
                                    onclick="window.print()">
                                Cetak Detail
                            </button>
                            <a href="/hakikah/pelanggan/pesanan" class="btn btn-outline-primary btn-cancel" style="width: 100%;">
                                Kembali ke Daftar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="card">
                    <div class="card-header">
                        <h4 style="margin: 0;">Butuh Bantuan?</h4>
                    </div>
                    <div class="card-body">
                        <p style="margin: 0 0 1rem 0; font-size: 0.9rem; color: var(--secondary-color);">
                            Hubungi kami jika ada pertanyaan tentang pesanan Anda.
                        </p>
                        <div style="font-size: 0.9rem;">
                            <div style="margin-bottom: 0.5rem;">📞 +62 812-3456-7890</div>
                            <div style="margin-bottom: 0.5rem;">📧 info@haqiqahrental.com</div>
                            <div>🕒 08:00 - 17:00 WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal untuk melihat bukti transfer -->
<div id="buktiTransferModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Bukti Transfer</h2>
        <div style="text-align: center;">
            <img id="buktiTransferImg" src="" alt="Bukti Transfer" 
                 style="max-width: 100%; max-height: 500px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        </div>
        <div class="btn-actions" style="justify-content: flex-end; margin-top: 1.5rem;">
            <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeModal('buktiTransferModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
// Function to view bukti transfer
function viewBuktiTransfer(filePath) {
    const img = document.getElementById('buktiTransferImg');
    img.src = '/hakikah/' + filePath;
    openModal('buktiTransferModal');
}

// Confirmation for cancel order
document.addEventListener('DOMContentLoaded', function() {
    const cancelForm = document.querySelector('form[action="/hakikah/pelanggan/batalkan-pesanan"]');
    if (cancelForm) {
        cancelForm.addEventListener('submit', function(e) {
            if (!confirm('Yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    }
});

// Print styles
const printStyles = `
    <style media="print">
        .sidebar, .btn, nav, footer, .modal { display: none !important; }
        .main-content { margin-left: 0 !important; }
        .card { break-inside: avoid; }
        body { font-size: 12px; }
        h1, h2, h3, h4 { color: #000 !important; }
    </style>
`;
document.head.insertAdjacentHTML('beforeend', printStyles);
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>