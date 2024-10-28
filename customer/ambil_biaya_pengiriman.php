<?php
include '../admin/konfig.php';

if (isset($_GET['kota'])) {
    $kota = $_GET['kota'];
    $query = "SELECT biaya_pengiriman FROM pengiriman WHERE nama_kota='$kota'";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo number_format($row['biaya_pengiriman'], 0, ',', '.');
    } else {
        echo "0";
    }
}
?>
