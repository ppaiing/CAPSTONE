<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();
$pageTitle = 'Profil Saya';
$errors = [];
$success = false;
$user = $conn->query("SELECT * FROM users WHERE id=" . (int)$_SESSION['user_id'])->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nama = sanitize($_POST['nama'] ?? '');
    if (empty($nama)) { $errors[] = 'Nama tidak boleh kosong.'; }
    if (empty($errors)) {
        $conn->query("UPDATE users SET nama='$nama' WHERE id=" . (int)$_SESSION['user_id']);
        $_SESSION['nama'] = $nama;
        $success = 'Profil berhasil diperbarui!';
        $user['nama'] = $nama;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $cfm = $_POST['confirm_password'] ?? '';
    if (!password_verify($old, $user['password'])) { $errors[] = 'Password lama tidak sesuai.'; }
    if (strlen($new) < 6) { $errors[] = 'Password baru minimal 6 karakter.'; }
    if ($new !== $cfm)   { $errors[] = 'Konfirmasi password tidak cocok.'; }
    if (empty($errors)) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hash' WHERE id=" . (int)$_SESSION['user_id']);
        $success = 'Password berhasil diubah!';
    }
}
include 'includes/header.php';
?>
<div style="max-width:700px">
    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= implode('<br>',$errors) ?></div><?php endif; ?>

    <!-- Info Card -->
    <div class="card" style="margin-bottom:20px">
        <div class="card-header"><h3><i class="fas fa-user-circle"></i> Informasi Akun</h3></div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px">
                <img src="<?= BASE_URL ?>assets/Logo_HMIF FT.png"
                alt="Logo HMIF"
                style="
                    width:70px;
                    height:70px;
                    border-radius:50%;
                    object-fit:cover;
                    background:#fff;
                    padding:3px;
                    border:2px solid #ddd;
                    flex-shrink:0;">
                <div>
                    <div style="font-size:20px;font-weight:700"><?= htmlspecialchars($user['nama']) ?></div>
                    <div style="color:var(--text-muted);font-size:13px">@<?= htmlspecialchars($user['username']) ?></div>
                    <div style="margin-top:6px">
                        <span style="background:#ebf4ff;color:var(--primary);padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600"><?= ucfirst($user['role']) ?></span>
                    </div>
                </div>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled style="background:#f5f5f5">
                </div>
                <button type="submit" name="update_profil" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <!-- Password Card -->
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-lock"></i> Ubah Password</h3></div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="old_password" class="form-control" placeholder="Masukkan password lama">
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru">
                </div>
                <button type="submit" name="update_password" class="btn btn-primary"><i class="fas fa-key"></i> Ubah Password</button>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
