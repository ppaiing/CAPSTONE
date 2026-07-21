<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$type = $_GET['type'] ?? '';
$id   = (int)($_GET['id'] ?? 0);

if ($type === 'masuk') {
    $s = $conn->query("SELECT file_surat FROM surat_masuk WHERE id=$id")->fetch_assoc();
    if ($s && $s['file_surat'] && file_exists('uploads/surat/' . $s['file_surat'])) {
        unlink('uploads/surat/' . $s['file_surat']);
    }
    $conn->query("DELETE FROM surat_masuk WHERE id=$id");
    header('Location: surat_masuk.php?msg=deleted');
} elseif ($type === 'keluar') {
    $s = $conn->query("SELECT file_surat FROM surat_keluar WHERE id=$id")->fetch_assoc();
    if ($s && $s['file_surat'] && file_exists('uploads/surat/' . $s['file_surat'])) {
        unlink('uploads/surat/' . $s['file_surat']);
    }
    $conn->query("DELETE FROM surat_keluar WHERE id=$id");
    header('Location: surat_keluar.php?msg=deleted');
} else {
    header('Location: dashboard.php');
}
exit();
