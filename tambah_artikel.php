<?php
session_start();
require_once 'functions.php';

// Cek apakah user sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$loggedInAuthorId = null;
if (isset($_SESSION['email'])) {
    $authorData = query("SELECT id FROM author WHERE email = '" . $_SESSION['email'] . "'");
    if (!empty($authorData)) {
        $loggedInAuthorId = $authorData[0]['id'];
    }
}

if (is_null($loggedInAuthorId)) {
    echo "<script>alert('Error: Data penulis tidak ditemukan. Silakan login kembali.');</script>";
    header("Location: login.php");
    exit;
}

$categories = getAllCategories();

// Logika untuk menangani penambahan artikel (jika tombol simpan diklik)
if (isset($_POST['btn_simpan'])) {
    // Siapkan data untuk dikirim ke addArticle
    $articleData = $_POST;
    $articleData['author'] = [$loggedInAuthorId];

    // PENTING: Ubah format tanggal di sini sebelum dikirim ke fungsi addArticle
    // Ambil tanggal saat ini dalam format YYYY-MM-DD HH:MM:SS
    date_default_timezone_set("Asia/Jakarta"); // Pastikan zona waktu sudah diatur
    $articleData['date'] = date("Y-m-d H:i:s"); // Format standar DATETIME MySQL

    if (addArticle($articleData, $_FILES)) {
        echo "<script>alert('Artikel berhasil ditambahkan!');</script>";
        echo "<script>window.location.href = 'artikel.php';</script>";
    } else {
        // Gagal menambahkan artikel, alert sudah ada di functions.php
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Form untuk menambah artikel baru" />
    <meta name="author" content="Nama Anda" />
    <title>Tambah Artikel Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <style type="text/css">
        .ck-editor__editable[role="textbox"] {
            min-height: 300px; /* Sesuaikan tinggi editor */
        }
        body {
            background-color: #f8f9fa; /* Warna latar belakang */
        }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-4">Tambah Artikel Baru</h2>
        <hr>
        <form method="POST" enctype="multipart/form-data">
            <?php
            // Ini hanya untuk tampilan di form, tidak dikirim ke database
            date_default_timezone_set("Asia/Jakarta");
            $hari = date("l");
            $hari_indonesia = hariIndonesia($hari);
            $tahun = date("Y");
            $bulan = date("m");
            $nama_bulan = namaBulan($bulan);
            $tanggal = date("d");
            $tanggal_lengkap = $tanggal . " " . $nama_bulan . " " . $tahun;
            $hari_tanggal = $hari_indonesia . ", " . $tanggal_lengkap;
            $waktu = date("H:i");
            $hari_tanggal_waktu_display = $hari_tanggal . " | " . $waktu; // Nama variabel baru untuk display
            ?>

            <div class="mb-3 mt-3">
                <label for="date" class="form-label">Tanggal:</label>
                <input type="text" class="form-control" id="date" name="date_display" value="<?php echo $hari_tanggal_waktu_display; ?>" readonly>
                </div>
            <div class="mb-3 mt-3">
                <label for="title" class="form-label">Judul Artikel:</label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="Masukkan judul artikel">
            </div>
            <div class="mb-3 mt-3">
                <label for="category" class="form-label">Kategori:</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php
                    if (!empty($categories)) {
                        foreach ($categories as $cat) {
                            echo "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada kategori ditemukan</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3 mt-3">
                <label for="content">Isi Artikel:</label>
                <textarea class="form-control" rows="5" id="content" name="content" placeholder="Tulis artikel di sini..."></textarea>
            </div>
            <div class="mb-3">
                <label for="picture" class="form-label">Gambar Artikel:</label>
                <input class="form-control" type="file" id="picture" name="picture" accept="image/*">
            </div>
            <div class="mb-3 mt-3 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary" name="btn_simpan"><i class="bi bi-save"></i> Simpan</button>
                <button type="reset" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Reset</button>
                <a href="artikel.php" class="btn btn-danger"><i class="bi bi-x-lg"></i> Batal</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        CKEDITOR.ClassicEditor.create(document.getElementById("content"), {
            toolbar: {
                items: [
                    'exportPDF', 'exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
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
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
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
                        '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                        '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                        '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                        '@sugar', '@sweet', '@topping', '@wafer'
                    ],
                    minimumCharacters: 1
                }]
            },
            removePlugins: [
                'AIAssistant',
                'CKBox',
                'CKFinder',
                'EasyImage',
                'MultiLevelList',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                'MathType',
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents',
                'PasteFromOfficeEnhanced',
                'CaseChange'
            ]
        });
    </script>
</body>

</html>