<?php
session_start();
include '../admin/konfig.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    die("Anda harus login terlebih dahulu.");
}

// Ambil customer_ID dari session
$username = $_SESSION['username'];
$query = "SELECT customer_ID FROM customer WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Pengguna tidak ditemukan.");
}
$customer = mysqli_fetch_assoc($result);
$customer_ID = $customer['customer_ID'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_ulasan'])) {
    // Ambil data dari form
    $order_ID = mysqli_real_escape_string($koneksi, $_POST['order_ID']);
    $ulasan = mysqli_real_escape_string($koneksi, $_POST['ulasan']);
    $penilaian = mysqli_real_escape_string($koneksi, $_POST['penilaian']);

    // Validasi input
    if (empty($ulasan) || empty($penilaian)) {
        die("Ulasan dan penilaian tidak boleh kosong.");
    }

    // Ambil tanggal review
    $tanggal_ulasan = date('Y-m-d H:i:s'); // Format tanggal dan waktu saat ini

    // Query untuk menyimpan ulasan dan rating ke database
    $query = "
        INSERT INTO ulasan (order_ID, customer_ID, isi_ulasan, penilaian, tanggal_ulasan, relasi_karyawan_ID)
        VALUES ('$order_ID', '$customer_ID', '$ulasan', '$penilaian', '$tanggal_ulasan', 'RELASI04')
    ";

    if (mysqli_query($koneksi, $query)) {
        // Redirect ke halaman ulasan_komplain.php
        header("Location: ulasan_komplain.php#ulasan");
        exit();
    } else {
        echo "Terjadi kesalahan: " . mysqli_error($koneksi);
    }
} else {
    // Jika bukan POST request atau submit_ulasan tidak ada
    echo "Permintaan tidak valid.";
}
?>