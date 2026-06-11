<?php
// dashboard/index.php  –  Halaman Ringkasan / Home Dashboard
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/header.php';  // sudah include requireAdmin() + buka dash-layout

$total_produk    = $conn->query("SELECT COUNT(*) as n FROM produk")->fetch_assoc()['n'];
$total_users     = $conn->query("SELECT COUNT(*) as n FROM users")->fetch_assoc()['n'];
$total_transaksi = $conn->query("SELECT COUNT(*) as n FROM transaksi")->fetch_assoc()['n'];
$total_omzet     = $conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi")->fetch_assoc()['n'];

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<h1>Ringkasan Dashboard</h1>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-num"><?= $total_produk ?></div>
        <div class="stat-label">Total Produk</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $total_users ?></div>
        <div class="stat-label">Total Pengguna</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $total_transaksi ?></div>
        <div class="stat-label">Total Transaksi</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="font-size:1.2rem;"><?= formatRupiah($total_omzet) ?></div>
        <div class="stat-label">Total Omzet</div>
    </div>
</div>

<!-- Shortcut ke Kelola Produk -->
<div style="margin-bottom:28px;">
    <a href="/kopi_nusantara/dashboard/produk.php" class="btn btn-primary">
        ☕ &nbsp;Kelola Produk
    </a>
    <a href="/kopi_nusantara/dashboard/transaksi.php" class="btn btn-dark" style="margin-left:10px;">
        🧾 &nbsp;Lihat Transaksi
    </a>
</div>

<h2 style="font-size:1.1rem;margin-bottom:16px;color:var(--coklat-tua);">Transaksi Terbaru</h2>
<div style="overflow-x:auto;">
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Pembeli</th>
            <th>Produk</th>
            <th>Total</th>
            <th>Tanggal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $recent = $conn->query("
            SELECT t.*, u.nama_lengkap, p.nama_kopi
            FROM transaksi t
            LEFT JOIN users u ON t.id_user = u.id_user
            LEFT JOIN produk p ON t.id_produk = p.id_produk
            ORDER BY t.id_transaksi DESC
            LIMIT 5
        ");
        while ($r = $recent->fetch_assoc()):
        ?>
        <tr>
            <td>#<?= $r['id_transaksi'] ?></td>
            <td><?= htmlspecialchars($r['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($r['nama_kopi']) ?></td>
            <td><?= formatRupiah($r['total_harga']) ?></td>
            <td><?= $r['tgl_transaksi'] ?></td>
            <td>
                <span class="badge badge-<?= $r['status_pesanan'] ?>">
                    <?= ucfirst($r['status_pesanan']) ?>
                </span>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>