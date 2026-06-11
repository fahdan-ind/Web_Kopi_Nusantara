<?php
// dashboard/users.php  —  hanya admin
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/header.php';

// Agen tidak boleh akses halaman ini
if (getRole() !== 'admin') {
    header("Location: /kopi_nusantara/dashboard/index.php");
    exit;
}

$users = $conn->query("SELECT * FROM users ORDER BY id_user ASC");
?>

<h1>Data Pengguna</h1>

<div style="overflow-x:auto;">
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th><th>Nama Lengkap</th><th>Email</th>
            <th>Role</th><th>No. HP</th><th>Alamat</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $u['id_user'] ?></td>
            <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <span class="badge <?= $u['role']==='admin' ? 'badge-dikirim'
                                    : ($u['role']==='agen'  ? 'badge-dibayar'
                                    : 'badge-pending') ?>">
                    <?= ucfirst($u['role']) ?>
                </span>
            </td>
            <td><?= htmlspecialchars($u['no_hp'] ?? '-') ?></td>
            <td><?= htmlspecialchars($u['alamat'] ?? '-') ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>