<?php
session_start();
require 'functions.php';

// LOGIN
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Using prepared statements for security (good practice even for simple login)
    $stmt = $conn->prepare("SELECT id, nickname, email FROM author WHERE LOWER(email) = LOWER(?) AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['login'] = true;
        $_SESSION['nickname'] = $row['nickname'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['author_id'] = $row['id']; // <--- ADDED THIS LINE
        header("Location: index.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}

// REGISTRASI
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $ulangi  = $_POST['ulangi_password'];

    if ($password !== $ulangi) {
        $reg_error = "Konfirmasi password tidak cocok!";
    } else {
        $stmt_cek = $conn->prepare("SELECT * FROM author WHERE email = ?");
        $stmt_cek->bind_param("s", $email);
        $stmt_cek->execute();
        $cek_result = $stmt_cek->get_result();

        if ($cek_result->num_rows > 0) {
            $reg_error = "Email sudah terdaftar!";
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO author (nickname, email, password) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $username, $email, $password); // Password should be hashed in production!
            if ($stmt_insert->execute()) {
                $reg_success = "Registrasi berhasil! Silakan login.";
            } else {
                $reg_error = "Registrasi gagal. Silakan coba lagi. " . $stmt_insert->error;
            }
        }
    }
}

// RESET PASSWORD
if (isset($_POST['reset'])) {
    $email = strtolower(trim($_POST['email']));
    $newpass = $_POST['new_password']; // Password should be hashed in production!

    $stmt_cek = $conn->prepare("SELECT * FROM author WHERE email = ?");
    $stmt_cek->bind_param("s", $email);
    $stmt_cek->execute();
    $cek_result = $stmt_cek->get_result();

    if ($cek_result->num_rows === 1) {
        $stmt_update = $conn->prepare("UPDATE author SET password = ? WHERE email = ?");
        $stmt_update->bind_param("ss", $newpass, $email);
        if ($stmt_update->execute()) {
            $reset_success = "Password berhasil diperbarui!";
        } else {
            $reset_error = "Gagal memperbarui password. " . $stmt_update->error;
        }
    } else {
        $reset_error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login & Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(to right, #3f0d12, #a71d31, #5c3a2e);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
        }
        .welcome-text {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
        }
        .welcome-text h1 {
            font-size: 2rem;
            font-weight: bold;
        }
        .welcome-text p {
            font-size: 1.1rem;
        }
        .form-card {
            background-color: #fff;
            color: #333;
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 12px 25px rgba(0,0,0,0.3);
            border-radius: 30px;
            transition: transform 0.3s ease;
            overflow: hidden;
        }
        .form-card:hover {
            transform: scale(1.02);
        }
        .card-header-custom {
            background: linear-gradient(to right, #6e1414, #a93226);
            color: white;
            font-size: 1.3rem;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .login-input {
            height: 45px;
            font-size: 1rem;
        }
        .login-btn {
            background-color: #7b241c;
            color: #fff;
            font-weight: bold;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background-color: #922b21;
        }
        .switch-link {
            color: #7b241c;
            font-weight: bold;
            cursor: pointer;
        }
        .switch-link:hover {
            text-decoration: underline;
        }
        #registerForm, #resetForm {
            display: none;
        }
    </style>
</head>
<body>
    <div class="welcome-text">
        <h1>Selamat Datang di Admin</h1>
        <p>Artikel Menarik dari Kota Malang — Temukan destinasi wisata dan cerita seru lainnya</p>
    </div>

    <div class="form-card" id="loginForm">
        <div class="card-header-custom">Login</div>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control login-input" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control login-input" required>
            </div>
            <button type="submit" name="login" class="btn login-btn w-100">Login</button>
            <div class="text-center mt-3">
                <span class="switch-link" onclick="showReset()">Lupa Password?</span><br>
                Belum punya akun? <span class="switch-link" onclick="showRegister()">Daftar</span>
            </div>
        </form>
    </div>

    <div class="form-card" id="registerForm">
        <div class="card-header-custom">Registrasi</div>
        <?php if (isset($reg_error)) echo "<div class='alert alert-danger'>$reg_error</div>"; ?>
        <?php if (isset($reg_success)) echo "<div class='alert alert-success'>$reg_success</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control login-input" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control login-input" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control login-input" required>
            </div>
            <div class="mb-3">
                <label>Ulangi Password</label>
                <input type="password" name="ulangi_password" class="form-control login-input" required>
            </div>
            <button type="submit" name="register" class="btn login-btn w-100">Daftar</button>
            <div class="text-center mt-3">
                Sudah punya akun? <span class="switch-link" onclick="showLogin()">Login</span>
            </div>
        </form>
    </div>

    <div class="form-card" id="resetForm">
        <div class="card-header-custom">Reset Password</div>
        <?php if (isset($reset_error)) echo "<div class='alert alert-danger'>$reset_error</div>"; ?>
        <?php if (isset($reset_success)) echo "<div class='alert alert-success'>$reset_success</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control login-input" required>
            </div>
            <div class="mb-3">
                <label>Password Baru</label>
                <input type="password" name="new_password" class="form-control login-input" required>
            </div>
            <button type="submit" name="reset" class="btn login-btn w-100">Ubah Password</button>
            <div class="text-center mt-3">
                Kembali ke <span class="switch-link" onclick="showLogin()">Login</span>
            </div>
        </form>
    </div>

    <script>
        function showLogin() {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('resetForm').style.display = 'none';
        }

        function showRegister() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
            document.getElementById('resetForm').style.display = 'none';
        }

        function showReset() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('resetForm').style.display = 'block';
        }

        window.onload = showLogin;
    </script>
</body>
</html>