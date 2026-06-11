<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';

$stmt = $conn->prepare("SELECT p.*, u.nama_lengkap as nama_agen FROM produk p LEFT JOIN users u ON p.id_agen = u.id_user ORDER BY p.id_produk DESC LIMIT 6");
$stmt->execute();
$produk_unggulan = $stmt->get_result();

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// SVG placeholder per jenis kopi
function getKopiPlaceholder($nama_kopi = '', $jenis_kopi = '') {
    $colors = [
        'Arabica'  => ['bg'=>'#6B3A2A','accent'=>'#C4863A','label'=>'AR'],
        'Robusta'  => ['bg'=>'#3B1A08','accent'=>'#8B5E3C','label'=>'RO'],
        'Liberika' => ['bg'=>'#2C4A1E','accent'=>'#7DB87A','label'=>'LI'],
        'Excelsa'  => ['bg'=>'#1A2C4A','accent'=>'#6A9FD4','label'=>'EX'],
        'Blend'    => ['bg'=>'#4A3A1A','accent'=>'#D4AA6A','label'=>'BL'],
    ];
    $c = $colors[$jenis_kopi] ?? ['bg'=>'#5C3D1E','accent'=>'#C4863A','label'=>'KO'];
    $inisial = mb_strtoupper(mb_substr($nama_kopi ?: 'Kopi', 0, 2));
    return '
    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="180" viewBox="0 0 300 180">
        <rect width="300" height="180" fill="'.$c['bg'].'" rx="0"/>
        <!-- Lingkaran dekoratif -->
        <circle cx="150" cy="75" r="52" fill="rgba(255,255,255,0.06)"/>
        <circle cx="150" cy="75" r="38" fill="rgba(255,255,255,0.07)"/>
        <!-- Ikon cangkir kopi -->
        <g transform="translate(118, 42)">
            <path d="M8 6h1a5 5 0 0 1 0 10H8" fill="none" stroke="'.$c['accent'].'" stroke-width="2.2" stroke-linecap="round"/>
            <path d="M2 6h17v11a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5z" fill="none" stroke="'.$c['accent'].'" stroke-width="2.2" stroke-linecap="round"/>
            <!-- Steam -->
            <path d="M7 2 Q8.5 0 7 -2" fill="none" stroke="'.$c['accent'].'" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
            <path d="M11 2 Q12.5 0 11 -2" fill="none" stroke="'.$c['accent'].'" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
            <path d="M15 2 Q16.5 0 15 -2" fill="none" stroke="'.$c['accent'].'" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
        </g>
        <!-- Nama -->
        <text x="150" y="132" text-anchor="middle" font-family="Segoe UI, sans-serif" font-size="13" font-weight="600" fill="rgba(255,255,255,0.9)">'.$nama_kopi.'</text>
        <!-- Badge jenis -->
        <rect x="105" y="143" width="90" height="22" rx="11" fill="'.$c['accent'].'" opacity="0.85"/>
        <text x="150" y="158" text-anchor="middle" font-family="Segoe UI, sans-serif" font-size="11" font-weight="600" fill="#fff">'.$jenis_kopi.'</text>
    </svg>';
}
?>

<section class="hero">
    <div class="hero-text">
        <h1>Kopi Asli Nusantara<br>Kini di Sini</h1>
        <p>Temukan ratusan varian kopi premium dari seluruh pelosok Indonesia — Aceh, Toraja, Flores, Papua, dan lebih banyak lagi. Langsung dari agen terpercaya.</p>
        <a href="<?= BASE_URL ?>/pages/katalog.php" class="btn btn-primary">Lihat Katalog</a>
    </div>
    <!-- Hero illustration SVG -->
    <div class="hero-visual">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 260" width="360" height="260">
            <!-- Piring / saucer -->
            <ellipse cx="180" cy="210" rx="95" ry="18" fill="#5C2E00" opacity="0.5"/>
            <!-- Cangkir body -->
            <path d="M110 130 Q108 195 145 210 Q180 220 215 210 Q252 195 250 130 Z" fill="#7B3F00"/>
            <path d="M115 130 Q113 192 148 207 Q180 216 212 207 Q247 192 245 130 Z" fill="#9B5500"/>
            <!-- Handle -->
            <path d="M250 148 Q285 148 285 175 Q285 202 250 202" fill="none" stroke="#7B3F00" stroke-width="14" stroke-linecap="round"/>
            <path d="M250 152 Q280 152 280 175 Q280 198 250 198" fill="none" stroke="#9B5500" stroke-width="8" stroke-linecap="round"/>
            <!-- Kopi (isi) -->
            <ellipse cx="180" cy="138" rx="63" ry="14" fill="#3B1A08"/>
            <ellipse cx="180" cy="136" rx="60" ry="12" fill="#2A1005"/>
            <!-- Buih latte art -->
            <ellipse cx="175" cy="135" rx="35" ry="7" fill="rgba(196,134,58,0.35)"/>
            <path d="M155 132 Q175 125 195 132" fill="none" stroke="rgba(196,134,58,0.5)" stroke-width="2" stroke-linecap="round"/>
            <path d="M162 136 Q175 130 188 136" fill="none" stroke="rgba(196,134,58,0.4)" stroke-width="1.5" stroke-linecap="round"/>
            <!-- Steam 1 -->
            <path d="M155 118 Q149 104 155 90 Q161 76 155 62" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="3" stroke-linecap="round"/>
            <!-- Steam 2 -->
            <path d="M180 112 Q174 98 180 84 Q186 70 180 56" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="3" stroke-linecap="round"/>
            <!-- Steam 3 -->
            <path d="M205 118 Q199 104 205 90 Q211 76 205 62" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3" stroke-linecap="round"/>
            <!-- Biji kopi dekoratif kiri -->
            <ellipse cx="68" cy="160" rx="16" ry="22" fill="#5C2E00" transform="rotate(-20,68,160)"/>
            <path d="M68 138 Q68 160 68 182" fill="none" stroke="#3B1A08" stroke-width="2" stroke-linecap="round" transform="rotate(-20,68,160)"/>
            <!-- Biji kopi dekoratif kanan -->
            <ellipse cx="295" cy="155" rx="14" ry="19" fill="#5C2E00" transform="rotate(15,295,155)"/>
            <path d="M295 136 Q295 155 295 174" fill="none" stroke="#3B1A08" stroke-width="2" stroke-linecap="round" transform="rotate(15,295,155)"/>
            <!-- Bintik dekoratif -->
            <circle cx="75" cy="95" r="5" fill="rgba(196,134,58,0.3)"/>
            <circle cx="290" cy="100" r="7" fill="rgba(196,134,58,0.2)"/>
            <circle cx="60" cy="200" r="4" fill="rgba(196,134,58,0.25)"/>
            <circle cx="310" cy="195" r="5" fill="rgba(196,134,58,0.2)"/>
        </svg>
    </div>
</section>

<!-- Kategori -->
<section class="section" style="background: var(--putih);">
    <h2 class="section-title">Kategori Kopi</h2>
    <p class="section-sub">Pilih sesuai selera dan kebutuhanmu</p>
    <div class="kategori-grid">
        <a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Arabica" class="kategori-card">
            <div class="kat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4z"/>
                    <line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/>
                </svg>
            </div>
            <h4>Arabica</h4>
            <p>Cita rasa halus</p>
        </a>
        <a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Robusta" class="kategori-card">
            <div class="kat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <ellipse cx="12" cy="12" rx="6" ry="9" transform="rotate(-20,12,12)"/>
                    <path d="M12 3 Q12 12 12 21" stroke-width="1.5" opacity="0.6"/>
                </svg>
            </div>
            <h4>Robusta</h4>
            <p>Rasa kuat pekat</p>
        </a>
        <a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Liberika" class="kategori-card">
            <div class="kat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a9 9 0 0 1 9 9c0 5-9 13-9 13S3 16 3 11a9 9 0 0 1 9-9z"/>
                    <circle cx="12" cy="11" r="3"/>
                </svg>
            </div>
            <h4>Liberika</h4>
            <p>Aroma unik kayu</p>
        </a>
        <a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Excelsa" class="kategori-card">
            <div class="kat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </div>
            <h4>Excelsa</h4>
            <p>Fruity dan asam</p>
        </a>
        <a href="<?= BASE_URL ?>/pages/katalog.php?jenis=Blend" class="kategori-card">
            <div class="kat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/>
                    <path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                </svg>
            </div>
            <h4>Paket Gift</h4>
            <p>Hadiah istimewa</p>
        </a>
    </div>
</section>

<!-- Produk Unggulan -->
<section class="section">
    <h2 class="section-title">Produk Unggulan</h2>
    <p class="section-sub">Pilihan terbaik dari agen kami</p>
    <div class="card-grid">
        <?php while ($p = $produk_unggulan->fetch_assoc()): ?>
        <div class="card">
            <?php if ($p['gambar_produk'] && file_exists(__DIR__ . '/uploads/produk/' . $p['gambar_produk'])): ?>
                <img src="<?= BASE_URL ?>/uploads/produk/<?= htmlspecialchars($p['gambar_produk']) ?>" alt="<?= htmlspecialchars($p['nama_kopi']) ?>" class="card-img">
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
                <a href="<?= BASE_URL ?>/pages/detail.php?id=<?= $p['id_produk'] ?>" class="btn btn-dark" style="flex:1; text-align:center; font-size:0.85rem;">Detail</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <div style="text-align:center; margin-top:32px;">
        <a href="<?= BASE_URL ?>/pages/katalog.php" class="btn btn-outline">Lihat Semua Produk</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>