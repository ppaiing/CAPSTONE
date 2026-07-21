<?php
require_once 'includes/config.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_surat    = sanitize($_POST['nomor_surat'] ?? '');
    $tanggal_surat  = sanitize($_POST['tanggal_surat'] ?? '');
    $pengirim       = sanitize($_POST['pengirim'] ?? '');
    $instansi       = sanitize($_POST['instansi'] ?? '');
    $email_pengirim = sanitize($_POST['email_pengirim'] ?? '');
    $no_telp        = sanitize($_POST['no_telp'] ?? '');
    $perihal        = sanitize($_POST['perihal'] ?? '');
    $kategori       = sanitize($_POST['kategori'] ?? '');
    $isi_surat      = sanitize($_POST['isi_surat'] ?? '');

    if (empty($nomor_surat))   $errors[] = 'Nomor surat wajib diisi.';
    if (empty($tanggal_surat)) $errors[] = 'Tanggal surat wajib diisi.';
    if (empty($pengirim))      $errors[] = 'Nama pengirim / penanggung jawab wajib diisi.';
    if (empty($instansi))      $errors[] = 'Nama instansi / lembaga wajib diisi.';
    if (empty($email_pengirim) || !filter_var($email_pengirim, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Email pengirim tidak valid.';
    if (empty($perihal))       $errors[] = 'Perihal surat wajib diisi.';
    if (empty($kategori))      $errors[] = 'Kategori surat wajib dipilih.';
    if (empty($isi_surat))     $errors[] = 'Isi / deskripsi surat wajib diisi.';

    // File upload (opsional)
    $file_surat = null;
    if (!empty($_FILES['file_surat']['name'])) {
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $ext     = strtolower(pathinfo($_FILES['file_surat']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format file tidak valid. Gunakan: PDF, DOC, DOCX, JPG, atau PNG.';
        } elseif ($_FILES['file_surat']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Ukuran file maksimal 5 MB.';
        } else {
            $filename = 'DISP_' . date('YmdHis') . '_' . uniqid() . '.' . $ext;
            $uploadDir = 'uploads/surat/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
            if (move_uploaded_file($_FILES['file_surat']['tmp_name'], $uploadDir . $filename)) {
                $file_surat = $filename;
            } else {
                $errors[] = 'Gagal mengupload file. Coba lagi.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO disposisi (nomor_surat, tanggal_surat, pengirim, instansi, email_pengirim, no_telp, perihal, kategori, isi_surat, file_surat) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('ssssssssss',
            $nomor_surat, $tanggal_surat, $pengirim, $instansi,
            $email_pengirim, $no_telp, $perihal, $kategori, $isi_surat, $file_surat
        );
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = 'Gagal menyimpan data. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Surat ke HMIF-FT UNISMUH Makassar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0f4c81;
            --primary-light: #1a6bb5;
            --primary-dark: #08305a;
            --accent: #e8b84b;
            --danger: #dc2626;
            --success: #16a34a;
            --bg: #f0f4f8;
            --border: #e2e8f0;
            --text: #1a202c;
            --text-muted: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

        /* NAVBAR */
        .navbar {
            background: var(--primary-dark);
            padding: 0 5%; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,.2);
        }
        .nav-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .nav-brand img { width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid var(--accent); }
        .nav-brand-text h1 { font-family:'Sora',sans-serif; font-size:13px; font-weight:800; color:#fff; line-height:1.1; }
        .nav-brand-text p { font-size:10px; color:rgba(255,255,255,.5); }
        .nav-links { display:flex; align-items:center; gap:4px; }
        .nav-link { padding:7px 14px; color:rgba(255,255,255,.75); text-decoration:none; font-size:13px; font-weight:500; border-radius:7px; transition:all .2s; }
        .nav-link:hover { background:rgba(255,255,255,.1); color:#fff; }
        .nav-link.active { background:rgba(255,255,255,.12); color:var(--accent); font-weight:600; }
        .btn-login-nav { background:linear-gradient(135deg,var(--accent),#f0a500); color:var(--primary-dark)!important; font-weight:700!important; border-radius:8px; }
        .btn-login-nav:hover { opacity:.9; }
        .nav-toggle { display:none; background:none; border:none; color:#fff; font-size:20px; cursor:pointer; }

        /* HERO STRIP */
        .page-hero {
            background: linear-gradient(135deg, #08305a, #0f4c81, #1a6bb5);
            padding: 48px 5% 40px;
            text-align: center;
            position: relative; overflow: hidden;
        }
        .page-hero::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at 50% 0%, rgba(232,184,75,.1) 0%, transparent 60%);
        }
        .page-hero-content { position: relative; z-index: 1; }
        .page-hero img { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent); margin-bottom: 14px; box-shadow: 0 4px 20px rgba(0,0,0,.3); }
        .page-hero h1 { font-family:'Sora',sans-serif; font-size: clamp(20px,3vw,32px); font-weight:800; color:#fff; margin-bottom:8px; }
        .page-hero p { font-size:14px; color:rgba(255,255,255,.65); max-width:600px; margin:0 auto; line-height:1.7; }
        .breadcrumb { display:flex; align-items:center; gap:8px; justify-content:center; margin-bottom:16px; }
        .breadcrumb a { color:rgba(255,255,255,.5); text-decoration:none; font-size:12px; }
        .breadcrumb a:hover { color:var(--accent); }
        .breadcrumb span { color:rgba(255,255,255,.25); font-size:12px; }
        .breadcrumb .current { color:var(--accent); font-size:12px; font-weight:600; }

        /* MAIN CONTAINER */
        .main-wrap {
            max-width: 860px;
            margin: 0 auto;
            padding: 36px 20px 60px;
        }

        /* NOTICE BOX */
        .notice-box {
            background: linear-gradient(135deg, #eff6ff, #fff);
            border: 1.5px solid #bfdbfe;
            border-left: 4px solid var(--primary);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 28px;
            display: flex; gap: 14px; align-items: flex-start;
        }
        .notice-box i { color: var(--primary); font-size: 18px; margin-top: 1px; flex-shrink: 0; }
        .notice-box .notice-text h4 { font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 4px; }
        .notice-box .notice-text p { font-size: 13px; color: #374151; line-height: 1.6; }

        /* CARD */
        .card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
        }
        .card-header {
            padding: 20px 26px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }
        .card-header .ch-icon {
            width: 40px; height: 40px;
            background: #ebf4ff; color: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .card-header h3 { font-family:'Sora',sans-serif; font-size:16px; font-weight:700; color: var(--text); }
        .card-header p { font-size:12px; color:var(--text-muted); margin-top:1px; }
        .card-body { padding: 26px; }

        /* FORM */
        .form-section-label {
            font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .1em;
            color: var(--primary);
            margin: 20px 0 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .form-section-label::after {
            content: ''; flex: 1;
            height: 1px; background: var(--border);
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0 20px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display:block; font-size:12.5px; font-weight:700; color:var(--text); margin-bottom:6px; }
        .form-label .req { color:var(--danger); margin-left:2px; }
        .form-label .opt { font-size:10.5px; color:var(--text-muted); font-weight:400; margin-left:4px; }
        .form-control {
            width:100%; padding:10px 14px;
            border:1.5px solid var(--border); border-radius:9px;
            font-family:inherit; font-size:13px; color:var(--text);
            background:#fff; outline:none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus { border-color:var(--primary-light); box-shadow:0 0 0 3px rgba(26,107,181,.1); }
        textarea.form-control { resize:vertical; min-height:100px; }
        select.form-control { cursor:pointer; }

        /* DROP ZONE */
        .drop-zone {
            border: 2px dashed var(--border);
            border-radius: 10px;
            padding: 28px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            background: #fafcff;
        }
        .drop-zone:hover, .drop-zone.dragover { border-color: var(--primary); background: #eff6ff; }
        .drop-zone i { font-size: 28px; color: var(--primary-light); display:block; margin-bottom:8px; }
        .drop-zone p { font-size:13px; color:var(--text-muted); margin-bottom:4px; }
        .drop-zone small { font-size:11px; color:#aaa; }
        #fileNameDisplay { font-size:12px; color:var(--primary); margin-top:8px; font-weight:600; }

        /* SUBMIT BTN */
        .submit-row {
            display:flex; justify-content:space-between; align-items:center;
            flex-wrap:wrap; gap:12px;
            padding-top:20px; border-top:1px solid var(--border);
        }
        .btn-submit {
            padding:13px 32px;
            background:linear-gradient(135deg,var(--primary),var(--primary-light));
            color:#fff; border:none; border-radius:10px;
            font-family:inherit; font-size:15px; font-weight:700;
            cursor:pointer; display:flex; align-items:center; gap:9px;
            transition:all .2s;
            box-shadow:0 4px 14px rgba(15,76,129,.3);
        }
        .btn-submit:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(15,76,129,.4); }
        .btn-back {
            padding:12px 20px;
            background:transparent; border:1.5px solid var(--border);
            color:var(--text-muted); border-radius:10px;
            text-decoration:none; font-size:14px; font-weight:500;
            display:flex; align-items:center; gap:8px; transition:all .2s;
        }
        .btn-back:hover { border-color:var(--primary); color:var(--primary); }

        /* ALERTS */
        .alert { padding:14px 18px; border-radius:10px; font-size:13.5px; margin-bottom:20px; display:flex; gap:10px; align-items:flex-start; }
        .alert-danger  { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
        .alert-danger i, .alert-success i { margin-top:1px; flex-shrink:0; }
        .alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }

        /* SUCCESS STATE */
        .success-wrap {
            text-align:center; padding:48px 32px;
        }
        .success-icon {
            width:80px; height:80px;
            background:linear-gradient(135deg,#16a34a,#22c55e);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:32px; color:#fff;
            margin:0 auto 20px;
            box-shadow:0 8px 24px rgba(22,163,74,.3);
            animation:popIn .4s ease;
        }
        @keyframes popIn { from{transform:scale(.5);opacity:0} to{transform:scale(1);opacity:1} }
        .success-wrap h2 { font-family:'Sora',sans-serif; font-size:24px; font-weight:800; color:var(--text); margin-bottom:10px; }
        .success-wrap p { font-size:14px; color:var(--text-muted); line-height:1.7; max-width:460px; margin:0 auto 28px; }
        .success-detail {
            background:var(--bg); border-radius:12px;
            padding:18px 22px; max-width:420px; margin:0 auto 28px;
            text-align:left;
        }
        .success-detail .sd-row { display:flex; gap:12px; font-size:13px; padding:6px 0; border-bottom:1px solid var(--border); }
        .success-detail .sd-row:last-child { border-bottom:none; }
        .success-detail .sd-label { color:var(--text-muted); width:120px; flex-shrink:0; }
        .success-detail .sd-val { font-weight:600; color:var(--text); }
        .btn-back-home {
            display:inline-flex; align-items:center; gap:8px;
            padding:12px 24px;
            background:var(--primary); color:#fff;
            border-radius:10px; text-decoration:none;
            font-weight:600; font-size:14px; transition:all .2s;
        }
        .btn-back-home:hover { background:var(--primary-light); }

        /* FOOTER */
        .site-footer {
            background:var(--primary-dark);
            padding:20px 5%; text-align:center;
            font-size:12px; color:rgba(255,255,255,.4);
        }

        @media(max-width:700px) {
            .form-row, .form-row-3 { grid-template-columns:1fr; }
            .nav-links { display:none; position:fixed; top:64px; left:0; right:0; background:var(--primary-dark); padding:12px; flex-direction:column; }
            .nav-links.open { display:flex; }
            .nav-toggle { display:block; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="home.php" class="nav-brand">
        <img src="assets/Logo_HMIF FT.png" alt="Logo HMIF-FT UNISMUH">
        <div class="nav-brand-text">
            <h1>HMIF-FT UNISMUH</h1>
            <p>Fakultas Teknik Universitas Muhammadiyah Makassar</p>
        </div>
    </a>
    <div class="nav-links" id="navLinks">
        <a href="home.php" class="nav-link">Beranda</a>
        <a href="home.php#tentang" class="nav-link">Tentang</a>
        <a href="home.php#cara-kirim" class="nav-link">Panduan</a>
        <a href="kirim_surat.php" class="nav-link active">Kirim Surat</a>
        <a href="login.php" class="nav-link btn-login-nav"><i class="fas fa-sign-in-alt"></i> Login Admin</a>
    </div>
    <button class="nav-toggle" onclick="document.getElementById('navLinks').classList.toggle('open')">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<!-- PAGE HERO -->
<div class="page-hero">
    <div class="page-hero-content">
        <div class="breadcrumb">
            <a href="home.php"><i class="fas fa-home"></i> Beranda</a>
            <span>/</span>
            <span class="current">Kirim Surat</span>
        </div>
        <img src="assets/Logo_HMIF FT.png" alt="Logo">
        <h1>Kirim Surat ke HMIF-FT UNISMUH</h1>
        <p>Formulir pengiriman surat resmi dari lembaga / instansi lain kepada
        HMIF-FT UNISMUH Makassar</p>
    </div>
</div>

<div class="main-wrap">

<?php if ($success): ?>
    <!-- SUCCESS STATE -->
    <div class="card">
        <div class="success-wrap">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h2>Surat Berhasil Dikirim!</h2>
            <p>
                Surat Anda telah berhasil diterima dan sedang menunggu proses review
                oleh Admin / Sekretaris Umum HMIF-FT UNISMUH. Kami akan menghubungi Anda
                melalui email yang telah didaftarkan.
            </p>
            <div class="success-detail">
                <div class="sd-row">
                    <span class="sd-label">Pengirim</span>
                    <span class="sd-val"><?= htmlspecialchars($_POST['pengirim']) ?></span>
                </div>
                <div class="sd-row">
                    <span class="sd-label">Instansi</span>
                    <span class="sd-val"><?= htmlspecialchars($_POST['instansi']) ?></span>
                </div>
                <div class="sd-row">
                    <span class="sd-label">Perihal</span>
                    <span class="sd-val"><?= htmlspecialchars($_POST['perihal']) ?></span>
                </div>
                <div class="sd-row">
                    <span class="sd-label">Status</span>
                    <span class="sd-val" style="color:var(--success)">
                        <i class="fas fa-clock"></i> Menunggu Review
                    </span>
                </div>
            </div>
            <a href="home.php" class="btn-back-home"><i class="fas fa-home"></i> Kembali ke Beranda</a>
        </div>
    </div>

<?php else: ?>
    <!-- NOTICE -->
    <div class="notice-box">
        <i class="fas fa-info-circle"></i>
        <div class="notice-text">
            <h4>Informasi Penting</h4>
            <p>
                Formulir ini diperuntukkan bagi <strong>lembaga atau instansi luar</strong> yang ingin
                mengirim surat kepada HMIF-FT UNISMUH Makassar. Surat yang masuk akan
                melalui proses <strong>review disposisi</strong> oleh Admin atau Sekretaris Umum sebelum
                dinyatakan resmi diterima. Pastikan data yang diisi lengkap dan benar.
            </p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong>Terdapat kesalahan, mohon periksa kembali:</strong>
            <ul style="margin-top:6px;padding-left:18px">
                <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="ch-icon"><i class="fas fa-file-signature"></i></div>
            <div>
                <h3>Formulir Pengiriman Surat</h3>
                <p>Isi semua kolom yang bertanda <span style="color:var(--danger)">*</span> wajib</p>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" id="suratForm">

                <!-- IDENTITAS PENGIRIM -->
                <div class="form-section-label"><i class="fas fa-building"></i> Identitas Pengirim</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Pengirim / Penanggung Jawab <span class="req">*</span></label>
                        <input type="text" name="pengirim" class="form-control"
                               placeholder="Nama lengkap pengirim atau PJ surat"
                               value="<?= htmlspecialchars($_POST['pengirim'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lembaga / Instansi <span class="req">*</span></label>
                        <input type="text" name="instansi" class="form-control"
                               placeholder="Nama resmi lembaga Anda"
                               value="<?= htmlspecialchars($_POST['instansi'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email Pengirim <span class="req">*</span></label>
                        <input type="email" name="email_pengirim" class="form-control"
                               placeholder="email@lembaga.ac.id"
                               value="<?= htmlspecialchars($_POST['email_pengirim'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon / WhatsApp <span class="opt">(opsional)</span></label>
                        <input type="text" name="no_telp" class="form-control"
                               placeholder="08xxxxxxxxxx"
                               value="<?= htmlspecialchars($_POST['no_telp'] ?? '') ?>">
                    </div>
                </div>

                <!-- DETAIL SURAT -->
                <div class="form-section-label"><i class="fas fa-envelope-open-text"></i> Detail Surat</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nomor Surat <span class="req">*</span></label>
                        <input type="text" name="nomor_surat" class="form-control"
                               placeholder="Contoh: 007/BEM-UM/VI/2026"
                               value="<?= htmlspecialchars($_POST['nomor_surat'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Surat <span class="req">*</span></label>
                        <input type="date" name="tanggal_surat" class="form-control"
                               value="<?= htmlspecialchars($_POST['tanggal_surat'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Perihal / Pokok Surat <span class="req">*</span></label>
                    <input type="text" name="perihal" class="form-control"
                           placeholder="Isi perihal atau pokok surat secara ringkas dan jelas"
                           value="<?= htmlspecialchars($_POST['perihal'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Surat <span class="req">*</span></label>
                    <select name="kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="undangan"      <?= ($_POST['kategori']??'')==='undangan'?'selected':'' ?>>📋 Undangan</option>
                        <option value="permohonan"    <?= ($_POST['kategori']??'')==='permohonan'?'selected':'' ?>>📝 Permohonan</option>
                        <option value="pemberitahuan" <?= ($_POST['kategori']??'')==='pemberitahuan'?'selected':'' ?>>📢 Pemberitahuan</option>
                        <option value="keputusan"     <?= ($_POST['kategori']??'')==='keputusan'?'selected':'' ?>>⚖️ Keputusan</option>
                        <option value="lainnya"       <?= ($_POST['kategori']??'')==='lainnya'?'selected':'' ?>>📌 Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Isi / Deskripsi Surat <span class="req">*</span></label>
                    <textarea name="isi_surat" class="form-control" rows="5"
                              placeholder="Tuliskan isi ringkas atau maksud dan tujuan surat ini..."
                              required><?= htmlspecialchars($_POST['isi_surat'] ?? '') ?></textarea>
                </div>

                <!-- LAMPIRAN -->
                <div class="form-section-label"><i class="fas fa-paperclip"></i> Lampiran File</div>

                <div class="form-group">
                    <label class="form-label">Upload File Surat <span class="opt">(opsional, maks. 5 MB)</span></label>
                    <div class="drop-zone" id="dropZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Seret file ke sini atau <strong>klik untuk memilih</strong></p>
                        <small>Format: PDF, DOC, DOCX, JPG, PNG — Maks. 5 MB</small>
                        <div id="fileNameDisplay"></div>
                        <input type="file" name="file_surat" id="fileInput"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               style="display:none">
                    </div>
                </div>

                <!-- SUBMIT -->
                <div class="submit-row">
                    <a href="home.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Kirim Surat ke HMIF-FT UNISMUH
                    </button>
                </div>

            </form>
        </div>
    </div>
<?php endif; ?>

</div><!-- end .main-wrap -->

<div class="site-footer">
    © <?= date('Y') ?> <strong style="color:var(--accent)">HMIF-FT UNISMUH</strong> — Fakultas Teknik Universitas Muhammadiyah Makassar
</div>

<script>
// Drop Zone
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileDisplay = document.getElementById('fileNameDisplay');

if (dropZone) {
    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', updateFileName);
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        updateFileName();
    });
}
function updateFileName() {
    if (fileInput.files[0]) {
        fileDisplay.innerHTML = '<i class="fas fa-file-check" style="margin-right:5px"></i>' + fileInput.files[0].name;
        dropZone.style.borderColor = 'var(--primary)';
        dropZone.style.background  = '#eff6ff';
    }
}
// Submit loading state
document.getElementById('suratForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    btn.disabled = true;
});
// Mobile nav
document.addEventListener('click', e => {
    const nav = document.getElementById('navLinks');
    const tog = document.querySelector('.nav-toggle');
    if (nav && nav.classList.contains('open') && !nav.contains(e.target) && !tog?.contains(e.target))
        nav.classList.remove('open');
});
</script>
</body>
</html>
