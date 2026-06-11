<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: " . BASE_URL . "/pages/katalog.php"); exit; }

$stmt = $conn->prepare("SELECT p.*, u.nama_lengkap as nama_agen, u.alamat as alamat_agen FROM produk p LEFT JOIN users u ON p.id_agen = u.id_user WHERE p.id_produk = ?");
$stmt->bind_param("i", $id); $stmt->execute();
$produk = $stmt->get_result()->fetch_assoc();
if (!$produk) { header("Location: " . BASE_URL . "/pages/katalog.php"); exit; }

$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_keranjang'])) {
    $qty = max(1, (int)$_POST['qty']);
    if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];
    $found = false;
    foreach ($_SESSION['keranjang'] as &$item) {
        if ($item['id_produk'] == $id) { $item['qty'] += $qty; $found = true; break; }
    }
    if (!$found) {
        $_SESSION['keranjang'][] = [
            'id_produk' => $produk['id_produk'],
            'nama_kopi' => $produk['nama_kopi'],
            'harga'     => $produk['harga'],
            'qty'       => $qty,
            'gambar'    => $produk['gambar_produk'],
        ];
    }
    $pesan = 'success';
}

function formatRupiah($angka) { return 'Rp ' . number_format($angka, 0, ',', '.'); }

require_once __DIR__ . '/../includes/header.php';

$gambar_path = BASE_PATH . '/uploads/produk/' . ($produk['gambar_produk'] ?? '');
$gambar_url  = BASE_URL  . '/uploads/produk/' . htmlspecialchars($produk['gambar_produk'] ?? '');
?>

<?php if ($pesan === 'success'): ?>
<div class="alert alert-success" style="margin:16px 40px 0;">
    Produk berhasil ditambahkan ke keranjang.
    <a href="<?= BASE_URL ?>/pages/keranjang.php" style="color:var(--coklat-mid);font-weight:600;">Lihat Keranjang</a>
</div>
<?php endif; ?>

<div class="detail-page">
    <?php if (!empty($produk['gambar_produk']) && file_exists($gambar_path)): ?>
        <img src="<?= $gambar_url ?>" alt="<?= htmlspecialchars($produk['nama_kopi']) ?>" class="detail-img">
    <?php else: ?>
        <div class="detail-img" style="display:flex;align-items:center;justify-content:center;background:var(--abu-muda);">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#C4863A" stroke-width="1.2" stroke-linecap="round">
                <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4z"/>
                <line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/>
            </svg>
        </div>
    <?php endif; ?>

    <div class="detail-info">
        <span class="card-jenis" style="margin-bottom:10px;"><?= htmlspecialchars($produk['jenis_kopi']) ?></span>
        <h1><?= htmlspecialchars($produk['nama_kopi']) ?></h1>
        <div class="detail-agen">
            <strong><?= htmlspecialchars($produk['nama_agen'] ?? 'Agen Tidak Diketahui') ?></strong>
            <?= htmlspecialchars($produk['alamat_agen'] ?? '') ?>
        </div>
        <div class="detail-harga"><?= formatRupiah($produk['harga']) ?></div>
        <div class="detail-stok">
            <?php if ($produk['stok'] > 0): ?>
                Stok tersedia &mdash; Sisa <?= $produk['stok'] ?> unit
            <?php else: ?>
                <span style="color:var(--merah);">Stok habis</span>
            <?php endif; ?>
        </div>
        <p class="detail-desc"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>
        <?php if ($produk['stok'] > 0): ?>
        <form method="POST">
            <div class="qty-form">
                <label style="font-weight:600;">Jumlah:</label>
                <input type="number" name="qty" value="1" min="1" max="<?= $produk['stok'] ?>" class="qty-input">
                <button type="submit" name="tambah_keranjang" class="btn btn-primary">Tambah ke Keranjang</button>
            </div>
        </form>
        <?php endif; ?>
        <div style="margin-top:20px;">
            <a href="<?= BASE_URL ?>/pages/katalog.php" style="color:var(--coklat-mid);font-size:0.9rem;">&larr; Kembali ke Katalog</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>