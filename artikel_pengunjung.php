<?php
require '../admin/functions.php';
date_default_timezone_set('Asia/Jakarta');

$articleDetail = null;
$articles = [];
$relatedArticles = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $articleDetail = getArticleById($id);

    if ($articleDetail && !empty($articleDetail['categories_name'])) {
        $relatedArticles = getRelatedArticles($articleDetail['categories_name'], $id);
    }
} elseif (isset($_GET['q'])) {
    $keyword = $_GET['q'];
    $articles = searchArticles($keyword);
} elseif (isset($_GET['k'])) {
    $kategori = $_GET['k'];
    $articles = getArticlesByCategory($kategori);
} else {
    $articles = getAllArticles();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jalan Jalan Kota Malang</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="navbar">
        <div><strong>Jalan Jalan Kota Malang</strong></div>
        <nav>
            <a href="artikel_pengunjung.php">Beranda</a>
            <a href="tentang.php">Tentang</a>
            <a href="kontak.php">Kontak</a>
        </nav>
    </div>
</header>

<section class="hero-text">
    <h2 class="hero-title">Artikel Menarik dari Kota Malang</h2>
    <p class="hero-subtitle">Temukan destinasi wisata dan cerita seru lainnya</p>
</section>

<div class="container">
    <div class="main-content">
        <?php if ($articleDetail): ?>
            <div class="konten-artikel">
                <div class="card-with-image">
                    <?php if (!empty($articleDetail['picture'])): ?>
                        <img src="../admin/img/<?= htmlspecialchars($articleDetail['picture']) ?>" alt="<?= htmlspecialchars($articleDetail['title']) ?>">
                    <?php endif; ?>
                    <div class="card-text">
                        <p class="date"><?= strftime("%A, %d %B %Y | %H:%M", strtotime($articleDetail['date'])) ?></p>
                        <h2><?= htmlspecialchars($articleDetail['title']) ?></h2>
                        <p><strong>Penulis:</strong> <?= htmlspecialchars($articleDetail['authors_nickname'] ?? '-') ?></p>
                        <p><strong>Kategori:</strong> <?= htmlspecialchars($articleDetail['categories_name'] ?? '-') ?></p>
                        <div><?= $articleDetail['content'] ?></div>
                        <br>
                        <a href="artikel_pengunjung.php" class="btn-small">← Kembali</a>
                    </div>
                </div>
            </div>
        <?php elseif (!empty($articles)): ?>
            <?php $featured = array_shift($articles); ?>
            <div class="featured-article">
                <?php if (!empty($featured['picture'])): ?>
                    <img src="../admin/img/<?= htmlspecialchars($featured['picture']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>">
                <?php endif; ?>
                <div class="featured-text">
                    <p class="date"><?= strftime("%A, %d %B %Y | %H:%M", strtotime($featured['date'])) ?></p>
                    <h2><?= htmlspecialchars($featured['title']) ?></h2>
                    <p><strong>Penulis:</strong> <?= htmlspecialchars($featured['authors_nickname'] ?? '-') ?></p>
                    <p><strong>Kategori:</strong> <?= htmlspecialchars($featured['categories_name'] ?? '-') ?></p>
                    <p><?= substr(strip_tags($featured['content']), 0, 250) ?>...</p>
                    <a class="btn" href="artikel_pengunjung.php?id=<?= $featured['id'] ?>">Selengkapnya →</a>
                </div>
            </div>

            <div class="grid-articles">
                <?php foreach ($articles as $article): ?>
                    <div class="small-article">
                        <?php if (!empty($article['picture'])): ?>
                            <img src="../admin/img/<?= htmlspecialchars($article['picture']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                        <?php endif; ?>
                        <div class="text">
                            <p class="date"><?= strftime("%A, %d %B %Y | %H:%M", strtotime($article['date'])) ?></p>
                            <h3><?= htmlspecialchars($article['title']) ?></h3>
                            <p><strong>Penulis:</strong> <?= htmlspecialchars($article['authors_nickname'] ?? '-') ?></p>
                            <p><strong>Kategori:</strong> <?= htmlspecialchars($article['categories_name'] ?? '-') ?></p>
                            <p><?= substr(strip_tags($article['content']), 0, 100) ?>...</p>
                            <a class="btn-small" href="artikel_pengunjung.php?id=<?= $article['id'] ?>">Selengkapnya →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Tidak ditemukan artikel untuk kata kunci "<strong><?= htmlspecialchars($_GET['q'] ?? '') ?></strong>".</p>
        <?php endif; ?>
    </div>

    <div class="sidebar">
        <div class="search-box">
            <h4 class="sidebar-title">Pencarian</h4>
            <form method="get" action="artikel_pengunjung.php">
                <input type="text" name="q" placeholder="Masukkan kata kunci..." />
                <button type="submit">Go!</button>
            </form>
        </div>

        <div class="kategori-box">
            <h4 class="sidebar-title">Kategori</h4>
            <ul>
                <li><a href="artikel_pengunjung.php?k=Wisata">Wisata</a></li>
                <li><a href="artikel_pengunjung.php?k=Alam">Alam</a></li>
                <li><a href="artikel_pengunjung.php?k=Pantai">Pantai</a></li>
                <li><a href="artikel_pengunjung.php?k=Kuliner">Kuliner</a></li>
            </ul>
        </div>

        <?php if ($articleDetail && !empty($relatedArticles)): ?>
            <div class="related-articles">
                <h3>Artikel Terkait</h3>
                <?php foreach ($relatedArticles as $related): ?>
                    <div class="small-article">
                        <?php if (!empty($related['picture'])): ?>
                            <img src="../admin/img/<?= htmlspecialchars($related['picture']) ?>" alt="<?= htmlspecialchars($related['title']) ?>">
                        <?php endif; ?>
                        <div class="text">
                            <p class="date"><?= strftime("%A, %d %B %Y | %H:%M", strtotime($related['date'])) ?></p>
                            <h4><?= htmlspecialchars($related['title']) ?></h4>
                            <p><strong>Penulis:</strong> <?= htmlspecialchars($related['authors_nickname'] ?? '-') ?></p>
                            <p><?= substr(strip_tags($related['content']), 0, 80) ?>...</p>
                            <a class="btn-small" href="artikel_pengunjung.php?id=<?= $related['id'] ?>">Selengkapnya →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="tentang-box">
            <h4 class="sidebar-title">Tentang</h4>
            <p>Sekedar buah tangan catatan jalan-jalan dan kuliner ke tempat-tempat seputar Malang Raya.</p>
        </div>
    </div>
</div>

<footer>
    <p>&copy; Jalan Jalan Kota Malang 2025</p>
</footer>
</body>
</html>
