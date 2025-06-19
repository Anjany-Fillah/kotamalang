<?php
session_start();
require 'functions.php';

// Cek apakah user sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Pastikan ada ID penulis yang dikirim
if (!isset($_GET['id'])) {
    header("Location: penulis.php");
    exit;
}

$id = $_GET['id'];

if (deleteAuthor($id)) {
    echo "<script>alert('Penulis berhasil dihapus!');</script>";
    echo "<script>window.location.href = 'penulis.php';</script>";
} else {
    echo "<script>window.location.href = 'penulis.php';</script>"; // Pesan alert sudah ditangani di functions.php
}
exit;
?>