<!doctype html>
<html lang="en" class="fullscreen-bg">


<head>
    <title>Antika Anggrek | Home - Customer </title>
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

    <?php
      session_start();
      include '../admin/konfig.php';
      include 'cek.php'; //memastikan user udah login saat akses ke halaman ini

      // Ambil username dari session
      $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

      // Query untuk mengambil nama kustomer yang lagi login
      $username_customer = 'Guest'; // Default jika tidak ada session (as guest)
      if ($username) {
          $query = "SELECT nama_customer FROM customer WHERE username='$username'";
          $result = mysqli_query($koneksi, $query);

          if ($result && mysqli_num_rows($result) > 0) {
              $data = mysqli_fetch_assoc($result);
              $username_customer = $data['nama_customer'];
          }
      }
    ?>
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

<!-- start card produk -->
<div class="upper-deskripsi">
  <div class="desc-produk text-center"><h1>Produk Kami</h1></div>
</div>

<div class="container cont-body">
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php
    $sql = "SELECT produk_ID, gambar_produk, nama_produk, harga_produk FROM produk WHERE stok_produk > 0 AND request_tambah_produk = 'disetujui' "; //kalo stoknya 0 kaga ditampilin

    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $image_path = $row["gambar_produk"];
            echo '<div class="col col-produk">
            <div class="card h-100">
              <img src="../admin/' . $image_path . '" class="card-img-top" alt="' . $row["nama_produk"] . '"> 
              <div class="card-body">
                <h5 class="card-title">' . $row["nama_produk"] . '</h5>
                <p class="card-harga">Rp ' . number_format($row["harga_produk"], 0, ',', '.') . '</p>
                <a href="detail_produk.php?produk_ID=' . $row["produk_ID"] . '" class="btn btn-primary">Beli</a>
              </div>
            </div>
          </div>';
    
        }
    } else {
        echo '<div class="col"><p>0 results</p></div>'; //debugging
    }
    $koneksi->close();
    ?>
  </div>
</div>
<!-- end card produk -->

<!-- start footer -->
<footer class=" container-footer text-white mt-5">
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

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3G7E3k7cLk7aXzb30O8vI2HgEKn3rD1exuWUtW5J7dd2Z" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-JZR6Spejh4U02d8UazE1zE6qR9x18d5u5Z4x8kR9voEukT0j8s7hdIjCZRIbzz40" crossorigin="anonymous"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/scripts/klorofil-common.js"></script>

</body>
</html>
