<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Detail Produk </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- favicon -->
    <link rel="icon" href="../gambar/logoAntika.png" type="image/png"> 

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"> <!-- font Montserrat-->

    <!-- koneksi -->
    <?php
      session_start();
      include '../admin/konfig.php';
      include 'cek.php';

      // Ambil username dari session
      $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
      $username_customer = 'Guest'; // Default jika tidak ada session (as guest)
      if ($username) {
          $query = "SELECT nama_customer FROM customer WHERE username='$username'";
          $result = mysqli_query($koneksi, $query);

          if ($result && mysqli_num_rows($result) > 0) {
              $data = mysqli_fetch_assoc($result);
              $username_customer = $data['nama_customer'];
          }
      }
      $produk_ID = isset($_GET['produk_ID']) ? intval($_GET['produk_ID']) : 0;
      

      // Query untuk mengambil detail produk
      $query = "SELECT gambar_produk, nama_produk, deskripsi_produk, stok_produk, harga_produk FROM produk WHERE produk_ID = $produk_ID";
      $result = mysqli_query($koneksi, $query);

      if ($result && mysqli_num_rows($result) > 0) {
          $produk = mysqli_fetch_assoc($result);
      } else {
          $produk = null;
      }

      // Ambil parameter produk_ID dari URL
      $produk_ID = isset($_GET['produk_ID']) ? $_GET['produk_ID'] : '';

      // Query untuk mengambil detail produk
      $query = "SELECT gambar_produk, nama_produk, deskripsi_produk, stok_produk, harga_produk FROM produk WHERE produk_ID = '$produk_ID'";
      $result = mysqli_query($koneksi, $query);

      if ($result && mysqli_num_rows($result) > 0) {
          $produk = mysqli_fetch_assoc($result);
      } else {
          $produk = null;
      }
      ?>
      <!-- end koneksi -->

</head>
<body>

<!-- start navbar -->
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-navbar navbar-cust">
    <a class="navbar-left navbar-brand" href="#">
      <img style="margin-top:-5px" src="../gambar/logoAntika.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
      Antika Anggrek
    </a>
    <div class="navbar-right d-flex align-items-center">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse d-flex" id="navbarNavAltMarkup">
        <div class="navbar-nav">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'faqs.php' ? 'active' : ''; ?>" href="../faqs.php">FAQs</a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="https://api.whatsapp.com/send?phone=6281545863325&text=Halo,%20min%20mau%20tanya%20tentang%20produk%20anggrek%20dong!" target="_blank">Chat Admin</a>
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pesanan.php' ? 'active' : ''; ?>" href="pesanan.php">Pesanan</a>
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'keranjang.php' ? 'active' : ''; ?>" href="keranjang.php">Keranjang</a>
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'ulasan_komplain.php' ? 'active' : ''; ?>" href="ulasan_komplain.php">Ulasan & Komplain</a>
          <a style="margin-right:10px;"></a>

          <div class="nav-item dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown"><strong><?php echo htmlspecialchars($username_customer); ?></strong></a>
            <ul class="dropdown-menu">
              <li><a href="profil.php"><i class="lnr"></i> <span>Profil</span></a></li>
              <!-- <li><a href=" ulasan_komplain.php"><i class="lnr"></i> <span>Ulasan dan Komplain</span></a></li> -->
              <!-- <li><a href="../admin/logout.php"><i class="lnr lnr-exit"></i> <span>Logout</span></a></li> -->
            </ul>
          </div>
          <a href="../admin/logout.php" class="logout"><i class="fa fa-sign-out-alt" style="margin-left:10px; margin-top:8px; color:black;"></i></a>
        </div>
      </div>
    </div>
  </div>
</nav>
<!-- end navbar -->

<!-- start back -->
    <button class="btn btn-light mb-5" style="float: left; margin-left:30px; margin-top:-20px;" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> Kembali
    </button>
    <div style="clear: both;"></div>
<!-- end tombol back -->

<!-- start nampilin produk -->
 <div class="container cont-keranjang" style="min-height:20vh; margin-top:-20px;">
  <h1>Detail Produk</h1>
 </div>
<div class="container cont-profile" style="margin-top: -10px;">
  <?php if ($produk): ?>
    <div class="row">
      <div class="col-md-6">
        <img src="../admin/<?php echo htmlspecialchars($produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" width="300px" height="auto">
      </div>
      <div class="col-md-6">
        <h3><strong><?php echo htmlspecialchars($produk['nama_produk']); ?></strong></h3>
        <!-- <p><strong>Deskripsi:</strong> <?php echo htmlspecialchars($produk['deskripsi_produk']); ?></p> -->
        <p><strong>Deskripsi:</strong></p>
        <div><?php echo $produk['deskripsi_produk']; ?></div>
        <p class="mt-3"><strong>Stok:</strong> <?php echo htmlspecialchars($produk['stok_produk']); ?></p>
        <p><strong>Harga:</strong> Rp <?php echo number_format($produk['harga_produk'], 0, ',', '.'); ?></p>
        
        <!-- Input Kuantitas dan Kontrol -->
        <!-- <div class="quantity-controls">
          <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity()">-</button>
          <input type="text" id="kuantitas" value="1" readonly>
          <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity()">+</button>
        </div> -->

        <!-- Formulir untuk menambahkan ke keranjang -->
        <form action="tambah_keranjang.php" method="POST">
            <input type="hidden" name="produk_ID" value="<?php echo htmlspecialchars($produk_ID); ?>">
            <input type="hidden" name="kuantitas" id="kuantitas-input" value="1">
            <button type="submit" class="btn btn-primary mt-3">Tambahkan ke Keranjang</button>
        </form>
      </div>
    </div>

  <?php else: ?>
    <p>Produk tidak ditemukan.</p>
  <?php endif; ?>

</div>
<!-- end container nampilin detail produk -->

<!-- start footer -->
<footer class="container-footer text-white mt-5">
  <div class="container">
    <div class="row py-4">
      <div class="col-md-6 d-flex align-items-center">
        <img src="../gambar/logoAntika.png" alt="Logo Antika Anggrek" width="120" height="120" class="mr-3">
        <div>
          <h4 class="brand-footer">Antika Anggrek</h4>
          <p>Jl. RM Harsono Taman Anggrek Ragunan, Ragunan, Pasar Minggu, Jakarta Selatan, DKI Jakarta, Indonesia</p>
          <p>WhatsApp: 081218698361</p>
        </div>
      </div>
      <div class="col-md-2"></div>
      <div class="col-md-2">
        <h4 class="brand-footer">Branch</h4>
        <ul class="list-unstyled">
          <li><a href="#" class="text-white">Taman Mini</a></li>
          <li><a href="#" class="text-white">Darmawangsa</a></li>
          <li><a href="#" class="text-white">Tebet</a></li>
          <li><a href="#" class="text-white">Cikampek</a></li>
        </ul>
      </div>
      <div class="col-md-2">
        <h4 class="brand-footer">Media Sosial</h4>
        <ul class="list-unstyled">
          <li><a href="#" class="text-white">Facebook</a></li>
          <li><a href="#" class="text-white">Instagram</a></li>
          <li><a href="#" class="text-white">TikTok</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="bg-secondary text-center py-2">
    <p class="mb-0">Antika Anggrek | 2024</p>
  </div>
</footer>
<!-- end footer -->

<!-- atur untuk tombol increase dan decrease kuantitas -->
<script>
  var maxStock = <?php echo $produk['stok']; ?>; // Ambil nilai stok dari database kalo masukin ke keranjang melebihi kuantitas stok itu nggak bisa
  function increaseQuantity() {
    var quantityInput = document.getElementById('kuantitas');
    var currentQuantity = parseInt(quantityInput.value);
    if (!isNaN(currentQuantity)) {
      if (currentQuantity < maxStock) {
        quantityInput.value = currentQuantity + 1;
        document.getElementById('kuantitas-input').value = quantityInput.value; // Update hidden input
      } else {
        alert('Stok tidak mencukupi');
      }
    }
  }

  function decreaseQuantity() {
    var quantityInput = document.getElementById('kuantitas');
    var currentQuantity = parseInt(quantityInput.value);
    if (!isNaN(currentQuantity) && currentQuantity > 1) {
      quantityInput.value = currentQuantity - 1;
      document.getElementById('kuantitas-input').value = quantityInput.value; // Update hidden input
    }
  }
</script>

<script>
function goBack() {
    window.history.back();
}
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3G7E3k7cLk7aXzb30O8vI2HgEKn3rD1exuWUtW5J7dd2Z" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-JZR6Spejh4U02d8UazE1zE6qR9x18d5u5Z4x8kR9voEukT0j8s7hdIjCZRIbzz40" crossorigin="anonymous"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/scripts/klorofil-common.js"></script>


</body>
</html>
