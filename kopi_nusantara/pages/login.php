<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

if (isLoggedIn()) { header("Location: " . BASE_URL . "/index.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Email dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['nama']    = $user['nama_lengkap'];
            $_SESSION['role']    = $user['role'];

            $redirect = $_GET['redirect'] ?? '';
            if ($redirect === 'keranjang') {
                header("Location: " . BASE_URL . "/pages/keranjang.php");
            } elseif ($user['role'] === 'admin' || $user['role'] === 'agen') {
                header("Location: " . BASE_URL . "/dashboard/index.php");
            } else {
                header("Location: " . BASE_URL . "/index.php");
            }
            exit;
        } else {
            $error = "Email atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kopi Nusantara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        body{margin:0;padding:0;font-family:'Inter',sans-serif;}
        .auth-layout{display:flex;min-height:100vh;}
        .auth-photo{flex:1;position:relative;overflow:hidden;background:#3B1A08;}
        .auth-photo img{width:100%;height:100%;object-fit:cover;display:block;opacity:0.85;}
        .auth-photo-fallback{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(160deg,#3B1A08 0%,#7B3F00 50%,#C4863A 100%);}
        .auth-photo-overlay{position:absolute;inset:0;background:linear-gradient(to right,rgba(59,26,8,0.25),rgba(59,26,8,0.55));display:flex;flex-direction:column;justify-content:flex-end;padding:48px 44px;}
        .auth-photo-tagline{font-family:'Libre Baskerville',Georgia,serif;font-size:2rem;font-weight:700;color:#fff;line-height:1.3;text-shadow:0 2px 16px rgba(0,0,0,0.4);margin-bottom:12px;}
        .auth-photo-sub{font-size:0.95rem;color:rgba(245,236,215,0.85);line-height:1.6;max-width:320px;}
        .auth-form-panel{width:460px;flex-shrink:0;background:#FAF5EC;display:flex;flex-direction:column;justify-content:center;padding:56px 48px;overflow-y:auto;}
        .auth-brand{display:flex;align-items:center;gap:10px;margin-bottom:40px;}
        .auth-brand-name{font-family:'Libre Baskerville',Georgia,serif;font-size:1.15rem;font-weight:700;color:#7B3F00;}
        .auth-heading{font-family:'Libre Baskerville',Georgia,serif;font-size:2rem;font-weight:700;color:#3B1A08;margin-bottom:6px;}
        .auth-subheading{font-size:0.9rem;color:#6B6B6B;margin-bottom:32px;}
        .auth-form .form-group{margin-bottom:20px;}
        .auth-form label{display:block;font-size:0.82rem;font-weight:600;color:#3B1A08;margin-bottom:7px;letter-spacing:0.4px;text-transform:uppercase;}
        .auth-form input{width:100%;padding:12px 16px;border:1.5px solid #E8E0D5;border-radius:8px;font-family:'Inter',sans-serif;font-size:0.93rem;color:#3B1A08;background:#fff;transition:border-color 0.2s,box-shadow 0.2s;box-sizing:border-box;}
        .auth-form input:focus{outline:none;border-color:#C4863A;box-shadow:0 0 0 3px rgba(196,134,58,0.15);}
        .auth-form input::placeholder{color:#B0A898;}
        .auth-btn-submit{width:100%;padding:13px;background:#7B3F00;color:#fff;font-size:0.95rem;font-weight:600;border:none;border-radius:8px;cursor:pointer;margin-top:8px;transition:background 0.2s;}
        .auth-btn-submit:hover{background:#C4863A;}
        .auth-divider{display:flex;align-items:center;gap:12px;margin:24px 0;}
        .auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:#E8E0D5;}
        .auth-divider span{font-size:0.8rem;color:#B0A898;}
        .auth-link{text-align:center;font-size:0.88rem;color:#6B6B6B;}
        .auth-link a{color:#7B3F00;font-weight:600;text-decoration:none;}
        .auth-link a:hover{color:#C4863A;text-decoration:underline;}
        .auth-alert-danger{background:#fdecea;color:#C0392B;border:1px solid #f5c6cb;border-radius:8px;padding:10px 14px;font-size:0.88rem;margin-bottom:20px;}
        @media(max-width:768px){.auth-photo{display:none;}.auth-form-panel{width:100%;padding:40px 28px;}}
    </style>
</head>
<body>
<div class="auth-layout">
    <div class="auth-photo">
        <?php
        $fotoj = BASE_PATH . '/assets/images/bg-login.jpg';
        $fotow = BASE_PATH . '/assets/images/bg-login.webp';
        $fotop = BASE_PATH . '/assets/images/bg-login.png';
        if     (file_exists($fotoj)) $fs = BASE_URL . '/assets/images/bg-login.jpg';
        elseif (file_exists($fotow)) $fs = BASE_URL . '/assets/images/bg-login.webp';
        elseif (file_exists($fotop)) $fs = BASE_URL . '/assets/images/bg-login.png';
        else                         $fs = '';
        ?>
        <?php if ($fs): ?><img src="<?= $fs ?>" alt="Kopi Nusantara">
        <?php else: ?>
            <div class="auth-photo-fallback">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300" width="240" height="240" opacity="0.22">
                    <circle cx="150" cy="150" r="120" fill="none" stroke="#C4863A" stroke-width="2"/>
                    <g transform="translate(88,70)">
                        <path d="M22 20h1a14 14 0 0 1 0 28h-1" fill="none" stroke="#C4863A" stroke-width="5" stroke-linecap="round"/>
                        <path d="M6 20h48v32a14 14 0 0 1-14 14H20A14 14 0 0 1 6 52z" fill="none" stroke="#C4863A" stroke-width="5" stroke-linecap="round"/>
                        <path d="M20 6 Q24 0 20 -6" fill="none" stroke="#C4863A" stroke-width="4" stroke-linecap="round"/>
                        <path d="M30 6 Q34 0 30 -6" fill="none" stroke="#C4863A" stroke-width="4" stroke-linecap="round"/>
                        <path d="M40 6 Q44 0 40 -6" fill="none" stroke="#C4863A" stroke-width="4" stroke-linecap="round"/>
                    </g>
                </svg>
            </div>
        <?php endif; ?>
        <div class="auth-photo-overlay">
            <div class="auth-photo-tagline">Dari Ladang<br>ke Cangkirmu</div>
            <div class="auth-photo-sub">Masuk dan temukan ratusan varian kopi premium langsung dari agen terpercaya di seluruh Nusantara.</div>
        </div>
    </div>
    <div class="auth-form-panel">
        <div class="auth-brand">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#C4863A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4z"/>
                <line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/>
            </svg>
            <span class="auth-brand-name">Kopi Nusantara</span>
        </div>
        <h1 class="auth-heading">Selamat Datang</h1>
        <p class="auth-subheading">Masuk ke akun kamu untuk mulai berbelanja</p>
        <?php if ($error): ?>
            <div class="auth-alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="contoh@mail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="auth-btn-submit">Masuk</button>
        </form>
        <div class="auth-divider"><span>atau</span></div>
        <div class="auth-link">Belum punya akun? <a href="register.php">Daftar di sini</a></div>
    </div>
</div>
</body>
</html>