
<?php
session_start();
include '../admin/konfig.php';
include 'cek.php'; // Memastikan user adalah manajer

$promo_id = isset($_GET['promo_id']) ? mysqli_real_escape_string($koneksi, $_GET['promo_id']) : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (empty($promo_id) || empty($action)) {
    die('Data tidak lengkap.');
}

if ($action === 'accept') {
    // Jika disetujui, hapus promo
    $query = "UPDATE promo SET status_promo='nonaktif' WHERE promo_id='$promo_id'";
} else if ($action === 'reject') {
    // Jika ditolak, ubah status delete_request menjadi 'rejected'
    $query = "UPDATE promo SET request_nonaktif_promo='ditolak' WHERE promo_ID='$promo_id'";
} else {
    die('Aksi tidak valid.');
}

if (mysqli_query($koneksi, $query)) {
    echo "<script>window.location.href = 'promo.php';</script>";
} else {
    die('Query Error: ' . mysqli_error($koneksi));
}

mysqli_close($koneksi);
?>

