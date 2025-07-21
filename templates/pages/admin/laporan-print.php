<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyewaan - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #0d6efd;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .summary {
            margin: 20px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary h3 {
            margin-top: 0;
            color: #0d6efd;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        
        th {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
        }
        
        .summary-table th {
            background-color: #f2f2f2;
            color: #333;
            width: 200px;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        
        .signature p {
            margin: 5px 0;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 30px 0 10px auto;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                margin: 0;
            }
            
            @page {
                margin: 1cm;
            }
        }
        
        .print-controls {
            margin: 20px 0;
            text-align: center;
        }
        
        .btn {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545862;
        }
    </style>
    <script>
        function printReport() {
            window.print();
        }
        
        function goBack() {
            window.history.back();
        }
    </script>
</head>
<body>
    <div class="print-controls no-print">
        <button onclick="printReport()" class="btn">🖨️ Cetak Laporan</button>
        <button onclick="goBack()" class="btn btn-secondary">← Kembali</button>
        <a href="/hakikah/admin/laporan/cetak?start_date=<?= $_GET['start_date'] ?? date('Y-m-01') ?>&end_date=<?= $_GET['end_date'] ?? date('Y-m-t') ?>&format=pdf" class="btn" target="_blank">📄 Versi PDF</a>
    </div>

    <div class="header">
        <h1>LAPORAN PENYEWAAN ALAT PESTA</h1>
        <p><strong>Hakikah Rental</strong></p>
        <p>Periode: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?></p>
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
    </div>

    <div class="summary">
        <h3>📊 Ringkasan Laporan</h3>
        <table class="summary-table">
            <tr>
                <th>Total Transaksi</th>
                <td><?= count($transaksiList) ?> transaksi</td>
            </tr>
            <tr>
                <th>Total Pendapatan</th>
                <td>Rp <?= number_format($laporan['total_pendapatan'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Rata-rata per Transaksi</th>
                <td>Rp <?= number_format(count($transaksiList) > 0 ? $laporan['total_pendapatan'] / count($transaksiList) : 0, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Transaksi Lunas</th>
                <td><?= $laporan['transaksi_lunas'] ?> transaksi</td>
            </tr>
            <tr>
                <th>Transaksi Pending</th>
                <td><?= $laporan['transaksi_pending'] ?> transaksi</td>
            </tr>
            <tr>
                <th>Total Pelanggan Aktif</th>
                <td><?= $laporan['total_pelanggan'] ?> orang</td>
            </tr>
        </table>
    </div>

    <?php if (!empty($transaksiList)): ?>
    <h3>📋 Detail Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 120px;">Pelanggan</th>
                <th style="width: 100px;">Email</th>
                <th style="width: 120px;">Alat</th>
                <th style="width: 40px;">Qty</th>
                <th style="width: 120px;">Periode Sewa</th>
                <th style="width: 80px;">Total</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalPendapatan = 0;
            foreach ($transaksiList as $t): 
                $totalPendapatan += $t['total_harga'];
            ?>
            <tr>
                <td><?= $t['id_transaksi'] ?></td>
                <td><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                <td><?= htmlspecialchars($t['nama_pelanggan']) ?></td>
                <td><?= htmlspecialchars($t['email'] ?? '-') ?></td>
                <td><?= htmlspecialchars($t['nama_alat']) ?></td>
                <td><?= $t['jumlah_alat'] ?></td>
                <td><?= date('d/m', strtotime($t['tgl_sewa'])) ?> - <?= date('d/m', strtotime($t['tgl_kembali'])) ?></td>
                <td>Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></td>
                <td>
                    <span style="color: <?= $t['status'] === 'selesai' ? 'green' : ($t['status'] === 'aktif' ? 'blue' : 'orange') ?>;">
                        <?= ucfirst($t['status']) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="7" style="text-align: right;"><strong>TOTAL PENDAPATAN:</strong></td>
                <td><strong>Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align: center; padding: 40px; color: #666;">
        <p>📝 Tidak ada transaksi dalam periode ini</p>
    </div>
    <?php endif; ?>

    <div class="signature">
        <p><?= date('d F Y') ?></p>
        <div class="signature-line"></div>
        <p><strong>Administrator</strong></p>
        <p>Hakikah Rental</p>
    </div>
</body>
</html>