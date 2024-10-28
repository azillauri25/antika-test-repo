<?php
// Mulai session dan sertakan file koneksi
session_start();
include 'konfig.php';

// Pastikan request method adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $komplain_ID = isset($_POST['komplain_ID']) ? $_POST['komplain_ID'] : '';
    $status_komplain = isset($_POST['status_komplain']) ? $_POST['status_komplain'] : '';

    // Periksa apakah complaint_ID dan status_komplain valid
    if (!empty($komplain_ID) && !empty($status_komplain)) {
        // Update status komplain di database
        $query_update = "UPDATE komplain SET status_komplain = '$status_komplain' WHERE komplain_ID = '$komplain_ID'";
        $result_update = mysqli_query($koneksi, $query_update);

        if ($result_update) {
            // Redirect kembali ke halaman ulasan_komplain dengan pesan sukses dan ke section komplain
            $_SESSION['success_message'] = 'Status komplain berhasil diperbarui.';
            header('Location: ulasan_komplain.php#komplain');
            exit;
        } else {
            // Redirect kembali dengan pesan error jika gagal
            $_SESSION['error_message'] = 'Terjadi kesalahan saat memperbarui status komplain.';
            header('Location: ulasan_komplain.php#komplain');
            exit;
        }
    } else {
        // Jika data tidak valid, kembali ke halaman sebelumnya dengan error message
        $_SESSION['error_message'] = 'Data tidak valid.';
        header('Location: ulasan_komplain.php#komplain');
        exit;
    }
}
?>
