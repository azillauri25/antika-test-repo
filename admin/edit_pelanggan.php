<?php
session_start();
include 'konfig.php';
include 'cek.php';

// Ambil customer_ID dari URL
$customer_ID = isset($_GET['customer_ID']) ? $_GET['customer_ID'] : '';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$nama_karyawan = 'Guest';
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan']; // Ambil nama_karyawan dari hasil query
    }
}

if (!$customer_ID) {
    header('Location: pelanggan.php'); // Redirect jika customer_ID tidak ada
    exit();
}

// Ambil data customer
$query = "SELECT * FROM customer WHERE customer_ID = '" . mysqli_real_escape_string($koneksi, $customer_ID) . "'";
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<div class='alert alert-danger'>Data customer tidak ditemukan.</div>";
    exit();
}

$data = mysqli_fetch_assoc($result);

// Proses update data customer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_customer = mysqli_real_escape_string($koneksi, $_POST['nama_customer']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email_customer = mysqli_real_escape_string($koneksi, $_POST['email_customer']);
    $nomor_telepon_customer = mysqli_real_escape_string($koneksi, $_POST['nomor_telepon_customer']);
    $alamat_customer = mysqli_real_escape_string($koneksi, $_POST['alamat_customer']);
    $ttl_customer = mysqli_real_escape_string($koneksi, $_POST['ttl_customer']);
    $password_baru = mysqli_real_escape_string($koneksi, $_POST['password_baru']);

    // Update data tanpa password
    $updateQuery = "UPDATE customer SET 
        nama_customer = '$nama_customer',
        username = '$username',
        email_customer = '$email_customer',
        nomor_telepon_customer = '$nomor_telepon_customer',
        alamat_customer = '$alamat_customer',
        ttl_customer = '$ttl_customer'
        WHERE customer_ID = '$customer_ID'";

    if (!empty($password_baru)) {
        $password_baru = mysqli_real_escape_string($koneksi, $password_baru);
        $updateQuery = "UPDATE customer SET 
            nama_customer = '$nama_customer',
            username = '$username',
            email_customer = '$email_customer',
            nomor_telepon_customer = '$nomor_telepon_customer',
            alamat_customer = '$alamat_customer',
            ttl_customer = '$ttl_customer',
            password = '$password_baru'
            WHERE customer_ID = '$customer_ID'";
    }

    if (mysqli_query($koneksi, $updateQuery)) {
        echo "<div class='alert alert-success'>Data customer berhasil diperbarui!</div>";
        // Redirect kembali ke halaman customer setelah update
        header('Location: pelanggan.php');
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui data customer: " . mysqli_error($koneksi) . "</div>";
    }
}
?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Ubah password pelanggan - Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="icon" href="../gambar/logoAntika.png" type="image/png"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleadmin.css">
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- start sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="../gambar/logoAntika.png" alt="Logo Antika Anggrek">
        <h3>Antika Anggrek</h3>
    </div>
    <a style="padding-top:50px" href="index.php"><i class="fa fa-home"></i> Home</a>
    <a href="produk.php"><i class="fa fa-box"></i> Produk</a>
    <a href="pesanan.php"><i class="fa fa-shopping-cart"></i> Pesanan</a>
    <a href="pelanggan.php"><i class="fa fa-users"></i> customer</a>
    <a href="promo.php"><i class="fa fa-gift"></i> Promo</a>
    <a href="ulasan_komplain.php"><i class="fa fa-star"></i> Ulasan dan Komplain</a>
</div>
<!-- end sidebar -->

<!-- start navbar -->
<div class="header">
    <div class="username">
    Selamat Datang, Admin <strong><?php echo htmlspecialchars($nama_karyawan); ?></strong>
    </div>
    <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i></a>
</div>
<!-- end navbar -->

<!-- start main content -->
<div class="content content-main" style="margin-top: 50px;">
    <button class="btn btn-light mb-3" style="float: left;" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> Kembali
    </button>
    <div style="clear: both;"></div>
    <!-- end tombol back -->
     
    <h2 class="mb-5 mt-5 text-left">Ubah Password Pelanggan</h2>

    <form method="POST" action="">
        <div class="form-group">
            <label for="nama_customer">Nama Pelanggan:</label>
            <input type="text" id="nama_customer" name="nama_customer" class="form-control" value="<?php echo htmlspecialchars($data['nama_customer']); ?>" required readOnly>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($data['username']); ?>" required readOnly>
        </div>
        <div class="form-group">
            <label for="email_customer">Email Pelanggan:</label>
            <input type="email" id="email_customer" name="email_customer" class="form-control" value="<?php echo htmlspecialchars($data['email_customer']); ?>" required readOnly>
        </div>
        <div class="form-group">
            <label for="nomor_telepon_customer">Telepon Pelanggan:</label>
            <input type="text" id="nomor_telepon_customer" name="nomor_telepon_customer" class="form-control" value="<?php echo htmlspecialchars($data['nomor_telepon_customer']); ?>" required readOnly>
        </div>
        <div class="form-group">
            <label for="alamat_customer">Alamat Pelanggan:</label>
            <textarea id="alamat_customer" style="resize:none;"name="alamat_customer" class="form-control" rows="4" required readOnly><?php echo htmlspecialchars($data['alamat_customer']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="password_lama">Password Saat Ini:</label>
            <input type="text" id="password_lama" name="password_lama" class="form-control" value="<?php echo htmlspecialchars($data['password']); ?>" required readOnly>
        </div>
        <div class="form-group">
            <label for="password_baru">Password Baru:</label>
            <input type="password" id="password_baru" name="password_baru" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary mt-5 m-2">Ubah</button>
        <button type="button" class="btn btn-light mt-5 m-2" onclick="goBack()">Batal</button>
    </form>
</div>
<!-- end main content -->


<script>
function goBack() {
    window.history.back();
}
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
