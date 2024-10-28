<?php
// Mulai session dan sertakan file koneksi
session_start();
include 'konfig.php'; // Pastikan file koneksi ke database sudah ada

// Pastikan request method adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $komplain_ID = $_POST['komplain_ID']; // ID komplain yang diposting
    $solusi_komplain = $_POST['solusi_komplain']; // Solusi yang dimasukkan

    // Sanitasi input untuk mencegah SQL Injection
    $komplain_ID = $koneksi->real_escape_string($komplain_ID);
    $solusi_komplain = $koneksi->real_escape_string($solusi_komplain);

    // Query untuk memasukkan solusi komplain ke dalam database
    $sql = "UPDATE komplain SET solusi_komplain = '$solusi_komplain' WHERE komplain_ID = '$komplain_ID'";

    if ($koneksi->query($sql) === TRUE) {
        // Jika berhasil, redirect ke halaman yang diinginkan
        header('Location: ulasan_komplain.php#komplain'); // Ganti dengan halaman yang lo inginkan
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }
}

// Tutup koneksi database
$koneksi->close();
?>
