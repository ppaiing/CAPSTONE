<?php
// =====================================================
// Konfigurasi Database
// Sesuaikan dengan pengaturan MySQL Anda
// =====================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Ganti dengan username MySQL Anda
define('DB_PASS', '');            // Ganti dengan password MySQL Anda
define('DB_NAME', 'hmif_nexus'); // Pastikan database ini sudah dibuat (jalankan database.sql)

// Koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:40px;background:#fff1f0;border:1px solid #ffa39e;border-radius:8px;max-width:500px;margin:50px auto;">
        <h3 style="color:#cf1322;">⚠️ Koneksi Database Gagal</h3>
        <p>Pastikan:</p>
        <ul>
            <li>MySQL/MariaDB sudah berjalan</li>
            <li>Konfigurasi DB_HOST, DB_USER, DB_PASS sudah benar di <code>includes/config.php</code></li>
            <li>Database <strong>HMIF-Nexus</strong> sudah dibuat (jalankan <code>database.sql</code>)</li>
        </ul>
        <small style="color:#888;">Error: ' . $conn->connect_error . '</small>
    </div>');
}

$conn->set_charset("utf8mb4");

// Base URL aplikasi
define('BASE_URL', '/hmif_nexus/');
define('APP_NAME', 'HMIF-FT UNISMUH');
define('APP_SUBTITLE', 'Sistem Administrasi hmif_nexus');
define('FAKULTAS', 'Fakultas Teknik UNISMUH Makassar');

// Fungsi helper
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

function formatTanggal($tanggal) {
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $parts = explode('-', $tanggal);
    return $parts[2] . ' ' . $bulan[$parts[1]] . ' ' . $parts[0];
}

function badgeStatus($status) {
    $map = [
        'belum_diproses' => ['Belum Diproses', '#ff4d4f', '#fff1f0'],
        'diproses'       => ['Diproses', '#1677ff', '#e6f4ff'],
        'selesai'        => ['Selesai', '#52c41a', '#f6ffed'],
        'draft'          => ['Draft', '#faad14', '#fffbe6'],
        'dikirim'        => ['Dikirim', '#1677ff', '#e6f4ff'],
    ];
    $s = $map[$status] ?? [$status, '#666', '#f5f5f5'];
    return "<span style='background:{$s[2]};color:{$s[1]};padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid {$s[1]}33'>{$s[0]}</span>";
}

function badgeKategori($kat) {
    $map = [
        'undangan'       => ['Undangan', '#722ed1', '#f9f0ff'],
        'permohonan'     => ['Permohonan', '#13c2c2', '#e6fffb'],
        'pemberitahuan'  => ['Pemberitahuan', '#eb2f96', '#fff0f6'],
        'keputusan'      => ['Keputusan', '#fa8c16', '#fff7e6'],
        'lainnya'        => ['Lainnya', '#8c8c8c', '#fafafa'],
    ];
    $k = $map[$kat] ?? [$kat, '#666', '#f5f5f5'];
    return "<span style='background:{$k[2]};color:{$k[1]};padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;border:1px solid {$k[1]}44'>{$k[0]}</span>";
}
?>
