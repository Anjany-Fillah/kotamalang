<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];
$articles = getAllArticles();

function isActive($filename) {
    return basename($_SERVER['PHP_SELF']) == $filename ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Artikel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <style>
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


    <div class="main-content">
        <h2>Artikel</h2>
        <p>Silakan kelola artikel.</p>

        <a href="tambah_artikel.php" class="btn btn-primary mb-3">
            <i class="fas fa-file-lines"></i> Artikel Baru
        </a>

        <table id="tabelArtikel" class="table table-bordered table-hover">
            <thead class="table-secondary">
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($articles)): ?>
                    <tr><td colspan="7" class="text-center">Belum ada artikel.</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($articles as $article): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date('j F Y H:i', strtotime($article['date'])); ?></td>
                            <td><?= htmlspecialchars($article['title']); ?></td>
                            <td><?= htmlspecialchars($article['categories_name'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($article['authors_nickname'] ?? '-'); ?></td>
                            <td>
                                <?php
                                $imgPath = 'img/' . $article['picture'];
                                if (!empty($article['picture']) && file_exists($imgPath)): ?>
                                    <img src="<?= $imgPath ?>" alt="Gambar Artikel" width="150">
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada gambar</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="edit_artikel.php?id=<?= $article['id']; ?>" class="btn btn-sm btn-warning" title="Ubah">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus_artikel.php?id=<?= $article['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus artikel ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tabelArtikel').DataTable({
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
                language: {
                    search: "",
                    searchPlaceholder: "Cari artikel...",
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

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const topbar = document.querySelector('.topbar');

            if (sidebar.style.display === 'none') {
                sidebar.style.display = 'flex';
                mainContent.style.margin = '80px 30px 30px 270px';
                mainContent.style.width = 'calc(100% - 300px)';
                topbar.style.left = '240px';
            } else {
                sidebar.style.display = 'none';
                mainContent.style.margin = '80px 30px 30px 30px';
                mainContent.style.width = 'calc(100% - 60px)';
                topbar.style.left = '0';
            }
        }
    </script>
</body>
</html>
