<!doctype html>
<html lang="en">
<head>
    <title>Antika Anggrek | Detail Pesanan</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- favicon -->
    <link rel="icon" href="../gambar/logoAntika.png" type="image/png"> 

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleadmin.css">
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

        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $username_customer = 'Guest'; // Default jika tidak ada session
        if ($username) {
            $query = "SELECT nama_customer FROM customer WHERE username='$username'";
            $result = mysqli_query($koneksi, $query);
  
            if ($result && mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $username_customer = $data['nama_customer'];
            }
        }

        // Ambil order_ID dari URL
        $order_ID = isset($_GET['order_ID']) ? $_GET['order_ID'] : '';

        // Ambil data pesanan
        $query = "SELECT o.order_ID, o.status_pesanan, o.harga_total, o.nama_penerima, d.alamat_penerima, d.nomor_telepon_penerima, d.biaya_pengiriman,
                  d.produk_ID, d.catatan_pesanan, d.kuantitas, d.harga_produk, p.nama_produk, p.gambar_produk, pr.nominal_diskon, pb.bukti_bayar
                  FROM orders o
                  JOIN order_details d ON o.order_ID = d.order_ID
                  JOIN produk p ON d.produk_ID = p.produk_ID
                  JOIN pembayaran pb ON o.order_ID = pb.order_ID
                  LEFT JOIN promo pr ON d.promo_ID = pr.promo_ID
                  WHERE o.order_ID = '$order_ID'
                  GROUP BY d.produk_ID
                  ";
        $result = mysqli_query($koneksi, $query);
        
        if (!$result) {
            die("Query gagal: " . mysqli_error($koneksi));
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

<div class="container cont-keranjang">
  <div class="container">
    <h1 class="mb-5">Detail Pesanan</h1>
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
      <?php
      
      $orderData = [];
      $showComplaintButton = true;
      while ($order = mysqli_fetch_assoc($result)):
        // Save order data
        if (!isset($orderData['order_ID'])) {
            $orderData['order_ID'] = $order['order_ID'];
            $orderData['status_pesanan'] = $order['status_pesanan'];
            $orderData['harga_total'] = $order['harga_total'];
            $orderData['nama_penerima'] = $order['nama_penerima'];
            $orderData['alamat_penerima'] = $order['alamat_penerima'];
            $orderData['nomor_telepon_penerima'] = $order['nomor_telepon_penerima'];
            $orderData['biaya_pengiriman'] = $order['biaya_pengiriman'];
            $orderData['nominal_diskon'] = $order['nominal_diskon'];
            $orderData['catatan_pesanan'] = $order['catatan_pesanan'];
            $orderData['bukti_bayar'] = $order['bukti_bayar'];
            $orderData['produk'] = [];
        }

        // Add produk to the order data
        $orderData['produk'][] = [
            'produk_ID' => $order['produk_ID'],
            'kuantitas' => $order['kuantitas'],
            'harga_produk' => $order['harga_produk'],
            'nama_produk' => $order['nama_produk'],
            'gambar_produk' => $order['gambar_produk']
        ];
      endwhile;
      ?>
      
      <!-- detail pesanan -->
      
      <div class="row">
        <div class="col-md-3 text-left">
          <p><strong>Nama Penerima</strong></p>
          <p><strong>Alamat Penerima</strong></p>
          <p><strong>Nomor Telepon Penerima</strong></p>
          <p><strong>Catatan Pesanan</strong></p>
        </div>
        <div class="col-md-9 text-left">
          <p>: <?php echo htmlspecialchars($orderData['nama_penerima']); ?></p>
          <p>: <?php echo htmlspecialchars($orderData['alamat_penerima']); ?></p>
          <p>: <?php echo htmlspecialchars($orderData['nomor_telepon_penerima']); ?></p>
          <p>: <?php echo htmlspecialchars($orderData['catatan_pesanan']); ?></p>
        </div>
      </div>

      
      <table class="table table-bordered table-striped mt-5">
        <thead class="thead thead-admin">
            <tr>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Catatan Pesanan</th>
                <th>Harga Produk</th>
                <th>kuantitas</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orderData['produk'])): ?>
                <?php foreach ($orderData['produk'] as $produk): ?>
                    <tr>
                    <td><img src="../admin/<?php echo htmlspecialchars($produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" style="width:50px; height:auto;"></td>    
                    <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                    <td><?php echo htmlspecialchars($orderData['catatan_pesanan']); ?></td>
                        <td>Rp <?php echo number_format($produk['harga_produk'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($produk['kuantitas']); ?></td>
                        <td>Rp <?php echo number_format($produk['harga_produk'] * $produk['kuantitas'], 0, ',', '.'); ?></td>
                    </tr>

                    <?php endforeach; ?>
                    <tr>
                        <td colspan="5" style="text-align: right;">Biaya pengiriman</td>
                        <td>Rp <?php echo isset($orderData['biaya_pengiriman']) ? number_format($orderData['biaya_pengiriman'], 0, ',', '.') : '0'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: right;">Promo yang digunakan</td>
                        <td>Rp -<?php echo isset($orderData['nominal_diskon']) ? number_format($orderData['nominal_diskon'], 0, ',', '.') : '0'; ?></td>
                    </tr>

                    <tr>
                        <td colspan="5" style="text-align: right;"><strong>Total yang harus dibayar</strong></td>
                        <td><strong>Rp <?php echo isset($orderData['harga_total']) ? number_format($orderData['harga_total'], 0, ',', '.') : '0'; ?></strong></td>
                    </tr>
            <?php else: ?>
                <tr><td colspan="6">Tidak ada detail produk untuk pesanan ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

  <div class="text-center mb-5">  
  <h3 class="mt-5">Bukti Pembayaran</h3>
    <?php if (!empty($orderData['bukti_bayar'])): ?>
        <img src="<?php echo htmlspecialchars($orderData['bukti_bayar']); ?>" alt="Bukti Pembayaran" style="max-width: 400px; max-height: 400px;">
    <?php else: ?>
        <p>Belum ada bukti pembayaran untuk pesanan ini.</p>
    <?php endif; ?>
  </div>

<?php if ($orderData['status_pesanan'] === 'pesanan selesai'): ?>
    <div class="d-flex justify-content-center">
        <a href="form_ulasan_komplain.php?order_ID=<?php echo $orderData['order_ID']; ?>" class="btn btn-primary mb-3">Beri Ulasan atau Komplain</a>
    </div>

<?php else: ?>
    <p>Ulasan dan komplain bisa dilakukan setelah pesanan selesai.</p>
<?php endif; ?>
<!-- akhir beri ulasan -->

<?php else: ?>
    <p>Tidak ada data pesanan yang ditemukan.</p>
<?php endif; ?>
  </div>


</div>

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
<script>
function goBack() {
    window.history.back();
}
    document.getElementById("btnKomplain").addEventListener("click", function() {
        var form = document.getElementById("formKomplain");
        form.style.display = form.style.display === "none" ? "block" : "none";
    });

    document.getElementById("btnUlasan").addEventListener("click", function() {
        var form = document.getElementById("formUlasan");
        form.style.display = form.style.display === "none" ? "block" : "none";
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