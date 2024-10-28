<?php
session_start();
include '../admin/konfig.php';
include 'cek.php';

// Periksa koneksi database
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$customer_ID = '';
if ($username) {
    $query = "SELECT customer_ID FROM customer WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        $customer_ID = $customer['customer_ID'];
    } else {
        die("Error: User not found.");
    }
}

$username_customer = 'Guest'; // Default jika tidak ada session (as guest)
if ($username) {
    $query = "SELECT nama_customer FROM customer WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $username_customer = $data['nama_customer'];
    }
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = '';
$filters = [];

if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $filters[] = "(o.order_ID LIKE '%$search%' OR p.nama_produk LIKE '%$search%' OR o.status_pesanan LIKE '%$search%')";
}

$statusFilter = isset($_GET['status_pesanan']) ? $_GET['status_pesanan'] : '';
if (!empty($statusFilter)) {
    $filters[] = "o.status_pesanan = '" . mysqli_real_escape_string($koneksi, $statusFilter) . "'";
}

if (!empty($filters)) {
    $searchQuery = "WHERE " . implode(' AND ', $filters);
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'DESC';
$next_sort = ($sort === 'ASC') ? 'DESC' : 'ASC'; 

$query_pesanan = "SELECT o.order_ID, o.status_pesanan, o.harga_total, o.nama_penerima, o.nama_kota, o.waktu_pengiriman, o.waktu_sampai, d.catatan_pesanan, d.produk_ID, d.kuantitas, d.harga_produk, p.nama_produk AS nama_produk, p.gambar_produk
    FROM orders o
    JOIN order_details d ON o.order_ID = d.order_ID
    JOIN produk p ON d.produk_ID = p.produk_ID
    $searchQuery
    AND o.customer_ID = '$customer_ID'
    ORDER BY o.order_ID $sort";


$result = mysqli_query($koneksi, $query_pesanan);

// Debug: Periksa apakah query berhasil
if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}

// Mengelompokkan data berdasarkan order_ID
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $order_ID = $row['order_ID'];
    if (!isset($orders[$order_ID])) {
        $orders[$order_ID] = [
            'status_pesanan' => $row['status_pesanan'],
            'harga_total' => $row['harga_total'],
            'produk' => []
        ];
    }
    $orders[$order_ID]['produk'][] = $row;
}

// Pastikan variabel $orders ada sebelum digunakan di HTML
if (empty($orders)) {
    $orders = []; // Pastikan variabel $orders ada dan merupakan array kosong jika tidak ada data
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Antika Anggrek | Pesanan</title>
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

<!-- Main Content -->
<div class="container cont-keranjang" style="font-size:10px;">
    <h1>Pesanan</h1>
    <div class="mb-3">
        <form method="GET" action="">
            <div class="form-group col-md-6" style="padding-left:0px !important;">
                <label for="statusFilter">Filter berdasarkan status:</label>
                <select id="statusFilter" style="font-size: 10px;"name="status_pesanan" class="form-control" style="width:300px;" onchange="this.form.submit()">
                    <option value="">Semua Pesanan</option>
                    <option value="menunggu validasi pembayaran" <?php echo $statusFilter == 'menunggu validasi pembayaran' ? 'selected' : ''; ?>>Menunggu Validasi Pembayaran</option>
                    <option value="pesanan diterima" <?php echo $statusFilter == 'pesanan diterima' ? 'selected' : ''; ?>>Pesanan Diterima</option>
                    <option value="pesanan ditolak" <?php echo $statusFilter == 'pesanan ditolak' ? 'selected' : ''; ?>>Pesanan Ditolak</option>
                    <option value="pesanan dikirim" <?php echo $statusFilter == 'pesanan dikirim' ? 'selected' : ''; ?>>Pesanan Dikirim</option>
                    <option value="pesanan selesai" <?php echo $statusFilter == 'pesanan selesai' ? 'selected' : ''; ?>>Pesanan Selesai</option>
                </select>
            </div>
            <div class="form-group col-md-6 mt-2 text-right" style="padding-right:0px !important;">
            <label for="statusFilter"></label></br>
                <form method="GET" action="pesanan.php">
                    <input type="text" name="search" placeholder="Cari pesanan" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
                    <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
                    <button type="submit" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</button>
                </form>
            </div>

        </form>
    </div>
    <!-- <div class="mb-4 float-right">
        <form method="GET" action="pesanan.php">
            <input type="text" name="search" placeholder="Cari pesanan" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</button>
        </form>
    </div> -->
    <table class="table table-bordered table-striped">
        <thead class="thead thead-cust">
            <tr>
                <th scope="col">
                    <a href="?sort=<?php echo $next_sort; ?>" style="color: white; text-decoration: none;">
                        Order ID
                        <?php if ($sort === 'ASC') : ?>
                            <i class="fas fa-sort-up"></i> <!-- Ikon naik untuk ascending -->
                        <?php else : ?>
                            <i class="fas fa-sort-down"></i> <!-- Ikon turun untuk descending -->
                        <?php endif; ?>
                    </a>
                </th>
                <th>Nama Produk</th>
                <!-- <th>Kuantitas</th> -->
                <th>Total Harga</th>
                <th>Status Pesanan</th>
                <!-- <th>Catatan</th> -->
                <th>Nama Penerima</th>
                <th>Nama Kota</th>
                <th>Estimasi Pengiriman</th>
                <th>Lihat Detail</th>
                <th>Invoice</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($orders)) { // Cek apakah $orders tidak kosong ?>
      <?php foreach ($orders as $order_ID => $order) {
          foreach ($order['produk'] as $produk) { ?>
              <tr>
                  <?php if ($produk === reset($order['produk'])) { ?>
                      <td rowspan="<?php echo count($order['produk']); ?>"><?php echo htmlspecialchars($order_ID); ?></td>
                  <?php } ?>
                  <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                
                  <!-- <td><?php echo htmlspecialchars($produk['kuantitas']); ?></td> -->
                  <?php if ($produk === reset($order['produk'])) { ?>
                      <td rowspan="<?php echo count($order['produk']); ?>">
                          <span><?php echo number_format($order['harga_total'], 0, ',', '.'); ?></span>
                      </td>
                      <?php } ?>

                     <?php if ($produk === reset($order['produk'])) { ?>
                      <td rowspan="<?php echo count($order['produk']); ?>">
                          <span><?php echo htmlspecialchars($order['status_pesanan']); ?></span>
                      </td>
                      <!-- <td rowspan="<?php echo count($order['produk']); ?>">
                          <span><?php echo htmlspecialchars($produk['catatan_pesanan']); ?></span>
                      </td> -->
                      <td rowspan="<?php echo count($order['produk']); ?>">
                          <span><?php echo htmlspecialchars($produk['nama_penerima']); ?></span>
                      </td>
                      <td rowspan="<?php echo count($order['produk']); ?>">
                          <span><?php echo htmlspecialchars($produk['nama_kota']); ?></span>
                      </td>
                      <td rowspan="<?php echo count($order['produk']); ?>">
                      <?php if ($order['status_pesanan'] === 'pesanan selesai' || $order['status_pesanan'] === 'pesanan dikirim'): ?>
                          <?php
                            
                              $waktuKirim = !empty($produk['waktu_pengiriman']) ? DateTime::createFromFormat('Y-m-d H:i:s', $produk['waktu_pengiriman']) : null;
                              $estimasiSampai = !empty($produk['waktu_sampai']) ? DateTime::createFromFormat('Y-m-d H:i:s', $produk['waktu_sampai']) : null;
                          ?>
                          
                          <?php if ($waktuKirim && $estimasiSampai): ?>
                              <span>Waktu Pengiriman:</span> <br>
                              <span><strong><?php echo htmlspecialchars($waktuKirim->format('d-m-Y H:i')); ?></strong></span><br>
                              <span>Estimasi Sampai:</span> <br>
                              <span><strong><?php echo htmlspecialchars($estimasiSampai->format('d-m-Y H:i')); ?></strong></span>
                          <?php else: ?>
                              <span><strong>Produk tidak valid</strong></span>
                          <?php endif; ?>
                      <?php endif; ?>
                      </td>
                      <td rowspan="<?php echo count($order['produk']); ?>">
                          <a href="detail_pesanan.php?order_ID=<?php echo urlencode($order_ID); ?>" class="btn btn-primary btn-sm" style="font-size:10px;">Lihat Detail</a>
                      </td>
                      <td rowspan="<?php echo count($order['produk']); ?>">
                          <a href="unduh_invoice.php?order_ID=<?php echo urlencode($order_ID); ?>" class="btn btn-success btn-sm" style="font-size:10px;>
                              <i class="fas fa-file-pdf"></i> Cetak Invoice
                          </a>
                      </td>
                  <?php } ?>
              </tr>
          <?php } ?>
      <?php } ?>
  <?php } else { ?>
      <tr>
          <td colspan="9" class="text-center">Tidak ada pesanan.</td>
      </tr>
  <?php } ?>

        </tbody>
    </table>
</div>


<!-- footer -->
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
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3G7E3k7cLk7aXzb30O8vI2HgEKn3rD1exuWUtW5J7dd2Z" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-JZR6Spejh4U02d8UazE1zE6qR9x18d5u5Z4x8kR9voEukT0j8s7hdIjCZRIbzz40" crossorigin="anonymous"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/scripts/klorofil-common.js"></script>
</body>
</html>
