<?php
session_start();
require 'functions.php';
date_default_timezone_set('Asia/Jakarta');


$email = $_SESSION['email'];
$authors = query("SELECT id, nickname, email FROM author ORDER BY nickname ASC");

function isActive($filename) {
    return basename($_SERVER['PHP_SELF']) === $filename ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Penulis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <style>
        /* Sidebar, Topbar, Layout Style (dipertahankan) */
        body {
            display: flex;
            font-family: 'Segoe UI', sans-serif;
            background: white;
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

        .btn-primary {
            background-color: #6b0f1a;
            border: none;
        }

        .btn-primary:hover {
            background-color: #a0003f;
        }

        table th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h5>MENU UTAMA</h5>
            <a href="index.php" class="<?= isActive('index.php') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="artikel.php" class="<?= isActive('artikel.php') ?>"><i class="fas fa-file-alt"></i> Artikel</a>
            <a href="kategori.php" class="<?= isActive('kategori.php') ?>"><i class="fas fa-bookmark"></i> Kategori</a>
            <a href="penulis.php" class="<?= isActive('penulis.php') ?>"><i class="fas fa-user"></i> Penulis</a>
        </div>
        <div class="footer">
            Logged in as:<br>
            <?= htmlspecialchars($email); ?>
        </div>
    </div>

    <!-- Topbar -->
<div class="topbar">
    <i class="fas fa-bars" onclick="toggleSidebar()"></i>
    <div class="dropdown">
        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle fa-lg"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
    </div>
</div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Data Penulis</h2>
        <p>Kelola informasi penulis yang terdaftar dalam sistem blog.</p>

        <a href="tambah_penulis.php" class="btn btn-primary mb-3">
            <i class="fas fa-plus me-1"></i> Tambah Penulis
        </a>

        <div class="table-responsive">
            <table id="tabelPenulis" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nickname</th>
                        <th>Email</th>
                        <th style="width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($authors)): ?>
                        <?php $no = 1; foreach ($authors as $author): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($author['nickname']); ?></td>
                            <td><?= htmlspecialchars($author['email']); ?></td>
                            <td class="text-center">
                                <a href="edit_penulis.php?id=<?= $author['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <a href="hapus_penulis.php?id=<?= $author['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus penulis ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Belum ada penulis yang terdaftar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tabelPenulis').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Cari penulis...",
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
