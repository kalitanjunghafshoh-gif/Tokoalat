<?php
require 'koneksi.php'; require 'layout.php';
$pesan = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();   // semua langkah berhasil, atau batal semua

        if (isset($_POST['hapus'])) {
            // Hapus transaksi -> stok barang dikembalikan
            $s = $pdo->prepare("SELECT id_barang, jumlah_barang FROM transaksi WHERE id_transaksi=?");
            $s->execute([$_POST['hapus']]);
            if ($row = $s->fetch()) {
                $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang=?")
                    ->execute([$row['jumlah_barang'], $row['id_barang']]);
                $pdo->prepare("DELETE FROM transaksi WHERE id_transaksi=?")->execute([$_POST['hapus']]);
                $pesan = 'Transaksi dihapus dan stok dikembalikan.';
            }
        } else {
            // Catat transaksi baru
            $jumlah = (int)$_POST['jumlah_barang'];
            if ($jumlah < 1) throw new Exception('Jumlah barang minimal 1.');

            $s = $pdo->prepare("SELECT harga, stok FROM barang WHERE id_barang=? FOR UPDATE");
            $s->execute([$_POST['id_barang']]);
            $barang = $s->fetch();
            if (!$barang) throw new Exception('Barang tidak ditemukan.');
            if ($barang['stok'] < $jumlah) throw new Exception('Stok tidak cukup. Sisa stok: ' . $barang['stok']);

            $total = $barang['harga'] * $jumlah;   // total otomatis = harga x jumlah

            $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang=?")->execute([$jumlah, $_POST['id_barang']]);
            $pdo->prepare("INSERT INTO transaksi (id_barang,id_pelanggan,tanggal_transaksi,jumlah_barang,total_harga) VALUES (?,?,?,?,?)")
                ->execute([$_POST['id_barang'], $_POST['id_pelanggan'], $_POST['tanggal_transaksi'], $jumlah, $total]);
            $pesan = 'Transaksi berhasil dicatat. Total: ' . rp($total);
        }
        $pdo->commit();
    } catch (Exception $ex) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $err = $ex->getMessage();
    }
}

$pelanggan = $pdo->query("SELECT id_pelanggan, nama_pelanggan, kelas FROM pelanggan ORDER BY nama_pelanggan")->fetchAll();
$barang    = $pdo->query("SELECT id_barang, nama_barang, harga, stok FROM barang WHERE stok > 0 ORDER BY nama_barang")->fetchAll();

// JOIN: gabungkan 3 tabel
$data = $pdo->query("
  SELECT t.id_transaksi, t.tanggal_transaksi, p.nama_pelanggan, b.nama_barang, t.jumlah_barang, t.total_harga
  FROM transaksi t
  JOIN pelanggan p ON p.id_pelanggan = t.id_pelanggan
  JOIN barang b    ON b.id_barang    = t.id_barang
  ORDER BY t.id_transaksi DESC")->fetchAll();

header_html('Transaksi Penjualan', 'transaksi.php');
alert_html($pesan, $err);
?>
<div class="card">
  <h2>➕ Catat Transaksi Baru</h2>
  <form method="post" class="grid">
    <label>Pelanggan
      <select name="id_pelanggan" required><option value="">— pilih pelanggan —</option>
      <?php foreach ($pelanggan as $p): ?>
        <option value="<?= $p['id_pelanggan'] ?>"><?= e($p['nama_pelanggan']) ?> (<?= e($p['kelas']) ?>)</option>
      <?php endforeach; ?></select></label>
    <label>Barang
      <select name="id_barang" id="barang" required><option value="" data-harga="0">— pilih barang —</option>
      <?php foreach ($barang as $b): ?>
        <option value="<?= $b['id_barang'] ?>" data-harga="<?= $b['harga'] ?>"><?= e($b['nama_barang']) ?> — <?= rp($b['harga']) ?> (stok <?= $b['stok'] ?>)</option>
      <?php endforeach; ?></select></label>
    <label>Jumlah <input type="number" name="jumlah_barang" id="jumlah" min="1" value="1" required></label>
    <label>Tanggal <input type="date" name="tanggal_transaksi" value="<?= date('Y-m-d') ?>" required></label>
    <div><span class="muted">Total harga:</span> <div class="total" id="total">Rp 0</div></div>
    <div><button>Simpan Transaksi</button></div>
  </form>
</div>

<div class="card wrap">
  <table>
    <tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Barang</th><th class="r">Jumlah</th><th class="r">Total Harga</th><th>Aksi</th></tr>
    <?php foreach ($data as $r): ?>
    <tr><td>#<?= $r['id_transaksi'] ?></td><td><?= tgl($r['tanggal_transaksi']) ?></td>
      <td><?= e($r['nama_pelanggan']) ?></td><td><?= e($r['nama_barang']) ?></td>
      <td class="r"><?= $r['jumlah_barang'] ?></td><td class="r"><?= rp($r['total_harga']) ?></td>
      <td><form method="post" onsubmit="return confirm('Hapus transaksi ini? Stok akan dikembalikan.')">
        <button class="btn sm d" name="hapus" value="<?= $r['id_transaksi'] ?>">Hapus</button></form></td></tr>
    <?php endforeach; if (!$data) echo '<tr><td colspan="7" class="muted">Belum ada transaksi.</td></tr>'; ?>
  </table>
</div>

<script>
// Hitung total otomatis di layar: harga x jumlah
const sel = document.getElementById('barang'), jml = document.getElementById('jumlah'), out = document.getElementById('total');
function hitung() {
  const harga = +sel.selectedOptions[0].dataset.harga || 0;
  out.textContent = 'Rp ' + (harga * (+jml.value || 0)).toLocaleString('id-ID');
}
sel.addEventListener('change', hitung); jml.addEventListener('input', hitung);
</script>
<?php footer_html();
