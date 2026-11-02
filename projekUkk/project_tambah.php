<?php
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';

check_login();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project']);
    $deskripsi    = trim($_POST['deskripsi']);
    $link_project = trim($_POST['link_project']);

    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        $error = "Gambar wajib diunggah.";
    } else {
        $gambar = simpan_gambar($_FILES['gambar'], $error);
        if ($gambar) {
            $stmt = $pdo->prepare("INSERT INTO projects (user_id, nama_project, deskripsi, gambar, link_project)
                                   VALUES (:uid, :nama, :desk, :img, :link)");
            $stmt->execute([
                ':uid' => $_SESSION['user_id'], ':nama' => $nama_project,
                ':desk' => $deskripsi, ':img' => $gambar, ':link' => $link_project
            ]);
            header('Location: index.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Karya - Etalase Digital Stella</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
<div class="form-container">
    <h2>Tambah Karya Digital</h2>
    <?php if ($error): ?><p style="color:#721c24; margin-bottom:12px;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Karya</label>
            <input type="text" name="nama_project" required>
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" required></textarea>
        </div>
        <div class="form-group">
            <label>Link Project</label>
            <input type="url" name="link_project" required>
        </div>
        <div class="form-group">
            <label>Gambar</label>
            <input type="file" name="gambar" id="gambar" accept="image/*" required>
            <div class="preview-box" id="previewBox"><img id="previewImg" alt="Preview"></div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script>
document.getElementById('gambar').addEventListener('change', function () {
    const box = document.getElementById('previewBox');
    const file = this.files[0];
    if (!file) { box.style.display = 'none'; return; }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        box.style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>
</body>
</html>
