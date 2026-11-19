<?php
require 'koneksi.php'; require 'layout.php';

$barang    = $pdo->query("SELECT * FROM barang ORDER BY id_barang")->fetchAll();
$pelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY id_pelanggan")->fetchAll();
$transaksi = $pdo->query("SELECT * FROM transaksi ORDER BY id_transaksi")->fetchAll();

// Fungsi kecil untuk menampilkan tabel apa pun
function tampil_tabel($judul, $rows) {
    echo '<div class="card wrap"><h2>' . e($judul) . ' <span class="badge">' . count($rows) . ' baris</span></h2><table>';
    if ($rows) {
        echo '<tr>';
        foreach (array_keys($rows[0]) as $kolom) echo '<th>' . e($kolom) . '</th>';
        echo '</tr>';
        foreach ($rows as $r) {
            echo '<tr>';
            foreach ($r as $v) echo '<td>' . e($v) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td class="muted">Tabel kosong.</td></tr>';
    }
    echo '</table></div>';
}

header_html('Semua Data (3 Tabel)', 'semua_data.php');
tampil_tabel('Tabel barang', $barang);
tampil_tabel('Tabel pelanggan', $pelanggan);
tampil_tabel('Tabel transaksi (id_barang & id_pelanggan = Foreign Key)', $transaksi);
footer_html();
