<?php
require '../admin/functions.php'; // koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama   = htmlspecialchars($_POST['nama']);
    $email  = htmlspecialchars($_POST['email']);
    $subjek = htmlspecialchars($_POST['subjek']);
    $pesan  = htmlspecialchars($_POST['pesan']);

    // Query insert
    $query = "INSERT INTO pesan_kontak (nama, email, subjek, pesan) 
              VALUES ('$nama', '$email', '$subjek', '$pesan')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('Pesan berhasil dikirim!');
            window.location.href = 'kontak.php';
        </script>";
    } else {
        echo "Gagal menyimpan pesan: " . mysqli_error($conn);
    }
}
?>
