<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();
$pageTitle = 'Dashboard';

// Statistik
$stats = [];
$stats['masuk_total']    = $conn->query("SELECT COUNT(*) as c FROM surat_masuk")->fetch_assoc()['c'];
$stats['masuk_baru']     = $conn->query("SELECT COUNT(*) as c FROM surat_masuk WHERE status='belum_diproses'")->fetch_assoc()['c'];
$stats['masuk_proses']   = $conn->query("SELECT COUNT(*) as c FROM surat_masuk WHERE status='diproses'")->fetch_assoc()['c'];
$stats['masuk_selesai']  = $conn->query("SELECT COUNT(*) as c FROM surat_masuk WHERE status='selesai'")->fetch_assoc()['c'];
$stats['keluar_total']   = $conn->query("SELECT COUNT(*) as c FROM surat_keluar")->fetch_assoc()['c'];
$stats['keluar_draft']   = $conn->query("SELECT COUNT(*) as c FROM surat_keluar WHERE status='draft'")->fetch_assoc()['c'];
$stats['keluar_dikirim'] = $conn->query("SELECT COUNT(*) as c FROM surat_keluar WHERE status='dikirim'")->fetch_assoc()['c'];
$stats['disposisi_pending'] = $conn->query("SELECT COUNT(*) as c FROM disposisi WHERE status='menunggu'")->fetch_assoc()['c'];

// Surat masuk terbaru
$recentMasuk  = $conn->query("SELECT * FROM surat_masuk ORDER BY created_at DESC LIMIT 5");
// Surat keluar terbaru
$recentKeluar = $conn->query("SELECT * FROM surat_keluar ORDER BY created_at DESC LIMIT 5");

// Statistik bulanan (6 bulan terakhir) untuk chart
$chartData = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));
    $masuk  = $conn->query("SELECT COUNT(*) as c FROM surat_masuk WHERE DATE_FORMAT(created_at,'%Y-%m')='$month'")->fetch_assoc()['c'];
    $keluar = $conn->query("SELECT COUNT(*) as c FROM surat_keluar WHERE DATE_FORMAT(created_at,'%Y-%m')='$month'")->fetch_assoc()['c'];
    $chartData[] = ['label' => $label, 'masuk' => $masuk, 'keluar' => $keluar];
}

include 'includes/header.php';
?>

<style>
.stat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 28px; }
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 22px 20px;
    display: flex; align-items: center; gap: 16px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    transition: transform .2s, box-shadow .2s;
    position: relative; overflow: hidden;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.stat-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}
.stat-card.blue::after   { background: linear-gradient(90deg, #0f4c81, #1a6bb5); }
.stat-card.green::after  { background: linear-gradient(90deg, #38a169, #68d391); }
.stat-card.orange::after { background: linear-gradient(90deg, #d69e2e, #f6ad55); }
.stat-card.red::after    { background: linear-gradient(90deg, #e53e3e, #fc8181); }
.stat-icon {
    width: 52px; height: 52px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.stat-icon.blue   { background: #ebf4ff; color: #0f4c81; }
.stat-icon.green  { background: #f0fff4; color: #38a169; }
.stat-icon.orange { background: #fffbeb; color: #d69e2e; }
.stat-icon.red    { background: #fff5f5; color: #e53e3e; }
.stat-info .value { font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; }
.stat-info .label { font-size: 12px; color: var(--text-muted); margin-top: 4px; font-weight: 500; }
.stat-info .sub   { font-size: 11px; color: var(--text-muted); margin-top: 6px; }

.two-col { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }
.chart-bars {
    display: flex; align-items: flex-end; gap: 10px;
    height: 160px; padding: 10px 0;
}
.chart-group { flex: 1; display: flex; gap: 4px; align-items: flex-end; position: relative; }
.chart-bar {
    flex: 1; border-radius: 4px 4px 0 0;
    transition: opacity .2s;
    min-width: 8px;
    cursor: default;
    position: relative;
}
.chart-bar:hover { opacity: .75; }
.chart-bar.masuk  { background: linear-gradient(180deg, #1a6bb5, #0f4c81); }
.chart-bar.keluar { background: linear-gradient(180deg, var(--accent), #f0a500); }
.chart-label {
    text-align: center; font-size: 10px; color: var(--text-muted);
    margin-top: 6px; white-space: nowrap;
}
.pie-container {
    display: flex; flex-direction: column; gap: 10px; padding: 8px 0;
}
.pie-item { display: flex; align-items: center; gap: 10px; font-size: 13px; }
.pie-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.pie-label { flex: 1; color: var(--text-muted); }
.pie-val { font-weight: 700; color: var(--text); }

.quick-actions {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 16px;
}
.qa-btn {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px;
    border-radius: 10px; border: 1.5px solid var(--border);
    text-decoration: none; font-size: 12.5px; font-weight: 600;
    color: var(--text); transition: all .2s;
    background: #fff;
}
.qa-btn:hover { border-color: var(--primary); color: var(--primary); background: #f0f7ff; }
.qa-btn i { font-size: 16px; color: var(--primary); }

@media (max-width: 1200px) { .stat-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 1100px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 800px)  { .stat-grid { grid-template-columns: 1fr 1fr; } .two-col { grid-template-columns: 1fr; } }
@media (max-width: 500px)  { .stat-grid { grid-template-columns: 1fr; } }
</style>

<!-- STAT CARDS -->
<div class="stat-grid">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-envelope-open-text"></i></div>
        <div class="stat-info">
            <div class="value"><?= $stats['masuk_total'] ?></div>
            <div class="label">Total Surat Masuk</div>
            <div class="sub"><span style="color:#e53e3e;font-weight:600"><?= $stats['masuk_baru'] ?> baru</span> • <?= $stats['masuk_proses'] ?> diproses</div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-paper-plane"></i></div>
        <div class="stat-info">
            <div class="value"><?= $stats['keluar_total'] ?></div>
            <div class="label">Total Surat Keluar</div>
            <div class="sub"><span style="color:#d69e2e;font-weight:600"><?= $stats['keluar_draft'] ?> draft</span> • <?= $stats['keluar_dikirim'] ?> dikirim</div>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="value"><?= $stats['masuk_baru'] ?></div>
            <div class="label">Belum Diproses</div>
            <div class="sub">Perlu segera ditangani</div>
        </div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon red"><i class="fas fa-check-double"></i></div>
        <div class="stat-info">
            <div class="value"><?= $stats['masuk_selesai'] ?></div>
            <div class="label">Surat Selesai</div>
            <div class="sub">Total selesai diproses</div>
        </div>
    </div>
    <div class="stat-card" style="--c:#d97706">
        <div class="stat-icon" style="background:#fffbeb;color:#d97706"><i class="fas fa-inbox"></i></div>
        <div class="stat-info" style="border-top:3px solid #d97706">
            <div class="value"><?= $stats['disposisi_pending'] ?></div>
            <div class="label" style="color:var(--text-muted)">Disposisi Pending</div>
            <div class="sub"><a href="disposisi.php" style="color:#d97706;font-weight:600;font-size:11px;text-decoration:none">Lihat &amp; Review →</a></div>
        </div>
    </div>
</div>

<!-- CHART + QUICK ACTIONS -->
<div class="two-col">
    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar"></i> Statistik Surat (6 Bulan Terakhir)</h3>
            <div style="display:flex;gap:14px;font-size:12px">
                <span><i style="color:#0f4c81" class="fas fa-square"></i> Masuk</span>
                <span><i style="color:#e8b84b" class="fas fa-square"></i> Keluar</span>
            </div>
        </div>
        <div class="card-body">
            <div style="display:flex;gap:0;align-items:flex-end">
                <div style="display:flex;flex-direction:column;justify-content:space-between;height:160px;padding:0;margin-right:8px">
                    <?php
                    $allVals = array_merge(array_column($chartData, 'masuk'), array_column($chartData, 'keluar'));
                    $maxVal  = max(max($allVals), 1);
                    for ($y = $maxVal; $y >= 0; $y -= max(1, ceil($maxVal/4))) {
                        echo "<span style='font-size:10px;color:#aaa'>$y</span>";
                        if ($y === 0) break;
                    }
                    ?>
                </div>
                <div style="flex:1">
                    <div class="chart-bars">
                        <?php foreach ($chartData as $d): ?>
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center">
                            <div class="chart-group" style="height:160px">
                                <div class="chart-bar masuk"
                                     style="height:<?= $maxVal > 0 ? round(($d['masuk']/$maxVal)*140) : 0 ?>px"
                                     title="Masuk: <?= $d['masuk'] ?>"></div>
                                <div class="chart-bar keluar"
                                     style="height:<?= $maxVal > 0 ? round(($d['keluar']/$maxVal)*140) : 0 ?>px"
                                     title="Keluar: <?= $d['keluar'] ?>"></div>
                            </div>
                            <div class="chart-label"><?= $d['label'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar card -->
    <div style="display:flex;flex-direction:column;gap:18px">
        <!-- Status Masuk -->
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-chart-pie"></i> Status Surat Masuk</h3></div>
            <div class="card-body">
                <div class="pie-container">
                    <div class="pie-item">
                        <div class="pie-dot" style="background:#e53e3e"></div>
                        <div class="pie-label">Belum Diproses</div>
                        <div class="pie-val"><?= $stats['masuk_baru'] ?></div>
                    </div>
                    <div class="pie-item">
                        <div class="pie-dot" style="background:#1677ff"></div>
                        <div class="pie-label">Diproses</div>
                        <div class="pie-val"><?= $stats['masuk_proses'] ?></div>
                    </div>
                    <div class="pie-item">
                        <div class="pie-dot" style="background:#38a169"></div>
                        <div class="pie-label">Selesai</div>
                        <div class="pie-val"><?= $stats['masuk_selesai'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-bolt"></i> Aksi Cepat</h3></div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="input_surat_masuk.php" class="qa-btn"><i class="fas fa-file-import"></i> Input Masuk</a>
                    <a href="input_surat_keluar.php" class="qa-btn"><i class="fas fa-file-export"></i> Input Keluar</a>
                    <a href="surat_masuk.php" class="qa-btn"><i class="fas fa-inbox"></i> Lihat Masuk</a>
                    <a href="surat_keluar.php" class="qa-btn"><i class="fas fa-paper-plane"></i> Lihat Keluar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tables -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <!-- Surat Masuk Terbaru -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-envelope-open-text"></i> Surat Masuk Terbaru</h3>
            <a href="surat_masuk.php" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>No. Surat</th><th>Pengirim</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php if ($recentMasuk->num_rows > 0): while ($s = $recentMasuk->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:12px"><?= htmlspecialchars($s['nomor_surat']) ?></div>
                            <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(substr($s['perihal'],0,30)) ?>...</div>
                        </td>
                        <td style="font-size:12px"><?= htmlspecialchars(substr($s['pengirim'],0,20)) ?></td>
                        <td><?= badgeStatus($s['status']) ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:20px">Belum ada surat masuk</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Surat Keluar Terbaru -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-paper-plane"></i> Surat Keluar Terbaru</h3>
            <a href="surat_keluar.php" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>No. Surat</th><th>Tujuan</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php if ($recentKeluar->num_rows > 0): while ($s = $recentKeluar->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:12px"><?= htmlspecialchars($s['nomor_surat']) ?></div>
                            <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars(substr($s['perihal'],0,30)) ?>...</div>
                        </td>
                        <td style="font-size:12px"><?= htmlspecialchars(substr($s['tujuan'],0,20)) ?></td>
                        <td><?= badgeStatus($s['status']) ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:20px">Belum ada surat keluar</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
