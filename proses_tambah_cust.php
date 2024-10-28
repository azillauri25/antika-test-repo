<?php
include 'admin/konfig.php';

$nama = $_POST['nama_customer'];
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email_customer'];
$telepon = $_POST['nomor_telepon_customer'];
$alamat = $_POST['alamat_customer'];
$ttl = $_POST['ttl_customer'];
$nama_kota = $_POST['nama_kota'];

if (empty($nama) || empty($username) || empty($password) || empty($email) || empty($telepon) || empty($alamat) || empty($ttl) || empty($nama_kota)) {
    echo "<script>
        alert('Silahkan lengkapi data Anda terlebih dahulu');
        window.location = 'registrasi.php';
    </script>";
} elseif (strlen($telepon) < 11) { // Cek minimal panjang telepon
    echo "<script>
        alert('Nomor telepon minimal harus 11 karakter');
        window.location = 'registrasi.php';
    </script>";
} else {
    $sql = "SELECT * FROM customer WHERE username = '$username'";
    $result = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>
            alert('Username telah digunakan, silakan pilih username lain.');
            window.location = 'registrasi.php';
        </script>";
    } else {
        $query = "INSERT INTO customer (nama_customer, username, password, email_customer, nomor_telepon_customer, alamat_customer, ttl_customer, nama_kota) 
                  VALUES ('$nama', '$username', '$password', '$email', '$telepon', '$alamat', '$ttl', '$nama_kota')";
        $result = mysqli_query($koneksi, $query);

        if (!$result) {
            die("Error: " . mysqli_error($koneksi));
        } else {
            echo "<script>
                alert('Data berhasil ditambah.');
                window.location = 'login.php';
            </script>";
        }
    }
}


mysqli_close($koneksi);
?>
