<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$d  = $conn->query("SELECT * FROM disposisi WHERE id=$id")->fetch_assoc();
if (!$d) { header('Location: disposisi.php'); exit(); }
$pageTitle = 'Detail Disposisi';

include 'includes/header.php';
?>

<div style="max-width:820px">
    <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
        <a href="disposisi.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        <?php if ($d['status'] === 'menunggu'): ?>
        <a href="proses_disposisi.php?id=<?= $id ?>&action=terima" class="btn btn-success btn-sm"
           onclick="return confirm('Terima dan pindahkan ke Surat Masuk?')">
            <i class="fas fa-check-circle"></i> Terima Surat
        </a>
        <a href="proses_disposisi.php?id=<?= $id ?>&action=tolak" class="btn btn-danger btn-sm"
           onclick="return confirm('Tolak surat ini?')">
            <i class="fas fa-times-circle"></i> Tolak Surat
        </a>
        <?php endif; ?>
    </div>

    <!-- Status Banner -->
    <?php if ($d['status'] === 'menunggu'): ?>
    <div style="background:linear-gradient(135deg,#fffbeb,#fef9ec);border:1.5px solid #fde68a;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
        <i class="fas fa-clock" style="color:#d97706;font-size:20px"></i>
        <div>
            <div style="font-weight:700;color:#92400e;font-size:14px">Menunggu Review</div>
            <div style="font-size:12.5px;color:#b45309">Surat ini belum diproses. Anda dapat menerima atau menolak surat dari lembaga ini.</div>
        </div>
    </div>
    <?php elseif ($d['status'] === 'diterima'): ?>
    <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
        <i class="fas fa-check-circle" style="color:#16a34a;font-size:20px"></i>
        <div>
            <div style="font-weight:700;color:#166534;font-size:14px">Surat Telah Diterima</div>
            <div style="font-size:12.5px;color:#16a34a">Surat ini telah diterima dan masuk ke arsip Surat Masuk resmi.</div>
        </div>
    </div>
    <?php else: ?>
    <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
        <i class="fas fa-times-circle" style="color:#dc2626;font-size:20px"></i>
        <div>
            <div style="font-weight:700;color:#991b1b;font-size:14px">Surat Ditolak</div>
            <div style="font-size:12.5px;color:#dc2626">Surat ini telah ditolak oleh admin.</div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-alt"></i> Detail Surat Disposisi</h3>
            <?= badgeKategori($d['kategori']) ?>
        </div>
        <div class="card-body">
            <!-- Grid detail -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
                <div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:4px">Nomor Surat</div>
                        <div style="font-size:16px;font-weight:800;color:var(--primary)"><?= htmlspecialchars($d['nomor_surat']) ?></div>
                    </div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:4px">Tanggal Surat</div>
                        <div style="font-size:14px"><?= formatTanggal($d['tanggal_surat']) ?></div>
                    </div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:4px">Nama Pengirim</div>
                        <div style="font-size:14px;font-weight:600"><?= htmlspecialchars($d['pengirim']) ?></div>
                    </div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:4px">Instansi / Lembaga</div>
                        <div style="font-size:14px;font-weight:600"><?= htmlspecialchars($d['instansi']) ?></div>
                    </div>
                </div>
                <div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:4px">Email Pengirim</div>
                        <div style="font-size:14px"><a href="mailto:<?= htmlspecialchars($d['email_pengirim']) ?>" style="color:var(--primary)"><?= htmlspecialchars($d['email_pengirim']) ?></a></div>
                    </div>
                    <?php if ($d['no_telp']): ?>
                    <div style="margin-bottom:16px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:4px">No. Telepon / WA</div>
                        <div style="font-size:14px"><?= htmlspecialchars($d['no_telp']) ?></div>
                    </div>
                    <?php endif; ?>
                    <div style="margin-bottom:16px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:4px">Perihal</div>
                        <div style="font-size:14px"><?= htmlspecialchars($d['perihal']) ?></div>
                    </div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:4px">Diterima Sistem</div>
                        <div style="font-size:13px;color:var(--text-muted)"><?= date('d M Y H:i', strtotime($d['created_at'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Isi Surat -->
            <div style="border-top:1px solid var(--border);padding-top:18px;margin-top:4px">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:10px">Isi / Deskripsi Surat</div>
                <div style="background:var(--bg);border-radius:10px;padding:18px;font-size:14px;line-height:1.7;color:var(--text)">
                    <?= nl2br(htmlspecialchars($d['isi_surat'])) ?>
                </div>
            </div>

            <!-- File lampiran -->
            <?php if ($d['file_surat']): ?>
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:16px">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:10px">File Lampiran</div>
                <a href="uploads/surat/<?= $d['file_surat'] ?>" target="_blank" class="btn btn-outline">
                    <i class="fas fa-file-download"></i> Unduh File Surat
                </a>
            </div>
            <?php endif; ?>

            <!-- Tombol aksi -->
            <?php if ($d['status'] === 'menunggu'): ?>
            <div style="border-top:1px solid var(--border);padding-top:20px;margin-top:20px;display:flex;gap:12px;justify-content:flex-end;flex-wrap:wrap">
                <a href="proses_disposisi.php?id=<?= $id ?>&action=tolak"
                   class="btn btn-danger"
                   onclick="return confirm('Yakin menolak surat ini?')">
                    <i class="fas fa-times-circle"></i> Tolak Surat
                </a>
                <a href="proses_disposisi.php?id=<?= $id ?>&action=terima"
                   class="btn btn-success"
                   onclick="return confirm('Terima dan pindahkan surat ini ke Surat Masuk resmi?')">
                    <i class="fas fa-check-circle"></i> Terima & Masukkan ke Surat Masuk
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
