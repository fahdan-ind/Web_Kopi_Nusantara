<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

$pesan = '';
$error = '';

if (isset($_GET['hapus'])) {
    $idx = (int)$_GET['hapus'];
    if (isset($_SESSION['keranjang'][$idx])) array_splice($_SESSION['keranjang'], $idx, 1);
    header("Location: " . BASE_URL . "/pages/keranjang.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $idx => $val) {
        $qty = max(1, (int)$val);
        if (isset($_SESSION['keranjang'][$idx])) $_SESSION['keranjang'][$idx]['qty'] = $qty;
    }
    header("Location: " . BASE_URL . "/pages/keranjang.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!isLoggedIn()) { header("Location: " . BASE_URL . "/pages/login.php?redirect=keranjang"); exit; }
    if (empty($_SESSION['keranjang'])) {
        $error = "Keranjang kosong.";
    } else {
        $id_user = $_SESSION['user_id'];
        $tgl     = date('Y-m-d');
        $sukses  = true;

        foreach ($_SESSION['keranjang'] as $item) {
            $id_produk   = (int)$item['id_produk'];
            $jumlah_beli = (int)$item['qty'];
            $total_harga = (int)$item['harga'] * $jumlah_beli;
            $status      = 'pending';

            $cek = $conn->prepare("SELECT stok FROM produk WHERE id_produk=?");
            $cek->bind_param("i", $id_produk); $cek->execute();
            $sr = $cek->get_result()->fetch_assoc();

            if (!$sr || $sr['stok'] < $jumlah_beli) { $error = "Stok tidak mencukupi."; $sukses = false; break; }

            $ins = $conn->prepare("INSERT INTO transaksi (id_user,id_produk,tgl_transaksi,jumlah_beli,total_harga,status_pesanan) VALUES (?,?,?,?,?,?)");
            $ins->bind_param("iiisss", $id_user, $id_produk, $tgl, $jumlah_beli, $total_harga, $status);
            $ins->execute();
            $conn->query("UPDATE produk SET stok = stok - $jumlah_beli WHERE id_produk = $id_produk");
        }

        if ($sukses) { $_SESSION['keranjang'] = []; $pesan = "Pesanan berhasil dibuat! Status: Pending."; }
    }
}

$keranjang = $_SESSION['keranjang'] ?? [];
$total = 0;
foreach ($keranjang as $item) $total += $item['harga'] * $item['qty'];

function formatRupiah($angka) { return 'Rp ' . number_format($angka, 0, ',', '.'); }

require_once __DIR__ . '/../includes/header.php';
?>

<div class="keranjang-page">
    <h1>Keranjang Belanja</h1>
    <?php if ($pesan): ?><div class="alert alert-success"><?= htmlspecialchars($pesan) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (empty($keranjang)): ?>
        <div class="empty-state">
            <h3>Keranjang masih kosong</h3>
            <p>Temukan kopi favoritmu di katalog kami</p>
            <a href="<?= BASE_URL ?>/pages/katalog.php" class="btn btn-primary" style="margin-top:16px;">Lihat Katalog</a>
        </div>
    <?php else: ?>
        <form method="POST">
        <table class="keranjang-table">
            <thead><tr><th>Produk</th><th>Harga Satuan</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($keranjang as $idx => $item): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <?php
                            $gpath = BASE_PATH . '/uploads/produk/' . ($item['gambar'] ?? '');
                            $gurl  = BASE_URL  . '/uploads/produk/' . htmlspecialchars($item['gambar'] ?? '');
                            ?>
                            <?php if (!empty($item['gambar']) && file_exists($gpath)): ?>
                                <img src="<?= $gurl ?>" class="produk-thumb" alt="">
                            <?php else: ?>
                                <div class="produk-thumb" style="background:var(--abu-muda);"></div>
                            <?php endif; ?>
                            <strong><?= htmlspecialchars($item['nama_kopi']) ?></strong>
                        </div>
                    </td>
                    <td><?= formatRupiah($item['harga']) ?></td>
                    <td><input type="number" name="qty[<?= $idx ?>]" value="<?= $item['qty'] ?>" min="1" class="qty-input" style="width:60px;"></td>
                    <td><?= formatRupiah($item['harga'] * $item['qty']) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/pages/keranjang.php?hapus=<?= $idx ?>" class="btn btn-danger"
                           style="font-size:0.8rem;padding:5px 12px;"
                           onclick="return confirm('Hapus item ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;">
            <button type="submit" name="update_qty" class="btn btn-outline">Update Keranjang</button>
            <div class="total-box">
                <div class="total-row"><span>Subtotal</span><span><?= formatRupiah($total) ?></span></div>
                <div class="total-row"><span>Ongkos Kirim</span><span>Gratis</span></div>
                <div class="total-row total-final"><span>Total</span><span><?= formatRupiah($total) ?></span></div>
                <?php if (!isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/pages/login.php?redirect=keranjang" class="btn btn-primary" style="width:100%;text-align:center;margin-top:12px;display:block;">Login untuk Checkout</a>
                <?php else: ?>
                    <button type="submit" name="checkout" class="btn btn-primary" style="width:100%;margin-top:12px;">Checkout Sekarang</button>
                <?php endif; ?>
            </div>
        </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>