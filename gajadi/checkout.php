<?php
session_start();
include '../admin/konfig.php';
include 'cek.php';

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$username_customer = 'Guest'; // Default jika tidak ada session
if ($username) {
    $query = "SELECT username FROM customer WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $username_customer = $data['username'];
    }
}

if (!isset($_SESSION['biaya_pengiriman'])) {
  $_SESSION['biaya_pengiriman'] = 0; // Atau nilai default lainnya
}
?>
<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Checkout</title>
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
</head>


<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-navbar navbar-cust">
    <a class="navbar-left navbar-brand" href="#">
      <img src="../gambar/logoAntika.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
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
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>" href="logout.php"></a>
          <div class="nav-item dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown"><?php echo htmlspecialchars($username_customer); ?></a>
            <ul class="dropdown-menu">
              <li><a href="profil.php"><i class="lnr lnr-user"></i> <span>Profil</span></a></li>
              <li><a href="../admin/logout.php"><i class="lnr lnr-exit"></i> <span>Logout</span></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- start back -->
    <button class="btn btn-light mb-5" style="float: left; margin-left:30px; margin-top:-20px;" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> Kembali
    </button>
    <div style="clear: both;"></div>
<!-- end tombol back -->
<!-- Form input kode promo -->



<div class="container cont-keranjang">

  <!-- start tabel daftar produk yang mau di checkout -->
    <h1 style="padding-bottom: 50px">Checkout</h1>
    <table class="table table-striped">
        <thead>
            <tr class="table-head">
                <th style="text-align:left">Gambar</th>
                <th style="text-align:left;">Nama Produk</th>
                <th style="text-align:center;">Kuantitas</th>
                <th style="text-align:left;">Harga</th>
                <th style="text-align:left;">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $query = "SELECT customer_ID FROM customer WHERE username='$username'";
        $result = mysqli_query($koneksi, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $customer = mysqli_fetch_assoc($result);
            $customer_ID = $customer['customer_ID'];

            $query = "SELECT p.produk_ID, p.nama_produk, p.harga, p.gambar, c.quantity 
                      FROM cart c 
                      JOIN produk p ON c.produk_ID = p.produk_ID 
                      WHERE c.customer_ID = '$customer_ID'";
            $result = mysqli_query($koneksi, $query);

            $total = 0;

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $produk_name = $row['nama_produk'];
                    $harga = $row['harga'];
                    $quantity = $row['quantity'];
                    $produk_image = $row['gambar'];
                    $subtotal = $harga * $quantity;
                    $total += $subtotal;

                    echo "<tr>
                            <td><img src='../admin/{$produk_image}' alt='{$produk_name}' width='50'></td>
                            <td>{$produk_name}</td>
                            <td style='text-align:center;'>{$quantity}</td>
                            <td>Rp " . number_format($harga, 0, ',', '.') . "</td>
                            <td>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
                        </tr>";
                        
                }
                echo "<tr>
                        <td colspan='4' class='text-right'><strong>Total</strong></td>
                        <td><strong>Rp " . number_format($total, 0, ',', '.') . "</strong></td>

                    </tr>";
            } else {
                echo '<tr><td colspan="5">Keranjang kosong atau data produk tidak ditemukan.</td></tr>';
            }
        } else {
            echo '<tr><td colspan="5">Username tidak ditemukan.</td></tr>';
        }
        ?>
        </tbody>
    </table>
<!-- end tabel daftar produk yang mau di checkout -->

<!-- start untuk nampilin form penerima -->
    <div class="container cont-checkout">
        <h2 class="header text-center">Alamat Pengiriman</h2>
        <form action="pembayaran.php" method="post">
            <div class="form-group form-checkout">
                <label class="label-checkout" for="nama">Nama Lengkap</label>
                <input type="text" class="form-control form-checkout" id="nama" name="nama" required>
            </div>
            <div class="form-group form-checkout">
                <label class="label-checkout" for="alamat">Alamat Lengkap</label>
                <textarea class="form-control form-checkout" id="alamat" name="alamat" rows="3" required></textarea>
            </div>
            <div class="form-group form-checkout">
                <label class="label-checkout" for="nama_kota">Kota</label>
                <select class="form-control form-checkout" style="min-height: 30px !important;" id="nama_kota" name="nama_kota" required>
                  <option value="" disabled selected>Pilih Kota</option>
                  <?php
                  // Ambil data kota dan biaya pengiriman dari tabel shippingfee
                  $sql = "SELECT nama_kota, biaya_pengiriman FROM shippingfee";
                  $result = $koneksi->query($sql);

                  if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          echo '<option value="' . htmlspecialchars($row['nama_kota']) . '" data-biaya="' . htmlspecialchars($row['biaya_pengiriman']) . '">' . htmlspecialchars($row['nama_kota']) . '</option>';
                      }
                  } else {
                      echo '<option value="">Tidak ada data kota tersedia</option>';
                  }
                  ?>
              </select>
              <label class="label-checkout" style="font-weight: 100 !important;">
                  <p>Biaya Pengiriman: Rp <span id="biaya_pengiriman">0</span></p>
              </label>
              <input type="hidden" name="biaya_pengiriman" id="biaya_pengiriman_hidden" value="0">
              
              <input type="hidden" name="diskon" value="<?php echo $diskon; ?>">


            </div>
            <div class="form-group form-checkout">
                <label class="label-checkout" for="telepon">Nomor Telepon</label>
                <input type="text" class="form-control form-checkout" id="telepon" name="telepon" required>
            </div>
            <div class="form-group form-checkout">
                <label class="label-checkout" for="catatan">Catatan (Optional)</label>
                <textarea class="form-control form-checkout" id="catatan" name="catatan" rows="3"></textarea>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Lanjutkan ke Pembayaran</button>
            </div>
        </form>
    </div>
    <!-- end nampilin form penerima -->


</div>

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

<!-- Bootstrap JS dan dependencies lain-->

<!-- script unutk nampilin biaya pengiriman berdasarkan kota yang dipilih -->
<script>
document.getElementById('nama_kota').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var biayaPengiriman = selectedOption.getAttribute('data-biaya');
    document.getElementById('biaya_pengiriman').textContent = biayaPengiriman ? biayaPengiriman.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '0';
    document.getElementById('biaya_pengiriman_hidden').value = biayaPengiriman;
});
</script>
<script>
function goBack() {
    window.history.back();
}
</script>
<script>
  document.getElementById('nama_kota').addEventListener('change', function() {
      // Ambil elemen dropdown yang dipilih
      var selectedOption = this.options[this.selectedIndex];
      
      // Ambil data biaya dari data atribut 'data-biaya'
      var biayaPengiriman = selectedOption.getAttribute('data-biaya');
      
      // Jika ada biaya pengiriman, perbarui tampilannya
      if (biayaPengiriman) {
          // Update tampilan biaya pengiriman
          document.getElementById('biaya_pengiriman').innerText = biayaPengiriman;
          
          // Simpan nilai biaya pengiriman ke input hidden untuk dikirim ke server saat submit
          document.getElementById('biaya_pengiriman_hidden').value = biayaPengiriman;
      }
  });
</script>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3G7E3k7cLk7aXzb30O8vI2HgEKn3rD1exuWUtW5J7dd2Z" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-JZR6Spejh4U02d8UazE1zE6qR9x18d5u5Z4x8kR9voEukT0j8s7hdIjCZRIbzz40" crossorigin="anonymous"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/scripts/klorofil-common.js"></script>

</body>
</html>
