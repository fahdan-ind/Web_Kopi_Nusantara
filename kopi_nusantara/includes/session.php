<?php
// includes/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan BASE_URL tersedia (kalau session.php di-include tanpa db.php)
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/app.php';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getRole() {
    return $_SESSION['role'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/pages/login.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (getRole() !== 'admin' && getRole() !== 'agen') {
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}