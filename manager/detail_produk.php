<?php
session_start();
include '../admin/konfig.php';
include 'cek.php'; // Memastikan user sudah login saat akses ke halaman ini

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Query untuk mengambil nama admin yang sedang login
$nama_karyawan = 'Guest'; // Default jika tidak ada session (as guest)
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan'];
    }
}

// $produk_ID = isset($_GET['produk_ID']) ? intval($_GET['produk_ID']) : 0;
      

// // Query untuk mengambil detail produk
// $query = "SELECT gambar, nama_produk, deskripsi, stok, harga FROM produk WHERE produk_ID = $produk_ID";
// $result = mysqli_query($koneksi, $query);

// if ($result && mysqli_num_rows($result) > 0) {
//     $produk = mysqli_fetch_assoc($result);
// } else {
//     $produk = null;
// }

$produk_ID = isset($_GET['produk_ID']) ? $_GET['produk_ID'] : '';

// Query untuk mengambil detail produk
$query = "SELECT gambar_produk, nama_produk, deskripsi_produk, stok_produk, harga_produk FROM produk WHERE produk_ID = '$produk_ID'";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $produk = mysqli_fetch_assoc($result);
} else {
    $produk = null;
}
$query_perubahan = "SELECT gambar_produk, nama_produk, deskripsi_produk, stok_produk, harga_produk FROM perubahan_produk WHERE produk_ID = '$produk_ID'";
$result_perubahan = mysqli_query($koneksi, $query_perubahan);

if ($result_perubahan && mysqli_num_rows($result_perubahan) > 0) {
    $produk2 = mysqli_fetch_assoc($result_perubahan);
} else {
    $produk2 = null;
}

$isPerubahanAda = $produk2 ? true : false;
?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Detail Produk - Manager</title>
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
    <a href="promo.php"><i class="fa fa-gift"></i> Promo</a>
</div>
<!-- end sidebar -->

<!-- start navbar -->
<div class="header">
    <div class="username">
        Selamat Datang, Manager <strong><?php echo htmlspecialchars($nama_karyawan); ?></strong>
    </div>
    <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i></a>
</div>
<!-- end navbar -->

<!-- start main content -->
<div class="content">

    <button class="btn btn-light mb-3" style="float: left;" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> Kembali
    </button>
    <div style="clear: both;"></div>
    <!-- end tombol back -->


    <h2 class="mb-5 mt-5 text-left">Detail Produk</h2>
    <div class="container cont-profile text-left" >
  <?php if ($produk): ?>
    <div class="row">
      <div class="col-md-6">
        <img src="../admin/<?php echo htmlspecialchars($produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" width="100%" height="auto">
      </div>
      <div class="col-md-6">
        <h3><strong><?php echo htmlspecialchars($produk['nama_produk']); ?></strong></h3>
        <p><strong>Deskripsi:</strong></p>
        <div><?php echo $produk['deskripsi_produk']; ?></div>
        <p><strong>Stok:</strong> <?php echo htmlspecialchars($produk['stok_produk']); ?></p>
        <p><strong>Harga:</strong> Rp <?php echo number_format($produk['harga_produk'], 0, ',', '.'); ?></p>
        

      </div>
    </div>

  <?php else: ?>
    <p>Produk tidak ditemukan.</p>
  <?php endif; ?>

</div>
<div id="ubah-produk" style="display: <?php echo $isPerubahanAda ? 'block' : 'none'; ?>; margin-top:-150px;">
        <h3 class="mb-5 mt-5 text-center">Detail Perubahan Produk</h3>
<div class="container cont-profile text-left">
    <?php if ($produk && $produk2): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Data Awal</th>
                    <th class="text-center">Data Perubahan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">
                        <img src="../admin/<?php echo htmlspecialchars($produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" width="100px" height="auto">
                    </td>
                    <td class="text-center">
                        <img src="../admin/<?php echo htmlspecialchars($produk2['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk2['nama_produk']); ?>" width="100px" height="auto">
                    </td>
                </tr>
                <tr>
                    <td><strong>Nama Produk: </strong><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                    <td><strong>Nama Produk: </strong><?php echo htmlspecialchars($produk2['nama_produk']); ?></td>
                </tr>
                <tr>
                <td><strong>Deskripsi: </strong><?php echo($produk['deskripsi_produk']); ?></td>
                <td><strong>Deskripsi: </strong><?php echo($produk2['deskripsi_produk']); ?></td>


                </tr>
                <tr>
                    <td><strong>Stok: </strong><?php echo htmlspecialchars($produk['stok_produk']); ?></td>
                    <td><strong>Stok: </strong><?php echo htmlspecialchars($produk2['stok_produk']); ?></td>
                </tr>
                <tr>
                    <td><strong>Harga: </strong>Rp <?php echo number_format($produk['harga_produk'], 0, ',', '.'); ?></td>
                    <td><strong>Harga: </strong>Rp <?php echo number_format($produk2['harga_produk'], 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>Perubahan produk tidak ditemukan atau produk asli tidak tersedia.</p>
    <?php endif; ?>
</div>
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
