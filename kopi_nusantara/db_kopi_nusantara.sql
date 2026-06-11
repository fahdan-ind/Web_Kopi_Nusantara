-- ============================================================
-- db_kopi_nusantara.sql
-- Kopi Nusantara Marketplace Database
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_kopi_nusantara DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_kopi_nusantara;

-- ============================================================
-- DDL: TABEL USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id_user      INT(11)      NOT NULL AUTO_INCREMENT,
    nama_lengkap VARCHAR(100) NOT NULL,
    email        VARCHAR(100) NOT NULL UNIQUE,
    password     VARCHAR(255) NOT NULL,
    role         ENUM('admin','agen','pembeli') NOT NULL DEFAULT 'pembeli',
    no_hp        VARCHAR(15),
    alamat       TEXT,
    PRIMARY KEY (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DDL: TABEL PRODUK
-- ============================================================
CREATE TABLE IF NOT EXISTS produk (
    id_produk    INT(11)      NOT NULL AUTO_INCREMENT,
    id_agen      INT(11)      NOT NULL,
    nama_kopi    VARCHAR(100) NOT NULL,
    jenis_kopi   VARCHAR(50)  NOT NULL,
    deskripsi    TEXT,
    harga        INT(11)      NOT NULL,
    stok         INT(11)      NOT NULL DEFAULT 0,
    gambar_produk VARCHAR(255),
    PRIMARY KEY (id_produk),
    FOREIGN KEY (id_agen) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DDL: TABEL TRANSAKSI
-- ============================================================
CREATE TABLE IF NOT EXISTS transaksi (
    id_transaksi   INT(11) NOT NULL AUTO_INCREMENT,
    id_user        INT(11) NOT NULL,
    id_produk      INT(11) NOT NULL,
    tgl_transaksi  DATE    NOT NULL,
    jumlah_beli    INT(11) NOT NULL,
    total_harga    INT(11) NOT NULL,
    status_pesanan ENUM('pending','dibayar','dikirim') NOT NULL DEFAULT 'pending',
    PRIMARY KEY (id_transaksi),
    FOREIGN KEY (id_user)    REFERENCES users(id_user)       ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_produk)  REFERENCES produk(id_produk)    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DML: INSERT USERS (termasuk admin)
-- ============================================================
INSERT INTO users (id_user, nama_lengkap, email, password, role, no_hp, alamat) VALUES
(1, 'Admin Kopi Nusantara', 'admin@kopinusantara.id', 'admin123', 'admin', '081200000000', 'Jl. Sudirman No. 1, Jakarta'),
(2, 'Budi Santoso', 'budi.agen@mail.com', '12345', 'agen', '081234567890', 'Jl. Kopi Gayo No. 1, Aceh'),
(3, 'Siti Aminah', 'siti.agen@mail.com', '12345', 'agen', '081987654321', 'Jl. Toraja Indah No. 5, Sulsel'),
(4, 'Andi Pratama', 'andi.pembeli@mail.com', '12345', 'pembeli', '081122334455', 'Jl. Sudirman No. 10, Jakarta'),
(5, 'Rina Kumalasari', 'rina.pembeli@mail.com', '12345', 'pembeli', '085566778899', 'Jl. Merdeka No. 45, Bandung'),
(6, 'Dedi Irawan', 'dedi.pembeli@mail.com', '12345', 'pembeli', '081333222111', 'Jl. Pahlawan No. 88, Surabaya');

-- ============================================================
-- DML: INSERT PRODUK
-- ============================================================
INSERT INTO produk (id_produk, id_agen, nama_kopi, jenis_kopi, deskripsi, harga, stok, gambar_produk) VALUES
(1, 2, 'Kopi Arabica Gayo', 'Arabica', 'Kopi khas dataran tinggi Gayo dengan aroma rempah.', 85000, 50, 'Arabica_gayo.jpg'),
(2, 3, 'Kopi Toraja Sapan', 'Arabica', 'Kopi Toraja dengan tingkat keasaman seimbang.', 90000, 30, 'Toraja_sapan.png'),
(3, 2, 'Kopi Robusta Dampit', 'Robusta', 'Kopi Robusta murni dengan rasa cokelat pekat.', 65000, 100, 'Robusta_dampit.jpg'),
(4, 3, 'Kopi Bali Kintamani', 'Arabica', 'Kopi dengan sensasi rasa fruity segar asam jeruk.', 75000, 45, 'Bali_kintamani.jpg'),
(5, 2, 'Kopi Luwak Liar', 'Arabica', 'Kopi premium dari fermentasi luwak liar asli.', 250000, 10, 'Luwak_liar.jpg');

-- ============================================================
-- DML: INSERT TRANSAKSI
-- ============================================================
INSERT INTO transaksi (id_transaksi, id_user, id_produk, tgl_transaksi, jumlah_beli, total_harga, status_pesanan) VALUES
(1, 4, 1, '2026-06-01', 2, 170000, 'dikirim'),
(2, 5, 2, '2026-06-02', 1, 90000, 'dibayar'),
(3, 6, 4, '2026-06-05', 3, 225000, 'pending'),
(4, 4, 3, '2026-06-08', 2, 130000, 'dikirim'),
(5, 5, 5, '2026-06-09', 1, 250000, 'dibayar');

-- ============================================================
-- QUERY WHERE (8 Kondisi - untuk LK)
-- ============================================================

-- Kondisi A: Produk jenis Arabica
SELECT * FROM produk WHERE jenis_kopi = 'Arabica';

-- Kondisi B: Pengguna dengan role agen
SELECT * FROM users WHERE role = 'agen';

-- Kondisi C: Transaksi dengan status dikirim
SELECT * FROM transaksi WHERE status_pesanan = 'dikirim';

-- Kondisi D: Produk dengan harga di bawah Rp 80.000
SELECT * FROM produk WHERE harga < 80000;

-- Kondisi E: Produk dengan stok <= 30
SELECT * FROM produk WHERE stok <= 30;

-- Kondisi F: Transaksi dengan total harga > Rp 150.000
SELECT * FROM transaksi WHERE total_harga > 150000;

-- Kondisi G: Transaksi dengan jumlah beli > 1
SELECT * FROM transaksi WHERE jumlah_beli > 1;

-- Kondisi H: Pengguna dengan alamat di Jakarta
SELECT * FROM users WHERE alamat LIKE '%Jakarta%';
