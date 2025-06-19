<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$nickname = $_SESSION['nickname'] ?? 'Pengguna';
$email = $_SESSION['email'] ?? 'email@example.com';

function isActive($filename) {
    return basename($_SERVER['PHP_SELF']) === $filename ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            font-family: 'Segoe UI', sans-serif;
            background: url('img/wisata di kota malang.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0,0,0,0.6);
            z-index: 0;
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
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar h5 {
            padding: 20px 15px;
            margin: 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            font-size: 1.1rem;
        }

        .sidebar a {
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background 0.2s, padding-left 0.2s;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            padding-left: 25px;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .sidebar .footer {
            padding: 15px;
            font-size: 0.85rem;
            background-color: rgba(0, 0, 0, 0.1);
            border-top: 1px solid rgba(255,255,255,0.2);
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
            position: relative;
            z-index: 1;
            color: white;
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

        .card {
            border: none;
            border-radius: 0.5rem;
            transition: transform 0.2s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            font-size: 1.25rem;
            font-weight: bold;
            padding: 1.5rem;
        }

        .card-footer {
            background-color: rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        footer.dashboard-footer {
            background: linear-gradient(90deg, #6b0f1a, #b91372);
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-left: 240px;
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
        <h2>Dashboard</h2>
        <p>Selamat datang, <strong><?= htmlspecialchars($nickname); ?></strong>!</p>
        <div class="row">
            <div class="col-md-4 mb-4">
                <a href="artikel.php" class="text-decoration-none">
                    <div class="card text-white" style="background-color: #6a1b1a;">
                        <div class="card-body">Artikel</div>
                        <div class="card-footer">
                            <span>12 artikel</span> <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="kategori.php" class="text-decoration-none">
                    <div class="card text-white" style="background-color: #6a1b1a;">
                        <div class="card-body">Kategori</div>
                        <div class="card-footer">
                            <span>4 kategori</span> <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="penulis.php" class="text-decoration-none">
                    <div class="card text-white" style="background-color: #6a1b1a;">
                        <div class="card-body">Penulis</div>
                        <div class="card-footer">
                            <span>1 penulis</span> <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
