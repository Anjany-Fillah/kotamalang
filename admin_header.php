<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$email = $_SESSION['email'] ?? 'Guest';

function isActive($filename) {
    return basename($_SERVER['PHP_SELF']) == $filename ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <style>
        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background-color: rgb(0, 0, 0);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            z-index: 1001;
            transition: transform 0.3s ease;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            display: block;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar h5 {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .sidebar .footer {
            font-size: 12px;
            margin-top: auto;
        }

        /* TOPBAR */
        .topbar {
            position: fixed;
            top: 0;
            left: 240px;
            right: 0;
            height: 60px;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            transition: left 0.3s ease;
        }

        .toggle-icon {
            font-size: 20px;
            cursor: pointer;
        }

        /* DROPDOWN */
        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            background: none;
            border: none;
            cursor: pointer;
        }

        .profile-icon {
            font-size: 32px;
            color: #555;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            min-width: 160px;
            z-index: 1001;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background-color: #f5f5f5;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 270px;
            margin-top: 80px;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-hidden .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-hidden .topbar {
            left: 0;
        }

        body.sidebar-hidden .main-content {
            margin-left: 30px;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .topbar {
                left: 0;
            }

            .main-content {
                margin-left: 30px;
            }
        }
    </style>
</head>
<body class="sidebar-visible">

<!-- ====== SIDEBAR ====== -->
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

<!-- ====== TOPBAR ====== -->
<div class="topbar">
    <i class="fas fa-bars toggle-icon" onclick="toggleSidebar()"></i>
    <div class="dropdown">
        <button class="dropdown-toggle">
            <i class="fas fa-user-circle profile-icon"></i>
        </button>
        <div class="dropdown-menu">
            <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </div>
    </div>
</div>

<!-- ====== TOGGLE SIDEBAR SCRIPT ====== -->
<script>
function toggleSidebar() {
    document.body.classList.toggle('sidebar-hidden');
    document.body.classList.toggle('sidebar-visible');
}
</script>
