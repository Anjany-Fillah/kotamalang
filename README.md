
# 📚 Struktur Proyek Aplikasi Artikel – `230605110033`

## 📁 admin/
File dan folder untuk keperluan admin, termasuk manajemen artikel, kategori, dan penulis.

```
├── admin_header.php          # Header yang digunakan di halaman admin
├── artikel.php              # Daftar semua artikel untuk admin
├── edit_artikel.php         # Form edit artikel oleh admin
├── edit_kategori.php        # Form edit kategori
├── edit_penulis.php         # Form edit penulis
├── functions.php            # Kumpulan fungsi utama (CRUD, helper)
├── hapus_artikel.php        # Menghapus artikel
├── hapus_kategori.php       # Menghapus kategori
├── hapus_penulis.php        # Menghapus penulis
├── index.php                # Halaman dashboard admin utama
├── kategori.php             # Menampilkan dan mengelola kategori
├── login.php                # Form login untuk admin
├── logout.php               # Logout admin
├── penulis.php              # Manajemen daftar penulis
├── tambah_artikel.php       # Form untuk menambah artikel baru
├── tambah_kategori.php      # Form untuk menambah kategori baru
├── tambah_penulis.php       # Form untuk menambah penulis baru
```

## 📁 admin/img/
```
├── (image assets)           # Gambar-gambar yang digunakan dalam halaman admin
```

---

## 📁 pengunjung/
File-file untuk tampilan pengunjung publik.

```
├── artikel_pengunjung.php   # Menampilkan detail artikel untuk pengunjung
├── kontak.php               # Halaman formulir kontak
├── proses_kontak.php        # Proses pengiriman data dari form kontak
├── tentang.php              # Halaman tentang website
```

---

## 📁 Root Folder
```
├── malang-background.jpg    # Gambar latar belakang situs
├── style.css                # Gaya utama (CSS) untuk tampilan situs
```

---

## ℹ️ Penjelasan Umum

Struktur proyek ini membagi fungsionalitas menjadi dua peran utama:

1. **Admin**: Dapat mengelola artikel, kategori, dan penulis melalui folder `admin/`.
2. **Pengunjung**: Dapat membaca artikel, menghubungi admin via kontak, dan melihat informasi tentang situs melalui folder `pengunjung/`.
