<?php
session_start();
include 'konfig.php';
include 'cek.php'; // Memastikan user sudah login saat akses ke halaman ini

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Query untuk mengambil nama admin yang sedang login
$nama_karyawan = 'Guest';
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan']; // Ambil nama_karyawan dari hasil query
    }
}
?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
<title>Antika Anggrek | Tambah Produk -Admin</title>
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
    <a href="pelanggan.php"><i class="fa fa-users"></i> Pelanggan</a>
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

<!-- start tambah produk -->
<div class="content content-main">
    <button class="btn btn-light mb-3" style="float: left;" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> Kembali
    </button>
    <div style="clear: both;"></div>
    
    <h2 class="mb-5 mt-5 text-left">Tambah Produk</h2>
    
    <form action="proses_tambah_produk.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi Produk</label>
            <textarea class="form-control"style="resize:none;"id="deskripsi" name="deskripsi_produk" rows="8" required></textarea>
        </div>
        <div class="form-group">
            <label for="stok">Stok Produk</label>
            <input type="number" class="form-control" id="stok" name="stok_produk" required>
        </div>
        <div class="form-group">
            <label for="harga">Harga Produk</label>
            <input type="number" class="form-control" id="harga" name="harga_produk" required>
        </div>
        <div class="form-group">
            <label for="gambar">Gambar Produk</label>
            <input type="file" class="form-control-file" id="gambar" name="gambar_produk" accept="image/*" onchange="previewImage(event)" required>
            <img id="gambar-preview" src="#" alt="Preview Gambar" style="display:none; max-width: 100px; margin-top: 10px;">
        </div>
        <button type="submit" class="btn btn-primary mt-5 m-2">Simpan</button>
        <button type="button" class="btn btn-light mt-5 m-2" onclick="goBack()">Batal</button>

    </form>
</div>
<!-- end tambah produk -->

<script>
    function previewImage(event) {
        var reader = new FileReader();
        var image = document.getElementById('gambar-preview');
        reader.onload = function() {
            image.src = reader.result;
            image.style.display = 'block';
        }
        if (event.target.files.length > 0) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
            image.style.display = 'none';
        }
    }
</script>
<script>
function goBack() {
    window.history.back();

}
</script>
</body>
</html>
