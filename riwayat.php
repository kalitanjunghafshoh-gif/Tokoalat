<?php
require 'koneksi.php'; require 'layout.php';

$semua = $pdo->query("SELECT id_pelanggan, nama_pelanggan, kelas FROM pelanggan ORDER BY nama_pelanggan")->fetchAll();
$id = $_GET['id'] ?? '';
$plg = null; $riwayat = []; $totalBelanja = 0;

if ($id !== '') {
    $s = $pdo->prepare("SELECT * FROM pelanggan WHERE id_pelanggan=?");
    $s->execute([$id]);
    $plg = $s->fetch();
    if ($plg) {
        $s = $pdo->prepare("
          SELECT t.id_transaksi, t.tanggal_transaksi, b.nama_barang, b.kategori, b.harga, t.jumlah_barang, t.total_harga
          FROM transaksi t
          JOIN barang b ON b.id_barang = t.id_barang
          WHERE t.id_pelanggan = ?
          ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC");
        $s->execute([$id]);
        $riwayat = $s->fetchAll();
        $totalBelanja = array_sum(array_column($riwayat, 'total_harga'));
    }
}

header_html('Riwayat Transaksi Pelanggan', 'riwayat.php');
?>
<div class="card">
  <form method="get" class="grid">
    <label>Pilih Pelanggan
      <select name="id" onchange="this.form.submit()"><option value="">— pilih pelanggan —</option>
      <?php foreach ($semua as $p): ?>
        <option value="<?= $p['id_pelanggan'] ?>" <?= $p['id_pelanggan'] == $id ? 'selected' : '' ?>><?= e($p['nama_pelanggan']) ?> (<?= e($p['kelas']) ?>)</option>
      <?php endforeach; ?></select></label>
    <div><button>Tampilkan</button></div>
  </form>
</div>

<?php if ($plg): ?>
<div class="stats">
  <div class="stat"><div class="ic">👤</div><div><b><?= e($plg['nama_pelanggan']) ?></b><span>Kelas <?= e($plg['kelas']) ?> · <?= e($plg['no_hp']) ?></span></div></div>
  <div class="stat"><div class="ic">🧾</div><div><b><?= count($riwayat) ?></b><span>Jumlah Transaksi</span></div></div>
  <div class="stat"><div class="ic">💰</div><div><b><?= rp($totalBelanja) ?></b><span>Total Belanja</span></div></div>
</div>
<div class="card wrap">
  <table>
    <tr><th>ID</th><th>Tanggal</th><th>Barang</th><th>Kategori</th><th class="r">Harga</th><th class="r">Jumlah</th><th class="r">Total</th></tr>
    <?php foreach ($riwayat as $r): ?>
    <tr><td>#<?= $r['id_transaksi'] ?></td><td><?= tgl($r['tanggal_transaksi']) ?></td><td><?= e($r['nama_barang']) ?></td>
      <td><span class="badge"><?= e($r['kategori']) ?></span></td><td class="r"><?= rp($r['harga']) ?></td>
      <td class="r"><?= $r['jumlah_barang'] ?></td><td class="r"><?= rp($r['total_harga']) ?></td></tr>
    <?php endforeach; if (!$riwayat) echo '<tr><td colspan="7" class="muted">Pelanggan ini belum pernah bertransaksi.</td></tr>'; ?>
  </table>
</div>
<?php elseif ($id !== ''): ?>
  <div class="alert err">⚠️ Pelanggan tidak ditemukan.</div>
<?php endif; ?>
<?php footer_html();
