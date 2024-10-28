
<?php
session_start();
include 'konfig.php';
include 'cek.php'; // Pastikan user sudah login

// Ambil produk_ID dari URL
$produk_ID = isset($_GET['produk_ID']) ? mysqli_real_escape_string($koneksi, $_GET['produk_ID']) : '';

if (empty($produk_ID)) {
    die('Promo tidak ditemukan.');
}

// Update status nonaktif_request menjadi 'pending' untuk menonaktifkan promo
$query = "UPDATE produk SET request_hapus_produk='menunggu' WHERE produk_ID='$produk_ID'";

if (mysqli_query($koneksi, $query)) {
    echo "<script>window.location.href = 'produk.php';</script>";
} else {
    die('Query Error: ' . mysqli_error($koneksi));
}

mysqli_close($koneksi);
?>

