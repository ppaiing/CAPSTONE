<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();
$id = (int)($_GET['id'] ?? 0);
$s  = $conn->query("SELECT * FROM surat_keluar WHERE id=$id")->fetch_assoc();
if (!$s) { header('Location: surat_keluar.php'); exit(); }
$pageTitle = 'Edit Surat Keluar';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_surat   = sanitize($_POST['nomor_surat'] ?? '');
    $tanggal_surat = sanitize($_POST['tanggal_surat'] ?? '');
    $tujuan        = sanitize($_POST['tujuan'] ?? '');
    $perihal       = sanitize($_POST['perihal'] ?? '');
    $kategori      = sanitize($_POST['kategori'] ?? '');
    $status        = sanitize($_POST['status'] ?? '');
    $keterangan    = sanitize($_POST['keterangan'] ?? '');
    if (empty($nomor_surat)||empty($tanggal_surat)||empty($tujuan)||empty($perihal)||empty($kategori))
        $errors[] = 'Semua field wajib diisi.';
    $file_surat = $s['file_surat'];
    if (!empty($_FILES['file_surat']['name'])) {
        $ext = strtolower(pathinfo($_FILES['file_surat']['name'], PATHINFO_EXTENSION));
        $filename = 'SK_' . date('YmdHis') . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['file_surat']['tmp_name'], 'uploads/surat/' . $filename))
            $file_surat = $filename;
    }
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE surat_keluar SET nomor_surat=?,tanggal_surat=?,tujuan=?,perihal=?,kategori=?,status=?,keterangan=?,file_surat=? WHERE id=?");
        $stmt->bind_param('ssssssssi', $nomor_surat,$tanggal_surat,$tujuan,$perihal,$kategori,$status,$keterangan,$file_surat,$id);
        $stmt->execute();
        header('Location: surat_keluar.php?msg=updated');
        exit();
    }
    $s = array_merge($s, $_POST);
}
include 'includes/header.php';
?>
<div style="max-width:800px">
    <a href="surat_keluar.php" class="btn btn-outline btn-sm" style="margin-bottom:20px"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-edit"></i> Edit Surat Keluar</h3></div>
        <div class="card-body">
            <?php if (!empty($errors)): ?><div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= implode('<br>', $errors) ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nomor Surat <span>*</span></label>
                        <input type="text" name="nomor_surat" class="form-control" value="<?= htmlspecialchars($s['nomor_surat']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Surat <span>*</span></label>
                        <input type="date" name="tanggal_surat" class="form-control" value="<?= $s['tanggal_surat'] ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tujuan <span>*</span></label>
                    <input type="text" name="tujuan" class="form-control" value="<?= htmlspecialchars($s['tujuan']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Perihal <span>*</span></label>
                    <input type="text" name="perihal" class="form-control" value="<?= htmlspecialchars($s['perihal']) ?>" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-control">
                            <?php foreach (['undangan','permohonan','pemberitahuan','keputusan','lainnya'] as $k): ?>
                            <option value="<?= $k ?>" <?= $s['kategori']===$k?'selected':'' ?>><?= ucfirst($k) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <?php foreach (['draft'=>'Draft','dikirim'=>'Dikirim','selesai'=>'Selesai'] as $v=>$l): ?>
                            <option value="<?= $v ?>" <?= $s['status']===$v?'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control"><?= htmlspecialchars($s['keterangan'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Ganti File Surat (opsional)</label>
                    <?php if ($s['file_surat']): ?>
                    <div style="font-size:12px;color:var(--primary);margin-bottom:8px"><i class="fas fa-file"></i> <?= $s['file_surat'] ?></div>
                    <?php endif; ?>
                    <input type="file" name="file_surat" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </div>
                <div style="display:flex;gap:12px;justify-content:flex-end;border-top:1px solid var(--border);padding-top:16px">
                    <a href="surat_keluar.php" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
