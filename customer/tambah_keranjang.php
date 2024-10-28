<?php
session_start();
include '../admin/konfig.php';

// Ambil data dari formulir
$produk_ID = isset($_POST['produk_ID']) ? $_POST['produk_ID'] : '';
$kuantitas = isset($_POST['kuantitas']) ? intval($_POST['kuantitas']) : 1;

// Ambil username dari sesi
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Debugging: Log ke file log server
error_log("Received produk ID: " . htmlspecialchars($produk_ID));
error_log("Received Kuantitas: " . htmlspecialchars($kuantitas));
error_log("Received Username: " . htmlspecialchars($username));

// Validasi dan tambah ke keranjang
if ($produk_ID && $kuantitas > 0 && $username !== '') {
    // Ambil data produk dari database
    $query = "SELECT * FROM produk WHERE produk_ID = '$produk_ID'";
    error_log("Executing query: " . $query); // Log query
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $produk = mysqli_fetch_assoc($result);
        $harga = $produk['harga'];
        $harga_total_produk = $harga * $kuantitas;

        // Ambil customer_ID berdasarkan username
        $query = "SELECT customer_ID FROM customer WHERE username = '$username'";
        $result = mysqli_query($koneksi, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $customer = mysqli_fetch_assoc($result);
            $customer_ID = $customer['customer_ID'];

            // Tambah ke cart
            $query = "INSERT INTO keranjang (customer_ID, produk_ID, kuantitas, harga_total_produk) 
                      VALUES ('$customer_ID', '$produk_ID', $kuantitas, $harga_total_produk)
                      ON DUPLICATE KEY UPDATE kuantitas = kuantitas + VALUES(kuantitas), harga_total_produk = harga_total_produk + VALUES(harga_total_produk)";
            error_log("Executing query: " . $query); // Log query
            $result = mysqli_query($koneksi, $query);

            if ($result) {
                // Redirect ke keranjang.php
                header('Location: keranjang.php');
                exit();
            } else {
                error_log("Gagal menyimpan ke database: " . mysqli_error($koneksi));
                echo 'Gagal menyimpan ke database.';
            }
        } else {
            echo 'Username tidak ditemukan.';
        }
    } else {
        echo 'Produk tidak ditemukan.';
    }
} else {
    echo 'Data tidak valid.';
}
?>
