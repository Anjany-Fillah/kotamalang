<?php
session_start();
require 'functions.php';

// Cek apakah user sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Pastikan ada ID artikel yang dikirim
if (!isset($_GET['id'])) {
    header("Location: artikel.php");
    exit;
}

$id = $_GET['id'];

if (deleteArticle($id)) {
    echo "<script>alert('Artikel berhasil dihapus!');</script>";
    echo "<script>window.location.href = 'artikel.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus artikel!');</script>";
    echo "<script>window.location.href = 'artikel.php';</script>";
}
exit;
?>