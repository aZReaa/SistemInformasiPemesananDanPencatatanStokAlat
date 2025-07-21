<?php 
$title = 'Kelola Pelanggan - Admin Dashboard';
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
            <li><a href="/hakikah/admin/pelanggan" class="active">Kelola Pelanggan</a></li>
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
            <h1 style="margin-bottom: 0.5rem;">Kelola Pelanggan</h1>
            <p style="color: var(--secondary-color);">Manajemen data pelanggan yang terdaftar</p>
        </div>

        <!-- Search Section -->
        <div class="card" style="margin-bottom: 1rem;">
            <div class="card-body">
                <form method="GET" action="/hakikah/admin/pelanggan">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 150px; gap: 1rem; align-items: end;">
                        <div>
                            <label>Cari Pelanggan:</label>
                            <input type="text" name="search" placeholder="Nama, email, HP, username..." class="form-control" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div>
                            <label>Dari Tanggal:</label>
                            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                        </div>
                        <div>
                            <label>Sampai Tanggal:</label>
                            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                        </div>
                        <div class="btn-actions">
                            <button type="submit" class="btn btn-primary btn-submit">Filter</button>
                            <a href="/hakikah/admin/pelanggan" class="btn btn-outline-primary btn-cancel">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 style="margin: 0;">Daftar Pelanggan</h3>
                <div style="display: flex; gap: 1rem;">
                    <button class="btn btn-primary btn-submit" onclick="openModal('tambahModal')">Tambah Pelanggan</button>
                    <span class="badge badge-primary"><?= count($pelangganList) ?> Total</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($pelangganList)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Belum ada pelanggan terdaftar
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table" id="pelangganTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No. HP</th>
                                    <th>Alamat</th>
                                    <th>Tgl Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pelangganList as $pelanggan): ?>
                                    <tr>
                                        <td><?= $pelanggan['id_pelanggan'] ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($pelanggan['nama']) ?></td>
                                        <td><?= htmlspecialchars($pelanggan['email']) ?></td>
                                        <td><?= htmlspecialchars($pelanggan['no_hp']) ?></td>
                                        <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" 
                                            title="<?= htmlspecialchars($pelanggan['alamat']) ?>">
                                            <?= htmlspecialchars($pelanggan['alamat']) ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($pelanggan['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-actions" style="flex-wrap: wrap;">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="lihatDetail(<?= $pelanggan['id_pelanggan'] ?>)">
                                                    Detail
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="editPelanggan(<?= $pelanggan['id_pelanggan'] ?>)">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="resetPassword(<?= $pelanggan['id_pelanggan'] ?>)">
                                                    Reset PW
                                                </button>
                                                <form method="POST" action="/hakikah/admin/pelanggan/delete" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?= $pelanggan['id_pelanggan'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            data-confirm="Yakin ingin menghapus pelanggan ini?">
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

<!-- Modal Tambah Pelanggan -->
<div id="tambahModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Tambah Pelanggan Baru</h2>
        <form method="POST" action="/hakikah/admin/pelanggan/tambah">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="grid-column: 1 / -1;">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div>
                    <label>Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div>
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div>
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div>
                    <label>No. HP:</label>
                    <input type="text" name="no_hp" class="form-control" required>
                </div>
                <div style="grid-column: 1 / -1;">
                    <label>Alamat:</label>
                    <textarea name="alamat" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeModal('tambahModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pelanggan -->
<div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Edit Data Pelanggan</h2>
        <form method="POST" action="/hakikah/admin/pelanggan/edit" id="editForm">
            <input type="hidden" name="id" id="edit_id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="grid-column: 1 / -1;">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>
                <div>
                    <label>Username:</label>
                    <input type="text" name="username" id="edit_username" class="form-control">
                </div>
                <div>
                    <label>Email:</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div>
                    <label>No. HP:</label>
                    <input type="text" name="no_hp" id="edit_no_hp" class="form-control" required>
                </div>
                <div style="grid-column: 1 / -1;">
                    <label>Alamat:</label>
                    <textarea name="alamat" id="edit_alamat" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="resetPasswordModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Reset Password</h2>
        <form method="POST" action="/hakikah/admin/pelanggan/reset-password" id="resetPasswordForm">
            <input type="hidden" name="id" id="reset_id">
            <div style="margin-bottom: 1rem;">
                <p><strong>Pelanggan:</strong> <span id="reset_nama"></span></p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label>Password Baru:</label>
                <input type="password" name="new_password" class="form-control" required minlength="6">
            </div>
            <div style="margin-bottom: 1rem;">
                <label>Konfirmasi Password:</label>
                <input type="password" id="confirm_password" class="form-control" required minlength="6">
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('resetPasswordModal')">Batal</button>
                <button type="submit" class="btn btn-warning">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Pelanggan -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Detail Pelanggan</h2>
        <div id="detailContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
// Pelanggan data for JavaScript
const pelangganData = <?= json_encode($pelangganList) ?>;

function lihatDetail(id) {
    const pelanggan = pelangganData.find(p => p.id_pelanggan == id);
    if (pelanggan) {
        document.getElementById('detailContent').innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <strong>ID Pelanggan:</strong><br>
                    ${pelanggan.id_pelanggan}
                </div>
                <div>
                    <strong>Nama:</strong><br>
                    ${pelanggan.nama}
                </div>
                <div>
                    <strong>Username:</strong><br>
                    ${pelanggan.username || 'N/A'}
                </div>
                <div>
                    <strong>Email:</strong><br>
                    ${pelanggan.email}
                </div>
                <div>
                    <strong>No. HP:</strong><br>
                    ${pelanggan.no_hp}
                </div>
                <div style="grid-column: 1 / -1;">
                    <strong>Alamat:</strong><br>
                    ${pelanggan.alamat}
                </div>
                <div>
                    <strong>Tanggal Daftar:</strong><br>
                    ${new Date(pelanggan.created_at).toLocaleDateString('id-ID')}
                </div>
                <div>
                    <strong>Terakhir Update:</strong><br>
                    ${new Date(pelanggan.updated_at).toLocaleDateString('id-ID')}
                </div>
            </div>
        `;
        openModal('detailModal');
    }
}

function editPelanggan(id) {
    const pelanggan = pelangganData.find(p => p.id_pelanggan == id);
    if (pelanggan) {
        document.getElementById('edit_id').value = pelanggan.id_pelanggan;
        document.getElementById('edit_nama').value = pelanggan.nama;
        document.getElementById('edit_username').value = pelanggan.username || '';
        document.getElementById('edit_email').value = pelanggan.email;
        document.getElementById('edit_no_hp').value = pelanggan.no_hp;
        document.getElementById('edit_alamat').value = pelanggan.alamat;
        openModal('editModal');
    }
}

function resetPassword(id) {
    const pelanggan = pelangganData.find(p => p.id_pelanggan == id);
    if (pelanggan) {
        document.getElementById('reset_id').value = pelanggan.id_pelanggan;
        document.getElementById('reset_nama').textContent = pelanggan.nama;
        openModal('resetPasswordModal');
    }
}

function getStatusClass(status) {
    switch(status) {
        case 'verified': return 'badge-success';
        case 'rejected': return 'badge-danger';
        default: return 'badge-warning';
    }
}

function getAkunClass(status) {
    switch(status) {
        case 'active': return 'badge-success';
        case 'suspended': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

// Password confirmation validation
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="new_password"]').value;
    const confirm = document.getElementById('confirm_password').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Password dan konfirmasi password tidak sama!');
    }
});
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>