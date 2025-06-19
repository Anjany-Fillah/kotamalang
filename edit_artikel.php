<?php
session_start();
require 'functions.php'; // Pastikan file functions.php sudah ada dan berisi fungsi-fungsi yang dibutuhkan

// Atur zona waktu ke Asia/Jakarta untuk memastikan waktu server akurat
date_default_timezone_set('Asia/Jakarta');

// Cek apakah user sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Pastikan ada ID artikel yang dikirim melalui URL
if (!isset($_GET['id'])) {
    header("Location: artikel.php");
    exit;
}

$id = $_GET['id'];
$article = getArticleById($id); // Ambil data artikel berdasarkan ID

// Jika artikel tidak ditemukan, berikan pesan dan arahkan kembali
if (!$article) {
    echo "<script>alert('Artikel tidak ditemukan!');</script>";
    echo "<script>window.location.href = 'artikel.php';</script>";
    exit;
}

$categories = getAllCategories(); // Ambil semua kategori dari database
// $authors = getAllAuthors();    // Tidak perlu lagi mengambil semua penulis jika otomatis dari sesi

// Logika untuk menangani update artikel ketika tombol "Update" diklik
if (isset($_POST['btn_update_artikel'])) {
    // Mengatur tanggal dan waktu update otomatis
    $_POST['date'] = date('Y-m-d H:i:s');

    // **Penting: Mengatur author_id dari sesi user yang sedang login**
    // Asumsi: ID penulis disimpan di $_SESSION['author_id'] setelah login
    // Anda mungkin perlu menyesuaikan nama kunci sesi ini ('author_id')
    if (isset($_SESSION['author_id'])) {
        $_POST['author'] = $_SESSION['author_id'];
    } else {
        // Handle jika author_id tidak ada di sesi (misal, redirect ke login atau berikan error)
        echo "<script>alert('Author ID tidak ditemukan di sesi. Silakan login ulang.');</script>";
        echo "<script>window.location.href = 'login.php';</script>";
        exit;
    }

    // Panggil fungsi updateArticle yang ada di functions.php
    if (updateArticle($_POST, $_FILES)) {
        echo "<script>alert('Artikel berhasil diubah!');</script>";
        echo "<script>window.location.href = 'artikel.php';</script>"; // Redirect ke halaman daftar artikel
    } else {
        echo "<script>alert('Gagal mengubah artikel!');</script>";
        // Jika gagal, refresh halaman ini agar form menampilkan data terbaru (jika ada perubahan lain yang terjadi)
        echo "<script>window.location.href = 'edit_artikel.php?id=" . $id . "';</script>";
    }
    exit; // Pastikan tidak ada kode lain yang dieksekusi setelah redirect
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Form untuk mengubah artikel" />
    <meta name="author" content="Nama Anda" />
    <title>Ubah Artikel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <style type="text/css">
        /* Style untuk CKEditor */
        .ck-editor__editable[role="textbox"] {
            min-height: 300px;
        }
        /* Style umum untuk body */
        body {
            background-color: #f8f9fa;
        }
        /* Style untuk container form */
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        /* Style untuk preview gambar */
        .img-preview {
            max-width: 200px;
            height: auto;
            margin-top: 10px;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-4">Ubah Artikel</h2>
        <hr>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($article['id']); ?>">
            <input type="hidden" name="old_picture" value="<?= htmlspecialchars($article['picture']); ?>">

            <div class="mb-3 mt-3">
                <label for="date_display" class="form-label">Tanggal:</label>
                <input type="text" class="form-control" id="date_display" name="date_display" value="<?= date('l, d F Y | H:i'); ?>" readonly>
            </div>
            <div class="mb-3 mt-3">
                <label for="title" class="form-label">Judul Artikel:</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($article['title']); ?>">
            </div>
            <div class="mb-3 mt-3">
                <label for="category" class="form-label">Kategori:</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat) : ?>
                        <option value="<?= $cat['id']; ?>"
                            <?php
                            // Perbaikan: Pastikan 'category_id' ada sebelum mengaksesnya
                            if (isset($article['category_id']) && $cat['id'] == $article['category_id']) {
                                echo 'selected';
                            }
                            ?>
                        >
                            <?= htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3 mt-3">
                <label for="content">Isi Artikel:</label>
                <textarea class="form-control" rows="5" id="content" name="content"><?= htmlspecialchars($article['content']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="picture" class="form-label">Gambar Artikel:</label>
                <?php if (!empty($article['picture'])) : ?>
                    <div class="mb-2">
                        <img src="uploads/<?= htmlspecialchars($article['picture']); ?>" alt="Gambar Saat Ini" class="img-preview img-thumbnail">
                        <small class="d-block text-muted">Gambar saat ini: <?= htmlspecialchars($article['picture']); ?></small>
                    </div>
                <?php endif; ?>
                <input class="form-control" type="file" id="picture" name="picture" accept="image/*">
                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
            </div>
            <div class="mb-3 mt-3 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary" name="btn_update_artikel"><i class="bi bi-arrow-repeat"></i> Update</button>
                <a href="artikel.php" class="btn btn-secondary"><i class="bi bi-x-lg"></i> Batal</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        CKEDITOR.ClassicEditor.create(document.getElementById("content"), {
            toolbar: {
                items: [
                    'exportPDF', 'exportWord', '|', 'findAndReplace', 'selectAll', '|', 'heading', '|', 'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|', 'bulletedList', 'numberedList', 'todoList', '|', 'outdent', 'indent', '|', 'undo', 'redo', '-', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|', 'alignment', '|', 'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|', 'specialCharacters', 'horizontalLine', 'pageBreak', '|', 'textPartLanguage', '|', 'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            heading: {
                options: [{
                    model: 'paragraph',
                    title: 'Paragraph',
                    class: 'ck-heading_paragraph'
                }, {
                    model: 'heading1',
                    view: 'h1',
                    title: 'Heading 1',
                    class: 'ck-heading_heading1'
                }, {
                    model: 'heading2',
                    view: 'h2',
                    title: 'Heading 2',
                    class: 'ck-heading_heading2'
                }, {
                    model: 'heading3',
                    view: 'h3',
                    title: 'Heading 3',
                    class: 'ck-heading_heading3'
                }, {
                    model: 'heading4',
                    view: 'h4',
                    title: 'Heading 4',
                    class: 'ck-heading_heading4'
                }, {
                    model: 'heading5',
                    view: 'h5',
                    title: 'Heading 5',
                    class: 'ck-heading_heading5'
                }, {
                    model: 'heading6',
                    view: 'h6',
                    title: 'Heading 6',
                    class: 'ck-heading_heading6'
                }]
            },
            placeholder: 'Tulis artikel di sini...',
            fontFamily: {
                options: [
                    'default', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace', 'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif', 'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif', 'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            fontSize: {
                options: [10, 12, 14, 'default', 18, 20, 22],
                supportAllValues: true
            },
            htmlSupport: {
                allow: [{
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }]
            },
            htmlEmbed: {
                showPreviews: false
            },
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            mention: {
                feeds: [{
                    marker: '@',
                    feed: [
                        '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream', '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o', '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé', '@sugar', '@sweet', '@topping', '@wafer'
                    ],
                    minimumCharacters: 1
                }]
            },
            removePlugins: [
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'MultiLevelList', 'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges', 'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
            ]
        });
    </script>
</body>

</html>