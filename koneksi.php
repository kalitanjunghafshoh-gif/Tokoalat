<?php
// ---------- Pengaturan koneksi (ubah jika perlu) ----------
$host = 'localhost';
$db   = 'toko_alat_tulis';
$user = 'root';
$pass = '';          // password MySQL; di XAMPP biasanya kosong

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Koneksi database gagal. Pastikan MySQL aktif dan database sudah di-import. Detail: ' . $e->getMessage());
}

// Aman menampilkan teks (mencegah XSS)
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
// Format rupiah
function rp($n) { return 'Rp ' . number_format((int)$n, 0, ',', '.'); }
// Format tanggal dd/mm/yyyy
function tgl($d) { return date('d/m/Y', strtotime($d)); }
