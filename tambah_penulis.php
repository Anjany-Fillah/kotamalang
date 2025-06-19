<?php
session_start();
require_once 'functions.php';

// Cek apakah user sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Logika untuk menangani penambahan penulis (jika tombol simpan diklik)
if (isset($_POST['btn_simpan'])) {
    if (addAuthor($_POST)) {
        echo "<script>alert('Penulis berhasil ditambahkan!');</script>";
        echo "<script>window.location.href = 'penulis.php';</script>"; // Redirect ke halaman daftar penulis
    } else {
        echo "<script>alert('Gagal menambahkan penulis!');</script>";
        // Tetap di halaman ini agar user bisa mencoba lagi
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Form untuk menambah penulis baru" />
    <meta name="author" content="Nama Anda" />
    <title>Tambah Penulis Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
    <style type="text/css">
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-4">Tambah Penulis Baru</h2>
        <hr>
        <form method="POST">
            <div class="mb-3 mt-3">
                <label for="nickname" class="form-label">Nama Panggilan:</label>
                <input type="text" class="form-control" id="nickname" name="nickname" required placeholder="Masukkan nama panggilan penulis">
            </div>
            <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Masukkan email penulis">
            </div>
            <div class="mb-3 mt-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password penulis">
            </div>
            <div class="mb-3 mt-3 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary" name="btn_simpan"><i class="bi bi-save"></i> Simpan</button>
                <button type="reset" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Reset</button>
                <a href="penulis.php" class="btn btn-danger"><i class="bi bi-x-lg"></i> Batal</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>