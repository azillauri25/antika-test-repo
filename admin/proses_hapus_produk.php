<?php
session_start();
include 'konfig.php';
include 'cek.php'; 


if (isset($_GET['produk_ID'])) {

    $produk_ID = $_GET['produk_ID'];

    $query_hapus = "DELETE FROM produk WHERE produk_ID = ?";
    

    if ($stmt = mysqli_prepare($koneksi, $query_hapus)) {
        mysqli_stmt_bind_param($stmt, "s", $produk_ID);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan_sukses'] = "Produk berhasil dihapus.";
        } else {
            // Penghapusan gagal
            $_SESSION['pesan_error'] = "Gagal menghapus produk.";
        }
        
        mysqli_stmt_close($stmt);
    } else {

        $_SESSION['pesan_error'] = "Terjadi kesalahan dalam menghapus produk.";
    }


    header("Location: produk.php");
    exit();
} else {

    $_SESSION['pesan_error'] = "Tidak ada ID produk yang ditemukan.";
    header("Location: produk.php");
    exit();
}
?>
