<?php
include 'admin/konfig.php';

// Ambil username dari request
$username = mysqli_real_escape_string($koneksi, $_POST['username']);

// Query untuk mengecek keberadaan username
$sql = "SELECT * FROM customer WHERE username = '$username'";
$result = mysqli_query($koneksi, $sql);

// Mengembalikan hasil sebagai JSON
$response = array('exists' => mysqli_num_rows($result) > 0);

echo json_encode($response);

// Menutup koneksi database
mysqli_close($koneksi);
?>
