<?php
session_start();
require 'functions.php';

// Cek apakah user sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Pastikan ada ID kategori yang dikirim
if (!isset($_GET['id'])) {
    // Berikan pesan error jika ID tidak ada
    echo "<script>alert('ID kategori tidak ditemukan. Tidak dapat menghapus.');</script>";
    echo "<script>window.location.href = 'kategori.php';</script>";
    exit;
}

$id = $_GET['id'];

// Panggil fungsi deleteCategory dan tangkap hasilnya
$deleteResult = deleteCategory($id);

// Periksa hasil dari fungsi deleteCategory
if ($deleteResult === true) {
    echo "<script>alert('Kategori berhasil dihapus!');</script>";
} else {
    // Jika hasilnya bukan true, berarti itu adalah pesan error (string)
    echo "<script>alert('" . $deleteResult . "');</script>";
}

// Selalu redirect kembali ke halaman kategori setelah menampilkan alert
echo "<script>window.location.href = 'kategori.php';</script>";
exit;
?>