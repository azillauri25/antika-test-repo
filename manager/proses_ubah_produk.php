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
        // Update status request ubah produk jadi 'disetujui'
        $query_update_status = "UPDATE perubahan_produk SET request_ubah_produk = 'disetujui' WHERE produk_ID = '$produk_ID'";
        mysqli_query($koneksi, $query_update_status);

        // Ambil data perubahan produk dari tabel perubahan_produk
        $query_get_changes = "SELECT nama_produk, stok_produk, harga_produk, gambar_produk FROM perubahan_produk WHERE produk_ID = '$produk_ID'";
        $result_changes = mysqli_query($koneksi, $query_get_changes);

        if (mysqli_num_rows($result_changes) > 0) {
            $row_changes = mysqli_fetch_assoc($result_changes);

            // Update field di tabel produk dengan data dari perubahan_produk
            $query_update_produk = "UPDATE produk SET 
                        nama_produk = '" . mysqli_real_escape_string($koneksi, $row_changes['nama_produk']) . "', 
                        stok_produk = '" . mysqli_real_escape_string($koneksi, $row_changes['stok_produk']) . "', 
                        harga_produk = '" . mysqli_real_escape_string($koneksi, $row_changes['harga_produk']) . "', 
                        gambar_produk = '" . mysqli_real_escape_string($koneksi, $row_changes['gambar_produk']) . "', 
                        request_tambah_produk = 'disetujui',
                        request_ubah_produk = 'disetujui' 
                        WHERE produk_ID = '$produk_ID'";

            mysqli_query($koneksi, $query_update_produk);

            // Hapus data di tabel perubahan_produk setelah approve
            $query_delete_perubahan = "DELETE FROM perubahan_produk WHERE produk_ID = '$produk_ID'";
            mysqli_query($koneksi, $query_delete_perubahan);
        }
    } elseif ($action === 'reject') {
        // Jika ditolak, update status jadi 'ditolak' di tabel perubahan_produk
        $query_reject = "UPDATE perubahan_produk SET request_ubah_produk = 'ditolak' WHERE produk_ID = '$produk_ID'";
        mysqli_query($koneksi, $query_reject);

        // Update request_ubah_produk di tabel produk menjadi 'ditolak'
        $query_update_produk_reject = "UPDATE produk SET request_ubah_produk = 'ditolak' WHERE produk_ID = '$produk_ID'";
        mysqli_query($koneksi, $query_update_produk_reject);
        $query_delete_perubahan = "DELETE FROM perubahan_produk WHERE produk_ID = '$produk_ID'";
            mysqli_query($koneksi, $query_delete_perubahan);
    }

    // Redirect ke halaman review produk
    header('Location: produk.php');
    exit();
} else {
    header('Location: produk.php');
    exit();
}

mysqli_close($koneksi);
?>
