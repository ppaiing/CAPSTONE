<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();
$pageTitle = 'Surat Keluar';

$msg = $_GET['msg'] ?? '';

$search = sanitize($_GET['search'] ?? '');
$filterStatus   = sanitize($_GET['status'] ?? '');
$filterKategori = sanitize($_GET['kategori'] ?? '');

$where = "WHERE 1=1";
if ($search)        $where .= " AND (nomor_surat LIKE '%$search%' OR tujuan LIKE '%$search%' OR perihal LIKE '%$search%')";
if ($filterStatus)  $where .= " AND status = '$filterStatus'";
if ($filterKategori) $where .= " AND kategori = '$filterKategori'";

$perPage   = 10;
$page      = max(1, (int)($_GET['page'] ?? 1));
$offset    = ($page - 1) * $perPage;
$totalRow  = $conn->query("SELECT COUNT(*) as c FROM surat_keluar $where")->fetch_assoc()['c'];
$totalPage = ceil($totalRow / $perPage);
$surat = $conn->query("SELECT * FROM surat_keluar $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");

include 'includes/header.php';
?>

<style>
.filter-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:20px; }
.search-input-wrap { position:relative; flex:1; min-width:200px; }
.search-input-wrap i { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa; }
.search-input { padding:9px 12px 9px 36px; border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:13px; outline:none; width:100%; }
.search-input:focus { border-color:var(--primary-light); }
.filter-select { padding:9px 12px; border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:13px; outline:none; }
.pagination { display:flex; gap:6px; margin-top:16px; justify-content:flex-end; align-items:center; flex-wrap:wrap; }
.page-btn { padding:6px 12px; border:1.5px solid var(--border); border-radius:6px; font-size:13px; font-weight:500; text-decoration:none; color:var(--text); background:#fff; transition:all .2s; }
.page-btn:hover { border-color:var(--primary); color:var(--primary); }
.page-btn.active { background:var(--primary); border-color:var(--primary); color:#fff; }
.action-btns { display:flex; gap:6px; }
</style>

<?php if ($msg === 'added'): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> Surat keluar berhasil ditambahkan!</div>
<?php elseif ($msg === 'updated'): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> Surat keluar berhasil diperbarui!</div>
<?php elseif ($msg === 'deleted'): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> Surat keluar berhasil dihapus!</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-paper-plane"></i> Daftar Surat Keluar
            <span style="background:var(--primary);color:#fff;font-size:11px;padding:2px 8px;border-radius:20px;margin-left:6px"><?= $totalRow ?></span>
        </h3>
        <a href="input_surat_keluar.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Surat Keluar
        </a>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="filter-bar">
                <div class="search-input-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="search-input"
                           placeholder="Cari nomor, tujuan, atau perihal..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="status" class="filter-select">
                    <option value="">Semua Status</option>
                    <option value="draft"   <?= $filterStatus==='draft'?'selected':'' ?>>Draft</option>
                    <option value="dikirim" <?= $filterStatus==='dikirim'?'selected':'' ?>>Dikirim</option>
                    <option value="selesai" <?= $filterStatus==='selesai'?'selected':'' ?>>Selesai</option>
                </select>
                <select name="kategori" class="filter-select">
                    <option value="">Semua Kategori</option>
                    <option value="undangan"      <?= $filterKategori==='undangan'?'selected':'' ?>>Undangan</option>
                    <option value="permohonan"    <?= $filterKategori==='permohonan'?'selected':'' ?>>Permohonan</option>
                    <option value="pemberitahuan" <?= $filterKategori==='pemberitahuan'?'selected':'' ?>>Pemberitahuan</option>
                    <option value="keputusan"     <?= $filterKategori==='keputusan'?'selected':'' ?>>Keputusan</option>
                    <option value="lainnya"       <?= $filterKategori==='lainnya'?'selected':'' ?>>Lainnya</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="surat_keluar.php" class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Reset</a>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nomor Surat</th>
                        <th>Tanggal Surat</th>
                        <th>Tujuan</th>
                        <th>Perihal</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($surat && $surat->num_rows > 0): $no = $offset + 1; while ($s = $surat->fetch_assoc()): ?>
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px"><?= $no++ ?></td>
                        <td>
                            <div style="font-weight:700;font-size:12.5px;color:var(--primary)"><?= htmlspecialchars($s['nomor_surat']) ?></div>
                        </td>
                        <td style="font-size:12px;white-space:nowrap"><?= formatTanggal($s['tanggal_surat']) ?></td>
                        <td style="font-size:13px;font-weight:500"><?= htmlspecialchars($s['tujuan']) ?></td>
                        <td style="font-size:12.5px;max-width:180px">
                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px" title="<?= htmlspecialchars($s['perihal']) ?>">
                                <?= htmlspecialchars($s['perihal']) ?>
                            </div>
                        </td>
                        <td><?= badgeKategori($s['kategori']) ?></td>
                        <td><?= badgeStatus($s['status']) ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="detail_surat_keluar.php?id=<?= $s['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                <a href="edit_surat_keluar.php?id=<?= $s['id'] ?>" class="btn btn-accent btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="hapus_surat.php?type=keluar&id=<?= $s['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin hapus surat ini?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
                            <i class="fas fa-paper-plane" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3"></i>
                            <?= $search ? 'Tidak ditemukan hasil pencarian.' : 'Belum ada surat keluar.' ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPage > 1): ?>
        <div class="pagination">
            <span style="font-size:12px;color:var(--text-muted)">Halaman <?= $page ?> dari <?= $totalPage ?> (<?= $totalRow ?> data)</span>
            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $filterStatus ?>&kategori=<?= $filterKategori ?>"
               class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
