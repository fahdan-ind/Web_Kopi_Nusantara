<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function getKopiPlaceholder($nama_kopi = '', $jenis_kopi = '') {
    $colors = [
        'Arabica'  => ['bg'=>'#6B3A2A','accent'=>'#C4863A'],
        'Robusta'  => ['bg'=>'#3B1A08','accent'=>'#8B5E3C'],
        'Liberika' => ['bg'=>'#2C4A1E','accent'=>'#7DB87A'],
        'Excelsa'  => ['bg'=>'#1A2C4A','accent'=>'#6A9FD4'],
        'Blend'    => ['bg'=>'#4A3A1A','accent'=>'#D4AA6A'],
    ];
    $c = $colors[$jenis_kopi] ?? ['bg'=>'#5C3D1E','accent'=>'#C4863A'];
    return '
    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="180" viewBox="0 0 300 180">
        <rect width="300" height="180" fill="'.$c['bg'].'"/>
        <circle cx="150" cy="78" r="50" fill="rgba(255,255,255,0.06)"/>
        <circle cx="150" cy="78" r="36" fill="rgba(255,255,255,0.07)"/>
        <g transform="translate(118, 45)">
            <path d="M8 6h1a5 5 0 0 1 0 10H8" fill="none" stroke="'.$c['accent'].'" stroke-width="2.2" stroke-linecap="round"/>
            <path d="M2 6h17v11a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5z" fill="none" stroke="'.$c['accent'].'" stroke-width="2.2" stroke-linecap="round"/>
            <path d="M7 2 Q8.5 0 7 -2" fill="none" stroke="'.$c['accent'].'" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
            <path d="M11 2 Q12.5 0 11 -2" fill="none" stroke="'.$c['accent'].'" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
            <path d="M15 2 Q16.5 0 15 -2" fill="none" stroke="'.$c['accent'].'" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
        </g>
        <text x="150" y="133" text-anchor="middle" font-family="Segoe UI,sans-serif" font-size="12" font-weight="600" fill="rgba(255,255,255,0.9)">'.$nama_kopi.'</text>
        <rect x="108" y="143" width="84" height="20" rx="10" fill="'.$c['accent'].'" opacity="0.85"/>
        <text x="150" y="157" text-anchor="middle" font-family="Segoe UI,sans-serif" font-size="10" font-weight="600" fill="#fff">'.$jenis_kopi.'</text>
    </svg>';
}

$search = isset($_GET['q']) ? trim($conn->real_escape_string($_GET['q'])) : '';
$jenis  = isset($_GET['jenis']) ? trim($conn->real_escape_string($_GET['jenis'])) : '';

$where = "WHERE 1=1";
if ($search) $where .= " AND (p.nama_kopi LIKE '%$search%' OR p.jenis_kopi LIKE '%$search%')";
if ($jenis)  $where .= " AND p.jenis_kopi = '$jenis'";

$result = $conn->query("SELECT p.*, u.nama_lengkap as nama_agen FROM produk p LEFT JOIN users u ON p.id_agen = u.id_user $where ORDER BY p.id_produk DESC");
?>

<div class="search-bar">
    <form method="GET" action="" style="display:flex; gap:12px; flex:1;">
        <input type="text" name="q" placeholder="Cari kopi..." value="<?= htmlspecialchars($search) ?>">
        <?php if ($jenis): ?>
            <input type="hidden" name="jenis" value="<?= htmlspecialchars($jenis) ?>">
        <?php endif; ?>
        <button type="submit" class="btn btn-dark">Cari</button>
        <?php if ($search || $jenis): ?>
            <a href="katalog.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
    </form>
</div>

<section class="section">
    <?php if ($jenis): ?>
        <h2 class="section-title">Kategori: <?= htmlspecialchars($jenis) ?></h2>
    <?php else: ?>
        <h2 class="section-title">Katalog Produk</h2>
    <?php endif; ?>
    <p class="section-sub">Semua produk kopi nusantara tersedia di sini</p>

    <?php if ($result->num_rows === 0): ?>
        <div class="empty-state">
            <h3>Produk tidak ditemukan</h3>
            <p>Coba kata kunci lain atau <a href="katalog.php" style="color:var(--coklat-mid);">lihat semua produk</a></p>
        </div>
    <?php else: ?>
    <div class="card-grid">
        <?php while ($p = $result->fetch_assoc()): ?>
        <div class="card">
            <?php if ($p['gambar_produk'] && file_exists(__DIR__ . '/../uploads/produk/' . $p['gambar_produk'])): ?>
                <img src="<?= rtrim(BASE_URL, '/') ?>/uploads/produk/<?= htmlspecialchars($p['gambar_produk']) ?>" alt="<?= htmlspecialchars($p['nama_kopi']) ?>" class="card-img">
            <?php else: ?>
                <div class="card-img-placeholder">
                    <?= getKopiPlaceholder($p['nama_kopi'], $p['jenis_kopi']) ?>
                </div>
            <?php endif; ?>
            <div class="card-body">
                <div class="card-title"><?= htmlspecialchars($p['nama_kopi']) ?></div>
                <span class="card-jenis"><?= htmlspecialchars($p['jenis_kopi']) ?></span>
                <div class="card-harga"><?= formatRupiah($p['harga']) ?></div>
                <div class="text-muted">Agen: <?= htmlspecialchars($p['nama_agen'] ?? '-') ?></div>
            </div>
            <div class="card-footer">
                <a href="<?= rtrim(BASE_URL, '/') ?>/pages/detail.php?id=<?= urlencode($p['id_produk']) ?>" class="btn btn-dark" style="flex:1; text-align:center; font-size:0.85rem;">Detail</a>
                <a href="<?= rtrim(BASE_URL, '/') ?>/pages/detail.php?id=<?= urlencode($p['id_produk']) ?>" class="btn btn-primary" style="flex:1; text-align:center; font-size:0.85rem;">Keranjang</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>