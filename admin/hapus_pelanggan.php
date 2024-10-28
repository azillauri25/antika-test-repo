<?php
session_start();
include 'konfig.php'; // Koneksi ke database

// Pastikan parameter customer_ID ada di URL
if (isset($_GET['customer_ID'])) {
    $customer_ID = $_GET['customer_ID'];

    // Query untuk menghapus data pelanggan berdasarkan customer_ID (VARCHAR)
    $query = "DELETE FROM customer WHERE customer_ID = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        // Bind parameter dan eksekusi statement
        mysqli_stmt_bind_param($stmt, 's', $customer_ID); // 's' untuk tipe string (VARCHAR)
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Redirect kembali ke halaman pelanggan setelah sukses
            $_SESSION['message'] = "Pelanggan berhasil dihapus.";
            header("Location: pelanggan.php");
            exit();
        } else {
            // Jika tidak ada baris yang terpengaruh (misalnya ID tidak ditemukan)
            $_SESSION['error'] = "Pelanggan tidak ditemukan atau sudah dihapus.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Terjadi kesalahan pada sistem.";
    }
} else {
    $_SESSION['error'] = "ID pelanggan tidak valid.";
}

// Redirect kembali ke halaman pelanggan jika ada kesalahan
header("Location: pelanggan.php");
exit();
?>
