<?php 
$title = 'Kelola Pengembalian - Admin Dashboard';
include __DIR__ . '/../../layouts/header.php';

// Get completed transactions that haven't been returned yet
$db = Database::getInstance();
$availableTransaksi = $db->fetchAll("
    SELECT t.*, p.nama as nama_pelanggan, a.nama_alat 
    FROM transaksi t 
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
    JOIN alat a ON t.id_alat = a.id_alat 
    WHERE t.status IN ('approved', 'ongoing') 
    AND t.id_transaksi NOT IN (SELECT id_transaksi FROM pengembalian)
    ORDER BY t.tgl_kembali ASC
");
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
            <li><a href="/hakikah/admin/pembayaran">Pembayaran</a></li>
            <li><a href="/hakikah/admin/pengembalian" class="active">Pengembalian</a></li>
            <li><a href="/hakikah/admin/laporan">Laporan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Kelola Pengembalian Alat</h1>
            <p style="color: var(--secondary-color);">Manajemen pengembalian alat dan kondisinya</p>
        </div>

        <!-- Statistics Cards -->
        <?php 
        $pengembalianModel = new Pengembalian();
        $statistik = $pengembalianModel->getStatistikKondisi();
        ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="stats-card">
                <div class="stats-number" style="color: var(--success-color);"><?= $statistik['baik'] ?? 0 ?></div>
                <div class="stats-label">Kondisi Baik</div>
            </div>
            <div class="stats-card">
                <div class="stats-number" style="color: var(--warning-color);"><?= $statistik['rusak'] ?? 0 ?></div>
                <div class="stats-label">Kondisi Rusak</div>
            </div>
            <div class="stats-card">
                <div class="stats-number" style="color: var(--danger-color);"><?= $statistik['hilang'] ?? 0 ?></div>
                <div class="stats-label">Hilang</div>
            </div>
            <div class="stats-card">
                <div class="stats-number" style="color: var(--primary-color);">Rp <?= number_format($statistik['total_denda'] ?? 0, 0, ',', '.') ?></div>
                <div class="stats-label">Total Denda</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Form untuk catat pengembalian baru -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Catat Pengembalian Baru</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($availableTransaksi)): ?>
                        <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                            Tidak ada transaksi yang perlu dikembalikan
                        </p>
                    <?php else: ?>
                        <form method="POST" action="/hakikah/admin/pengembalian/tambah">
                            <div class="form-group">
                                <label for="id_transaksi" class="form-label">Pilih Transaksi</label>
                                <select id="id_transaksi" name="id_transaksi" class="form-control" required>
                                    <option value="">Pilih transaksi...</option>
                                    <?php foreach ($availableTransaksi as $transaksi): ?>
                                        <option value="<?= $transaksi['id_transaksi'] ?>" 
                                                data-tgl-kembali="<?= $transaksi['tgl_kembali'] ?>"
                                                data-pelanggan="<?= htmlspecialchars($transaksi['nama_pelanggan']) ?>"
                                                data-alat="<?= htmlspecialchars($transaksi['nama_alat']) ?>"
                                                data-jumlah="<?= $transaksi['jumlah_alat'] ?>">
                                            #<?= $transaksi['id_transaksi'] ?> - <?= htmlspecialchars($transaksi['nama_pelanggan']) ?> 
                                            (<?= htmlspecialchars($transaksi['nama_alat']) ?>) - 
                                            Jatuh Tempo: <?= date('d/m/Y', strtotime($transaksi['tgl_kembali'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tanggal" class="form-label">Tanggal Pengembalian</label>
                                <input type="date" id="tanggal" name="tanggal" class="form-control" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="kondisi_alat" class="form-label">Kondisi Alat</label>
                                <select id="kondisi_alat" name="kondisi_alat" class="form-control" required>
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea id="catatan" name="catatan" class="form-control" rows="3" 
                                          placeholder="Catatan kondisi alat atau keterangan lainnya..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="denda" class="form-label">Denda (Rp)</label>
                                <input type="number" id="denda" name="denda" class="form-control" min="0" 
                                       value="0" placeholder="0">
                                <small class="form-text">Kosongkan jika tidak ada denda</small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-submit">Catat Pengembalian</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Pengembalian -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Riwayat Pengembalian</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($pengembalianList)): ?>
                        <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                            Belum ada riwayat pengembalian
                        </p>
                    <?php else: ?>
                        <div style="max-height: 500px; overflow-y: auto;">
                            <?php foreach (array_slice($pengembalianList, 0, 10) as $pengembalian): ?>
                                <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 0.5rem;">
                                        <div>
                                            <strong><?= htmlspecialchars($pengembalian['nama_pelanggan']) ?></strong>
                                            <div style="font-size: 0.9rem; color: var(--secondary-color);">
                                                <?= htmlspecialchars($pengembalian['nama_alat']) ?>
                                            </div>
                                        </div>
                                        <span class="badge badge-<?= $pengembalian['kondisi_alat'] === 'baik' ? 'success' : ($pengembalian['kondisi_alat'] === 'rusak' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($pengembalian['kondisi_alat']) ?>
                                        </span>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--secondary-color);">
                                        Dikembalikan: <?= date('d/m/Y', strtotime($pengembalian['tanggal'])) ?>
                                        <?php if ($pengembalian['denda'] > 0): ?>
                                            <br>Denda: Rp <?= number_format($pengembalian['denda'], 0, ',', '.') ?>
                                        <?php endif; ?>
                                        <?php if ($pengembalian['catatan']): ?>
                                            <br>Catatan: <?= htmlspecialchars($pengembalian['catatan']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($pengembalianList) > 10): ?>
                            <div style="text-align: center; margin-top: 1rem;">
                                <a href="#" onclick="showAllPengembalian()" class="btn btn-outline-primary">
                                    Lihat Semua (<?= count($pengembalianList) ?> pengembalian)
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Daftar lengkap pengembalian -->
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 style="margin: 0;">Daftar Pengembalian Lengkap</h3>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <select id="filterKondisi" class="form-control" style="width: 150px;">
                        <option value="">Semua Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="rusak">Rusak</option>
                        <option value="hilang">Hilang</option>
                    </select>
                    <input type="text" id="searchPengembalian" placeholder="Cari pengembalian..." 
                           class="form-control" style="width: 250px;">
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($pengembalianList)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Belum ada data pengembalian
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table" id="pengembalianTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pelanggan</th>
                                    <th>Alat</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Kondisi</th>
                                    <th>Denda</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengembalianList as $pengembalian): ?>
                                    <tr>
                                        <td><?= $pengembalian['id_pengembalian'] ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($pengembalian['nama_pelanggan']) ?></td>
                                        <td><?= htmlspecialchars($pengembalian['nama_alat']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($pengembalian['tanggal'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $pengembalian['kondisi_alat'] === 'baik' ? 'success' : ($pengembalian['kondisi_alat'] === 'rusak' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($pengembalian['kondisi_alat']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($pengembalian['denda'] > 0): ?>
                                                <span style="color: var(--danger-color); font-weight: 600;">
                                                    Rp <?= number_format($pengembalian['denda'], 0, ',', '.') ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?= htmlspecialchars($pengembalian['catatan']) ?: '-' ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewDetail(<?= htmlspecialchars(json_encode($pengembalian)) ?>)">
                                                Detail
                                            </button>
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

<!-- Modal Detail Pengembalian -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Detail Pengembalian</h2>
        <div id="detailContent">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
// Auto calculate fine for late returns
document.getElementById('id_transaksi').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.value) {
        const tglKembali = new Date(option.dataset.tglKembali);
        const today = new Date();
        const tanggalInput = document.getElementById('tanggal');
        const dendaInput = document.getElementById('denda');
        
        if (today > tglKembali) {
            const daysDiff = Math.ceil((today - tglKembali) / (1000 * 60 * 60 * 24));
            const fine = daysDiff * 10000; // Rp 10,000 per day
            dendaInput.value = fine;
        } else {
            dendaInput.value = 0;
        }
    }
});

// Filter and search functionality
document.getElementById('filterKondisi').addEventListener('change', filterTable);
document.getElementById('searchPengembalian').addEventListener('input', filterTable);

function filterTable() {
    const filterKondisi = document.getElementById('filterKondisi').value.toLowerCase();
    const searchTerm = document.getElementById('searchPengembalian').value.toLowerCase();
    const table = document.getElementById('pengembalianTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        const kondisi = row.cells[4].textContent.toLowerCase();
        const text = row.textContent.toLowerCase();
        
        const kondisiMatch = !filterKondisi || kondisi.includes(filterKondisi);
        const searchMatch = !searchTerm || text.includes(searchTerm);
        
        row.style.display = (kondisiMatch && searchMatch) ? '' : 'none';
    }
}

function viewDetail(pengembalian) {
    const content = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
                <strong>ID Pengembalian:</strong> ${pengembalian.id_pengembalian}<br>
                <strong>ID Transaksi:</strong> ${pengembalian.id_transaksi}<br>
                <strong>Pelanggan:</strong> ${pengembalian.nama_pelanggan}<br>
                <strong>Alat:</strong> ${pengembalian.nama_alat}<br>
                <strong>Jumlah:</strong> ${pengembalian.jumlah_alat} unit
            </div>
            <div>
                <strong>Tanggal Kembali:</strong> ${new Date(pengembalian.tanggal).toLocaleDateString('id-ID')}<br>
                <strong>Kondisi:</strong> <span class="badge badge-${pengembalian.kondisi_alat === 'baik' ? 'success' : (pengembalian.kondisi_alat === 'rusak' ? 'warning' : 'danger')}">${pengembalian.kondisi_alat}</span><br>
                <strong>Denda:</strong> Rp ${new Intl.NumberFormat('id-ID').format(pengembalian.denda || 0)}<br>
                <strong>Dicatat:</strong> ${new Date(pengembalian.created_at).toLocaleDateString('id-ID')}
            </div>
        </div>
        ${pengembalian.catatan ? `<div style="margin-top: 1rem;"><strong>Catatan:</strong><br>${pengembalian.catatan}</div>` : ''}
    `;
    
    document.getElementById('detailContent').innerHTML = content;
    openModal('detailModal');
}

function showAllPengembalian() {
    // Scroll to the complete list
    document.querySelector('.card:last-of-type').scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>