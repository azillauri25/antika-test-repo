<?php
session_start();
include '../admin/konfig.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produk_ID = $_POST['produk_ID'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $query = "UPDATE produk SET request_tambah_produk = 'disetujui' WHERE produk_ID = '$produk_ID'";
        mysqli_query($koneksi, $query);
    } elseif ($action === 'reject') {
        $query = "UPDATE produk SET request_tambah_produk = 'ditolak' WHERE produk_ID = '$produk_ID'";
        mysqli_query($koneksi, $query);
    }

    header('Location: produk.php');
    exit();
} else {
    header('Location: produk.php');
    exit();
}

mysqli_close($koneksi);
?>
