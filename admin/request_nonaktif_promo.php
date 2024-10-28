<?php
session_start();
include 'konfig.php';
include 'cek.php'; // Pastikan user sudah login

// Ambil promo_id dari URL
$promo_ID = isset($_GET['promo_ID']) ? mysqli_real_escape_string($koneksi, $_GET['promo_ID']) : '';

if (empty($promo_ID)) {
    die('Promo tidak ditemukan.');
}

// Update status nonaktif_request menjadi 'pending' untuk menonaktifkan promo
$query = "UPDATE promo SET request_nonaktif_promo='menunggu' WHERE promo_ID='$promo_ID'";

if (mysqli_query($koneksi, $query)) {
    echo "<script>window.location.href = 'promo.php';</script>";
} else {
    die('Query Error: ' . mysqli_error($koneksi));
}

mysqli_close($koneksi);
?>
