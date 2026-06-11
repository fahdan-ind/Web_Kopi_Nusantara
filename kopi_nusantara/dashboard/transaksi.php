<?php
// dashboard/transaksi.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/header.php';

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_t    = (int)$_POST['id_transaksi'];
    $status  = $_POST['status_pesanan'];
    $allowed = ['pending','dibayar','dikirim'];
    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE transaksi SET status_pesanan=? WHERE id_transaksi=?");
        $stmt->bind_param("si", $status, $id_t);
        $stmt->execute();
    }
    header("Location: transaksi.php");
    exit;
}

$transaksi = $conn->query("
    SELECT t.*, u.nama_lengkap AS nama_pembeli, u.no_hp, p.nama_kopi, p.jenis_kopi
    FROM transaksi t
    LEFT JOIN users u ON t.id_user=u.id_user
    LEFT JOIN produk p ON t.id_produk=p.id_produk
    ORDER BY t.id_transaksi DESC
");
?>

<h1>Data Transaksi</h1>

<div style="overflow-x:auto;">
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th><th>Pembeli</th><th>No. HP</th><th>Produk</th>
            <th>Jml</th><th>Total</th><th>Tanggal</th><th>Status</th><th>Ubah Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($t = $transaksi->fetch_assoc()): ?>
        <tr>
            <td>#<?= $t['id_transaksi'] ?></td>
            <td><?= htmlspecialchars($t['nama_pembeli']) ?></td>
            <td><?= htmlspecialchars($t['no_hp'] ?? '-') ?></td>
            <td>
                <?= htmlspecialchars($t['nama_kopi']) ?>
                <br><span class="text-muted"><?= $t['jenis_kopi'] ?></span>
            </td>
            <td><?= $t['jumlah_beli'] ?> pcs</td>
            <td><?= formatRupiah($t['total_harga']) ?></td>
            <td><?= $t['tgl_transaksi'] ?></td>
            <td><span class="badge badge-<?= $t['status_pesanan'] ?>"><?= ucfirst($t['status_pesanan']) ?></span></td>
            <td>
                <form method="POST" style="display:flex;gap:4px;align-items:center;">
                    <input type="hidden" name="id_transaksi" value="<?= $t['id_transaksi'] ?>">
                    <select name="status_pesanan"
                            style="padding:4px 8px;border:1px solid var(--abu-muda);border-radius:4px;font-size:0.82rem;">
                        <option value="pending" <?= $t['status_pesanan']==='pending' ?'selected':'' ?>>Pending</option>
                        <option value="dibayar" <?= $t['status_pesanan']==='dibayar' ?'selected':'' ?>>Dibayar</option>
                        <option value="dikirim" <?= $t['status_pesanan']==='dikirim' ?'selected':'' ?>>Dikirim</option>
                    </select>
                    <button type="submit" name="update_status"
                            class="btn btn-success"
                            style="font-size:0.78rem;padding:4px 10px;">Simpan</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>