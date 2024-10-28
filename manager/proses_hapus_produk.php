<?php
session_start();
include '../admin/konfig.php';
include 'cek.php'; // Memastikan user adalah manajer

$produk_id = isset($_GET['produk_ID']) ? mysqli_real_escape_string($koneksi, $_GET['produk_ID']) : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (empty($produk_id) || empty($action)) {
    die('Data tidak lengkap.');
}

if ($action === 'approve') {
    // Jika disetujui, hapus produk
    $query = "DELETE FROM produk WHERE produk_ID='$produk_id'";
} else if ($action === 'reject') {
    // Jika ditolak, ubah status delete_request menjadi 'rejected'
    $query = "UPDATE produk SET request_hapus_produk='ditolak' WHERE produk_ID='$produk_id'";
} else {
    die('Aksi tidak valid.');
}

if (mysqli_query($koneksi, $query)) {
    echo "<script>window.location.href = 'produk.php';</script>";
} else {
    die('Query Error: ' . mysqli_error($koneksi));
}

mysqli_close($koneksi);
?>
