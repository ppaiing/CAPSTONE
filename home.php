<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Statistik publik
$totalMasuk  = $conn->query("SELECT COUNT(*) as c FROM surat_masuk")->fetch_assoc()['c'];
$totalKeluar = $conn->query("SELECT COUNT(*) as c FROM surat_keluar")->fetch_assoc()['c'];
$totalDisp   = $conn->query("SELECT COUNT(*) as c FROM disposisi WHERE status='menunggu'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Himpunan Mahasiswa Informatika FT UNISMUH Makassar — Sistem HMIF-Nexus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0f4c81;
            --primary-light: #1a6bb5;
            --primary-dark: #08305a;
            --accent: #e8b84b;
            --accent-light: #f5d07a;
            --red: #dc2626;
            --white: #ffffff;
            --bg: #f0f4f8;
            --text: #1a202c;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text); background: #fff; }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 200;
            background: rgba(8, 48, 90, 0.97);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,.08);
            padding: 0 5%;
            height: 68px;
            display: flex; align-items: center; justify-content: space-between;
            transition: all .3s;
        }
        .navbar.scrolled {
            background: rgba(8,48,90,.99);
            box-shadow: 0 4px 20px rgba(0,0,0,.3);
        }
        .nav-brand {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none;
        }
        .nav-brand img {
            width: 40px; height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }
        .nav-brand-text h1 {
            font-family: 'Sora', sans-serif;
            font-size: 14px; font-weight: 800;
            color: #fff; line-height: 1.1;
        }
        .nav-brand-text p {
            font-size: 10px; color: rgba(255,255,255,.5);
        }
        .nav-links {
            display: flex; align-items: center; gap: 4px;
        }
        .nav-link {
            padding: 8px 16px;
            color: rgba(255,255,255,.8);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            border-radius: 8px;
            transition: all .2s;
        }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .nav-link.active { color: var(--accent); }
        .btn-login-nav {
            padding: 8px 20px;
            background: linear-gradient(135deg, var(--accent), #f0a500);
            color: var(--primary-dark) !important;
            font-weight: 700 !important;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            transition: all .2s;
        }
        .btn-login-nav:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(232,184,75,.4); background: rgba(255,255,255,.1) !important; color: #fff !important; }
        .nav-toggle { display: none; background: none; border: none; color: #fff; font-size: 22px; cursor: pointer; }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(150deg, #051c35 0%, #0a2d4d 40%, #0f4c81 80%, #1a6bb5 100%);
            display: flex; align-items: center;
            position: relative; overflow: hidden;
            padding: 100px 5% 60px;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(232,184,75,.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(26,107,181,.15) 0%, transparent 50%),
                radial-gradient(circle at 60% 80%, rgba(220,38,38,.05) 0%, transparent 40%);
        }
        /* Circuit pattern */
        .hero::after {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .hero-content {
            position: relative; z-index: 1;
            display: flex; align-items: center;
            gap: 60px; max-width: 1200px; margin: 0 auto; width: 100%;
        }
        .hero-text { flex: 1; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(232,184,75,.15);
            border: 1px solid rgba(232,184,75,.3);
            color: var(--accent);
            padding: 6px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            letter-spacing: .05em; text-transform: uppercase;
            margin-bottom: 20px;
        }
        .hero-title {
            font-family: 'Sora', sans-serif;
            font-size: clamp(28px, 4vw, 52px);
            font-weight: 800; color: #fff;
            line-height: 1.15; margin-bottom: 16px;
        }
        .hero-title span { color: var(--accent); }
        .hero-title .red { color: #fc8181; }
        .hero-sub {
            font-size: 16px; color: rgba(255,255,255,.7);
            line-height: 1.7; margin-bottom: 32px; max-width: 520px;
        }
        .hero-btns { display: flex; gap: 14px; flex-wrap: wrap; }
        .btn-hero-primary {
            padding: 14px 28px;
            background: linear-gradient(135deg, var(--accent), #f0a500);
            color: var(--primary-dark);
            font-weight: 700; font-size: 14px;
            border-radius: 10px; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            transition: all .2s;
            box-shadow: 0 4px 18px rgba(232,184,75,.35);
        }
        .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(232,184,75,.5); }
        .btn-hero-outline {
            padding: 14px 28px;
            border: 2px solid rgba(255,255,255,.3);
            color: #fff;
            font-weight: 600; font-size: 14px;
            border-radius: 10px; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            transition: all .2s; backdrop-filter: blur(4px);
        }
        .btn-hero-outline:hover { border-color: #fff; background: rgba(255,255,255,.1); }
        .hero-logo {
            flex-shrink: 0;
            width: 300px; height: 300px;
            position: relative;
        }
        .hero-logo img {
            width: 100%; height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 40px rgba(232,184,75,.3)) drop-shadow(0 0 80px rgba(26,107,181,.4));
            animation: floatLogo 4s ease-in-out infinite;
        }
        @keyframes floatLogo {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-14px); }
        }
        .hero-stats {
            display: flex; gap: 32px; margin-top: 40px;
            padding-top: 32px; border-top: 1px solid rgba(255,255,255,.1);
        }
        .hero-stat .value {
            font-family: 'Sora', sans-serif;
            font-size: 28px; font-weight: 800; color: var(--accent);
        }
        .hero-stat .label {
            font-size: 12px; color: rgba(255,255,255,.5);
            margin-top: 2px; font-weight: 500;
        }

        /* ── SECTION COMMON ── */
        section { padding: 80px 5%; }
        .section-tag {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(15,76,129,.08);
            color: var(--primary); border: 1px solid rgba(15,76,129,.2);
            padding: 5px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; margin-bottom: 14px;
        }
        .section-title {
            font-family: 'Sora', sans-serif;
            font-size: clamp(22px, 3vw, 36px);
            font-weight: 800; color: var(--text);
            line-height: 1.25; margin-bottom: 14px;
        }
        .section-title span { color: var(--primary); }
        .section-sub {
            font-size: 15px; color: var(--text-muted);
            line-height: 1.7; max-width: 620px;
        }

        /* ── ABOUT ── */
        .about-section { background: #fff; }
        .about-grid {
            display: grid; grid-template-columns: 1fr 1.2fr;
            gap: 60px; align-items: center;
            max-width: 1100px; margin: 0 auto;
        }
        .about-img-wrap {
            position: relative;
        }
        .about-img-wrap img {
            width: 100%; max-width: 340px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(15,76,129,.2);
            display: block; margin: 0 auto;
        }
        .about-img-badge {
            position: absolute; bottom: -16px; right: 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff; border-radius: 12px;
            padding: 14px 18px; text-align: center;
            box-shadow: 0 8px 24px rgba(15,76,129,.3);
        }
        .about-img-badge .num { font-size: 24px; font-weight: 800; }
        .about-img-badge .txt { font-size: 11px; opacity: .8; }
        .about-text { }
        .info-list { margin-top: 28px; display: flex; flex-direction: column; gap: 14px; }
        .info-item {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 14px 16px;
            background: var(--bg);
            border-radius: 10px;
            border-left: 3px solid var(--primary);
        }
        .info-item i { color: var(--primary); font-size: 16px; margin-top: 1px; flex-shrink: 0; }
        .info-item .info-content .label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; }
        .info-item .info-content .val { font-size: 14px; font-weight: 600; color: var(--text); margin-top: 2px; }

        /* ── VISI MISI ── */
        .visi-section { background: var(--bg); }
        .vm-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 24px; max-width: 1000px; margin: 40px auto 0;
        }
        .vm-card {
            background: #fff; border-radius: 16px;
            padding: 28px; border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .vm-card.visi { border-top: 4px solid var(--primary); }
        .vm-card.misi { border-top: 4px solid var(--accent); }
        .vm-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 16px;
        }
        .vm-icon.blue { background: #ebf4ff; color: var(--primary); }
        .vm-icon.gold { background: #fffbeb; color: #b45309; }
        .vm-card h3 { font-family: 'Sora', sans-serif; font-size: 17px; font-weight: 700; margin-bottom: 10px; }
        .vm-card p, .vm-card li { font-size: 14px; color: var(--text-muted); line-height: 1.7; }
        .vm-card ul { padding-left: 18px; display: flex; flex-direction: column; gap: 6px; }

        /* ── STRUKTUR ── */
        .struktur-section { background: #fff; }
                .struktur-grid-top {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 900px;
            margin: 40px auto 25px;
        }

        .struktur-grid-bottom {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .struct-card {
            text-align: center; padding: 24px 16px;
            background: var(--bg); border-radius: 14px;
            border: 1px solid var(--border);
        }
        .struct-avatar {
            width: 60px; height: 60px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 22px;
            margin: 0 auto 12px;
            border: 3px solid #fff;
            box-shadow: 0 4px 14px rgba(15,76,129,.25);
        }
        .struct-card .jabatan { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--primary); margin-bottom: 4px; }
        .struct-card .nama { font-size: 14px; font-weight: 700; color: var(--text); }
        .struct-card .divisi { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

        /* ── CARA KIRIM ── */
        .cara-section { background: linear-gradient(135deg, #0a2d4d, #0f4c81); }
        .cara-section .section-tag { background: rgba(232,184,75,.15); color: var(--accent); border-color: rgba(232,184,75,.3); }
        .cara-section .section-title { color: #fff; }
        .cara-section .section-sub { color: rgba(255,255,255,.65); }
        .steps-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 20px; max-width: 1000px; margin: 40px auto 0;
        }
        .step-card {
            text-align: center; padding: 28px 20px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 16px;
            position: relative;
            transition: all .2s;
        }
        .step-card:hover { background: rgba(255,255,255,.1); transform: translateY(-4px); }
        .step-num {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--accent), #f0a500);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 16px; color: var(--primary-dark);
            margin: 0 auto 16px;
        }
        .step-icon { font-size: 28px; margin-bottom: 12px; color: rgba(255,255,255,.7); }
        .step-card h4 { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .step-card p { font-size: 12px; color: rgba(255,255,255,.55); line-height: 1.6; }
        .step-arrow {
            position: absolute; right: -12px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,.2); font-size: 16px; z-index: 1;
        }
        .step-card:last-child .step-arrow { display: none; }

        /* ── CTA KIRIM ── */
        .cta-section { background: var(--bg); text-align: center; padding: 60px 5%; }
        .cta-box {
            background: linear-gradient(135deg, #fff, #f8fbff);
            border-radius: 20px;
            padding: 48px 40px;
            border: 1px solid var(--border);
            max-width: 700px; margin: 0 auto;
            box-shadow: 0 8px 32px rgba(15,76,129,.1);
        }
        .cta-box .logo-sm { width: 70px; height: 70px; border-radius: 50%; object-fit: cover; margin: 0 auto 20px; display: block; border: 3px solid var(--accent); }
        .cta-box h2 { font-family: 'Sora', sans-serif; font-size: 26px; font-weight: 800; color: var(--primary); margin-bottom: 10px; }
        .cta-box p { font-size: 14px; color: var(--text-muted); margin-bottom: 28px; line-height: 1.7; }
        .btn-cta {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 15px 32px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff; border-radius: 12px;
            font-weight: 700; font-size: 15px; text-decoration: none;
            transition: all .2s;
            box-shadow: 0 4px 18px rgba(15,76,129,.3);
        }
        .btn-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(15,76,129,.4); }

        /* ── FOOTER ── */
        footer {
            background: #051c35;
            padding: 48px 5% 24px;
            color: rgba(255,255,255,.6);
        }
        .footer-grid {
            display: grid; grid-template-columns: 1.5fr 1fr 1fr;
            gap: 40px; margin-bottom: 36px;
        }
        .footer-brand img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; margin-bottom: 14px; }
        .footer-brand h3 { font-family: 'Sora', sans-serif; font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 6px; }
        .footer-brand p { font-size: 13px; line-height: 1.7; }
        .footer-col h4 { font-size: 13px; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 14px; }
        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .footer-col ul li a { font-size: 13px; color: rgba(255,255,255,.5); text-decoration: none; transition: color .2s; }
        .footer-col ul li a:hover { color: var(--accent); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.08);
            padding-top: 20px;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 10px;
            font-size: 12px;
        }
        .footer-bottom a { color: var(--accent); text-decoration: none; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .hero-content { flex-direction: column; text-align: center; }
            .hero-logo { width: 200px; height: 200px; }
            .hero-btns { justify-content: center; }
            .hero-stats { justify-content: center; }
            .about-grid { grid-template-columns: 1fr; }
            .about-img-wrap { order: -1; }
            .vm-grid, .steps-grid { grid-template-columns: 1fr; }
            .struktur-grid { grid-template-columns: 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .nav-links { display: none; position: fixed; top: 68px; left: 0; right: 0; background: #08305a; padding: 16px; flex-direction: column; gap: 4px; }
            .nav-links.open { display: flex; }
            .nav-toggle { display: block; }
        }
        @media (max-width: 500px) {
            .struktur-grid { grid-template-columns: 1fr; }
            .hero-stats { flex-direction: column; gap: 16px; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar" id="navbar">
    <a href="home.php" class="nav-brand">
    <img src="assets/Logo_HMIF FT.png" alt="Logo HMIF-FT UNISMUH">
        <div class="nav-brand-text">
            <h1>HMIF</h1>
            <p>FT UNISMUH Makassar</p>
        </div>
    </a>
    <div class="nav-links" id="navLinks">
        <a href="home.php" class="nav-link active">Beranda</a>
        <a href="home.php#tentang" class="nav-link">Tentang</a>
        <a href="home.php#visi-misi" class="nav-link">Visi &amp; Misi</a>
        <a href="home.php#cara-kirim" class="nav-link">Cara Kirim Surat</a>
        <a href="kirim_surat.php" class="nav-link">Kirim Surat</a>
        <a href="login.php" class="nav-link btn-login-nav"><i class="fas fa-sign-in-alt"></i> Login Admin</a>
    </div>
    <button class="nav-toggle" onclick="document.getElementById('navLinks').classList.toggle('open')">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<!-- ── HERO ── -->
<section class="hero" id="home">
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-badge">
                <i class="fas fa-microchip"></i> Sistem Administrasi HMIF-Nexus
            </div>
            <h1 class="hero-title">
                Himpunan Mahasiswa<br>
                <span>Informatika</span><br>
                <span class="red" style="font-size:.75em">Fakultas Teknik</span>
            </h1>
            <p class="hero-sub">
                Universitas Muhammadiyah Makassar — Platform digital pengelolaan surat menyurat
                HMIF-FT UNISMUH. Kirim surat undangan, permohonan, dan pemberitahuan secara
                mudah dan terdokumentasi.
            </p>
            <div class="hero-btns">
                <a href="kirim_surat.php" class="btn-hero-primary">
                    <i class="fas fa-paper-plane"></i> Kirim Surat ke HMIF
                </a>
                <a href="#tentang" class="btn-hero-outline">
                    <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
                </a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="value"><?= $totalMasuk ?></div>
                    <div class="label">Surat Masuk</div>
                </div>
                <div class="hero-stat">
                    <div class="value"><?= $totalKeluar ?></div>
                    <div class="label">Surat Keluar</div>
                </div>
                <div class="hero-stat">
                    <div class="value"><?= $totalDisp ?></div>
                    <div class="label">Menunggu Review</div>
                </div>
            </div>
        </div>
        <div class="hero-logo">
            <img src="assets/Logo_HMIF FT.png" alt="Logo HMIF-FT UNISMUH">
        </div>
    </div>
</section>

<!-- ── TENTANG HMIF ── -->
<section class="about-section" id="tentang">
    <div class="about-grid">
        <div class="about-img-wrap">
            <img src="assets/Logo_HMIF FT.png" alt="Logo HMIF-FT UNISMUH">
            <div class="about-img-badge">
                <div class="num">2026</div>
                <div class="txt">Periode Aktif</div>
            </div>
        </div>
        <div class="about-text">
            <div class="section-tag"><i class="fas fa-users"></i> Tentang Kami</div>
            <h2 class="section-title">Mengenal <span>HMIF</span><br>FT UNISMUH Makassar</h2>
            <p class="section-sub">
                Himpunan Mahasiswa Informatika (HMIF-FT UNISMUH) adalah organisasi kemahasiswaan
                tingkat program studi yang berada di bawah naungan <strong>Fakultas Teknik
                Universitas Muhammadiyah Makassar</strong>. HMIF-FT UNISMUH berperan sebagai
                wadah pengembangan akademik, kreativitas, dan kepemimpinan mahasiswa Informatika.
            </p>
            <div class="info-list">
                <div class="info-item">
                    <i class="fas fa-university"></i>
                    <div class="info-content">
                        <div class="label">Naungan</div>
                        <div class="val">Fakultas Teknik, Universitas Muhammadiyah Makassar</div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-laptop-code"></i>
                    <div class="info-content">
                        <div class="label">Program Studi</div>
                        <div class="val">Teknik Informatika — S1</div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="info-content">
                        <div class="label">Alamat</div>
                        <div class="val">Jl. Sultan Alauddin No.259, Makassar, Sulawesi Selatan</div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div class="info-content">
                        <div class="label">Kontak Resmi</div>
                        <div class="val">hmifftunismuh@ft.unismuh.ac.id</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── VISI MISI ── -->
<section class="visi-section" id="visi-misi">
    <div style="max-width:1000px;margin:0 auto">
        <div style="text-align:center">
            <div class="section-tag"><i class="fas fa-star"></i> Landasan Organisasi</div>
            <h2 class="section-title">Visi &amp; <span>Misi</span></h2>
            <p class="section-sub" style="margin:0 auto">
                Landasan gerak HMIF-FT UNISMUH MAKASSAR dalam menjalankan roda organisasi
                demi kemajuan mahasiswa Informatika UNISMUH Makassar.
            </p>
        </div>
        <div class="vm-grid">
            <div class="vm-card visi">
                <div class="vm-icon blue"><i class="fas fa-eye"></i></div>
                <h3>Visi</h3>
                <p>
                    Menjadi himpunan mahasiswa yang profesional, inovatif, dan berdaya saing tinggi
                    dalam bidang teknologi informasi, serta berkontribusi nyata bagi masyarakat
                    berdasarkan nilai-nilai keislaman dan kemuhammadiyahan.
                </p>
            </div>
            <div class="vm-card misi">
                <div class="vm-icon gold"><i class="fas fa-bullseye"></i></div>
                <h3>Misi</h3>
                <ul>
                    <li>Meningkatkan kualitas akademik dan kompetensi mahasiswa Informatika</li>
                    <li>Mengembangkan kreativitas dan jiwa kewirausahaan berbasis teknologi</li>
                    <li>Menjalin kerjasama dengan berbagai lembaga internal dan eksternal kampus</li>
                    <li>Menyelenggarakan kegiatan yang bermanfaat bagi mahasiswa dan masyarakat</li>
                    <li>Menanamkan nilai-nilai Islami dalam setiap aspek keorganisasian</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ── STRUKTUR ORGANISASI ── -->
<section class="struktur-section" id="struktur">
    <div style="max-width:1200px;margin:0 auto">
        <div style="text-align:center">
            <div class="section-tag"><i class="fas fa-sitemap"></i> Kepengurusan</div>
            <h2 class="section-title">Struktur <span>Pengurus</span></h2>
            <p class="section-sub" style="margin:0 auto">
                Jajaran pengurus HMIF-FT UNISMUH periode aktif yang siap melayani.
            </p>
        </div>

        <!-- Baris Pertama -->
        <div class="struktur-grid-top">
            <div class="struct-card">
                <div class="struct-avatar">K</div>
                <div class="jabatan">Ketua Umum</div>
                <div class="nama">Pengurus Aktif</div>
                <div class="divisi">HMIF-FT UNISMUH</div>
            </div>

            <div class="struct-card">
                <div class="struct-avatar" style="background:linear-gradient(135deg,#e8b84b,#f0a500);color:#08305a">S</div>
                <div class="jabatan">Sekretaris Umum</div>
                <div class="nama">Pengurus Aktif</div>
                <div class="divisi">Administrasi</div>
            </div>

            <div class="struct-card">
                <div class="struct-avatar" style="background:linear-gradient(135deg,#16a34a,#22c55e)">B</div>
                <div class="jabatan">Bendahara Umum</div>
                <div class="nama">Pengurus Aktif</div>
                <div class="divisi">Keuangan</div>
            </div>
        </div>

        <!-- Baris Kedua -->
        <div class="struktur-grid-bottom">
            <div class="struct-card">
                <div class="struct-avatar" style="background:linear-gradient(135deg,#dc2626,#ef4444)">P</div>
                <div class="jabatan">Pengembangan Organisasi</div>
                <div class="nama">Pengurus Aktif</div>
                <div class="divisi">Bidang PO</div>
            </div>

            <div class="struct-card">
                <div class="struct-avatar" style="background:linear-gradient(135deg,#7c3aed,#a855f7)">K</div>
                <div class="jabatan">Keinformatikaan</div>
                <div class="nama">Pengurus Aktif</div>
                <div class="divisi">Bidang Keilmuan</div>
            </div>

            <div class="struct-card">
                <div class="struct-avatar" style="background:linear-gradient(135deg,#0891b2,#06b6d4)">S</div>
                <div class="jabatan">Sumber Daya Manusia</div>
                <div class="nama">Pengurus Aktif</div>
                <div class="divisi">SDM</div>
            </div>

            <div class="struct-card">
                <div class="struct-avatar" style="background:linear-gradient(135deg,#0ea5e9,#22d3ee)">A</div>
                <div class="jabatan">Advokasi</div>
                <div class="nama">Pengurus Aktif</div>
                <div class="divisi">Bidang Advokasi</div>
            </div>
        </div>
    </div>
</section>
<!-- ── CARA KIRIM SURAT ── -->
<section class="cara-section" id="cara-kirim">
    <div style="max-width:1000px;margin:0 auto">
        <div style="text-align:center">
            <div class="section-tag"><i class="fas fa-envelope"></i> Panduan Surat</div>
            <h2 class="section-title">Cara Mengirim <span style="color:var(--accent)">Surat ke HMIF</span></h2>
            <p class="section-sub" style="margin:0 auto;color:rgba(255,255,255,.65)">
                Lembaga atau instansi lain dapat mengirim surat ke HMIF-FT UNISMUH melalui platform digital ini.
                Surat akan masuk ke proses review (disposisi) sebelum diterima secara resmi.
            </p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">1</div>
                <div class="step-icon"><i class="fas fa-globe"></i></div>
                <h4>Akses Menu Kirim Surat</h4>
                <p>Klik tombol "Kirim Surat" di menu navigasi atau klik tombol di bawah halaman ini.</p>
                <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="step-card">
                <div class="step-num">2</div>
                <div class="step-icon"><i class="fas fa-file-alt"></i></div>
                <h4>Isi Formulir Surat</h4>
                <p>Lengkapi data: nomor surat, pengirim, perihal, isi surat dan lampirkan file (opsional).</p>
                <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="step-card">
                <div class="step-num">3</div>
                <div class="step-icon"><i class="fas fa-paper-plane"></i></div>
                <h4>Kirim &amp; Tunggu Review</h4>
                <p>Surat masuk ke disposisi dan akan direview oleh Admin atau Sekretaris Umum HMIF-FT UNISMUH.</p>
                <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="step-card">
                <div class="step-num">4</div>
                <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                <h4>Konfirmasi Diterima</h4>
                <p>Setelah disetujui, surat masuk ke arsip resmi dan Anda mendapat konfirmasi via email.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="cta-section">
    <div class="cta-box">
        <img src="assets/Logo_HMIF FT.png" alt="Logo HMIF-FT UNISMUH">  
        <h2>Kirim Surat ke HMIF-FT UNISMUH</h2>
        <p>
            Apakah lembaga Anda ingin mengirim surat undangan, permohonan kerjasama,
            atau pemberitahuan kepada HMIF-FT UNISMUH Makassar?
            Gunakan platform digital kami untuk proses yang lebih cepat dan terdokumentasi.
        </p>
        <a href="kirim_surat.php" class="btn-cta">
            <i class="fas fa-paper-plane"></i> Kirim Surat Sekarang
        </a>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <img src="assets/Logo_HMIF FT.png" alt="Logo HMIF-FT UNISMUH">
            <h3>HMIF-FT UNISMUH</h3>
            <p>Fakultas Teknik<br>Universitas Muhammadiyah Makassar<br>Jl. Sultan Alauddin No.259, Makassar</p>
        </div>
        <div class="footer-col">
            <h4>Menu</h4>
            <ul>
                <li><a href="home.php">Beranda</a></li>
                <li><a href="home.php#tentang">Tentang HMIF</a></li>
                <li><a href="home.php#visi-misi">Visi &amp; Misi</a></li>
                <li><a href="home.php#struktur">Struktur Organisasi</a></li>
                <li><a href="kirim_surat.php">Kirim Surat</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Akses Admin</h4>
            <ul>
                <li><a href="login.php">Login Admin</a></li>
                <li><a href="home.php#cara-kirim">Panduan Kirim Surat</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© <?= date('Y') ?> <strong style="color:var(--accent)">HMIF-FT UNISMUH</strong> — FT UNISMUH Makassar. All rights reserved.</span>
        <span>Sistem Administrasi Persuratan v1.0</span>
    </div>
</footer>

<script>
// Navbar scroll effect
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 30);
});
// Smooth active nav
const links = document.querySelectorAll('.nav-link[href^="home.php#"]');
window.addEventListener('scroll', () => {
    const pos = window.scrollY + 100;
    document.querySelectorAll('section[id]').forEach(sec => {
        if (pos >= sec.offsetTop && pos < sec.offsetTop + sec.offsetHeight) {
            links.forEach(l => l.classList.remove('active'));
            const active = document.querySelector(`.nav-link[href="home.php#${sec.id}"]`);
            if (active) active.classList.add('active');
        }
    });
});
</script>
</body>
</html>
