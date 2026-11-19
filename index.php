<?php
require 'koneksi.php'; require 'layout.php';

$jmlBarang    = $pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();
$jmlPelanggan = $pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
$jmlTransaksi = $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
$omzet        = $pdo->query("SELECT COALESCE(SUM(total_harga),0) FROM transaksi")->fetchColumn();

$terakhir = $pdo->query("
  SELECT t.id_transaksi, p.nama_pelanggan, b.nama_barang, t.jumlah_barang, t.total_harga, t.tanggal_transaksi
  FROM transaksi t
  JOIN pelanggan p ON p.id_pelanggan = t.id_pelanggan
  JOIN barang b    ON b.id_barang    = t.id_barang
  ORDER BY t.id_transaksi DESC LIMIT 5")->fetchAll();
$menipis = $pdo->query("SELECT nama_barang, stok FROM barang WHERE stok <= 5 ORDER BY stok")->fetchAll();

header_html('Dashboard', 'index.php');
?>
<div class="stats">
  <div class="stat"><div class="ic">📦</div><div><b><?= $jmlBarang ?></b><span>Jumlah Barang</span></div></div>
  <div class="stat"><div class="ic">👥</div><div><b><?= $jmlPelanggan ?></b><span>Jumlah Pelanggan</span></div></div>
  <div class="stat"><div class="ic">🧾</div><div><b><?= $jmlTransaksi ?></b><span>Jumlah Transaksi</span></div></div>
  <div class="stat"><div class="ic">💰</div><div><b><?= rp($omzet) ?></b><span>Total Penjualan</span></div></div>
</div>

<div class="card wrap">
  <h2>Transaksi Terakhir</h2>
  <table>
    <tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Barang</th><th class="r">Jumlah</th><th class="r">Total</th></tr>
    <?php foreach ($terakhir as $r): ?>
    <tr><td>#<?= $r['id_transaksi'] ?></td><td><?= tgl($r['tanggal_transaksi']) ?></td><td><?= e($r['nama_pelanggan']) ?></td>
        <td><?= e($r['nama_barang']) ?></td><td class="r"><?= $r['jumlah_barang'] ?></td><td class="r"><?= rp($r['total_harga']) ?></td></tr>
    <?php endforeach; if (!$terakhir) echo '<tr><td colspan="6" class="muted">Belum ada transaksi.</td></tr>'; ?>
  </table>
</div>
<?php footer_html();
