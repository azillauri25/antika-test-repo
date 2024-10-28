<?php
session_start();
include '../admin/konfig.php';
include 'cek.php';

//ambil username dari sesssion
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

// Ambil data dari form checkout
$nama = $_POST['nama'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$nama_kota = $_POST['nama_kota'] ?? '';
$telepon = $_POST['telepon'] ?? '';
$catatan = isset($_POST['catatan']) ? $_POST['catatan'] : ''; 
$biaya_pengiriman = isset($_POST['biaya_pengiriman']) ? $_POST['biaya_pengiriman'] : 0;



// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$customer_ID = '';
if ($username) {
    $query = "SELECT customer_ID FROM customer WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        $customer_ID = $customer['customer_ID'];
    }
}

// Ambil data keranjang
$query = "SELECT p.nama_produk, p.harga, c.quantity 
          FROM keranjang c 
          JOIN produk p ON c.produk_ID = p.produk_ID 
          WHERE c.customer_ID = '$customer_ID'";
$result = mysqli_query($koneksi, $query);

$total = 0; // Reset total
$items = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $produk_name = $row['nama_produk'];
        $harga = $row['harga'];
        $quantity = $row['quantity'];
        $subtotal = $harga * $quantity;
        $total += $subtotal; // Tambah subtotal ke total
        $items[] = [
            'name' => $produk_name,
            'harga' => $harga,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

$diskon = 0;
$promo_id = null; // Default value untuk promo_id

if (isset($_POST['kode_promo'])) {
    $kode_promo = $_POST['kode_promo'];

    // Cek kode promo di database
    $query_promo = "SELECT promo_id, diskon FROM promo WHERE kode_promo='$kode_promo' AND status_promo='aktif'";
    $result_promo = mysqli_query($koneksi, $query_promo);

    if ($result_promo && mysqli_num_rows($result_promo) > 0) {
        $promo_data = mysqli_fetch_assoc($result_promo);
        $diskon = $promo_data['diskon']; // Dapatkan diskon
        $promo_id = $promo_data['promo_id']; // Ambil promo_id
    } else {
        echo "<script>alert('Kode promo tidak tersedia');</script>"; // Alert jika kode promo tidak valid
        $kode_promo = ''; // Reset jika tidak valid
    }
}




if (isset($_POST['batalkan_promo'])) {
  $diskon = 0; // Reset diskon
  $kode_promo = '';
}



  if (!empty($diskon)) {
    $total_setelah_diskon = max(0, $total - $diskon);
} else {
    $total_setelah_diskon = $total;
}

$total_akhir = $total_setelah_diskon + $biaya_pengiriman;

?>

<!doctype html>
<html lang="en">
<head>
    <title>Antika Anggrek | Pembayaran</title>
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


<!-- informasi penerima dan daftar produk yang dibeli -->
<div class="container">
    <h1>Detail Pembayaran</h1>
    <div class="mt-5">
      <h3>Informasi Penerima</h3>
    </div>
    <p><strong>Nama:</strong> <?php echo htmlspecialchars($nama); ?></p>
    <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($telepon); ?></p>
    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($alamat); ?></p>

    <!-- <form method="POST" action="">
    <div class="form-group" style="margin-top:50px;">
        <label for="kode_promo">Masukkan Kode Promo</label>
        <input type="text" class="form-control" id="kode_promo" name="kode_promo" value="<?php echo isset($kode_promo) ? htmlspecialchars($kode_promo) : ''; ?>">
        <small class="form-text text-muted">Jika ada, masukkan kode promo di sini.</small>
    </div> -->
    <form method="POST" action="" style="display: flex; align-items: center; margin-top: 50px;">
    <div class="form-group" style="flex: 1; margin-right: 10px;">
        <label for="kode_promo">Masukkan Kode Promo</label>
        <input type="text" class="form-control" id="kode_promo" name="kode_promo" 
               value="<?php echo isset($kode_promo) ? htmlspecialchars($kode_promo) : ''; ?>">
    </div>

    <div style="display: flex; align-items: center; margin-top:10px">
    <button type="submit" class="btn btn-primary m-3">Cek Kode Promo</button>
    <button type="submit" class="btn btn-warning" name="batalkan_promo">Batal</button>
    </div>

    <!-- Tambahkan input hidden di sini -->
    <input type="hidden" name="nama" value="<?php echo htmlspecialchars($nama); ?>">
    <input type="hidden" name="alamat" value="<?php echo htmlspecialchars($alamat); ?>">
    <input type="hidden" name="telepon" value="<?php echo htmlspecialchars($telepon); ?>">
    <input type="hidden" name="biaya_pengiriman" value="<?php echo htmlspecialchars($biaya_pengiriman); ?>">
    <input type="hidden" name="catatan" value="<?php echo htmlspecialchars($catatan); ?>">
    <input type="hidden" name="nama_kota" value="<?php echo htmlspecialchars($nama_kota); ?>">

</form>

    <h3>Daftar Pembelian</h3>
    <table class="table table-bordered table-striped mt-3">
        <thead class="thead thead-cust">
            <tr>
                <th>Nama Produk</th>
                <th>Kuantitas</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Harga Barang</strong></td>
                <td><strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Biaya Pengiriman</strong></td>
                <td><strong>Rp <?php echo number_format($biaya_pengiriman, 0, ',', '.'); ?></strong></td>
            </tr>

            <tr>
                <td colspan="3" class="text-right"><strong>Total Keseluruhan</strong></td>
                <td><strong>Rp <?php echo number_format($total_akhir, 0, ',', '.'); ?></strong></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group">
        <label for="catatan">Catatan Pesanan</label>
        <textarea class="form-control" id="catatan" name="catatan" rows="3">
          <?php echo isset($catatan) ? htmlspecialchars($catatan) : ''; ?></textarea>
          
        <small class="form-text">Masukan catatan pesanan jika ada</small>
    </div>

    <h3 class="text-center mt-5 mb-5"><strong>Upload Bukti Pembayaran</strong></h3>
    <form action="konfirmasi_pembayaran.php" method="post" enctype="multipart/form-data">
    <div class="d-flex flex-column align-items-center">
        <div class="form-group text-center">
            <p style="max-height: 200px;">Pembayaran dilakukan melalui transfer ke rekening resmi kami atas nama PT. Antika Anggrek Indonesia</p>
            <p style="max-height: 200px;">BCA: 273812432698</p>
            <p style="max-height: 200px;">MANDIRI: 231209837921749</p>
            <label for="bukti_bayar">Pilih Bukti Pembayaran (JPG, JPEG, PNG): </label> </br>
            <input type="file" class="form-control-file" id="bukti_bayar" name="bukti_bayar" accept=".jpg, .jpeg, .png" required style="display: none;">
            <label class="btn btn-secondary" for="bukti_bayar">Pilih File</label>
            <p id="file-chosen">Belum ada file yang dipilih</p>
        </div>
    </div>


    <!-- Hidden fields untuk dikirim ke konfirmasi_pembayaran-->
    <input type="hidden" name="nama" value="<?php echo htmlspecialchars($nama); ?>">
    <input type="hidden" name="alamat" value="<?php echo htmlspecialchars($alamat); ?>">
    <input type="hidden" name="telepon" value="<?php echo htmlspecialchars($telepon); ?>">
    <input type="hidden" name="biaya_pengiriman" value="<?php echo htmlspecialchars($biaya_pengiriman); ?>">
    <input type="hidden" name="total_akhir" value="<?php echo htmlspecialchars($total_akhir); ?>">
    <input type="hidden" name="catatan" value="<?php echo htmlspecialchars($catatan); ?>">
    <input type="hidden" name="nama_kota" value="<?php echo htmlspecialchars($nama_kota); ?>">
    <input type="hidden" name="kode_promo" value="<?php echo htmlspecialchars($kode_promo); ?>">

    <!-- button konfirmasi pembayaran -->
    <div class="d-flex justify-content-center">
        <button type="submit" class="btn btn-primary mb-5">Buat Pesanan</button>
    </div>  
    </form>
</div>

<!-- footer -->
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

<!-- untuk nampilin nama file yang mau di upload -->
<script>
const fileInput = document.getElementById('bukti_bayar');
  const fileChosen = document.getElementById('file-chosen');

  fileInput.addEventListener('change', function() {
    if (fileInput.files.length > 0) {
      fileChosen.textContent = fileInput.files[0].name;
    } else {
      fileChosen.textContent = 'No file chosen';
    }
  });
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
