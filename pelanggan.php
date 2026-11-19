<?php
require 'koneksi.php'; require 'layout.php';
$pesan = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['hapus'])) {
            $pdo->prepare("DELETE FROM pelanggan WHERE id_pelanggan=?")->execute([$_POST['hapus']]);
            $pesan = 'Pelanggan berhasil dihapus.';
        } elseif (!empty($_POST['id_pelanggan'])) {
            $pdo->prepare("UPDATE pelanggan SET nama_pelanggan=?, kelas=?, no_hp=?, alamat=? WHERE id_pelanggan=?")
                ->execute([trim($_POST['nama_pelanggan']), trim($_POST['kelas']), trim($_POST['no_hp']), trim($_POST['alamat']), $_POST['id_pelanggan']]);
            $pesan = 'Pelanggan berhasil diperbarui.';
        } else {
            $pdo->prepare("INSERT INTO pelanggan (nama_pelanggan,kelas,no_hp,alamat) VALUES (?,?,?,?)")
                ->execute([trim($_POST['nama_pelanggan']), trim($_POST['kelas']), trim($_POST['no_hp']), trim($_POST['alamat'])]);
            $pesan = 'Pelanggan berhasil ditambahkan.';
        }
    } catch (PDOException $ex) {
        $err = $ex->getCode() == 23000
            ? 'Pelanggan tidak bisa dihapus karena sudah punya data transaksi.'
            : 'Terjadi kesalahan: ' . $ex->getMessage();
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM pelanggan WHERE id_pelanggan=?");
    $s->execute([$_GET['edit']]);
    $edit = $s->fetch();
}
$data = $pdo->query("SELECT * FROM pelanggan ORDER BY id_pelanggan DESC")->fetchAll();

header_html('Data Pelanggan', 'pelanggan.php');
alert_html($pesan, $err);
?>
<div class="card">
  <h2><?= $edit ? '✏️ Edit Pelanggan' : '➕ Tambah Pelanggan' ?></h2>
  <form method="post" class="grid">
    <input type="hidden" name="id_pelanggan" value="<?= e($edit['id_pelanggan'] ?? '') ?>">
    <label>Nama Pelanggan <input name="nama_pelanggan" required value="<?= e($edit['nama_pelanggan'] ?? '') ?>"></label>
    <label>Kelas <input name="kelas" placeholder="contoh: VII A" value="<?= e($edit['kelas'] ?? '') ?>"></label>
    <label>No. HP <input name="no_hp" value="<?= e($edit['no_hp'] ?? '') ?>"></label>
    <label>Alamat <input name="alamat" value="<?= e($edit['alamat'] ?? '') ?>"></label>
    <div><button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
      <?php if ($edit): ?> <a class="btn gr" href="pelanggan.php">Batal</a><?php endif; ?></div>
  </form>
</div>

<div class="card wrap">
  <table>
    <tr><th>ID</th><th>Nama</th><th>Kelas</th><th>No. HP</th><th>Alamat</th><th>Aksi</th></tr>
    <?php foreach ($data as $r): ?>
    <tr><td><?= $r['id_pelanggan'] ?></td><td><?= e($r['nama_pelanggan']) ?></td><td><?= e($r['kelas']) ?></td>
      <td><?= e($r['no_hp']) ?></td><td><?= e($r['alamat']) ?></td>
      <td><a class="btn sm" href="riwayat.php?id=<?= $r['id_pelanggan'] ?>">Riwayat</a>
        <a class="btn sm" href="?edit=<?= $r['id_pelanggan'] ?>">Edit</a>
        <form method="post" style="display:inline" onsubmit="return confirm('Hapus pelanggan ini?')">
          <button class="btn sm d" name="hapus" value="<?= $r['id_pelanggan'] ?>">Hapus</button></form></td></tr>
    <?php endforeach; ?>
  </table>
</div>
<?php footer_html();
