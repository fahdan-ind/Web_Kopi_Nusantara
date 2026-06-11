<?php
// dashboard/header.php
require_once __DIR__ . '/../includes/session.php';
requireAdmin();

$base         = BASE_URL;
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Kopi Nusantara</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
    <style>
        .sidebar a.active {
            background: rgba(255,255,255,0.15);
            color: #C4863A;
            font-weight: 600;
            border-left: 3px solid #C4863A;
            padding-left: 17px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="<?= $base ?>/index.php" class="nav-brand">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
             fill="none" stroke="#C4863A" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round" style="flex-shrink:0;">
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
        <li><a href="<?= $base ?>/pages/logout.php">
            Logout (<?= htmlspecialchars($_SESSION['nama']) ?>)
        </a></li>
    </ul>
</nav>

<div class="dash-layout">
    <aside class="sidebar">
        <h3>Menu</h3>
        <a href="<?= $base ?>/dashboard/index.php"
           class="<?= $current_page === 'index.php' ? 'active' : '' ?>">
            📊 &nbsp;Ringkasan
        </a>
        <a href="<?= $base ?>/dashboard/produk.php"
           class="<?= $current_page === 'produk.php' ? 'active' : '' ?>">
            ☕ &nbsp;Kelola Produk
        </a>
        <a href="<?= $base ?>/dashboard/transaksi.php"
           class="<?= $current_page === 'transaksi.php' ? 'active' : '' ?>">
            🧾 &nbsp;Transaksi
        </a>
        <?php if (getRole() === 'admin'): ?>
        <a href="<?= $base ?>/dashboard/users.php"
           class="<?= $current_page === 'users.php' ? 'active' : '' ?>">
            👥 &nbsp;Pengguna
        </a>
        <?php endif; ?>

        <div style="margin-top:24px;padding:16px 20px 0;border-top:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:0.78rem;color:rgba(245,236,215,0.5);margin-bottom:4px;">Login sebagai</div>
            <div style="font-size:0.88rem;color:var(--coklat-muda);font-weight:600;">
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </div>
            <div style="font-size:0.78rem;color:rgba(245,236,215,0.5);text-transform:capitalize;">
                <?= getRole() ?>
            </div>
        </div>
    </aside>

    <main class="dash-content">