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


// var_dump($_POST);
// Ambil data dari form checkout
$nama = $_POST['nama'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$nama_kota = $_POST['nama_kota'] ?? '';
$telepon = $_POST['telepon'] ?? '';
$catatan_pesanan = isset($_POST['catatan_pesanan']) ? $_POST['catatan_pesanan'] : ''; 
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
$query = "SELECT p.nama_produk, p.harga_produk, c.kuantitas 
          FROM keranjang c 
          JOIN produk p ON c.produk_ID = p.produk_ID 
          WHERE c.customer_ID = '$customer_ID'";
$result = mysqli_query($koneksi, $query);

$total = 0; // Reset total
$items = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $produk_name = $row['nama_produk'];
        $harga_produk = $row['harga_produk'];
        $kuantitas = $row['kuantitas'];
        $subtotal = $harga_produk * $kuantitas;
        $total += $subtotal; // Tambah subtotal ke total
        $items[] = [
            'name' => $produk_name,
            'harga_produk' => $harga_produk,
            'kuantitas' => $kuantitas,
            'subtotal' => $subtotal
        ];
    }
}

$nominal_diskon = 0;
$promo_ID = null; // Default value untuk promo_ID

if (isset($_POST['nama_promo'])) {
    $nama_promo = $_POST['nama_promo'];

    // Cek kode promo di database
    $query_promo = "SELECT promo_ID, nominal_diskon FROM promo WHERE nama_promo='$nama_promo' AND status_promo='aktif'";
    $result_promo = mysqli_query($koneksi, $query_promo);

    if ($result_promo && mysqli_num_rows($result_promo) > 0) {
        $promo_data = mysqli_fetch_assoc($result_promo);
        $nominal_diskon = $promo_data['nominal_diskon']; // Dapatkan diskon
        $promo_ID = $promo_data['promo_ID']; // Ambil promo_ID
    } else {
        echo "<script>alert('Promo tidak tersedia');</script>";
        $nama_promo = ''; 
    }
}




if (isset($_POST['batalkan_promo'])) {
  $nominal_diskon = 0; // Reset diskon
  $nama_promo = '';
}



  if (!empty($nominal_diskon)) {
    $total_setelah_diskon = max(0, $total - $nominal_diskon);
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
    <link rel="icon" href="../gambar/logoAntika.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
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
<div class="container cont-keranjang">
    <h1>Detail Pembayaran</h1>
    <div class="d-flex justify-content-between mt-5">
        <div style="flex: 1; margin-right: 5px; padding-top: 20px;">
        <h4 style="background-color:#30364d; padding: 10px; color:white; font-weight: bolder;">Daftar Pembelian</h4>

            <form method="POST" action="" style="display: flex; align-items: center; margin-top: 20px;">
                <div class="form-group" style="flex: 1; margin-right: 10px;">
                    <label for="nama_promo">Kode Promo</label>
                    <input type="text" class="form-control" id="nama_promo" name="nama_promo" 
                           value="<?php echo isset($nama_promo) ? htmlspecialchars($nama_promo) : ''; ?>" placeholder="Masukkan kode promo">
                </div>
                <div style="display: flex; align-items: center; margin-top:10px">
                    <button type="submit" class="btn btn-primary m-3">Cek Promo</button>
                    <!-- <button type="submit" class="btn btn-warning" name="batalkan_promo">Batal</button> -->
                </div>
                <input type="hidden" name="nama" value="<?php echo htmlspecialchars($nama); ?>">
                <input type="hidden" name="alamat" value="<?php echo htmlspecialchars($alamat); ?>">
                <input type="hidden" name="telepon" value="<?php echo htmlspecialchars($telepon); ?>">
                <input type="hidden" name="biaya_pengiriman" value="<?php echo htmlspecialchars($biaya_pengiriman); ?>">

                <input type="hidden" name="nama_kota" value="<?php echo htmlspecialchars($nama_kota); ?>">
            </form>
            
            <table class="table" style="border: none; background-color: transparent; font-size:12px;">
        <thead>
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
                <td class="text-center" ><?php echo htmlspecialchars($item['kuantitas']); ?></td>
                <td class="text-right">Rp <?php echo number_format($item['harga_produk'], 0, ',', '.'); ?></td>
                <td class="text-right">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <div class="col" style="font-size:12px;">
    <div class="row">
        <div class="col-1">
        </div>
        <div class="col-1">
        </div>
        <div class="col-7 text-right">
            <p>Total Harga Barang:</p>
            <p>Biaya Pengiriman:</p>
            <p>Promo Yang Digunakan:</p>
            <p><strong>Total Keseluruhan:</strong></p>
        </div>
        <div class="col-3 text-right">
            <p>Rp <?php echo number_format($total, 0, ',', '.'); ?></p>
            <!-- <p>Rp <?php echo number_format($biaya_kirim_value, 0 ); ?></p> -->
            <p>Rp <?php echo number_format($biaya_pengiriman, 0, ',', '.'); ?></p>
            <p>Rp -<?php echo number_format($nominal_diskon, 0, ',', '.'); ?></p>
            <p><strong>Rp <?php echo number_format($total_akhir, 0, ',', '.'); ?></strong></p>
        </div>
    </div>
</div>


        </div>
        <div style="flex: 1; margin-left: 30px; 
            border-left: 1px solid rgba(0, 0, 0, 0.2); 
            padding: 20px; 
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2)">

        <h4 style="padding-bottom: 10px; font-weight:bolder; margin-bottom:20px; border-bottom:2px solid #30364d;">Informasi Penerima</h4>
            <p style="font-size:12px"><strong>Nama Penerima:</strong> <?php echo htmlspecialchars($nama); ?></p>
            <p style="font-size:12px"><strong>Nomor Telepon Penerima:</strong> <?php echo htmlspecialchars($telepon); ?></p>
            <p style="font-size:12px"><strong>Alamat Penerima:</strong> <?php echo htmlspecialchars($alamat); ?></p>


            <form action="konfirmasi_pembayaran.php" method="post" enctype="multipart/form-data" style="font-size:12px">
            <div class="form-group" >
                <label class="mt-3"for="catatan_pesanan">Catatan Pesanan</label>
                <textarea class="form-control" style ="resize:none;"id="catatan_pesanan" name="catatan_pesanan" rows="3" placeholder="Tulis catatan pesanan di sini..."><?php echo htmlspecialchars($catatan_pesanan); ?></textarea>
            </div>
            <h5 class="text-left mt-5"><strong>Upload Bukti Pembayaran</strong></h5>
            <p style="max-height: 200px;">Pembayaran dilakukan melalui transfer ke rekening resmi kami atas nama PT. Antika Anggrek Indonesia</p>
            <p style="max-height: 200px;">BCA: 73812432</p>
            <p style="max-height: 200px; margin-bottom: 30px;">MANDIRI: 2-312-098-379-21749</p>
            <label for="bukti_bayar">Pilih Bukti Pembayaran (JPG, JPEG, PNG): </label> </br>

                <div class="form-group">
                  <input type="file" class="form-control-file" id="bukti_bayar" name="bukti_bayar" accept=".jpg, .jpeg, .png" required style="display: none;">
                  <label class="btn btn-secondary" for="bukti_bayar">Pilih File</label>
                  <p id="file-chosen">Belum ada file yang dipilih</p>
                </div>
                <input type="hidden" name="nama" value="<?php echo htmlspecialchars($nama); ?>">
                <input type="hidden" name="alamat" value="<?php echo htmlspecialchars($alamat); ?>">
                <input type="hidden" name="telepon" value="<?php echo htmlspecialchars($telepon); ?>">
                <input type="hidden" name="biaya_pengiriman" value="<?php echo htmlspecialchars($biaya_pengiriman); ?>">
                <input type="hidden" name="total_akhir" value="<?php echo htmlspecialchars($total_akhir); ?>">
                <!-- <input type="hidden" name="biaya_kirim_value" value="<?php echo htmlspecialchars($biaya_kirim_value); ?>"> -->
                <input type="hidden" name="nama_kota" value="<?php echo htmlspecialchars($nama_kota); ?>">
                <input type="hidden" name="nama_promo" value="<?php echo htmlspecialchars($nama_promo); ?>">
                <input type="hidden" name="nominal_diskon" value="<?php echo htmlspecialchars($nominal_diskon); ?>">
                <button type="submit" id="pesan-btn" class="btn btn-primary float-right" disabled>Buat Pesanan </button>
            </form>
        </div>
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

<script>
    function goBack() {
        window.history.back();
    }
    const fileInput = document.getElementById('bukti_bayar');
    const fileChosen = document.getElementById('file-chosen');
    const pesanBtn = document.getElementById('pesan-btn');

    fileInput.addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'Belum ada file yang dipilih';
        fileChosen.textContent = fileName;
        if (this.files.length > 0) {
            pesanBtn.removeAttribute('disabled'); // Enable button
        } else {
            pesanBtn.setAttribute('disabled', true); // Disable button
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-fq2g8c+QUA5vV1LwD1b0m//e6vq0SCTy0D4c6YhE1W4C7x2g2Iu4Pe1l5I9P3eJ0" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-m78jTAwD7kK12r5BXU4xng4yF1E3jIoKZJZPjB8cqPjl5U5rVs8l5iM7LEx8D00U" crossorigin="anonymous"></script>
</body>
</html>
