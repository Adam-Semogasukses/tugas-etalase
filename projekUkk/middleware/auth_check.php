<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// URL dasar proyek (mis. "/projekUkk" atau "" jika memakai virtual host)
$_root = str_replace(chr(92), '/', dirname(__DIR__));
$_doc  = rtrim(str_replace(chr(92), '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
define('BASE_URL', stripos($_root, $_doc) === 0 ? substr($_root, strlen($_doc)) : '');

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit();
    }
}

function check_role($roles = []) {
    check_login();
    if (!in_array($_SESSION['role'], $roles)) {
        die("Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.");
    }
}

// Upload gambar: hanya jpg/jpeg/png/gif/webp, maks 2MB. Return nama file atau null.
function simpan_gambar($file, &$error) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed) || @getimagesize($file['tmp_name']) === false) {
        $error = "File harus berupa gambar (jpg, png, gif, webp).";
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        $error = "Ukuran gambar maksimal 2MB.";
        return null;
    }
    $nama = uniqid('project_', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], dirname(__DIR__) . '/uploads/' . $nama)) {
        $error = "Gagal menyimpan gambar.";
        return null;
    }
    return $nama;
}
?>
