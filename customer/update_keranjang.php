<?php
session_start();
include '../admin/konfig.php';

// Ambil data dari formulir
$produk_ID = isset($_POST['produk_ID']) ? $_POST['produk_ID'] : '';
$kuantitas = isset($_POST['kuantitas']) ? intval($_POST['kuantitas']) : 1;
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Ambil username dari sesi
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Validasi
if (empty($produk_ID) || $kuantitas <= 0 || empty($username) || !in_array($action, ['increase', 'decrease'])) {
    die('Data tidak valid.');
}

// Ambil customer_ID berdasarkan username
$query = "SELECT customer_ID FROM customer WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $customer = mysqli_fetch_assoc($result);
    $customer_ID = $customer['customer_ID'];

    // Ambil stok dan kuantitas saat ini dari tabel produk dan keranjang
    $query = "SELECT p.stok_produk, c.kuantitas 
              FROM keranjang c 
              JOIN produk p ON c.produk_ID = p.produk_ID 
              WHERE c.customer_ID = '$customer_ID' AND c.produk_ID = '$produk_ID'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $cart_item = mysqli_fetch_assoc($result);
        $current_quantity = $cart_item['kuantitas'];
        $stock = $cart_item['stok_produk']; // Stok dari produk

        // Tentukan kuantitas baru berdasarkan aksi
        if ($action == 'increase') {
            // Pastikan kuantitas tidak melebihi stok
            $new_quantity = min($current_quantity + 1, $stock);
        } elseif ($action == 'decrease') {
            $new_quantity = max($current_quantity - 1, 1); // Setidaknya 1
        }

        // Update kuantitas di keranjang
        $query = "UPDATE keranjang SET kuantitas = $new_quantity WHERE customer_ID = '$customer_ID' AND produk_ID = '$produk_ID'";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            // Redirect ke keranjang.php setelah berhasil
            header('Location: keranjang.php');
            exit();
        } else {
            echo 'Gagal memperbarui keranjang: ' . mysqli_error($koneksi);
        }
    } else {
        echo 'Item tidak ditemukan dalam keranjang.';
    }
} else {
    echo 'Username tidak ditemukan.';
}
?>
