<?php
session_start();
include '../admin/konfig.php';

// Ambil data dari formulir
$produk_ID = isset($_POST['produk_ID']) ? $_POST['produk_ID'] : '';

// Ambil customer_ID berdasarkan username
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$query = "SELECT customer_ID FROM customer WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $customer = mysqli_fetch_assoc($result);
    $customer_ID = $customer['customer_ID'];

    // Hapus dari keranjang
    $query = "DELETE FROM keranjang WHERE customer_ID = '$customer_ID' AND produk_ID = '$produk_ID'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header('Location: keranjang.php');
        exit();
    } else {
        echo 'Gagal menghapus dari keranjang.';
    }
} else {
    echo 'Username tidak ditemukan.';
}
?>
