<?php 
$title = 'Kelola Alat - Admin Dashboard';
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
            <li><a href="/hakikah/admin/alat" class="active">Kelola Alat</a></li>
            <li><a href="/hakikah/admin/kategori">Kelola Kategori</a></li>
            <li><a href="/hakikah/admin/transaksi">Transaksi</a></li>
            <li><a href="/hakikah/admin/pembayaran">Pembayaran</a></li>
            <li><a href="/hakikah/admin/pengembalian">Pengembalian</a></li>
            <li><a href="/hakikah/admin/laporan">Laporan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Kelola Alat Pesta</h1>
            <p style="color: var(--secondary-color);">Manajemen inventory alat pesta yang tersedia</p>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 style="margin: 0;">Daftar Alat</h3>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <input type="text" id="searchAlat" placeholder="Cari alat..." class="form-control" style="width: 250px;">
                    <button class="btn btn-primary" onclick="openModal('tambahModal')">Tambah Alat</button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($alatList)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Belum ada alat tersedia
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table" id="alatTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Alat</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alatList as $alat): ?>
                                    <tr>
                                        <td><?= $alat['id_alat'] ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($alat['nama_alat']) ?></td>
                                        <td>
                                            <span class="badge badge-secondary" style="background-color: #6c757d !important; color: white !important;">
                                                <?= htmlspecialchars($alat['nama_kategori'] ?? 'No Category') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $alat['stok'] > 0 ? 'badge-success' : 'badge-danger' ?>">
                                                <?= $alat['stok'] ?> unit
                                            </span>
                                        </td>
                                        <td style="font-weight: 600; color: var(--primary-color);">
                                            Rp <?= number_format($alat['harga'], 0, ',', '.') ?>
                                        </td>
                                        <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?= htmlspecialchars($alat['deskripsi']) ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editAlat(<?= htmlspecialchars(json_encode($alat)) ?>)">
                                                    Edit
                                                </button>
                                                <form method="POST" action="/hakikah/admin/alat/delete" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?= $alat['id_alat'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            data-confirm="Yakin ingin menghapus alat ini?">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
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

<!-- Modal Tambah Alat -->
<div id="tambahModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Tambah Alat Baru</h2>
        <form method="POST" action="/hakikah/admin/alat/tambah" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_alat" class="form-label">Nama Alat</label>
                <input type="text" id="nama_alat" name="nama_alat" class="form-control" required 
                       value="<?= htmlspecialchars($_SESSION['old_data']['nama_alat'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="id_kategori" class="form-label">Kategori</label>
                <select id="id_kategori" name="id_kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($kategoriList as $kategori): ?>
                        <option value="<?= $kategori['id_kategori'] ?>" 
                                <?= ($_SESSION['old_data']['id_kategori'] ?? '') == $kategori['id_kategori'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kategori['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" id="stok" name="stok" class="form-control" min="0" required 
                           value="<?= htmlspecialchars($_SESSION['old_data']['stok'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="harga" class="form-label">Harga (Rp)</label>
                    <input type="number" id="harga" name="harga" class="form-control" min="0" step="1000" required 
                           value="<?= htmlspecialchars($_SESSION['old_data']['harga'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($_SESSION['old_data']['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="gambar" class="form-label">Gambar Alat</label>
                <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
                <small class="form-text text-muted">Format: JPG, PNG, GIF, WebP. Maksimal 5MB.</small>
            </div>

            <div class="btn-actions">
                <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeModal('tambahModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Alat -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Edit Alat</h2>
        <form method="POST" action="/hakikah/admin/alat/edit" id="editForm" enctype="multipart/form-data">
            <input type="hidden" id="edit_id" name="id">
            
            <div class="form-group">
                <label for="edit_nama_alat" class="form-label">Nama Alat</label>
                <input type="text" id="edit_nama_alat" name="nama_alat" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_id_kategori" class="form-label">Kategori</label>
                <select id="edit_id_kategori" name="id_kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($kategoriList as $kategori): ?>
                        <option value="<?= $kategori['id_kategori'] ?>">
                            <?= htmlspecialchars($kategori['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="edit_stok" class="form-label">Stok</label>
                    <input type="number" id="edit_stok" name="stok" class="form-control" min="0" required>
                </div>

                <div class="form-group">
                    <label for="edit_harga" class="form-label">Harga (Rp)</label>
                    <input type="number" id="edit_harga" name="harga" class="form-control" min="0" step="1000" required>
                </div>
            </div>

            <div class="form-group">
                <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                <textarea id="edit_deskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="edit_gambar" class="form-label">Gambar Alat</label>
                <input type="file" id="edit_gambar" name="gambar" class="form-control" accept="image/*">
                <small class="form-text text-muted">Format: JPG, PNG, GIF, WebP. Maksimal 5MB. Kosongkan jika tidak ingin mengubah gambar.</small>
                <div id="current_image_preview" style="margin-top: 10px;"></div>
            </div>

            <div class="btn-actions">
                <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-submit">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editAlat(alat) {
    document.getElementById('edit_id').value = alat.id_alat;
    document.getElementById('edit_nama_alat').value = alat.nama_alat;
    document.getElementById('edit_id_kategori').value = alat.id_kategori;
    document.getElementById('edit_stok').value = alat.stok;
    document.getElementById('edit_harga').value = alat.harga;
    document.getElementById('edit_deskripsi').value = alat.deskripsi || '';
    
    // Show current image if exists
    const previewDiv = document.getElementById('current_image_preview');
    if (alat.gambar) {
        previewDiv.innerHTML = `
            <p><strong>Gambar saat ini:</strong></p>
            <img src="/hakikah/public/uploads/alat/${alat.gambar}" alt="Current image" style="max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 4px;">
        `;
    } else {
        previewDiv.innerHTML = '<p><em>Belum ada gambar</em></p>';
    }
    
    openModal('editModal');
}

// Search functionality
document.getElementById('searchAlat').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('alatTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    }
});

// Auto open modal if there are errors
<?php if (isset($_SESSION['errors']) || isset($_SESSION['old_data'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openModal('tambahModal');
    });
<?php endif; ?>
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>