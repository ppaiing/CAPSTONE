<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$id     = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';
$d      = $conn->query("SELECT * FROM disposisi WHERE id=$id")->fetch_assoc();

if (!$d || !in_array($action, ['terima','tolak'])) {
    header('Location: disposisi.php');
    exit();
}

if ($d['status'] !== 'menunggu') {
    header('Location: disposisi.php?msg=sudah_diproses');
    exit();
}

if ($action === 'terima') {
    // Update status disposisi
    $conn->query("UPDATE disposisi SET status='diterima' WHERE id=$id");

    // Pindahkan ke surat_masuk
    $nomor    = $conn->real_escape_string($d['nomor_surat']);
    $tgl      = $conn->real_escape_string($d['tanggal_surat']);
    $today    = date('Y-m-d');
    $pengirim = $conn->real_escape_string($d['pengirim'] . ' — ' . $d['instansi']);
    $perihal  = $conn->real_escape_string($d['perihal']);
    $kategori = $conn->real_escape_string($d['kategori']);
    $file     = $conn->real_escape_string($d['file_surat'] ?? '');
    $user_id  = (int)$_SESSION['user_id'];

    $conn->query("INSERT INTO surat_masuk
        (nomor_surat, tanggal_surat, tanggal_terima, pengirim, perihal, kategori, status, keterangan, file_surat, dari_disposisi, user_id)
        VALUES
        ('$nomor', '$tgl', '$today', '$pengirim', '$perihal', '$kategori', 'belum_diproses',
        'Surat diterima melalui sistem disposisi online dari " . $conn->real_escape_string($d['instansi']) . "',
        " . ($file ? "'$file'" : "NULL") . ", $id, $user_id)");

    header('Location: disposisi.php?msg=diterima');
    exit();

} elseif ($action === 'tolak') {
    $conn->query("UPDATE disposisi SET status='ditolak' WHERE id=$id");
    header('Location: disposisi.php?msg=ditolak');
    exit();
}

header('Location: disposisi.php');
exit();
