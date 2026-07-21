<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$s  = $conn->query("SELECT * FROM surat_masuk WHERE id=$id")->fetch_assoc();
if (!$s) { header('Location: surat_masuk.php'); exit(); }
$pageTitle = 'Detail Surat Masuk';
include 'includes/header.php';
?>

<div style="max-width:800px">
    <div style="display:flex;gap:10px;margin-bottom:20px">
        <a href="surat_masuk.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        <a href="edit_surat_masuk.php?id=<?= $id ?>" class="btn btn-accent btn-sm"><i class="fas fa-edit"></i> Edit</a>
        <a href="hapus_surat.php?type=masuk&id=<?= $id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i> Hapus</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-envelope-open-text"></i> Detail Surat Masuk</h3>
            <div style="display:flex;gap:8px"><?= badgeKategori($s['kategori']) ?> <?= badgeStatus($s['status']) ?></div>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
                <div>
                    <div style="margin-bottom:18px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:5px">Nomor Surat</div>
                        <div style="font-size:16px;font-weight:700;color:var(--primary)"><?= htmlspecialchars($s['nomor_surat']) ?></div>
                    </div>
                    <div style="margin-bottom:18px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:5px">Pengirim</div>
                        <div style="font-size:14px;font-weight:600"><?= htmlspecialchars($s['pengirim']) ?></div>
                    </div>
                    <div style="margin-bottom:18px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:5px">Tanggal Surat</div>
                        <div style="font-size:14px"><?= formatTanggal($s['tanggal_surat']) ?></div>
                    </div>
                    <div style="margin-bottom:18px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:5px">Tanggal Diterima</div>
                        <div style="font-size:14px"><?= formatTanggal($s['tanggal_terima']) ?></div>
                    </div>
                </div>
                <div>
                    <div style="margin-bottom:18px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:5px">Perihal</div>
                        <div style="font-size:14px"><?= htmlspecialchars($s['perihal']) ?></div>
                    </div>
                    <div style="margin-bottom:18px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:5px">Kategori</div>
                        <div><?= badgeKategori($s['kategori']) ?></div>
                    </div>
                    <div style="margin-bottom:18px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:5px">Status</div>
                        <div><?= badgeStatus($s['status']) ?></div>
                    </div>
                    <div style="margin-bottom:18px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:5px">Diinput Pada</div>
                        <div style="font-size:13px;color:var(--text-muted)"><?= date('d M Y H:i', strtotime($s['created_at'])) ?></div>
                    </div>
                </div>
            </div>

            <?php if ($s['keterangan']): ?>
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:4px">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:8px">Keterangan</div>
                <div style="background:var(--bg);border-radius:8px;padding:14px;font-size:13.5px;line-height:1.6"><?= nl2br(htmlspecialchars($s['keterangan'])) ?></div>
            </div>
            <?php endif; ?>

            <?php if ($s['file_surat']): ?>
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:16px">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:8px">File Surat</div>
                <a href="uploads/surat/<?= $s['file_surat'] ?>" target="_blank" class="btn btn-outline">
                    <i class="fas fa-file-download"></i> Unduh File Surat
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
