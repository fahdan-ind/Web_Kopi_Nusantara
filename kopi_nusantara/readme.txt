====================================================
  KOPI NUSANTARA - Marketplace Agen Kopi
  Dokumentasi Konfigurasi & Panduan Instalasi
====================================================

DESKRIPSI
---------
Aplikasi Website Marketplace Agen Kopi Nusantara berbasis
PHP Native + MySQL. Memungkinkan agen menjual kopi dan
pembeli berbelanja secara online.


PERSYARATAN SISTEM
------------------
- XAMPP (PHP 7.4+ / 8.x, MySQL 5.7+ / MariaDB)
- Web browser modern


LANGKAH INSTALASI
-----------------
1. Salin folder "kopi_nusantara" ke dalam:
   C:\xampp\htdocs\

2. Buka XAMPP Control Panel, jalankan:
   - Apache
   - MySQL

3. Import database:
   a. Buka browser, akses: http://localhost/phpmyadmin
   b. Klik "New" atau "Baru"
   c. Buat database: db_kopi_nusantara
   d. Pilih database tersebut, klik tab "Import"
   e. Upload file: db_kopi_nusantara.sql
   f. Klik "Go"

4. Akses website:
   http://localhost/kopi_nusantara/


KONFIGURASI DATABASE
--------------------
File: config/db.php

  DB_HOST : localhost
  DB_USER : root
  DB_PASS : (kosong / sesuai instalasi XAMPP kamu)
  DB_NAME : db_kopi_nusantara

Jika password MySQL kamu bukan kosong, ubah DB_PASS
di file config/db.php sesuai password kamu.


AKUN LOGIN (DUMMY)
------------------

ADMIN:
  Email    : admin@kopinusantara.id
  Password : admin123
  Role     : admin
  Akses    : Dashboard penuh, kelola produk & transaksi

AGEN 1:
  Email    : budi.agen@mail.com
  Password : 12345
  Role     : agen

AGEN 2:
  Email    : siti.agen@mail.com
  Password : 12345
  Role     : agen

PEMBELI 1:
  Email    : andi.pembeli@mail.com
  Password : 12345
  Role     : pembeli

PEMBELI 2:
  Email    : rina.pembeli@mail.com
  Password : 12345
  Role     : pembeli


STRUKTUR FOLDER
---------------
kopi_nusantara/
  assets/
    css/          - File stylesheet (style.css)
    images/       - Gambar statis (hero, dll)
  config/
    app.php
    db.php        - Konfigurasi koneksi database
  dashboard/
    index.php     - Halaman ringkasan statistik
    produk.php    - CRUD kelola produk
    transaksi.php - Data transaksi masuk
    users.php     - Daftar pengguna
  includes/
    header.php    - Navbar publik
    footer.php    - Footer publik
    session.php   - Helper session & role
  pages/
    katalog.php   - Katalog semua produk
    detail.php    - Detail produk + tambah keranjang
    keranjang.php - Keranjang belanja + checkout
    login.php     - Form login
    register.php  - Form registrasi
    logout.php    - Proses logout
  uploads/
    produk/       - Gambar produk yang diupload
  index.php       - Halaman beranda (HOME)
  db_kopi_nusantara.sql - File backup database
  readme.txt      - File ini


FITUR APLIKASI
--------------
Publik:
  - Beranda dengan banner dan produk unggulan dinamis
  - Katalog produk dengan fitur pencarian dan filter kategori
  - Detail produk dengan info agen, stok, dan deskripsi
  - Keranjang belanja dengan update qty dan kalkulasi otomatis
  - Checkout masuk ke tabel transaksi
  - Registrasi akun baru (agen/pembeli)
  - Login dengan deteksi role

Dashboard Admin/Agen:
  - Statistik ringkasan (total produk, user, transaksi, omzet)
  - CRUD produk lengkap dengan upload gambar
  - Manajemen status transaksi (pending/dibayar/dikirim)
  - Tabel data pengguna


CATATAN HOSTING
---------------
Untuk hosting gratis, gunakan salah satu:
  - InfinityFree  : https://infinityfree.net
  - 000WebHost    : https://www.000webhost.com

Langkah hosting:
  1. Daftar akun di salah satu layanan di atas
  2. Upload semua file via File Manager atau FTP
  3. Import db_kopi_nusantara.sql via phpMyAdmin hosting
  4. Update config/db.php sesuai kredensial hosting


====================================================
  SMK Bina Informatika - Kelas X RPL
  Mata Pelajaran: Pemrograman Web
  Tahun Ajaran: 2025-2026
====================================================
