<?php
session_start();
include 'admin/konfig.php';
$username = $_POST['username'];
$password = $_POST['password'];
$pas = md5($password);

// kalau login customer maka akan mengarah ke index.php yang ada di folder customer
$result_customer = mysqli_query($koneksi,"SELECT * FROM customer WHERE username='$username' AND password='$password'");
$cek_customer = mysqli_num_rows($result_customer);

if ($cek_customer > 0) {
    $data_customer = mysqli_fetch_assoc($result_customer);

    $_SESSION['username'] = $username;
    header("location: customer/index.php");
} else {

    // kalau login admin maka akan mengarah ke index.php yang ada di folder admin
    // $result_karyawan = mysqli_query($koneksi,"SELECT * FROM karyawan WHERE username='$username' AND password='$password'");
    $result_karyawan = mysqli_query($koneksi, "SELECT * FROM karyawan WHERE username='$username' AND password='$password'");

    $cek_karyawan = mysqli_num_rows($result_karyawan);

    if ($cek_karyawan > 0) {
        $data_karyawan = mysqli_fetch_assoc($result_karyawan);

        $_SESSION['username'] = $username;
        
        // Cek apakah user adalah admin atau pimpinan
        if ($username == 'adminantika') {
            header("location: admin/index.php");
        } elseif ($username == 'dirutantika') {
            header("location: dirut/index.php");
        } elseif ($username == 'financeantika') {
            header("location: finance/index.php");
        } elseif ($username == 'managerantika') {
            header("location: manager/index.php");
        } else {
            header("location: login.php");
        }

    } else {
        header("location:login.php?pesan=Gagal");
    }
}
?>
