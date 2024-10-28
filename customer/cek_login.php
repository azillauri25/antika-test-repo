<?php
// Mengaktifkan session PHP
session_start();

// Menghubungkan dengan koneksi
include 'admin/konfig.php';

$username = $_POST['username'];
$password = $_POST['password'];
$pas = md5($password);

// Login customer akan mengarah ke dashboard customer
$result_customer = mysqli_query($koneksi,"SELECT * FROM customer WHERE username='$username' AND password='$password'");
$cek_customer = mysqli_num_rows($result_customer);

if ($cek_customer > 0) {
    $data_customer = mysqli_fetch_assoc($result_customer);

    $_SESSION['username'] = $username;
    header("location: customer/index.php");
} else {
    // Login Admin/pimpinan akan mengarah ke dashboard admin/pimpinan
    $result_management = mysqli_query($koneksi,"SELECT * FROM karyawan WHERE username='$username' AND password='$password'");
    $cek_management = mysqli_num_rows($result_management);

    if ($cek_management > 0) {
        $data_management= mysqli_fetch_assoc($result_management);

        $_SESSION['username'] = $username;
        header("location: index.php");
    } else {
        // Jika tidak ditemukan di kedua tabel
        header("location:login.php?pesan=Gagal");
    }
}
?>
