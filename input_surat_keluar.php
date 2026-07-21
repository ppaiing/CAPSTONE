<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();
$pageTitle = 'Input Surat Keluar';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_surat   = sanitize($_POST['nomor_surat'] ?? '');
    $tanggal_surat = sanitize($_POST['tanggal_surat'] ?? '');
    $tujuan        = sanitize($_POST['tujuan'] ?? '');
    $perihal       = sanitize($_POST['perihal'] ?? '');
    $kategori      = sanitize($_POST['kategori'] ?? '');
    $status        = sanitize($_POST['status'] ?? 'draft');
    $keterangan    = sanitize($_POST['keterangan'] ?? '');
    $user_id       = $_SESSION['user_id'];

    if (empty($nomor_surat))   $errors[] = 'Nomor surat wajib diisi.';
    if (empty($tanggal_surat)) $errors[] = 'Tanggal surat wajib diisi.';
    if (empty($tujuan))        $errors[] = 'Tujuan wajib diisi.';
    if (empty($perihal))       $errors[] = 'Perihal wajib diisi.';
    if (empty($kategori))      $errors[] = 'Kategori wajib dipilih.';

    $file_surat = null;
    if (!empty($_FILES['file_surat']['name'])) {
        $allowed = ['pdf','doc','docx','jpg','jpeg','png'];
        $ext     = strtolower(pathinfo($_FILES['file_surat']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format file tidak valid.';
        } elseif ($_FILES['file_surat']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Ukuran file maksimal 5 MB.';
        } else {
            $filename   = 'SK_' . date('YmdHis') . '_' . uniqid() . '.' . $ext;
            $uploadPath = 'uploads/surat/' . $filename;
            if (move_uploaded_file($_FILES['file_surat']['tmp_name'], $uploadPath)) {
                $file_surat = $filename;
            } else {
                $errors[] = 'Gagal mengupload file.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO surat_keluar (nomor_surat, tanggal_surat, tujuan, perihal, kategori, status, keterangan, file_surat, user_id) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('ssssssssi', $nomor_surat, $tanggal_surat, $tujuan, $perihal, $kategori, $status, $keterangan, $file_surat, $user_id);
        if ($stmt->execute()) {
            header('Location: surat_keluar.php?msg=added');
            exit();
        } else {
            $errors[] = 'Gagal menyimpan data.';
        }
    }
}

include 'includes/header.php';
?>

<div style="max-width:800px">
    <div style="margin-bottom:20px">
        <a href="surat_keluar.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-export"></i> Form Input Surat Keluar</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <div><i class="fas fa-exclamation-triangle"></i> <strong>Terdapat kesalahan:</strong></div>
                <ul style="margin-top:8px;padding-left:18px">
                    <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nomor Surat <span>*</span></label>
                        <input type="text" name="nomor_surat" class="form-control"
                               placeholder="Contoh: 001/HMIF-FT/V/2026"
                               value="<?= htmlspecialchars($_POST['nomor_surat'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Surat <span>*</span></label>
                        <input type="date" name="tanggal_surat" class="form-control"
                               value="<?= htmlspecialchars($_POST['tanggal_surat'] ?? date('Y-m-d')) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tujuan / Penerima <span>*</span></label>
                    <input type="text" name="tujuan" class="form-control"
                           placeholder="Nama instansi / penerima surat"
                           value="<?= htmlspecialchars($_POST['tujuan'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Perihal / Pokok Surat <span>*</span></label>
                    <input type="text" name="perihal" class="form-control"
                           placeholder="Isi perihal atau pokok surat"
                           value="<?= htmlspecialchars($_POST['perihal'] ?? '') ?>" required>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Kategori <span>*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="undangan"      <?= ($_POST['kategori']??'')==='undangan'?'selected':'' ?>>Undangan</option>
                            <option value="permohonan"    <?= ($_POST['kategori']??'')==='permohonan'?'selected':'' ?>>Permohonan</option>
                            <option value="pemberitahuan" <?= ($_POST['kategori']??'')==='pemberitahuan'?'selected':'' ?>>Pemberitahuan</option>
                            <option value="keputusan"     <?= ($_POST['kategori']??'')==='keputusan'?'selected':'' ?>>Keputusan</option>
                            <option value="lainnya"       <?= ($_POST['kategori']??'')==='lainnya'?'selected':'' ?>>Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="draft"   <?= ($_POST['status']??'draft')==='draft'?'selected':'' ?>>Draft</option>
                            <option value="dikirim" <?= ($_POST['status']??'')==='dikirim'?'selected':'' ?>>Dikirim</option>
                            <option value="selesai" <?= ($_POST['status']??'')==='selesai'?'selected':'' ?>>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan / Catatan</label>
                    <textarea name="keterangan" class="form-control" placeholder="Tambahkan catatan (opsional)"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Upload File Surat</label>
                    <div style="border:2px dashed var(--border);border-radius:10px;padding:24px;text-align:center;cursor:pointer;transition:border-color .2s" id="dropZone">
                        <i class="fas fa-cloud-upload-alt" style="font-size:28px;color:var(--primary-light);margin-bottom:8px;display:block"></i>
                        <p style="font-size:13px;color:var(--text-muted);margin-bottom:8px">Seret file ke sini atau klik untuk memilih</p>
                        <p style="font-size:11px;color:#aaa">PDF, DOC, DOCX, JPG, PNG — Maks. 5 MB</p>
                        <input type="file" name="file_surat" id="fileInput" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none">
                        <span id="fileName" style="font-size:12px;color:var(--primary);margin-top:8px;display:block"></span>
                    </div>
                </div>

                <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--border)">
                    <a href="surat_keluar.php" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Surat Keluar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileName  = document.getElementById('fileName');
dropZone.addEventListener('click', () => fileInput.click());
fileInput.addEventListener('change', () => {
    fileName.textContent = fileInput.files[0] ? '✓ ' + fileInput.files[0].name : '';
    dropZone.style.borderColor = fileInput.files[0] ? 'var(--primary)' : 'var(--border)';
});
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.borderColor = 'var(--primary)'; });
dropZone.addEventListener('dragleave', () => dropZone.style.borderColor = 'var(--border)');
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    fileInput.files = e.dataTransfer.files;
    fileName.textContent = fileInput.files[0] ? '✓ ' + fileInput.files[0].name : '';
});
</script>

<?php include 'includes/footer.php'; ?>
