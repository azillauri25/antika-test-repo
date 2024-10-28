<?php
session_start();
include '../admin/konfig.php';
include 'cek.php'; // Memastikan user sudah login saat akses ke halaman ini

// Ambil ID promo dari URL
if (isset($_GET['promo_ID'])) {
    $promo_ID = mysqli_real_escape_string($koneksi, $_GET['promo_ID']);
    $action = $_GET['action'];

    // Cek tindakan yang dipilih
    if ($action == 'accept') {
        // Update status_review menjadi approved dan status_aktif menjadi active
        $update_query = "UPDATE promo SET request_tambah_promo='disetujui', status_promo='aktif' WHERE promo_ID='$promo_ID'";
    } elseif ($action == 'reject') {

        $update_query = "UPDATE promo SET request_tambah_promo='ditolak', status_promo='nonaktif' WHERE promo_ID='$promo_ID'";
    } else {
        // Jika tidak ada aksi yang dikenali, redirect atau tampilkan pesan error
        header("Location: promo.php?error=Invalid action");
        exit;
    }

    // Eksekusi query untuk mengupdate status review dan status aktif
    if (mysqli_query($koneksi, $update_query)) {
        // Redirect ke halaman review promo setelah berhasil
        header("Location: promo.php?success=Promo status updated successfully");
    } else {
        // Jika terjadi error, redirect dengan pesan error
        header("Location: promo.php?error=" . mysqli_error($koneksi));
    }
} else {
    // Jika tidak ada ID, redirect dengan pesan error
    header("Location: promo.php?error=Invalid ID");
}

mysqli_close($koneksi);
?>
