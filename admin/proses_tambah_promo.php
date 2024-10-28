<?php
session_start();
include 'konfig.php';
include 'cek.php';

// Cek apakah form disubmit melalui POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_promo = mysqli_real_escape_string($koneksi, $_POST['nama_promo']);
    $nominal_diskon = mysqli_real_escape_string($koneksi, $_POST['nominal_diskon']);
    $deskripsi_promo = mysqli_real_escape_string($koneksi, $_POST['deskripsi_promo']);
    $tanggal_mulai_promo = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai_promo']);
    $tanggal_berakhir_promo = mysqli_real_escape_string($koneksi, $_POST['tanggal_berakhir_promo']);

    // Query untuk menyimpan promo baru ke dalam tabel promo
    $query = "INSERT INTO promo (nama_promo, nominal_diskon, deskripsi_promo, tanggal_mulai_promo, tanggal_berakhir_promo, request_tambah_promo, status_promo, relasi_karyawan_ID) 
    VALUES ('$nama_promo', '$nominal_diskon', '$deskripsi_promo', '$tanggal_mulai_promo', '$tanggal_berakhir_promo', 'menunggu', 'nonaktif', 'RELASI02')";


    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, redirect ke halaman promo
        $_SESSION['pesan'] = "Promo berhasil ditambahkan!";
        header("Location: promo.php");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }

    // Tutup koneksi database
    mysqli_close($koneksi);
} else {
    // Jika metode request tidak valid
    echo "Invalid request method!";
}
?>
