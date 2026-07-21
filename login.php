<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit();
}

$error = '';
$msg = '';

if (isset($_GET['msg']) && $_GET['msg'] === 'logout') {
    $msg = 'Anda telah berhasil keluar. Sampai jumpa!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['username']= $user['username'];
            $_SESSION['role']    = $user['role'];
            header('Location: ' . BASE_URL . 'dashboard.php');
            exit();
        } else {
            $error = 'Username atau password salah. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0f4c81;
            --primary-light: #1a6bb5;
            --accent: #e8b84b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f4f8;
        }
        .login-left {
            flex: 1;
            background: linear-gradient(145deg, #08305a 0%, #0f4c81 50%, #1a6bb5 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            top: -150px; right: -150px;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: rgba(232,184,75,.08);
            bottom: -80px; left: -80px;
        }
        .login-left-content { position: relative; z-index: 1; text-align: center; max-width: 400px; }
        .brand-logo {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, var(--accent), #f0a500);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Sora', sans-serif;
            font-size: 32px; font-weight: 800;
            color: var(--primary);
            margin: 0 auto 24px;
            box-shadow: 0 8px 24px rgba(232,184,75,.35);
        }
        .login-left h1 {
            font-family: 'Sora', sans-serif;
            font-size: 28px; font-weight: 800;
            color: #fff; margin-bottom: 8px;
        }
        .login-left .subtitle {
            font-size: 14px; color: rgba(255,255,255,.6);
            line-height: 1.6;
        }
        .divider-line {
            width: 60px; height: 3px;
            background: var(--accent);
            border-radius: 2px;
            margin: 20px auto;
        }
        .feature-list {
            list-style: none;
            text-align: left;
            margin-top: 32px;
        }
        .feature-list li {
            display: flex; align-items: center; gap: 12px;
            color: rgba(255,255,255,.75);
            font-size: 13px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .feature-list li i {
            color: var(--accent);
            width: 16px;
        }

        /* RIGHT: FORM */
        .login-right {
            width: 480px;
            display: flex; align-items: center; justify-content: center;
            padding: 48px 40px;
            background: #fff;
        }
        .login-form-wrap { width: 100%; max-width: 380px; }
        .login-form-wrap h2 {
            font-family: 'Sora', sans-serif;
            font-size: 26px; font-weight: 800;
            color: var(--primary);
            margin-bottom: 6px;
        }
        .login-form-wrap p {
            font-size: 13px; color: #718096;
            margin-bottom: 32px;
        }
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 12.5px; font-weight: 700;
            color: #374151; margin-bottom: 7px;
            letter-spacing: .02em; text-transform: uppercase;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #9ca3af; font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-family: inherit; font-size: 14px;
            outline: none;
            transition: all .2s;
            background: #f9fafb;
        }
        .form-control:focus {
            border-color: var(--primary-light);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26,107,181,.1);
        }
        .toggle-pass {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #9ca3af; cursor: pointer; font-size: 14px;
            padding: 0;
        }
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            border: none; border-radius: 10px;
            font-family: inherit; font-size: 15px; font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all .2s;
            box-shadow: 0 4px 14px rgba(15,76,129,.3);
            margin-top: 24px;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15,76,129,.4);
        }
        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-error { background: #fff5f5; border: 1px solid #fed7d7; color: #9b2c2c; }
        .alert-success { background: #f0fff4; border: 1px solid #9ae6b4; color: #276749; }
        .demo-info {
            margin-top: 24px;
            padding: 14px;
            background: #f0f4f8;
            border-radius: 8px;
            font-size: 12px;
            color: #718096;
        }
        .demo-info strong { color: var(--primary); }

        @media (max-width: 800px) {
            .login-left { display: none; }
            .login-right { width: 100%; }
        }
    </style>
</head>
<body>

<div class="login-left">
    <div class="login-left-content">
        <div class="brand-logo">HI</div>
        <h1>HMIF-FT UNISMUH</h1>
        <div class="divider-line"></div>
        <p class="subtitle">Fakultas Teknik<br>Universitas Muhammadiyah Makassar</p>
        <ul class="feature-list">
            <li><i class="fas fa-check-circle"></i> Manajemen Surat Masuk & Keluar</li>
            <li><i class="fas fa-check-circle"></i> Tracking Status Surat</li>
            <li><i class="fas fa-check-circle"></i> Arsip Digital Dokumen</li>
            <li><i class="fas fa-check-circle"></i> Laporan & Statistik</li>
            <li><i class="fas fa-check-circle"></i> Multi User & Role</li>
        </ul>
    </div>
</div>

<div class="login-right">
    <div class="login-form-wrap">
        <h2>Selamat Datang</h2>
        <p>Masuk ke Sistem Administrasi Persuratan HMIF-FT UNISMUH</p>

        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
        <?php endif; ?>
        <?php if ($msg): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $msg ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" class="form-control"
                           placeholder="Masukkan username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           required autocomplete="username">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="passInput" class="form-control"
                           placeholder="Masukkan password" required autocomplete="current-password">
                    <button type="button" class="toggle-pass" onclick="togglePass()">
                        <i class="fas fa-eye" id="passIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
            </button>
        </form>
        <div style="text-align:center;margin-top:16px">
            <a href="home.php" style="font-size:12.5px;color:#9ca3af;text-decoration:none">
                <i class="fas fa-arrow-left" style="margin-right:4px"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('passInput');
    const icon  = document.getElementById('passIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
</body>
</html>
