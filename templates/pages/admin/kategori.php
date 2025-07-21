<?php 
$title = 'Kelola Kategori - Admin Dashboard';
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
            <li><a href="/hakikah/admin/kategori" class="active">Kelola Kategori</a></li>
            <li><a href="/hakikah/admin/transaksi">Transaksi</a></li>
            <li><a href="/hakikah/admin/pembayaran">Pembayaran</a></li>
            <li><a href="/hakikah/admin/pengembalian">Pengembalian</a></li>
            <li><a href="/hakikah/admin/laporan">Laporan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Kelola Kategori Layanan</h1>
            <p style="color: var(--secondary-color);">Manajemen kategori layanan penyewaan alat pesta</p>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 style="margin: 0;">Daftar Kategori</h3>
                <button class="btn btn-primary" onclick="openModal('tambahModal')">Tambah Kategori</button>
            </div>
            <div class="card-body">
                <?php if (empty($kategoriList)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Belum ada kategori tersedia
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kategoriList as $kategori): ?>
                                    <tr>
                                        <td><?= $kategori['id_kategori'] ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($kategori['nama_kategori']) ?></td>
                                        <td style="max-width: 300px;">
                                            <?= htmlspecialchars($kategori['deskripsi']) ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($kategori['created_at'])) ?></td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editKategori(<?= htmlspecialchars(json_encode($kategori)) ?>)">
                                                    Edit
                                                </button>
                                                <form method="POST" action="/hakikah/admin/kategori/delete" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?= $kategori['id_kategori'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            data-confirm="Yakin ingin menghapus kategori ini?">
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

<!-- Modal Tambah Kategori -->
<div id="tambahModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Tambah Kategori Baru</h2>
        <form method="POST" action="/hakikah/admin/kategori/tambah">
            <div class="form-group">
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" required 
                       value="<?= htmlspecialchars($_SESSION['old_data']['nama_kategori'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($_SESSION['old_data']['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="btn-actions">
                <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeModal('tambahModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Edit Kategori</h2>
        <form method="POST" action="/hakikah/admin/kategori/edit" id="editForm">
            <input type="hidden" id="edit_id" name="id">
            
            <div class="form-group">
                <label for="edit_nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" id="edit_nama_kategori" name="nama_kategori" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                <textarea id="edit_deskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
            </div>

            <div class="btn-actions">
                <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-submit">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editKategori(kategori) {
    document.getElementById('edit_id').value = kategori.id_kategori;
    document.getElementById('edit_nama_kategori').value = kategori.nama_kategori;
    document.getElementById('edit_deskripsi').value = kategori.deskripsi || '';
    
    openModal('editModal');
}

// Auto open modal if there are errors
<?php if (isset($_SESSION['errors']) || isset($_SESSION['old_data'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openModal('tambahModal');
    });
<?php endif; ?>
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>