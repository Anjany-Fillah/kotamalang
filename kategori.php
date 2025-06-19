<?php
require 'functions.php'; // Pastikan fungsi getAllCategories() sudah ada di sini

session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'] ?? 'Guest';
$categories = getAllCategories();

function isActive($filename) {
    return basename($_SERVER['PHP_SELF']) === $filename ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <style>
        body {
            display: flex;
            font-family: 'Segoe UI', sans-serif;
            background: white
            background-size: cover;
            margin: 0;
        }

        .sidebar {
            width: 240px;
            background: linear-gradient(180deg, #6b0f1a, #b91372);
            color: white;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1001;
        }

        .sidebar h5 {
            padding: 20px 15px;
            margin: 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar a {
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background 0.2s;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background-color: rgba(255,255,255,0.15);
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .sidebar .footer {
            padding: 15px;
            font-size: 0.9em;
            background-color: rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .topbar {
            position: fixed;
            left: 240px;
            right: 0;
            height: 60px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            z-index: 1000;
        }

        .main-content {
            margin-left: 240px;
            margin-top: 80px;
            padding: 30px;
            width: calc(100% - 240px);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
            min-width: 160px;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .btn-primary {
            background-color: #6b0f1a;
            border: none;
        }

        .btn-primary:hover {
            background-color: #a0003f;
        }

        table th {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <h5>MENU UTAMA</h5>
            <a href="index.php" class="<?= isActive('index.php') ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="artikel.php" class="<?= isActive('artikel.php') ?>">
                <i class="fas fa-file-alt"></i> Artikel
            </a>
            <a href="kategori.php" class="<?= isActive('kategori.php') ?>">
                <i class="fas fa-bookmark"></i> Kategori
            </a>
            <a href="penulis.php" class="<?= isActive('penulis.php') ?>">
                <i class="fas fa-user"></i> Penulis
            </a>
        </div>
        <div class="footer">
            Logged in as:<br>
            <?= htmlspecialchars($email); ?>
        </div>
    </div>

    <div class="topbar">
        <i class="fas fa-bars"></i>
        <div class="dropdown">
            <button class="btn btn-link dropdown-toggle" type="button">
                <i class="fas fa-user-circle fa-lg"></i>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h2>Kategori</h2>
        <p>Kelola daftar kategori artikel.</p>

        <a href="tambah_kategori.php" class="btn btn-primary mb-3">
            <i class="fas fa-plus me-1"></i> Tambah Kategori
        </a>

        <table id="tabelKategori" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada kategori.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($categories as $kategori): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($kategori['name']); ?></td>
                            <td><?= htmlspecialchars($kategori['description']); ?></td>
                            <td class="text-center">
                                <a href="edit_kategori.php?id=<?= $kategori['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-pen"></i> Ubah
                                </a>
                                <a href="hapus_kategori.php?id=<?= $kategori['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tabelKategori').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Cari...",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    zeroRecords: "Tidak ditemukan data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Tidak ada entri",
                    infoFiltered: "(difilter dari _MAX_ entri)",
                    paginate: {
                        next: "→",
                        previous: "←"
                    }
                }
            });
        });
    </script>
</body>
</html>
