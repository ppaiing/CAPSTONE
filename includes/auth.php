<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

function currentUser() {
    return $_SESSION ?? [];
}

function logout() {
    session_destroy();
    header('Location: ' . BASE_URL . 'login.php?msg=logout');
    exit();
}
?>
