<?php
// dashboard/produk.php  –  Kelola Produk (CRUD + Upload Gambar)
// Akses: admin / agen saja  →  requireAdmin() ada di header.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/header.php';   // sudah include requireAdmin() + buka dash-layout

$pesan     = '';
$error     = '';
$edit_data = null;

/* ── helpers ─────────────────────────────────── */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function gambarDir()  { return __DIR__ . '/../uploads/produk/'; }
function gambarPath($f) { return gambarDir() . $f; }
function gambarUrl($f)  { return BASE_URL . '/uploads/produk/' . htmlspecialchars($f); }

function uploadGambar($file) {
    $dir = gambarDir();
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $allowed = ['jpg','jpeg','png','webp'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK)       return ['error'=>'Gagal upload. Kode: '.$file['error']];
    if (!in_array($ext, $allowed))               return ['error'=>'Format tidak didukung. Gunakan JPG/PNG/WEBP.'];
    if ($file['size'] > 2 * 1024 * 1024)        return ['error'=>'Ukuran file maks 2 MB.'];

    $nama = uniqid('kopi_') . '.' . $ext;
    return move_uploaded_file($file['tmp_name'], $dir . $nama)
        ? ['nama' => $nama]
        : ['error' => 'Gagal memindahkan file. Cek permission folder uploads/produk/'];
}

/* ════════════════════════════════════════════════
   DELETE
═══════════════════════════════════════════════ */
if (isset($_GET['hapus'])) {
    $id  = (int)$_GET['hapus'];
    $row = $conn->query("SELECT gambar_produk FROM produk WHERE id_produk=$id")->fetch_assoc();
    if ($row) {
        if ($row['gambar_produk'] && file_exists(gambarPath($row['gambar_produk'])))
            unlink(gambarPath($row['gambar_produk']));
        $conn->query("DELETE FROM produk WHERE id_produk=$id");
        $pesan = "Produk berhasil dihapus.";
    } else {
        $error = "Produk tidak ditemukan.";
    }
}

/* ════════════════════════════════════════════════
   LOAD EDIT
═══════════════════════════════════════════════ */
if (isset($_GET['edit'])) {
    $id        = (int)$_GET['edit'];
    $edit_data = $conn->query("SELECT * FROM produk WHERE id_produk=$id")->fetch_assoc();
    if (!$edit_data) { $error = "Produk tidak ditemukan."; }
}

/* ════════════════════════════════════════════════
   CREATE / UPDATE
═══════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kopi  = trim($_POST['nama_kopi']  ?? '');
    $jenis_kopi = trim($_POST['jenis_kopi'] ?? '');
    $deskripsi  = trim($_POST['deskripsi']  ?? '');
    $harga      = (int)($_POST['harga']     ?? 0);
    $stok       = (int)($_POST['stok']      ?? 0);
    $id_produk  = (int)($_POST['id_produk'] ?? 0);
    $id_agen    = (getRole() === 'admin')
                    ? (int)($_POST['id_agen'] ?? $_SESSION['user_id'])
                    : (int)$_SESSION['user_id'];

    if (!$nama_kopi || !$jenis_kopi || $harga <= 0) {
        $error = "Nama kopi, jenis kopi, dan harga wajib diisi.";
        // Pertahankan data form supaya user tidak perlu isi ulang
        $edit_data = $_POST + ['id_produk' => $id_produk];
    } else {
        $gambar_baru = null;
        if (!empty($_FILES['gambar_produk']['name'])) {
            $up = uploadGambar($_FILES['gambar_produk']);
            isset($up['error']) ? $error = $up['error'] : $gambar_baru = $up['nama'];
        }

        if (!$error) {
            if ($id_produk) {
                /* UPDATE */
                $old   = $conn->query("SELECT gambar_produk FROM produk WHERE id_produk=$id_produk")->fetch_assoc();
                $lama  = $old['gambar_produk'] ?? '';
                $final = $gambar_baru ?? $lama;

                if ($gambar_baru && $lama && file_exists(gambarPath($lama)))
                    unlink(gambarPath($lama));

                $stmt = $conn->prepare(
                    "UPDATE produk SET id_agen=?,nama_kopi=?,jenis_kopi=?,deskripsi=?,
                     harga=?,stok=?,gambar_produk=? WHERE id_produk=?"
                );
                $stmt->bind_param("isssiiis",
                    $id_agen,$nama_kopi,$jenis_kopi,$deskripsi,$harga,$stok,$final,$id_produk);
                $stmt->execute();
                $pesan     = "Produk <strong>".htmlspecialchars($nama_kopi)."</strong> berhasil diperbarui.";
                $edit_data = null;
            } else {
                /* CREATE */
                $final = $gambar_baru ?? '';
                $stmt  = $conn->prepare(
                    "INSERT INTO produk (id_agen,nama_kopi,jenis_kopi,deskripsi,harga,stok,gambar_produk)
                     VALUES (?,?,?,?,?,?,?)"
                );
                $stmt->bind_param("isssiis",
                    $id_agen,$nama_kopi,$jenis_kopi,$deskripsi,$harga,$stok,$final);
                $stmt->execute();
                $pesan = "Produk baru <strong>".htmlspecialchars($nama_kopi)."</strong> berhasil ditambahkan.";
            }
        }
    }
}

/* ── Query data ──────────────────────────────── */
$produk_list = $conn->query(
    "SELECT p.*, u.nama_lengkap AS nama_agen
     FROM produk p LEFT JOIN users u ON p.id_agen=u.id_user
     ORDER BY p.id_produk DESC"
);
$agen_list = $conn->query(
    "SELECT id_user, nama_lengkap FROM users WHERE role IN ('agen','admin') ORDER BY nama_lengkap ASC"
);
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <h1 style="margin:0;">Kelola Produk</h1>
    <?php if (!$edit_data): ?>
        <a href="#form-produk" class="btn btn-primary">+ Tambah Produk Baru</a>
    <?php endif; ?>
</div>

<?php if ($pesan): ?>
    <div class="alert alert-success"><?= $pesan ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════
     TABEL DAFTAR PRODUK  (READ)
══════════════════════════════════════════════ -->
<div style="overflow-x:auto; margin-bottom:40px;">
<table class="data-table">
    <thead>
        <tr>
            <th style="width:40px;">ID</th>
            <th style="width:56px;">Foto</th>
            <th>Nama Kopi</th>
            <th>Jenis</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Agen</th>
            <th style="width:145px;text-align:center;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($produk_list->num_rows === 0): ?>
            <tr>
                <td colspan="8" style="text-align:center;padding:40px;color:var(--abu);">
                    Belum ada produk. Gunakan form di bawah untuk menambahkan produk pertama.
                </td>
            </tr>
        <?php endif; ?>

        <?php while ($p = $produk_list->fetch_assoc()): ?>
        <tr <?= ($edit_data && ($edit_data['id_produk'] ?? 0) == $p['id_produk']) ? 'style="background:#fff8ee;"' : '' ?>>
            <td><?= $p['id_produk'] ?></td>

            <!-- Gambar thumbnail -->
            <td>
                <?php if (!empty($p['gambar_produk']) && file_exists(gambarPath($p['gambar_produk']))): ?>
                    <img src="<?= gambarUrl($p['gambar_produk']) ?>"
                         class="produk-thumb" alt="<?= htmlspecialchars($p['nama_kopi']) ?>">
                <?php else: ?>
                    <div class="produk-thumb"
                         style="background:var(--abu-muda);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="#bbb" stroke-width="1.5" stroke-linecap="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </td>

            <td style="font-weight:600;"><?= htmlspecialchars($p['nama_kopi']) ?></td>

            <td>
                <span style="background:var(--krem);color:var(--coklat-mid);padding:2px 10px;
                             border-radius:20px;font-size:0.78rem;font-weight:600;">
                    <?= htmlspecialchars($p['jenis_kopi']) ?>
                </span>
            </td>

            <td><?= formatRupiah($p['harga']) ?></td>

            <td>
                <?php if ($p['stok'] == 0): ?>
                    <span style="color:var(--merah);font-weight:700;">Habis</span>
                <?php elseif ($p['stok'] <= 10): ?>
                    <span style="color:#856404;font-weight:600;"><?= $p['stok'] ?> unit ⚠</span>
                <?php else: ?>
                    <?= $p['stok'] ?> unit
                <?php endif; ?>
            </td>

            <td style="color:var(--abu);font-size:0.85rem;">
                <?= htmlspecialchars($p['nama_agen'] ?? '-') ?>
            </td>

            <!-- TOMBOL EDIT & HAPUS -->
            <td style="text-align:center;white-space:nowrap;">
                <a href="produk.php?edit=<?= $p['id_produk'] ?>#form-produk"
                   class="btn btn-primary"
                   style="font-size:0.78rem;padding:5px 11px;">
                    ✏️ Edit
                </a>
                <a href="produk.php?hapus=<?= $p['id_produk'] ?>"
                   class="btn btn-danger"
                   style="font-size:0.78rem;padding:5px 11px;margin-left:4px;"
                   onclick="return confirm('Hapus produk «<?= addslashes(htmlspecialchars($p['nama_kopi'])) ?>»?\nFoto juga akan ikut dihapus.')">
                    🗑️ Hapus
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<!-- ══════════════════════════════════════════════
     FORM TAMBAH / EDIT  (CREATE & UPDATE)
══════════════════════════════════════════════ -->
<div class="dash-form" id="form-produk" style="max-width:660px;">
    <h2 style="margin-bottom:20px;">
        <?= $edit_data ? '✏️ Edit Produk' : '➕ Tambah Produk Baru' ?>
    </h2>

    <form method="POST" enctype="multipart/form-data">

        <?php if (!empty($edit_data['id_produk'])): ?>
            <input type="hidden" name="id_produk" value="<?= (int)$edit_data['id_produk'] ?>">
        <?php endif; ?>

        <!-- Baris 1: Nama + Jenis -->
        <div class="form-row">
            <div class="form-group">
                <label>Nama Kopi <span style="color:var(--merah)">*</span></label>
                <input type="text" name="nama_kopi"
                       placeholder="cth. Arabica Gayo Premium"
                       value="<?= htmlspecialchars($edit_data['nama_kopi'] ?? '') ?>"
                       required>
            </div>
            <div class="form-group">
                <label>Jenis Kopi <span style="color:var(--merah)">*</span></label>
                <select name="jenis_kopi">
                    <?php foreach (['Arabica','Robusta','Liberika','Excelsa','Blend'] as $j): ?>
                        <option value="<?= $j ?>"
                            <?= ($edit_data['jenis_kopi'] ?? 'Arabica') === $j ? 'selected' : '' ?>>
                            <?= $j ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Baris 2: Harga + Stok -->
        <div class="form-row">
            <div class="form-group">
                <label>Harga (Rp) <span style="color:var(--merah)">*</span></label>
                <input type="number" name="harga" min="1"
                       placeholder="cth. 85000"
                       value="<?= $edit_data['harga'] ?? '' ?>"
                       required>
            </div>
            <div class="form-group">
                <label>Stok (unit)</label>
                <input type="number" name="stok" min="0"
                       placeholder="0"
                       value="<?= $edit_data['stok'] ?? 0 ?>"
                       required>
            </div>
        </div>

        <!-- Agen — hanya tampil untuk admin -->
        <?php if (getRole() === 'admin'): ?>
        <div class="form-group">
            <label>Agen Penjual</label>
            <select name="id_agen">
                <?php $agen_list->data_seek(0); while ($a = $agen_list->fetch_assoc()): ?>
                    <option value="<?= $a['id_user'] ?>"
                        <?= ($edit_data['id_agen'] ?? $_SESSION['user_id']) == $a['id_user'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nama_lengkap']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Deskripsi -->
        <div class="form-group">
            <label>Deskripsi Produk</label>
            <textarea name="deskripsi" rows="3"
                      placeholder="Ceritakan asal-usul, rasa, dan keunggulan kopi ini…"
                      style="resize:vertical;"><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
        </div>

        <!-- Upload Gambar -->
        <div class="form-group">
            <label>
                Gambar Produk
                <?php if (!empty($edit_data['id_produk'])): ?>
                    <span style="font-size:0.8rem;font-weight:400;color:var(--abu);">
                        — kosongkan jika tidak ingin mengganti
                    </span>
                <?php endif; ?>
            </label>

            <input type="file" name="gambar_produk" id="inp-gambar"
                   accept="image/jpeg,image/png,image/webp"
                   style="display:block;margin-bottom:6px;">

            <!-- Preview gambar baru (via JS) -->
            <div id="preview-wrap" style="display:none;margin-top:8px;">
                <img id="preview-img" src="" alt="Preview"
                     style="height:90px;border-radius:6px;border:2px solid var(--coklat-muda);object-fit:cover;">
                <div class="text-muted" style="margin-top:4px;font-size:0.8rem;">Preview gambar baru</div>
            </div>

            <!-- Gambar tersimpan (saat edit) -->
            <?php if (!empty($edit_data['gambar_produk']) && file_exists(gambarPath($edit_data['gambar_produk']))): ?>
                <div id="current-img" style="margin-top:10px;">
                    <img src="<?= gambarUrl($edit_data['gambar_produk']) ?>"
                         style="height:90px;border-radius:6px;border:2px solid var(--abu-muda);object-fit:cover;"
                         alt="Gambar saat ini">
                    <div class="text-muted" style="margin-top:4px;font-size:0.8rem;">Gambar tersimpan</div>
                </div>
            <?php endif; ?>

            <div class="text-muted" style="margin-top:6px;font-size:0.8rem;">
                Format JPG / PNG / WEBP &bull; Maks. 2 MB
            </div>
        </div>

        <!-- Tombol submit -->
        <div style="display:flex;gap:12px;margin-top:8px;">
            <button type="submit" class="btn btn-primary">
                <?= !empty($edit_data['id_produk']) ? '💾 Simpan Perubahan' : '➕ Tambah Produk' ?>
            </button>
            <?php if (!empty($edit_data['id_produk'])): ?>
                <a href="produk.php" class="btn btn-outline">✖ Batal</a>
            <?php endif; ?>
        </div>

    </form>
</div><!-- /dash-form -->

<!-- JS: preview gambar sebelum upload -->
<script>
(function(){
    var inp     = document.getElementById('inp-gambar');
    var wrap    = document.getElementById('preview-wrap');
    var img     = document.getElementById('preview-img');
    var currImg = document.getElementById('current-img');
    if (!inp) return;
    inp.addEventListener('change', function(){
        var f = this.files[0];
        if (!f) { wrap.style.display = 'none'; return; }
        if (currImg) currImg.style.display = 'none';
        var r = new FileReader();
        r.onload = function(e){ img.src = e.target.result; wrap.style.display = 'block'; };
        r.readAsDataURL(f);
    });
})();
</script>

<?php require_once __DIR__ . '/footer.php'; ?>