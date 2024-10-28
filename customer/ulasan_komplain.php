<?php
session_start();
include '../admin/konfig.php';
include 'cek.php';

// Mendapatkan username dari session atau set sebagai guest jika tidak ada session
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

// Query untuk mendapatkan customer_ID
$query = "SELECT customer_ID FROM customer WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

// Cek apakah query berhasil dan ambil customer_ID
if (!$result || mysqli_num_rows($result) == 0) {
    die("Pengguna tidak ditemukan.");
}

$customer = mysqli_fetch_assoc($result);
$customer_ID = mysqli_real_escape_string($koneksi, $customer['customer_ID']); // Sanitasi input

// $query_pesanan = "SELECT o.order_ID, o.status_pesanan, o.harga_total, o.nama_penerima, o.nama_kota, o.waktu_pengiriman, o.waktu_sampai, d.catatan_pesanan, d.produk_ID, d.kuantitas, d.harga_produk, p.nama_produk AS nama_produk, p.gambar_produk
//     FROM orders o
//     JOIN order_details d ON o.order_ID = d.order_ID
//     JOIN produk p ON d.produk_ID = p.produk_ID
//     $searchQuery
//     WHERE o.customer_ID = '$customer_ID' 
//     ORDER BY o.order_ID, d.produk_ID";
// $kpesanan_result = mysqli_query($koneksi, $query_pesanan);

$komplain_query = "SELECT c.komplain_ID, c.order_ID, c.customer_ID, c.isi_komplain, c.bukti_komplain, c.status_komplain, c.tanggal_komplain, c.kontak_yg_dapat_dihubungi, c.solusi_komplain, o.status_pesanan AS status_pesanan
    FROM komplain c
    JOIN orders o ON c.order_ID = o.order_ID
    WHERE c.customer_ID = '$customer_ID'";

$ulasan_query = "SELECT r.ulasan_ID, r.order_ID, r.customer_ID, r.isi_ulasan, r.penilaian, r.tanggal_ulasan, o.status_pesanan, d.produk_ID, o.nama_penerima, o.harga_total, p.gambar_produk, p.nama_produk, o.waktu_sampai
    FROM ulasan r
    JOIN order_details d ON r.order_ID = d.order_ID
    JOIN produk p ON d.produk_ID = p.produk_ID
    JOIN orders o ON r.order_ID = o.order_ID
    WHERE r.customer_ID = '$customer_ID'";

$komplain_result = mysqli_query($koneksi, $komplain_query);
$ulasan_result = mysqli_query($koneksi, $ulasan_query);

// Cek jika query berhasil
if (!$komplain_result || !$ulasan_result) {
    die("Query error: " . mysqli_error($koneksi));
}
?>


<!doctype html>
<html lang="en">
<head>
    <title>Antika Anggrek | Ulasan dan Komplain</title>
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
    <!-- Navbar -->
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
    <!-- End Navbar -->

<!-- start back -->
    <button class="btn btn-light mb-3" style="float: left; margin-left:50px; margin-top:-20px;" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> Kembali
    </button>
    <div style="clear: both;"></div>
<!-- end tombol back -->

    <div class="container cont-keranjang">
        <div class="container">
            <h1>Ulasan dan Komplain</h1>

            <!-- Navigation Buttons -->
            <div class="mb-4 mt-5">
            <div class="mb-4 mt-5">
            <div class="mb-4 mt-5">
                <a id="ulasanBtn" href="#ulasan" class="btn btn-primary" onclick="toggleButtons('ulasan')">Ulasan</a>
                <a id="komplainBtn" href="#komplain" class="btn btn-light" onclick="toggleButtons('komplain')">Komplain</a>
            </div>
            </div>

            </div>

            <!-- Komplain Section -->
            <div id="komplain" class="content-section mt-5" style="font-size:10px;">
                <h4>Daftar Komplain</h4>
                <table class="table table-bordered table-striped ">
                    <thead class ="thead thead-cust text-center">
                        <tr>
                            <th>Order ID</th>
                            <th>Isi Komplain</th>
                            <th>Bukti Komplain</th>
                            <th>Status Komplain</th>
                            <th>Kontak Yang Dapat Dihubungi</th>
                            <th>Solusi Komplain</th>
                            <th>Tanggal Komplain</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($komplain = mysqli_fetch_assoc($komplain_result)): ?>
                        <tr>
                            <td class="text-center"><?php echo htmlspecialchars($komplain['order_ID']); ?></td>
                            <td ><?php echo nl2br(htmlspecialchars($komplain['isi_komplain'])); ?></td>
                            <td class="text-center">
                                <?php if ($komplain['bukti_komplain']): ?>
                                    <button type="button" class="btn btn-warning" style="font-size:10px;" data-toggle="modal" data-target="#modal-<?php echo $komplain['komplain_ID']; ?>">
                                        Lihat Bukti
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="modal-<?php echo $komplain['komplain_ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel-<?php echo $komplain['komplain_ID']; ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel-<?php echo $komplain['komplain_ID']; ?>">Bukti Komplain</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="../admin/uploads/komplain/<?php echo htmlspecialchars(basename($komplain['bukti_komplain'])); ?>" alt="Gambar Bukti" class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    Tidak ada bukti gambar.
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo htmlspecialchars($komplain['status_komplain']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($komplain['kontak_yg_dapat_dihubungi']); ?></td>
                            <td><?php echo htmlspecialchars($komplain['solusi_komplain']); ?></td>
                            <td class="text-center"><?php echo date('d-m-Y', strtotime($komplain['tanggal_komplain'])); ?></td>


                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Ulasan Section -->
            <div id="ulasan" class="content-section mt-5" style="display:block; font-size:10px;">
                <h4>Daftar Ulasan</h4>
                <table class="table table-bordered table-striped" >
                    <thead class="thead thead-cust text-center">
                        <tr>
                            <th>Order ID</th>
                            <th>Isi Ulasan</th>
                            <th>Penilaian</th>
                            <th>Tanggal Ulasan</th>
                            <th>Detail Ulasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ulasan = mysqli_fetch_assoc($ulasan_result)): ?>
                        <tr>
                            <td class="text-center"><?php echo htmlspecialchars($ulasan['order_ID']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($ulasan['isi_ulasan'])); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($ulasan['penilaian']); ?>/5</td>
                            <td class="text-center"><?php echo date('d-m-Y', strtotime($ulasan['tanggal_ulasan'])); ?></td>

                            <td class="text-center">
                            <button type="button" class="btn btn-warning" style="font-size:10px;" data-toggle="modal" data-target="#ulasanModal-<?php echo $ulasan['ulasan_ID']; ?>">
                                Detail Ulasan
                            </button>
                                    <div class="modal fade" id="ulasanModal-<?php echo $ulasan['ulasan_ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="ulasanModalLabel-<?php echo $ulasan['ulasan_ID']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="ulasanModalLabel-<?php echo $ulasan['ulasan_ID']; ?>">Detail Ulasan</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body text-left">
                                                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($ulasan['order_ID']); ?></p>
                                                <div class="modal-body">
                                                    <img src="../admin/uploads/<?php echo htmlspecialchars(basename($ulasan['gambar_produk'])); ?>" alt="Gambar Pesanan" class="img-fluid" style="width:80px;">
                                                </div>
                                                <p><strong>Nama Produk:</strong> <?php echo nl2br(htmlspecialchars($ulasan['nama_produk'])); ?></p>
                                                <p><strong>Nama Penerima:</strong> <?php echo nl2br(htmlspecialchars($ulasan['nama_penerima'])); ?></p>
                                                <p><strong>Waktu Sampai:</strong> <?php echo nl2br(htmlspecialchars($ulasan['waktu_sampai'])); ?></p>
                                                <p><strong>Harga Total:</strong> Rp <?php echo number_format($ulasan['harga_total'], 0, ',', '.'); ?></p>
                                                <p><strong>Isi Ulasan:</strong></br> <?php echo nl2br(htmlspecialchars($ulasan['isi_ulasan'])); ?></p>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
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
document.addEventListener("DOMContentLoaded", function() {
    // Tampilkan bagian ulasan secara default
    showSection('ulasan');

    // Set tombol ulasan ke btn-primary secara default
    const btnUlasan = document.querySelector('button[onclick="showSection(\'ulasan\')"]');
    const btnKomplain = document.querySelector('button[onclick="showSection(\'komplain\')"]');
    
    btnUlasan.classList.add('btn-primary');
    btnUlasan.classList.remove('btn-light');
    btnKomplain.classList.add('btn-light');
    btnKomplain.classList.remove('btn-primary');
});

function toggleButtons(section) {
    // Ambil elemen tombol
    var ulasanBtn = document.getElementById('ulasanBtn');
    var komplainBtn = document.getElementById('komplainBtn');

    // Cek tombol mana yang diklik dan set kelas yang sesuai
    if (section === 'ulasan') {
        ulasanBtn.classList.add('btn-primary');
        ulasanBtn.classList.remove('btn-light');
        komplainBtn.classList.add('btn-light');
        komplainBtn.classList.remove('btn-primary');
    } else if (section === 'komplain') {
        komplainBtn.classList.add('btn-primary');
        komplainBtn.classList.remove('btn-light');
        ulasanBtn.classList.add('btn-light');
        ulasanBtn.classList.remove('btn-primary');
    }
}
function goBack() {
    window.history.back();
}
function showSection(section) {
        document.getElementById('ulasan').style.display = section === 'ulasan' ? 'block' : 'none';
        document.getElementById('komplain').style.display = section === 'komplain' ? 'block' : 'none';
    }

    // Get the current hash from the URL
    window.onload = function() {
        var hash = window.location.hash.substring(1); // Remove the "#" from hash
        if (hash === 'ulasan' || hash === 'komplain') {
            showSection(hash);
        } else {
            showSection('ulasan'); // Default section
        }
    };

    // Change section dynamically if user clicks link
    window.onhashchange = function() {
        var hash = window.location.hash.substring(1); // Remove the "#" from hash
        showSection(hash);
    };
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3G7E3k7cLk7aXzb30O8vI2HgEKn3rD1exuWUtW5J7dd2Z" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-JZR6Spejh4U02d8UazE1zE6qR9x18d5u5Z4x8kR9voEukT0j8s7hdIjCZRIbzz40" crossorigin="anonymous"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/scripts/klorofil-common.js"></script>
</body>
</html>
