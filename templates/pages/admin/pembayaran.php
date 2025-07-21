<?php 
$title = 'Kelola Pembayaran - Admin Dashboard';
include __DIR__ . '/../../layouts/header.php';
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
            <li><a href="/hakikah/dashboard">Dashboard</a></li>
            <li><a href="/hakikah/admin/pelanggan">Kelola Pelanggan</a></li>
            <li><a href="/hakikah/admin/alat">Kelola Alat</a></li>
            <li><a href="/hakikah/admin/kategori">Kelola Kategori</a></li>
            <li><a href="/hakikah/admin/transaksi">Transaksi</a></li>
            <li><a href="/hakikah/admin/pembayaran" class="active">Pembayaran</a></li>
            <li><a href="/hakikah/admin/pengembalian">Pengembalian</a></li>
            <li><a href="/hakikah/admin/laporan">Laporan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Kelola Pembayaran</h1>
            <p style="color: var(--secondary-color);">Verifikasi dan kelola pembayaran dari pelanggan</p>
        </div>

        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <?php
            $pendingCount = count(array_filter($pembayaranList, fn($p) => $p['status_pembayaran'] === 'pending'));
            $verifiedCount = count(array_filter($pembayaranList, fn($p) => $p['status_pembayaran'] === 'verified'));
            $rejectedCount = count(array_filter($pembayaranList, fn($p) => $p['status_pembayaran'] === 'rejected'));
            ?>
            <div class="stat-card">
                <h3 style="color: var(--warning-color);"><?= $pendingCount ?></h3>
                <p>Menunggu Verifikasi</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--success-color);"><?= $verifiedCount ?></h3>
                <p>Terverifikasi</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--danger-color);"><?= $rejectedCount ?></h3>
                <p>Ditolak</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 style="margin: 0;">Daftar Pembayaran</h3>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <select id="filterStatus" class="form-control" style="width: 150px;">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <input type="text" id="searchPembayaran" placeholder="Cari pembayaran..." class="form-control" style="width: 250px;">
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($pembayaranList)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Belum ada pembayaran
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table" id="pembayaranTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pelanggan</th>
                                    <th>Alat</th>
                                    <th>Total Tagihan</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Bukti Transfer</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pembayaranList as $pembayaran): ?>
                                    <tr data-status="<?= $pembayaran['status_pembayaran'] ?>">
                                        <td><?= $pembayaran['id_pembayaran'] ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($pembayaran['nama_pelanggan']) ?></td>
                                        <td><?= htmlspecialchars($pembayaran['nama_alat'] ?? 'N/A') ?></td>
                                        <td style="color: var(--secondary-color);">
                                            Rp <?= number_format($pembayaran['total_harga'] ?? 0, 0, ',', '.') ?>
                                        </td>
                                        <td style="font-weight: 600; color: var(--primary-color);">
                                            Rp <?= number_format($pembayaran['jumlah'] ?? 0, 0, ',', '.') ?>
                                            <?php if (($pembayaran['jumlah'] ?? 0) != ($pembayaran['total_harga'] ?? 0)): ?>
                                                <br><small style="color: var(--danger-color);">⚠️ Tidak sesuai</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($pembayaran['bukti_transfer'])): ?>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewBuktiTransfer('<?= htmlspecialchars($pembayaran['bukti_transfer']) ?>')">
                                                    Lihat Bukti
                                                </button>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);">Tidak ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = match($pembayaran['status_pembayaran']) {
                                                'pending' => 'badge-warning',
                                                'verified' => 'badge-success',
                                                'rejected' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($pembayaran['status_pembayaran']) ?></span>
                                            <?php if (!empty($pembayaran['verified_by_name'])): ?>
                                                <br><small style="color: var(--secondary-color);">
                                                    oleh <?= htmlspecialchars($pembayaran['verified_by_name']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($pembayaran['status_pembayaran'] === 'pending'): ?>
                                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                                    <form method="POST" action="/hakikah/admin/pembayaran/verifikasi" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?= $pembayaran['id_pembayaran'] ?>">
                                                        <input type="hidden" name="status" value="verified">
                                                        <button type="submit" class="btn btn-sm btn-success">Verifikasi</button>
                                                    </form>
                                                    <form method="POST" action="/hakikah/admin/pembayaran/verifikasi" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?= $pembayaran['id_pembayaran'] ?>">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                data-confirm="Yakin ingin menolak pembayaran ini?">Tolak</button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color); font-size: 0.875rem;">
                                                    <?php if ($pembayaran['verified_at']): ?>
                                                        <?= date('d/m/Y H:i', strtotime($pembayaran['verified_at'])) ?>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </span>
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

<!-- Modal untuk melihat bukti transfer -->
<div id="buktiTransferModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Bukti Transfer</h2>
        <div style="text-align: center;">
            <img id="buktiTransferImg" src="" alt="Bukti Transfer" 
                 style="max-width: 100%; max-height: 500px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeModal('buktiTransferModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
// Filter by status
document.getElementById('filterStatus').addEventListener('change', function() {
    const selectedStatus = this.value;
    const table = document.getElementById('pembayaranTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        const status = row.getAttribute('data-status');
        if (selectedStatus === '' || status === selectedStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Search functionality
document.getElementById('searchPembayaran').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('pembayaranTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Function to view bukti transfer
function viewBuktiTransfer(filePath) {
    const img = document.getElementById('buktiTransferImg');
    img.src = '/hakikah/' + filePath;
    openModal('buktiTransferModal');
}
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>