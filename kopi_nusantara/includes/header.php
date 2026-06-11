<?php
// includes/header.php — dipakai semua halaman publik (pages/ dan root index.php)
require_once __DIR__ . '/../includes/session.php';
$base = BASE_URL;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi Nusantara</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="<?= $base ?>/index.php" class="nav-brand">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
             viewBox="0 0 24 24" fill="none" stroke="#C4863A"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             style="flex-shrink:0;">
            <path d="M17 8h1a4 4 0 1 1 0 8h-1"/>
            <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4z"/>
            <line x1="6" y1="2" x2="6" y2="4"/>
            <line x1="10" y1="2" x2="10" y2="4"/>
            <line x1="14" y1="2" x2="14" y2="4"/>
        </svg>
        <span>Kopi Nusantara</span>
    </a>
    <ul class="nav-links">
        <li><a href="<?= $base ?>/index.php">Home</a></li>
        <li><a href="<?= $base ?>/pages/katalog.php">Katalog</a></li>
        <?php if (isLoggedIn()): ?>
            <li><a href="<?= $base ?>/pages/keranjang.php">Keranjang</a></li>
            <?php if (getRole() === 'admin' || getRole() === 'agen'): ?>
                <li><a href="<?= $base ?>/dashboard/index.php">Dashboard</a></li>
            <?php endif; ?>
            <li><a href="<?= $base ?>/pages/logout.php">
                Logout (<?= htmlspecialchars($_SESSION['nama']) ?>)
            </a></li>
        <?php else: ?>
            <li><a href="<?= $base ?>/pages/keranjang.php">Keranjang</a></li>
            <li><a href="<?= $base ?>/pages/login.php" class="btn-nav">Masuk</a></li>
        <?php endif; ?>
    </ul>
</nav>