<?php
session_start();
include '../admin/konfig.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

if ($username) {
    $query = "SELECT customer.nama_customer, customer.nomor_telepon_customer, customer.alamat_customer, customer.nama_kota, pengiriman.biaya_pengiriman 
    FROM customer 
    LEFT JOIN pengiriman ON customer.nama_kota = pengiriman.nama_kota 
    WHERE username = '$username'";

    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Data tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'User tidak login']);
}

?>
