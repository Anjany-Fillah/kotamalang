<!-- kontak.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kontak Kami - Jalan Jalan Kota Malang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="navbar">
            <h1>Jalan Jalan Kota Malang</h1>
            <nav>
                <a href="artikel_pengunjung.php">Beranda</a>
                <a href="tentang.php">Tentang</a>
                <a href="kontak.php" class="active">Kontak</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <main class="main-content">
            <div class="konten-artikel">
                <div class="card">
                    <h2>Hubungi Kami</h2>
                    <p>Jika Anda memiliki pertanyaan, kritik, atau saran, silakan isi formulir di bawah ini:</p>

                    <!-- Form kirim ke proses_kontak.php -->
                    <form action="proses_kontak.php" method="post" class="contact-form">
                        <label for="nama">Nama:</label>
                        <input type="text" id="nama" name="nama" required>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>

                        <label for="subjek">Subjek:</label>
                        <input type="text" id="subjek" name="subjek" required>

                        <label for="pesan">Pesan:</label>
                        <textarea id="pesan" name="pesan" rows="5" required></textarea>

                        <button type="submit" class="btn-small">Kirim</button>
                    </form>

                    <hr style="margin: 30px 0;">

                    <h3>Kontak Langsung</h3>
                    <p>Email: <a href="mailto:fillahanjany1605@gmail.com">fillahanjany1605@gmail.com</a></p>
                    <p>Instagram: <a href="https://instagram.com/phipus.pill" target="_blank">@phipus.pill</a></p>
                    <p>WhatsApp: <a href="https://wa.me/6281330727585" target="_blank">+62 813-3072-7585</a></p>
                </div>
            </div>
        </main>

        <aside class="sidebar">
            <div class="tentang-box">
                <h4>Info Tambahan</h4>
                <p>Kami sangat menghargai setiap masukan dan pertanyaan Anda. Kami akan berusaha merespons secepat mungkin.</p>
            </div>
        </aside>
    </div>

    <footer>
        <p>&copy; Jalan Jalan Kota Malang 2025</p>
    </footer>

</body>
</html>
