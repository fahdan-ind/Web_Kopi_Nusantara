<?php
// config/db.php
require_once __DIR__ . '/app.php'; // load BASE_URL & BASE_PATH

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_kopi_nusantara');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");