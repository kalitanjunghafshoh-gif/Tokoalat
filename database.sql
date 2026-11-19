-- =====================================================
-- Sistem Informasi Toko Alat Tulis Sekolah
-- 3 tabel: barang, pelanggan, transaksi
-- =====================================================
CREATE DATABASE IF NOT EXISTS toko_alat_tulis CHARACTER SET utf8mb4;
USE toko_alat_tulis;

CREATE TABLE barang (
  id_barang   INT AUTO_INCREMENT PRIMARY KEY,
  nama_barang VARCHAR(100) NOT NULL,
  kategori    VARCHAR(50)  NOT NULL,
  harga       INT NOT NULL,
  stok        INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE pelanggan (
  id_pelanggan   INT AUTO_INCREMENT PRIMARY KEY,
  nama_pelanggan VARCHAR(100) NOT NULL,
  kelas          VARCHAR(20),
  no_hp          VARCHAR(20),
  alamat         VARCHAR(200)
) ENGINE=InnoDB;

CREATE TABLE transaksi (
  id_transaksi      INT AUTO_INCREMENT PRIMARY KEY,
  id_barang         INT NOT NULL,
  id_pelanggan      INT NOT NULL,
  tanggal_transaksi DATE NOT NULL,
  jumlah_barang     INT NOT NULL,
  total_harga       INT NOT NULL,
  CONSTRAINT fk_transaksi_barang
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_transaksi_pelanggan
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Data contoh
INSERT INTO barang (nama_barang,kategori,harga,stok) VALUES
('Buku Tulis Sidu 38 Lembar','Buku',4500,100),
('Pulpen Standard AE7','Alat Tulis',3000,150),
('Pensil 2B Faber-Castell','Alat Tulis',3500,80),
('Penggaris 30 cm','Perlengkapan',5000,40),
('Penghapus Putih','Alat Tulis',2000,60),
('Kertas Folio (1 rim)','Kertas',45000,4);

INSERT INTO pelanggan (nama_pelanggan,kelas,no_hp,alamat) VALUES
('Siti Aminah','VII A','081200001111','Jl. Kartini No. 5, Cirebon'),
('Budi Santoso','IX B','081300002222','Jl. Pemuda No. 12, Cirebon'),
('Rina Marlina','X IPA 1','081400003333','Jl. Merdeka No. 8, Cirebon');

INSERT INTO transaksi (id_barang,id_pelanggan,tanggal_transaksi,jumlah_barang,total_harga) VALUES
(1,1,CURDATE(),5,22500),
(2,1,CURDATE(),2,6000),
(3,2,CURDATE(),3,10500);
UPDATE barang SET stok=stok-5 WHERE id_barang=1;
UPDATE barang SET stok=stok-2 WHERE id_barang=2;
UPDATE barang SET stok=stok-3 WHERE id_barang=3;
