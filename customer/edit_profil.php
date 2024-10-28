<!doctype html>
<html lang="en" class="fullscreen-bg">


<head>
    <title>Antika Anggrek | Ubah Data Profil</title>
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

      // Default jika tidak ada session
      $nama_customer = 'Guest';
      $email_customer = '';
      $nomor_telepon_customer = '';
      $alamat_customer = '';

      if ($username) {
          // Query untuk mengambil nama, email, dan alamat customer
          $query = "SELECT nama_customer, email_customer, nomor_telepon_customer, alamat_customer, nama_kota FROM customer WHERE username='$username'";
          $result = mysqli_query($koneksi, $query);

          if ($result && mysqli_num_rows($result) > 0) {
              $data = mysqli_fetch_assoc($result);
              $nama_customer = $data['nama_customer'];
              $email_customer = $data['email_customer'];
              $nomor_telepon_customer = $data['nomor_telepon_customer'];
              $alamat_customer = $data['alamat_customer'];
              $nama_kota = $data['nama_kota'];
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

<!-- start back -->
    <button class="btn btn-light mb-3" style="float: left; margin-left:50px; margin-top:-20px;" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> Kembali
    </button>
    <div style="clear: both;"></div>
<!-- end tombol back -->

<!-- start container profile -->
<div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle">
                <div class="auth-box registrasi">
                    <div class="content">
                        <div class="header">
                            <div class="logo text-center"><h1>UBAH DATA PROFIL</h1></div>
                            <p class="lead">Silahkan ubah data diri Anda</p>
                        </div>
                        <form class="form-auth-small" method="post" action="proses_edit_cust.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label id="label-regis">Nama Lengkap</label>
                                <input type="text" id="namaLengkap" class="form-control" name="nama_customer" value="<?php echo htmlspecialchars($data['nama_customer']); ?>" placeholder="Nama lengkap" required="">
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Email</label>
                                <input type="email" id="email" class="form-control" name="email_customer" value="<?php echo htmlspecialchars($data['email_customer']); ?>" placeholder="Email" required="">
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Nomor Telepon</label>
                                <input type="tel" id="nomorTelepon" class="form-control" name="nomor_telepon_customer" value="<?php echo htmlspecialchars($data['nomor_telepon_customer']); ?>" placeholder="Nomor telepon" required="">
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Username</label>
                                <input type="text" id="usernameCust" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username" readonly>
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Password</label>
                                <input type="password" id="passwordCust" class="form-control" name="password" placeholder="Password (Kosongkan jika tidak diubah)">
                            </div>
                            <div class="form-group">
                                <label for="nama_kota">Pilih Kota:</label>
                                <select id="nama_kota" name="nama_kota" class="form-control" required="">
                                    <option value="" disabled selected>-Silahkan pilih kota anda-</option>
                                    <option value="Depok" <?php echo ($nama_kota == 'Depok') ? 'selected' : ''; ?>>Depok</option>
                                    <option value="Tangerang Selatan" <?php echo ($nama_kota == 'Tangerang Selatan') ? 'selected' : ''; ?>>Tangerang Selatan</option>
                                    <option value="Tangerang" <?php echo ($nama_kota == 'Tangerang') ? 'selected' : ''; ?>>Tangerang</option>
                                    <option value="Bogor Kota" <?php echo ($nama_kota == 'Bogor Kota') ? 'selected' : ''; ?>>Bogor Kota</option>
                                    <option value="Jakarta Selatan" <?php echo ($nama_kota == 'Jakarta Selatan') ? 'selected' : ''; ?>>Jakarta Selatan</option>
                                    <option value="Jakarta Utara" <?php echo ($nama_kota == 'Jakarta Utara') ? 'selected' : ''; ?>>Jakarta Utara</option>
                                    <option value="Jakarta Pusat" <?php echo ($nama_kota == 'Jakarta Pusat') ? 'selected' : ''; ?>>Jakarta Pusat</option>
                                    <option value="Jakarta Barat" <?php echo ($nama_kota == 'Jakarta Barat') ? 'selected' : ''; ?>>Jakarta Barat</option>
                                    <option value="Jakarta Timur" <?php echo ($nama_kota == 'Jakarta Timur') ? 'selected' : ''; ?>>Jakarta Timur</option>
                                    <option value="Bekasi" <?php echo ($nama_kota == 'Bekasi') ? 'selected' : ''; ?>>Bekasi</option>
                                    <option value="Lainnya" <?php echo ($nama_kota == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>

                                    <?php
                                    // Query buat ambil daftar kota yang ada di database
                                    $query_kota = "SELECT DISTINCT nama_kota FROM customer";
                                    $result_kota = mysqli_query($koneksi, $query_kota);

                                    if ($result_kota && mysqli_num_rows($result_kota) > 0) {
                                      while ($row_kota = mysqli_fetch_assoc($result_kota)) {
                                          // Debug output
                                          echo 'Kota dari database: ' . $row_kota['nama_kota'] . '<br>';
                                          $selected = ($row_kota['nama_kota'] == $nama_kota) ? 'selected' : '';
                                          echo '<option value="' . htmlspecialchars($row_kota['nama_kota']) . '" ' . $selected . '>' . htmlspecialchars($row_kota['nama_kota']) . '</option>';
                                      }
                                  } else {
                                      echo "Tidak ada kota yang ditemukan.";
                                  }
                                  
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label id="label-regis">Alamat Lengkap</label>
                                <input type="text" id="alamatLengkap" class="form-control" name="alamat_customer" value="<?php echo htmlspecialchars($data['alamat_customer']); ?>" placeholder="Alamat Lengkap" required="">
                            </div>
                            <div class="button-group text-center mt-5">
                              <!-- <a href="index.php" class="btn btn-outline-secondary d-inline-block mr-2">Cancel</a>
                              <button id="simpan-edit" type="submit" class="btn btn-primary d-inline-block">Simpan data</button> -->
                              <button type="submit" class="btn btn-primary mt-3 m-2 mb-5" id="simpan-edit">Simpan</button>
                              <button type="button" class="btn btn-light mt-3 m-2 mb-5" onclick="goBack()">Batal</button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
<!-- end container profile-->

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
