<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' — ' : '' ?><?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0f4c81;
            --primary-light: #1a6bb5;
            --primary-dark: #08305a;
            --accent: #e8b84b;
            --accent-light: #f5d07a;
            --danger: #e53e3e;
            --success: #38a169;
            --warning: #d69e2e;
            --info: #3182ce;
            --bg: #f0f4f8;
            --sidebar-bg: #0a2d4d;
            --sidebar-text: #c8daf0;
            --sidebar-active: #1a6bb5;
            --card-bg: #ffffff;
            --text: #1a202c;
            --text-muted: #718096;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,.1), 0 1px 2px rgba(0,0,0,.06);
            --shadow-md: 0 4px 6px rgba(0,0,0,.07), 0 2px 4px rgba(0,0,0,.06);
            --shadow-lg: 0 10px 25px rgba(0,0,0,.1);
            --radius: 10px;
            --radius-lg: 16px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s;
        }
        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 6px;
        }
        .sidebar-brand .logo-icon {
             width: 42px;
             height: 42px;
             border-radius: 10px;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 20px;
             color: var(--primary-dark);
             font-weight: 800;
             flex-shrink: 0;
        }
        .sidebar-brand h1 {
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .sidebar-brand p {
            font-size: 10px;
            color: rgba(255,255,255,.45);
            margin-top: 4px;
            line-height: 1.4;
        }
        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }
        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            color: rgba(255,255,255,.3);
            text-transform: uppercase;
            padding: 12px 10px 6px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all .2s;
            position: relative;
        }
        .nav-item:hover { background: rgba(255,255,255,.07); color: #fff; }
        .nav-item.active {
            background: var(--sidebar-active);
            color: #fff;
            font-weight: 600;
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 25%; bottom: 25%;
            width: 3px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; }
        .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: var(--primary-dark);
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 20px;
        }
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
        }
        .user-avatar{
            width:40px;
            height:40px;
            border-radius:50%;
            object-fit:cover;
            flex-shrink:0;
            padding:2px;
            border:2px solid rgba(255,255,255,.15);
        }
        .user-info { flex: 1; min-width: 0; }
        .user-info .name {
            font-size: 12.5px; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-info .role {
            font-size: 10.5px; color: rgba(255,255,255,.4);
        }
        .btn-logout {
            width: 30px; height: 30px;
            background: rgba(255,255,255,.07);
            border: none; border-radius: 6px;
            color: rgba(255,255,255,.5);
            cursor: pointer; font-size: 13px;
            display: flex; align-items: center; justify-content: center;
            transition: all .2s;
        }
        .btn-logout:hover { background: var(--danger); color: #fff; }

        /* ── MAIN ── */
        .main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: 0 28px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 50;
            box-shadow: var(--shadow);
        }
        .topbar-left h2 {
            font-family: 'Sora', sans-serif;
            font-size: 18px; font-weight: 700;
            color: var(--primary);
        }
        .topbar-left p {
            font-size: 12px; color: var(--text-muted); margin-top: 1px;
        }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-date {
            font-size: 12px; color: var(--text-muted);
            background: var(--bg);
            padding: 6px 12px; border-radius: 20px;
        }
        .content { padding: 28px; flex: 1; }

        /* ── CARDS ── */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px;
        }
        .card-header h3 {
            font-size: 15px; font-weight: 700; color: var(--text);
            display: flex; align-items: center; gap: 8px;
        }
        .card-header h3 i { color: var(--primary); }
        .card-body { padding: 22px; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px;
            border-radius: 8px; border: none;
            font-family: inherit; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: all .2s;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-light); }
        .btn-accent { background: var(--accent); color: var(--primary-dark); }
        .btn-accent:hover { background: var(--accent-light); }
        .btn-success { background: var(--success); color: #fff; }
        .btn-success:hover { background: #2f855a; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #c53030; }
        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--primary);
            color: var(--primary);
        }
        .btn-outline:hover { background: var(--primary); color: #fff; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }

        /* ── TABLE ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { background: var(--bg); }
        th {
            padding: 11px 14px;
            text-align: left;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .05em;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border);
        }
        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f7fafd; }

        /* ── FORM ── */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--text); margin-bottom: 6px;
        }
        .form-label span { color: var(--danger); }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: inherit; font-size: 13px;
            color: var(--text);
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(26,107,181,.12);
        }
        select.form-control { cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 90px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0 20px; }

        /* ── ALERTS ── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #f0fff4; border: 1px solid #9ae6b4; color: #276749; }
        .alert-danger  { background: #fff5f5; border: 1px solid #feb2b2; color: #9b2c2c; }
        .alert-warning { background: #fffbeb; border: 1px solid #fbd38d; color: #975a16; }
        .alert-info    { background: #ebf8ff; border: 1px solid #90cdf4; color: #2c5282; }

        /* ── MOBILE TOGGLE ── */
        .sidebar-toggle {
            display: none;
            background: none; border: none;
            font-size: 20px; color: var(--primary);
            cursor: pointer; padding: 8px;
        }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .content { padding: 16px; }
            .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-wrap">
            <img src="assets/Logo_HMIF FT.png" alt="Logo HMIF" class="logo-icon">
            <div>
                <h1><?= APP_NAME ?></h1>
                <p><?= FAKULTAS ?></p>
            </div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
        <a href="<?= BASE_URL ?>dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <div class="nav-section-label">Disposisi & Surat</div>
        <a href="<?= BASE_URL ?>disposisi.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'disposisi.php' || basename($_SERVER['PHP_SELF']) == 'detail_disposisi.php' ? 'active' : '' ?>">
            <i class="fas fa-inbox"></i> Disposisi Surat
            <?php
            global $conn;
            $cntDisp = $conn->query("SELECT COUNT(*) as c FROM disposisi WHERE status='menunggu'")->fetch_assoc()['c'];
            if ($cntDisp > 0) echo "<span class='nav-badge' style='background:#d97706'>$cntDisp</span>";
            ?>
        </a>
        <a href="<?= BASE_URL ?>surat_masuk.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'surat_masuk.php' || basename($_SERVER['PHP_SELF']) == 'detail_surat_masuk.php' ? 'active' : '' ?>">
            <i class="fas fa-envelope-open-text"></i> Surat Masuk
            <?php
            $cnt = $conn->query("SELECT COUNT(*) as c FROM surat_masuk WHERE status='belum_diproses'")->fetch_assoc()['c'];
            if ($cnt > 0) echo "<span class='nav-badge'>$cnt</span>";
            ?>
        </a>
        <a href="<?= BASE_URL ?>surat_keluar.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'surat_keluar.php' || basename($_SERVER['PHP_SELF']) == 'detail_surat_keluar.php' ? 'active' : '' ?>">
            <i class="fas fa-paper-plane"></i> Surat Keluar
        </a>
        <a href="<?= BASE_URL ?>input_surat_masuk.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'input_surat_masuk.php' ? 'active' : '' ?>">
            <i class="fas fa-file-import"></i> Input Surat Masuk
        </a>
        <a href="<?= BASE_URL ?>input_surat_keluar.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'input_surat_keluar.php' ? 'active' : '' ?>">
            <i class="fas fa-file-export"></i> Input Surat Keluar
        </a>

        <div class="nav-section-label">Pengaturan</div>
        <a href="<?= BASE_URL ?>profil.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
    </nav>

    <div class="user-card">
        <img src="assets/Logo_HMIF FT.png" alt="Logo HMIF" class="user-avatar">

        <div class="user-info">
            <div class="name"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></div>
            <div class="role"><?= ucfirst($_SESSION['role'] ?? 'admin') ?></div>
        </div>

        <a href="<?= BASE_URL ?>logout.php" class="btn-logout" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:14px">
            <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-left">
                <h2><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h2>
                <p><?= APP_SUBTITLE ?> — <?= FAKULTAS ?></p>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date">
                <i class="fas fa-calendar-alt" style="color:var(--primary);margin-right:5px"></i>
                <span id="topbar-date"></span>
            </div>
        </div>
    </header>
    <div class="content">
<script>
    const d = new Date();
    const opt = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
    document.getElementById('topbar-date').textContent = d.toLocaleDateString('id-ID', opt);
</script>
