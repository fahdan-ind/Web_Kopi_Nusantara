<?php
// config/app.php
// Auto-detect base URL dan base path — works di XAMPP lokal maupun hosting manapun.
// Include file ini SEKALI di db.php supaya semua file otomatis dapat konstanta ini.

if (!defined('BASE_PATH')) {
    // Absolute path ke root project (folder tempat index.php root berada)
    // __DIR__ di sini = config/, jadi naik 1 level
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('BASE_URL')) {
    // Deteksi protokol (kompatibel hosting gratis)
    $scheme = 'http';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $scheme = strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]));
    } elseif (!empty($_SERVER['REQUEST_SCHEME'])) {
        $scheme = strtolower($_SERVER['REQUEST_SCHEME']);
    } elseif (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443') {
        $scheme = 'https';
    }

    // Host (domain / localhost) - lebih aman untuk hosting gratis
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
    $host = strtolower(trim($host));

    // Hitung subfolder dari document root ke root project
    // Contoh: doc root = /var/www/html, BASE_PATH = /var/www/html/kopi_nusantara
    //   → subfolder = /kopi_nusantara
    // Contoh di hosting root: doc root = /home/user/public_html, BASE_PATH = /home/user/public_html
    //   → subfolder = '' (kosong)
    $doc_root   = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') : '';
    $base_clean = rtrim(str_replace('\\', '/', BASE_PATH), '/');
    $subfolder  = '';

    if ($doc_root !== '' && strpos($base_clean, $doc_root) === 0) {
        $subfolder = str_replace($doc_root, '', $base_clean);
    } else {
        $script_dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        $subfolder = $script_dir === '/' ? '' : $script_dir;
    }

    define('BASE_URL', $scheme . '://' . $host . $subfolder);
}