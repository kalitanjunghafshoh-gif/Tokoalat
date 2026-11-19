<?php
require 'koneksi.php'; require 'layout.php';
$pesan = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['hapus'])) {
            $pdo->prepare("DELETE FROM barang WHERE id_barang=?")->execute([$_POST['hapus']]);
            $pesan = 'Barang berhasil dihapus.';
        } elseif (!empty($_POST['id_barang'])) {
            $pdo->prepare("UPDATE barang SET nama_barang=?, kategori=?, harga=?, stok=? WHERE id_barang=?")
                ->execute([trim($_POST['nama_barang']), trim($_POST['kategori']), (int)$_POST['harga'], (int)$_POST['stok'], $_POST['id_barang']]);
            $pesan = 'Barang berhasil diperbarui.';
        } else {
            $pdo->prepare("INSERT INTO barang (nama_barang,kategori,harga,stok) VALUES (?,?,?,?)")
                ->execute([trim($_POST['nama_barang']), trim($_POST['kategori']), (int)$_POST['harga'], (int)$_POST['stok']]);
            $pesan = 'Barang berhasil ditambahkan.';
        }
    } catch (PDOException $ex) {
        // Kode 23000 = pelanggaran foreign key
        $err = $ex->getCode() == 23000
            ? 'Barang tidak bisa dihapus karena sudah dipakai di data transaksi.'
            : 'Terjadi kesalahan: ' . $ex->getMessage();
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM barang WHERE id_barang=?");
    $s->execute([$_GET['edit']]);
    $edit = $s->fetch();
}
$data = $pdo->query("SELECT * FROM barang ORDER BY id_barang DESC")->fetchAll();

header_html('Data Barang', 'barang.php');
alert_html($pesan, $err);
?>
<div class="card">
  <h2><?= $edit ? '✏️ Edit Barang' : '➕ Tambah Barang' ?></h2>
  <form method="post" class="grid">
    <input type="hidden" name="id_barang" value="<?= e($edit['id_barang'] ?? '') ?>">
    <label>Nama Barang <input name="nama_barang" required value="<?= e($edit['nama_barang'] ?? '') ?>"></label>
    <label>Kategori <input name="kategori" list="kat" required value="<?= e($edit['kategori'] ?? '') ?>">
      <datalist id="kat"><option>Alat Tulis</option><option>Buku</option><option>Kertas</option><option>Perlengkapan</option></datalist></label>
    <label>Harga (Rp) <input type="number" name="harga" min="0" required value="<?= e($edit['harga'] ?? '') ?>"></label>
    <label>Stok <input type="number" name="stok" min="0" required value="<?= e($edit['stok'] ?? '') ?>"></label>
    <div><button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
      <?php if ($edit): ?> <a class="btn gr" href="barang.php">Batal</a><?php endif; ?></div>
  </form>
</div>

<div class="card wrap">
  <table>
    <tr><th>ID</th><th>Nama Barang</th><th>Kategori</th><th class="r">Harga</th><th class="r">Stok</th><th>Aksi</th></tr>
    <?php foreach ($data as $r): ?>
    <tr><td><?= $r['id_barang'] ?></td><td><?= e($r['nama_barang']) ?></td>
      <td><span class="badge"><?= e($r['kategori']) ?></span></td>
      <td class="r"><?= rp($r['harga']) ?></td>
      <td class="r"><span class="badge <?= $r['stok'] <= 5 ? 'low' : 'ok' ?>"><?= $r['stok'] ?></span></td>
      <td><a class="btn sm" href="?edit=<?= $r['id_barang'] ?>">Edit</a>
        <form method="post" style="display:inline" onsubmit="return confirm('Hapus barang ini?')">
          <button class="btn sm d" name="hapus" value="<?= $r['id_barang'] ?>">Hapus</button></form></td></tr>
    <?php endforeach; ?>
  </table>
</div>
<?php footer_html();
