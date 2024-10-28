<?php
include '../admin/konfig.php';
$username = $_POST['username'];
$nama = $_POST['nama_customer'];
$password = $_POST['password'];
$email = $_POST['email_customer'];
$telepon = $_POST['nomor_telepon_customer'];
$alamat = $_POST['alamat_customer'];
$nama_kota = $_POST['nama_kota'];

if (empty($nama) || empty($email) || empty($telepon) || empty($alamat) || empty($nama_kota)) {
    echo "<script>
        alert('Silahkan lengkapi data Anda terlebih dahulu');
        window.location = 'edit_profil.php';
    </script>";
} else {
    if (empty($password)) {
        $query = "UPDATE customer 
                  SET nama_customer='$nama', email_customer='$email', nomor_telepon_customer='$telepon', alamat_customer='$alamat',nama_kota='$nama_kota' 
                  WHERE username='$username'";
    } else {
        // Jika password diisi, maka bakal update password juga
        $query = "UPDATE customer 
                  SET nama_customer='$nama', email_customer='$email', nomor_telepon_customer='$telepon', alamat_customer='$alamat', nama_kota='$nama_kota', password='$password' 
                  WHERE username='$username'";
    }

    $result = mysqli_query($koneksi, $query);
    if (!$result) {
        die("Error: " . mysqli_error($koneksi));
    } else {
        // echo "<script>
        //     alert('Profil berhasi diperbarui.');
        //     window.location = 'profil.php';
        // </script>";
        echo "<script>
        window.location = 'profil.php';
    </script>";
    }
}
mysqli_close($koneksi);
?>
