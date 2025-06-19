<?php

// Konfigurasi koneksi database
$host = "localhost";
$user = "root";
$pass = ""; // Pastikan password ini benar sesuai dengan konfigurasi database Anda
$db = "dbblogfillah";

// Membuat koneksi ke database MySQL
$conn = mysqli_connect($host, $user, $pass, $db);

// Memeriksa apakah koneksi berhasil atau gagal
if (!$conn) {
    // Jika koneksi gagal, hentikan eksekusi script dan tampilkan pesan error
    die("Koneksi gagal: " . mysqli_connect_error());
}

/**
 * Fungsi umum untuk menjalankan query SELECT dan mengambil semua hasil sebagai array asosiatif.
 *
 * @param string $query Query SQL yang akan dijalankan.
 * @return array Array asosiatif yang berisi hasil query, atau array kosong jika tidak ada hasil/terjadi error.
 */
function query($query) {
    global $conn; // Mengakses variabel koneksi global
    $result = mysqli_query($conn, $query);

    // Inisialisasi array untuk menampung hasil
    $rows = [];

    // Memeriksa apakah query berhasil dijalankan
    if (!$result) {
        // Jika query gagal, catat error ke log server dan kembalikan array kosong
        // Hindari menggunakan die() di sini agar aplikasi tetap berjalan
        error_log("Query error: " . mysqli_error($conn) . " Query: " . $query);
        return [];
    }

    // Mengambil setiap baris hasil dan menambahkannya ke array $rows
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
function searchArticles($keyword) {
    global $conn;

    $query = "SELECT a.*, 
                     au.nickname AS authors_nickname, 
                     c.name AS categories_name
              FROM article a
              LEFT JOIN article_author aa ON a.id = aa.article_id
              LEFT JOIN author au ON aa.author_id = au.id
              LEFT JOIN article_category ac ON a.id = ac.article_id
              LEFT JOIN category c ON ac.category_id = c.id
              WHERE a.title LIKE ? OR a.content LIKE ?";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Query preparation failed: " . mysqli_error($conn));
    }

    $likeKeyword = '%' . $keyword . '%';
    mysqli_stmt_bind_param($stmt, "ss", $likeKeyword, $likeKeyword);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $articles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $articles[] = $row;
    }

    return $articles;
}
function hariIndonesia($hariInggris) {
    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    return $hari[$hariInggris] ?? $hariInggris;
}

function namaBulan($bulanAngka) {
    $bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    return $bulan[str_pad($bulanAngka, 2, '0', STR_PAD_LEFT)] ?? $bulanAngka;
}
function getRelatedArticles($category, $excludeId, $limit = 3) {
    global $conn;
    
    $sql = "SELECT a.*, 
                   au.nickname AS authors_nickname, 
                   c.name AS categories_name
            FROM article a
            LEFT JOIN article_author aa ON a.id = aa.article_id
            LEFT JOIN author au ON aa.author_id = au.id
            LEFT JOIN article_category ac ON a.id = ac.article_id
            LEFT JOIN category c ON ac.category_id = c.id
            WHERE c.name = ? AND a.id != ?
            ORDER BY a.date DESC
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Query error (getRelatedArticles): " . $conn->error);
    }

    $stmt->bind_param("sii", $category, $excludeId, $limit);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}



// --- FUNGSI ARTIKEL ---

/**
 * Mengambil semua artikel dari database, beserta nama penulis dan kategori yang terkait.
 * Menggunakan LEFT JOIN untuk memastikan artikel tetap tampil meskipun tidak memiliki penulis/kategori.
 * Menggunakan GROUP_CONCAT untuk menggabungkan banyak penulis/kategori menjadi satu string.
 *
 * @return array Array asosiatif yang berisi semua artikel.
 */
function getAllArticles() {
    global $conn;

    $query = "
        SELECT a.*, 
            (SELECT GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ')
             FROM category c 
             JOIN article_category ac ON c.id = ac.category_id 
             WHERE ac.article_id = a.id) AS categories_name,
            (SELECT GROUP_CONCAT(DISTINCT au.nickname SEPARATOR ', ')
             FROM author au 
             JOIN article_author aa ON au.id = aa.author_id 
             WHERE aa.article_id = a.id) AS authors_nickname
        FROM article a
        ORDER BY a.date DESC
    ";
    
    $result = mysqli_query($conn, $query);
    $articles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $articles[] = $row;
    }
    return $articles;
}

/**
 * Mengambil satu artikel berdasarkan ID-nya, termasuk kategori dan penulis yang terkait.
 * Menggunakan prepared statements untuk mencegah SQL injection.
 *
 * @param int $id ID artikel yang akan diambil.
 * @return array|null Array asosiatif yang berisi data artikel, atau null jika tidak ditemukan.
 */
function getArticleById($id) {
    global $conn;

    // Ambil data artikel dan penulis
    $query = "
        SELECT a.*, au.nickname AS authors_nickname
        FROM article a
        LEFT JOIN article_author aa ON a.id = aa.article_id
        LEFT JOIN author au ON aa.author_id = au.id
        WHERE a.id = $id
        LIMIT 1
    ";
    $result = mysqli_query($conn, $query);
    $article = mysqli_fetch_assoc($result);

    // Ambil semua kategori terkait artikel
    $kategoriQuery = "
        SELECT c.name
        FROM category c
        JOIN article_category ac ON c.id = ac.category_id
        WHERE ac.article_id = $id
    ";
    $kategoriResult = mysqli_query($conn, $kategoriQuery);
    $categories = [];
    while ($row = mysqli_fetch_assoc($kategoriResult)) {
        $categories[] = $row['name'];
    }

    $article['categories_name'] = implode(', ', $categories);

    return $article;
}


/**
 * Menambah artikel baru ke database.
 * Melakukan validasi dan upload gambar, serta menyimpan relasi dengan kategori dan penulis.
 * Menggunakan prepared statements untuk semua operasi INSERT.
 *
 * @param array $data Data artikel (date, title, content, category, author).
 * @param array $files Data file gambar dari $_FILES.
 * @return bool True jika berhasil, false jika gagal.
 */
function addArticle($data, $files) {
    global $conn;

    $title     = htmlspecialchars($data['title']);
    $content   = $data['content'];
    $date      = $data['date'];
    $authorIds = $data['author']; // Berisi array id penulis
    $categoryId = $data['category'];

    // Cek apakah ada gambar yang diupload
    $imageName = null;
    if (isset($files['picture']) && $files['picture']['error'] == 0) {
        $tmpName = $files['picture']['tmp_name'];
        $ext = pathinfo($files['picture']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $ext;
        $targetDir = 'img/' . $imageName;

        // Pastikan folder img ada
        if (!is_dir('img')) {
            mkdir('img', 0755, true);
        }

        // Pindahkan file gambar
        if (!move_uploaded_file($tmpName, $targetDir)) {
            echo "<script>alert('Upload gambar gagal!');</script>";
            return false;
        }
    }

    // Simpan ke tabel article
    $stmt = $conn->prepare("INSERT INTO article (title, content, picture, date) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "<script>alert('Gagal prepare statement: {$conn->error}');</script>";
        return false;
    }
    $stmt->bind_param("ssss", $title, $content, $imageName, $date);

    if (!$stmt->execute()) {
        echo "<script>alert('Gagal menyimpan artikel utama: {$stmt->error}');</script>";
        return false;
    }

    $articleId = $conn->insert_id;

    // Simpan ke tabel relasi article_author
    foreach ($authorIds as $authorId) {
        $stmtAuthor = $conn->prepare("INSERT INTO article_author (article_id, author_id) VALUES (?, ?)");
        if ($stmtAuthor) {
            $stmtAuthor->bind_param("ii", $articleId, $authorId);
            $stmtAuthor->execute();
        }
    }

    // Simpan ke tabel relasi article_category
    $stmtCat = $conn->prepare("INSERT INTO article_category (article_id, category_id) VALUES (?, ?)");
    if ($stmtCat) {
        $stmtCat->bind_param("ii", $articleId, $categoryId);
        $stmtCat->execute();
    }

    return true;
}


function uploadGambar($file) {
    $namaFile = $file['image']['name'];
    $tmpName = $file['image']['tmp_name'];

    $ext = pathinfo($namaFile, PATHINFO_EXTENSION);
    $newName = uniqid() . '.' . $ext;
    $target = '../img/' . $newName;

    if (move_uploaded_file($tmpName, $target)) {
        return $newName;
    }
    return false;
}

function tambahArtikel($data, $file) {
    global $conn;

    $title = htmlspecialchars($data["title"]);
    $content = $data["content"];
    $author_id = $data["author_id"];
    $category_id = $data["category_id"];
    $created_at = date("Y-m-d H:i:s");

    // Upload gambar
    $gambar = uploadGambar($file);
    if (!$gambar) {
        echo "<script>alert('Gagal upload gambar.');</script>";
        return false;
    }

    // Simpan ke tabel `article`
    $query = "INSERT INTO article (title, content, image, created_at)
              VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $title, $content, $gambar, $created_at);
    mysqli_stmt_execute($stmt);

    // Ambil ID artikel yang baru saja disimpan
    $article_id = mysqli_insert_id($conn);

    // Simpan ke tabel relasi penulis
    $stmt1 = mysqli_prepare($conn, "INSERT INTO article_author (article_id, author_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt1, "ii", $article_id, $author_id);
    mysqli_stmt_execute($stmt1);

    // Simpan ke tabel relasi kategori
    $stmt2 = mysqli_prepare($conn, "INSERT INTO article_category (article_id, category_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt2, "ii", $article_id, $category_id);
    mysqli_stmt_execute($stmt2);

    return mysqli_affected_rows($conn);
}


/**
 * Memperbarui data artikel yang sudah ada.
 * Termasuk penanganan upload gambar baru, serta memperbarui relasi kategori dan penulis.
 * Menggunakan prepared statements untuk semua operasi UPDATE/DELETE/INSERT.
 *
 * @param array $data Data artikel yang akan diperbarui (id, title, content, old_picture, category, author).
 * @param array $files Data file gambar dari $_FILES (jika ada upload gambar baru).
 * @return bool True jika berhasil, false jika gagal.
 */
function updateArticle($data, $files) {
    global $conn;

    // Ambil dan bersihkan data
    $id = $data['id'];
    $title = $data['title'];
    $content = $data['content'];
    $old_picture = $data['old_picture']; // Nama gambar lama untuk dihapus jika ada gambar baru

    // Ambil array ID kategori dan penulis yang baru
    $new_category_ids = isset($data['category']) ? (array)$data['category'] : [];
    $new_author_ids = isset($data['author']) ? (array)$data['author'] : [];

    // Penanganan upload gambar baru
    $picture_name = $old_picture; // Default: gunakan nama gambar lama
    $upload_dir = 'uploads/';

    // Cek apakah ada file baru yang diupload
    if ($files['picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $new_picture_original_name = $files['picture']['name'];
        $picture_tmp_name = $files['picture']['tmp_name'];
        $picture_size = $files['picture']['size'];

        $upload_ok = 1;
        $image_file_type = strtolower(pathinfo($new_picture_original_name, PATHINFO_EXTENSION));

        // Validasi gambar baru
        $check = getimagesize($picture_tmp_name);
        if ($check === false) {
            echo "<script>alert('File bukan gambar.');</script>";
            $upload_ok = 0;
        }

        // Periksa ukuran file baru
        if ($picture_size > 5000000) { // Maksimal 5MB
            echo "<script>alert('Maaf, ukuran gambar terlalu besar (maksimal 5MB).');</script>";
            $upload_ok = 0;
        }

        // Izinkan format file tertentu untuk gambar baru
        if(!in_array($image_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "<script>alert('Maaf, hanya format JPG, JPEG, PNG & GIF yang diperbolehkan.');</script>";
            $upload_ok = 0;
        }

        // Jika validasi upload gambar baru berhasil
        if ($upload_ok == 1) {
            // Generate nama file unik untuk gambar baru
            $new_unique_picture_name = uniqid('img_', true) . '.' . $image_file_type;
            $target_file = $upload_dir . $new_unique_picture_name;

            // Hapus gambar lama jika ada dan berbeda dengan nama gambar unik yang baru
            if (!empty($old_picture) && file_exists($upload_dir . $old_picture)) {
                unlink($upload_dir . $old_picture); // Hapus file gambar dari server
            }
            // Pindahkan gambar baru
            if (move_uploaded_file($picture_tmp_name, $target_file)) {
                $picture_name = $new_unique_picture_name; // Update nama gambar yang akan disimpan ke DB
            } else {
                echo "<script>alert('Maaf, ada error saat mengupload gambar baru.');</script>";
                return false;
            }
        } else {
            return false; // Gagal upload gambar baru, batalkan update
        }
    }

    // Mulai transaksi untuk memastikan konsistensi data
    mysqli_begin_transaction($conn);

    try {
        // Update data di tabel 'article' menggunakan prepared statement
        $sql_update_article = "UPDATE article SET title = ?, content = ?, picture = ? WHERE id = ?";
        $stmt_article = mysqli_prepare($conn, $sql_update_article);
        // Bind parameter: 'sssi' -> 3 string (title, content, picture), 1 integer (id)
        mysqli_stmt_bind_param($stmt_article, "sssi", $title, $content, $picture_name, $id);

        if (!mysqli_stmt_execute($stmt_article)) {
            throw new Exception("Error mengupdate artikel: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt_article);

        // UPDATE RELASI KATEGORI (di tabel article_category)
        // 1. Hapus semua relasi kategori lama untuk artikel ini
        $stmt_delete_ac = mysqli_prepare($conn, "DELETE FROM article_category WHERE article_id = ?");
        mysqli_stmt_bind_param($stmt_delete_ac, "i", $id);
        if (!mysqli_stmt_execute($stmt_delete_ac)) {
            throw new Exception("Error menghapus relasi kategori lama: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt_delete_ac);

        // 2. Tambahkan relasi kategori baru (jika ada yang dipilih)
        if (!empty($new_category_ids)) {
            $sql_insert_ac = "INSERT INTO article_category (article_id, category_id) VALUES (?, ?)";
            $stmt_insert_ac = mysqli_prepare($conn, $sql_insert_ac);
            foreach ($new_category_ids as $cat_id) {
                mysqli_stmt_bind_param($stmt_insert_ac, "ii", $id, $cat_id);
                if (!mysqli_stmt_execute($stmt_insert_ac)) {
                    throw new Exception("Error menambahkan relasi kategori baru: " . mysqli_error($conn));
                }
            }
            mysqli_stmt_close($stmt_insert_ac);
        }

        // UPDATE RELASI PENULIS (di tabel article_author)
        // 1. Hapus semua relasi penulis lama untuk artikel ini
        $stmt_delete_aa = mysqli_prepare($conn, "DELETE FROM article_author WHERE article_id = ?");
        mysqli_stmt_bind_param($stmt_delete_aa, "i", $id);
        if (!mysqli_stmt_execute($stmt_delete_aa)) {
            throw new Exception("Error menghapus relasi penulis lama: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt_delete_aa);

        // 2. Tambahkan relasi penulis baru (jika ada yang dipilih)
        if (!empty($new_author_ids)) {
            $sql_insert_aa = "INSERT INTO article_author (article_id, author_id) VALUES (?, ?)";
            $stmt_insert_aa = mysqli_prepare($conn, $sql_insert_aa);
            foreach ($new_author_ids as $auth_id) {
                mysqli_stmt_bind_param($stmt_insert_aa, "ii", $id, $auth_id);
                if (!mysqli_stmt_execute($stmt_insert_aa)) {
                    throw new Exception("Error menambahkan relasi penulis baru: " . mysqli_error($conn));
                }
            }
            mysqli_stmt_close($stmt_insert_aa);
        }

        mysqli_commit($conn); // Commit transaksi jika semua berhasil
        return true; // Berhasil memperbarui artikel dan relasinya

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback transaksi jika terjadi error
        echo "<script>alert('Error mengupdate artikel: " . $e->getMessage() . "');</script>";
        return false;
    }
}

/**
 * Menghapus artikel dari database berdasarkan ID.
 * Juga menghapus file gambar terkait dan semua relasi di tabel pivot.
 * Menggunakan prepared statements untuk operasi DELETE.
 *
 * @param int $id ID artikel yang akan dihapus.
 * @return bool True jika berhasil, false jika gagal.
 */
function deleteArticle($id) {
    global $conn;

    // Mulai transaksi untuk memastikan konsistensi data
    mysqli_begin_transaction($conn);

    try {
        // Ambil nama gambar sebelum menghapus artikel agar bisa dihapus dari server
        $article = getArticleById($id);
        if ($article && !empty($article['picture'])) {
            $upload_dir = 'uploads/';
            $file_to_delete = $upload_dir . $article['picture'];
            if (file_exists($file_to_delete)) {
                if (!unlink($file_to_delete)) {
                    throw new Exception("Gagal menghapus file gambar lama.");
                }
            }
        }

        // Hapus dari tabel relasi 'article_category' terlebih dahulu
        $stmt_delete_ac = mysqli_prepare($conn, "DELETE FROM article_category WHERE article_id = ?");
        mysqli_stmt_bind_param($stmt_delete_ac, "i", $id);
        if (!mysqli_stmt_execute($stmt_delete_ac)) {
            throw new Exception("Error menghapus relasi kategori artikel.");
        }
        mysqli_stmt_close($stmt_delete_ac);

        // Hapus dari tabel relasi 'article_author'
        $stmt_delete_aa = mysqli_prepare($conn, "DELETE FROM article_author WHERE article_id = ?");
        mysqli_stmt_bind_param($stmt_delete_aa, "i", $id);
        if (!mysqli_stmt_execute($stmt_delete_aa)) {
            throw new Exception("Error menghapus relasi penulis artikel.");
        }
        mysqli_stmt_close($stmt_delete_aa);

        // Hapus artikel dari tabel 'article'
        $sql_delete = "DELETE FROM article WHERE id = ?";
        $stmt_delete_article = mysqli_prepare($conn, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete_article, "i", $id);

        if (!mysqli_stmt_execute($stmt_delete_article)) {
            throw new Exception("Error menghapus artikel dari database.");
        }
        mysqli_stmt_close($stmt_delete_article);

        mysqli_commit($conn); // Commit transaksi jika semua berhasil
        return true; // Berhasil menghapus artikel

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback transaksi jika terjadi error
        echo "<script>alert('Error menghapus artikel: " . $e->getMessage() . "');</script>";
        return false;
    }
}


// --- FUNGSI KATEGORI ---

/**
 * Mengambil semua kategori dari database.
 *
 * @return array Array asosiatif yang berisi semua kategori.
 */
function getAllCategories() {
    global $conn;
    return query("SELECT id, name, description FROM category ORDER BY name ASC");
}

/**
 * Mengambil satu kategori berdasarkan ID.
 * Menggunakan prepared statement.
 *
 * @param int $id ID kategori yang akan diambil.
 * @return array|null Array asosiatif yang berisi data kategori, atau null jika tidak ditemukan.
 */
function getCategoryById($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT id, name, description FROM category WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $category = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $category;
}

/**
 * Menambah kategori baru ke database.
 * Menggunakan prepared statement dan menangani error duplikasi nama.
 *
 * @param array $data Data kategori (name, description).
 * @return bool True jika berhasil, false jika gagal.
 */
function addCategory($data) {
    global $conn;
    $name = $data['name'];
    $description = $data['description'];

    $sql_insert = "INSERT INTO category (name, description) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt, "ss", $name, $description);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // Cek jika error karena duplikasi (MySQL error code 1062)
        if (mysqli_errno($conn) == 1062) {
            echo "<script>alert('Error: Nama kategori sudah ada.');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * Memperbarui data kategori yang sudah ada.
 * Menggunakan prepared statement dan menangani error duplikasi nama.
 *
 * @param array $data Data kategori yang akan diperbarui (id, name, description).
 * @return bool True jika berhasil, false jika gagal.
 */
function updateCategory($data) {
    global $conn;
    $id = $data['id'];
    $name = $data['name'];
    $description = $data['description'];

    $sql_update = "UPDATE category SET name = ?, description = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // Cek jika error karena duplikasi
        if (mysqli_errno($conn) == 1062) {
            echo "<script>alert('Error: Nama kategori sudah terdaftar.');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * Menghapus kategori dari database.
 * Akan memeriksa apakah kategori masih digunakan oleh artikel sebelum menghapus.
 *
 * @param int $id ID kategori yang akan dihapus.
 * @return bool|string True jika berhasil, atau string pesan error jika gagal.
 */
function deleteCategory($id) {
    global $conn;

    // Periksa apakah ada artikel yang masih menggunakan kategori ini di tabel pivot article_category
    $check_sql = "SELECT COUNT(*) FROM article_category WHERE category_id = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "i", $id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $article_count); // Bind hasil COUNT ke variabel
    mysqli_stmt_fetch($stmt_check); // Ambil hasil
    mysqli_stmt_close($stmt_check);

    if ($article_count > 0) {
        // Mengembalikan pesan error, bukan langsung alert
        return "Gagal menghapus kategori. Kategori ini masih digunakan oleh " . $article_count . " artikel.";
    }

    // Jika tidak ada artikel yang menggunakan kategori ini, baru hapus dari tabel 'category'
    $sql_delete = "DELETE FROM category WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true; // Berhasil menghapus
    } else {
        // Mengembalikan pesan error jika ada masalah lain saat menghapus
        return "Error menghapus kategori: " . mysqli_error($conn);
    }
}


// --- FUNGSI PENULIS ---

/**
 * Mengambil semua penulis dari database.
 *
 * @return array Array asosiatif yang berisi semua penulis.
 */
function getAllAuthors() {
    global $conn;
    return query("SELECT id, nickname, email FROM author ORDER BY nickname ASC");
}

/**
 * Mengambil satu penulis berdasarkan ID.
 * Menggunakan prepared statement.
 *
 * @param int $id ID penulis yang akan diambil.
 * @return array|null Array asosiatif yang berisi data penulis, atau null jika tidak ditemukan.
 */
function getAuthorById($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT id, nickname, email FROM author WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $author = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $author;
}

/**
 * Menambah penulis baru ke database.
 * Password akan di-hash menggunakan password_hash().
 * Menggunakan prepared statement dan menangani error duplikasi email.
 *
 * @param array $data Data penulis (nickname, email, password).
 * @return bool True jika berhasil, false jika gagal.
 */
function addAuthor($data) {
    global $conn;
    $nickname = $data['nickname'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash password

    $sql_insert = "INSERT INTO author (nickname, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt, "sss", $nickname, $email, $password);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // Cek jika error karena duplikasi (email unik)
        if (mysqli_errno($conn) == 1062) {
            echo "<script>alert('Error: Email sudah terdaftar.');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * Memperbarui data penulis yang sudah ada.
 * Menggunakan prepared statement dan menangani error duplikasi email.
 *
 * @param array $data Data penulis yang akan diperbarui (id, nickname, email).
 * @return bool True jika berhasil, false jika gagal.
 */
function updateAuthor($data) {
    global $conn;
    $id = $data['id'];
    $nickname = $data['nickname'];
    $email = $data['email'];

    $sql_update = "UPDATE author SET nickname = ?, email = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt, "ssi", $nickname, $email, $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        // Cek jika error karena duplikasi
        if (mysqli_errno($conn) == 1062) {
            echo "<script>alert('Error: Email sudah terdaftar.');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
        mysqli_stmt_close($stmt);
        return false;
    }
}

/**
 * Menghapus penulis dari database.
 * Akan memeriksa apakah penulis masih memiliki artikel sebelum menghapus.
 * Menggunakan prepared statement.
 *
 * @param int $id ID penulis yang akan dihapus.
 * @return bool True jika berhasil, false jika gagal (misal karena masih memiliki artikel).
 */
function deleteAuthor($id) {
    global $conn;

    // Periksa apakah ada artikel yang masih menggunakan penulis ini di tabel pivot article_author
    $check_sql = "SELECT COUNT(*) FROM article_author WHERE author_id = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "i", $id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $article_count);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($article_count > 0) {
        echo "<script>alert('Gagal menghapus penulis. Penulis ini masih memiliki " . $article_count . " artikel.');</script>";
        return false;
    }

    // Jika tidak ada artikel yang dimiliki penulis ini, baru hapus dari tabel 'author'
    $sql_delete = "DELETE FROM author WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        echo "<script>alert('Error menghapus penulis: " . mysqli_error($conn) . "');</script>";
        mysqli_stmt_close($stmt);
        return false;
    }
}

function getArticlesByCategory($categoryName) {
    global $conn;

    $query = "SELECT a.*, 
                     au.nickname AS authors_nickname, 
                     c.name AS categories_name
              FROM article a
              LEFT JOIN article_author aa ON a.id = aa.article_id
              LEFT JOIN author au ON aa.author_id = au.id
              LEFT JOIN article_category ac ON a.id = ac.article_id
              LEFT JOIN category c ON ac.category_id = c.id
              WHERE c.name LIKE ?";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Query preparation failed: " . mysqli_error($conn));
    }

    $likeCategory = '%' . $categoryName . '%';
    mysqli_stmt_bind_param($stmt, "s", $likeCategory);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $articles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $articles[] = $row;
    }

    return $articles;
}



?>