<?php
function header_html($judul, $aktif) {
    $menu = [
        'index.php'     => ['Dashboard', '🏠'],
        'barang.php'    => ['Barang', '📦'],
        'pelanggan.php' => ['Pelanggan', '👥'],
        'transaksi.php' => ['Transaksi', '🧾'],
        'riwayat.php'   => ['Riwayat Pelanggan', '🕘'],
        'semua_data.php'=> ['Semua Data', '🗂️'],
    ]; ?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> · Toko Alat Tulis</title>
<link rel="stylesheet" href="assets/style.css">
</head><body>
<aside class="side">
  <div class="brand">✏️ <span>Toko Alat Tulis<small>Sistem Informasi Sekolah</small></span></div>
  <nav>
  <?php foreach ($menu as $file => [$nama, $ikon]): ?>
    <a href="<?= $file ?>" class="<?= $file === $aktif ? 'on' : '' ?>"><?= $ikon ?> <?= $nama ?></a>
  <?php endforeach; ?>
  </nav>
</aside>
<main>
  <h1><?= e($judul) ?></h1>
<?php }
function footer_html() { echo "</main></body></html>"; }

function alert_html($pesan, $err) {
    if ($pesan) echo '<div class="alert ok">✅ ' . e($pesan) . '</div>';
    if ($err)   echo '<div class="alert err">⚠️ ' . e($err) . '</div>';
}
