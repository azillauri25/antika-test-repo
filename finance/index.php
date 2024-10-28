<?php
session_start();
include '../admin/konfig.php';
include 'cek.php'; // Memastikan user sudah login saat akses ke halaman ini

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Query untuk mengambil nama pengguna yang sedang login
$nama_karyawan = 'Guest'; // Default jika tidak ada session (as guest)
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan'];
    }
}

// Total penjualan
$query_total_penjualan = "SELECT SUM(harga_total) as total_penjualan FROM orders WHERE status_pesanan = 'pesanan selesai'";
$result_total_penjualan = mysqli_query($koneksi, $query_total_penjualan);
$data_total_penjualan = mysqli_fetch_assoc($result_total_penjualan);
$total_penjualan = $data_total_penjualan['total_penjualan'];

// Jumlah customer
$query_jumlah_customer = "SELECT COUNT(*) as total_customer FROM customer";
$result_jumlah_customer = mysqli_query($koneksi, $query_jumlah_customer);
$data_jumlah_customer = mysqli_fetch_assoc($result_jumlah_customer);
$total_customer = $data_jumlah_customer['total_customer'];

// Jumlah produk
$query_jumlah_produk = "SELECT COUNT(*) as total_produk FROM produk WHERE request_tambah_produk = 'disetujui'";
$result_jumlah_produk = mysqli_query($koneksi, $query_jumlah_produk);
$data_jumlah_produk = mysqli_fetch_assoc($result_jumlah_produk);
$total_produk = $data_jumlah_produk['total_produk'];
?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Home - Finance</title>
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
    <!-- <a href="produk.php"><i class="fa fa-box"></i> Produk</a>
    <a href="pesanan.php"><i class="fa fa-shopping-cart"></i> Pesanan</a>
    <a href="pelanggan.php"><i class="fa fa-users"></i> Pelanggan</a>
    <a href="ulasan_komplain.php"><i class="fa fa-star"></i> Ulasan dan Komplain</a> -->
    <a href="Pesanan.php"><i class="fa fa-shopping-cart"></i> Pesanan</a>
</div>
<!-- end sidebar -->

<!-- start navbar -->
<div class="header">
    <div class="username">
    Selamat Datang, Finance <strong><?php echo htmlspecialchars($nama_karyawan); ?></strong>
    </div>
    <a href="../admin/logout.php" class="logout"><i class="fa fa-sign-out-alt"></i></a>
</div>
<!-- end navbar -->

<!-- start content -->
<div class="content">
    <div class="row mt-5">
        <!-- Kotak Jumlah Penjualan -->
        <div class="col-md-4">
            <div class="card text-dark bg-light">
                <div class="card-header card-content"><strong>Jumlah Penjualan</strong></div>
                <div class="card-body">
                    <h3 class="card-title">Rp <?php echo number_format($total_penjualan, 0, ',', '.'); ?></h3>
                    <p class="card-text">Total penjualan selesai</p>
                </div>
            </div>
        </div>

        <!-- Kotak Jumlah Customer -->
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3">
                <div class="card-header card-content"><strong>Jumlah Pelanggan</strong></div>
                <div class="card-body">
                    <h3 class="card-title"><?php echo $total_customer; ?></h3>
                    <p class="card-text">Total pelanggan terdaftar</p>
                </div>
            </div>
        </div>

        <!-- Kotak Jumlah Produk -->
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3">
                <div class="card-header card-content"><strong>Jumlah Produk</strong></div>
                <div class="card-body">
                    <h3 class="card-title"><?php echo $total_produk; ?></h3>
                    <p class="card-text">Total produk</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end content -->

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3G7E3k7cLk7aXzb30O8vI2HgEKn3rD1exuWUtW5J7dd2Z" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-JZR6Spejh4U02d8UazE1zE6qR9x18d5u5Z4x8kR9voEukT0j8s7hdIjCZRIbzz40" crossorigin="anonymous"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/scripts/klorofil-common.js"></script>

</body>
</html>
