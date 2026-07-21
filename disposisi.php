<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();
$pageTitle = 'Disposisi Surat';

$msg = $_GET['msg'] ?? '';

// Hitung badge
$pending = $conn->query("SELECT COUNT(*) as c FROM disposisi WHERE status='menunggu'")->fetch_assoc()['c'];

// Filter
$search = sanitize($_GET['search'] ?? '');
$filterStatus = sanitize($_GET['status'] ?? '');

$where = "WHERE 1=1";
if ($search)       $where .= " AND (nomor_surat LIKE '%$search%' OR pengirim LIKE '%$search%' OR instansi LIKE '%$search%' OR perihal LIKE '%$search%')";
if ($filterStatus) $where .= " AND status='$filterStatus'";

// Pagination
$perPage   = 10;
$page      = max(1, (int)($_GET['page'] ?? 1));
$offset    = ($page - 1) * $perPage;
$totalRow  = $conn->query("SELECT COUNT(*) as c FROM disposisi $where")->fetch_assoc()['c'];
$totalPage = ceil($totalRow / $perPage);
$list      = $conn->query("SELECT * FROM disposisi $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");

include 'includes/header.php';
?>

<style>
.filter-bar { display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:20px; }
.search-wrap { position:relative;flex:1;min-width:200px; }
.search-wrap i { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa; }
.search-input { padding:9px 12px 9px 36px;border:1.5px solid var(--border);border-radius:8px;font-family:inherit;font-size:13px;outline:none;width:100%; }
.search-input:focus { border-color:var(--primary-light); }
.filter-select { padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-family:inherit;font-size:13px;outline:none;cursor:pointer; }
.pagination { display:flex;gap:6px;margin-top:16px;justify-content:flex-end;align-items:center;flex-wrap:wrap; }
.page-btn { padding:6px 12px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;color:var(--text);background:#fff;transition:all .2s; }
.page-btn:hover { border-color:var(--primary);color:var(--primary); }
.page-btn.active { background:var(--primary);border-color:var(--primary);color:#fff; }
.action-btns { display:flex;gap:5px; }
.status-menu { display:inline-flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.status-chip {
    padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700;
    text-decoration:none; transition:all .2s;
}
.status-chip.all { background:var(--bg); color:var(--text); border:1.5px solid var(--border); }
.status-chip.all.active, .status-chip.all:hover { background:var(--primary);color:#fff;border-color:var(--primary); }
.status-chip.menunggu { background:#fffbeb;color:#b45309;border:1.5px solid #fde68a; }
.status-chip.menunggu.active, .status-chip.menunggu:hover { background:#d97706;color:#fff;border-color:#d97706; }
.status-chip.diterima { background:#f0fdf4;color:#166534;border:1.5px solid #bbf7d0; }
.status-chip.diterima.active, .status-chip.diterima:hover { background:#16a34a;color:#fff;border-color:#16a34a; }
.status-chip.ditolak { background:#fef2f2;color:#991b1b;border:1.5px solid #fecaca; }
.status-chip.ditolak.active, .status-chip.ditolak:hover { background:#dc2626;color:#fff;border-color:#dc2626; }
</style>

<?php if ($msg === 'diterima'): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> Surat berhasil diterima dan dipindahkan ke Surat Masuk!</div><?php endif; ?>
<?php if ($msg === 'ditolak'): ?><div class="alert alert-warning" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e"><i class="fas fa-times-circle"></i> Surat telah ditolak.</div><?php endif; ?>

<!-- Stats row -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
    <?php
    $cnt_m = $conn->query("SELECT COUNT(*) as c FROM disposisi WHERE status='menunggu'")->fetch_assoc()['c'];
    $cnt_d = $conn->query("SELECT COUNT(*) as c FROM disposisi WHERE status='diterima'")->fetch_assoc()['c'];
    $cnt_t = $conn->query("SELECT COUNT(*) as c FROM disposisi WHERE status='ditolak'")->fetch_assoc()['c'];
    ?>
    <div style="background:#fff;border-radius:12px;padding:18px;border:1px solid var(--border);display:flex;align-items:center;gap:14px;border-top:3px solid #d97706">
        <div style="width:44px;height:44px;background:#fffbeb;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#d97706;font-size:18px"><i class="fas fa-clock"></i></div>
        <div><div style="font-size:24px;font-weight:800"><?= $cnt_m ?></div><div style="font-size:12px;color:var(--text-muted)">Menunggu Review</div></div>
    </div>
    <div style="background:#fff;border-radius:12px;padding:18px;border:1px solid var(--border);display:flex;align-items:center;gap:14px;border-top:3px solid #16a34a">
        <div style="width:44px;height:44px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:18px"><i class="fas fa-check-circle"></i></div>
        <div><div style="font-size:24px;font-weight:800"><?= $cnt_d ?></div><div style="font-size:12px;color:var(--text-muted)">Diterima</div></div>
    </div>
    <div style="background:#fff;border-radius:12px;padding:18px;border:1px solid var(--border);display:flex;align-items:center;gap:14px;border-top:3px solid #dc2626">
        <div style="width:44px;height:44px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:18px"><i class="fas fa-times-circle"></i></div>
        <div><div style="font-size:24px;font-weight:800"><?= $cnt_t ?></div><div style="font-size:12px;color:var(--text-muted)">Ditolak</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-inbox"></i> Disposisi Surat Masuk
            <?php if ($cnt_m > 0): ?><span style="background:#d97706;color:#fff;font-size:11px;padding:2px 8px;border-radius:20px;margin-left:6px"><?= $cnt_m ?> baru</span><?php endif; ?>
        </h3>
        <span style="font-size:12px;color:var(--text-muted)">Surat dari lembaga luar yang menunggu persetujuan</span>
    </div>
    <div class="card-body">
        <!-- Filter chips -->
        <div class="status-menu">
            <a href="?status=&search=<?= urlencode($search) ?>" class="status-chip all <?= empty($filterStatus)?'active':'' ?>">Semua (<?= $cnt_m+$cnt_d+$cnt_t ?>)</a>
            <a href="?status=menunggu&search=<?= urlencode($search) ?>" class="status-chip menunggu <?= $filterStatus==='menunggu'?'active':'' ?>"><i class="fas fa-clock"></i> Menunggu (<?= $cnt_m ?>)</a>
            <a href="?status=diterima&search=<?= urlencode($search) ?>" class="status-chip diterima <?= $filterStatus==='diterima'?'active':'' ?>"><i class="fas fa-check"></i> Diterima (<?= $cnt_d ?>)</a>
            <a href="?status=ditolak&search=<?= urlencode($search) ?>" class="status-chip ditolak <?= $filterStatus==='ditolak'?'active':'' ?>"><i class="fas fa-times"></i> Ditolak (<?= $cnt_t ?>)</a>
        </div>

        <!-- Search -->
        <form method="GET">
            <input type="hidden" name="status" value="<?= $filterStatus ?>">
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="search-input"
                           placeholder="Cari nomor, pengirim, instansi, atau perihal..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
                <a href="disposisi.php" class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Reset</a>
            </div>
        </form>

        <!-- Table -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nomor Surat</th>
                        <th>Pengirim & Instansi</th>
                        <th>Perihal</th>
                        <th>Kategori</th>
                        <th>Tgl Masuk</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($list && $list->num_rows > 0): $no = $offset + 1; while ($d = $list->fetch_assoc()): ?>
                    <tr <?= $d['status']==='menunggu' ? 'style="background:#fffef5"' : '' ?>>
                        <td style="color:var(--text-muted);font-size:12px"><?= $no++ ?></td>
                        <td>
                            <div style="font-weight:700;font-size:12.5px;color:var(--primary)"><?= htmlspecialchars($d['nomor_surat']) ?></div>
                            <div style="font-size:11px;color:var(--text-muted)"><?= formatTanggal($d['tanggal_surat']) ?></div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($d['pengirim']) ?></div>
                            <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($d['instansi']) ?></div>
                        </td>
                        <td style="max-width:180px">
                            <div style="font-size:12.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px" title="<?= htmlspecialchars($d['perihal']) ?>">
                                <?= htmlspecialchars($d['perihal']) ?>
                            </div>
                        </td>
                        <td><?= badgeKategori($d['kategori']) ?></td>
                        <td style="font-size:11.5px;white-space:nowrap"><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                        <td>
                            <?php
                            $badge = ['menunggu'=>['Menunggu','#d97706','#fffbeb'], 'diterima'=>['Diterima','#16a34a','#f0fdf4'], 'ditolak'=>['Ditolak','#dc2626','#fef2f2']];
                            $b = $badge[$d['status']] ?? [$d['status'],'#666','#f5f5f5'];
                            echo "<span style='background:{$b[2]};color:{$b[1]};padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700;border:1px solid {$b[1]}44'>{$b[0]}</span>";
                            ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="detail_disposisi.php?id=<?= $d['id'] ?>" class="btn btn-primary btn-sm" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                <?php if ($d['status'] === 'menunggu'): ?>
                                <a href="proses_disposisi.php?id=<?= $d['id'] ?>&action=terima"
                                   class="btn btn-success btn-sm" title="Terima"
                                   onclick="return confirm('Terima dan pindahkan ke Surat Masuk?')"><i class="fas fa-check"></i></a>
                                <a href="proses_disposisi.php?id=<?= $d['id'] ?>&action=tolak"
                                   class="btn btn-danger btn-sm" title="Tolak"
                                   onclick="return confirm('Tolak surat ini?')"><i class="fas fa-times"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
                            <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3"></i>
                            <?= $search ? 'Tidak ada hasil pencarian.' : 'Belum ada surat disposisi.' ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPage > 1): ?>
        <div class="pagination">
            <span style="font-size:12px;color:var(--text-muted)">Halaman <?= $page ?> dari <?= $totalPage ?></span>
            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $filterStatus ?>"
               class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
