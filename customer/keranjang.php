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

// Ambil detail alamat pengguna

?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Keranjang</title>
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

<!-- start tabel keranjang -->
<div class="container cont-keranjang" >
  <h1 style="padding-bottom: 50px">Keranjang Belanja</h1>
    <table class="table table-bordered table-striped">
        <thead class="thead thead-cust">
        <tr>
          <th>Gambar</th>
          <th>Nama Produk</th>
          <th>Kuantitas</th>
          <th>Harga</th>
          <th>Total</th>
          <th>Hapus Produk</th>
      </tr>
    </thead>
    <tbody>
    <?php

    // Ambil customer_ID berdasarkan username
    $query = "SELECT customer_ID FROM customer WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        $customer_ID = $customer['customer_ID'];

        // Ambil data keranjang dari database
        $query = "SELECT p.produk_ID, p.nama_produk, p.harga_produk, p.gambar_produk, c.kuantitas 
                  FROM keranjang c 
                  JOIN produk p ON c.produk_ID = p.produk_ID 
                  WHERE c.customer_ID = '$customer_ID'";
        $result = mysqli_query($koneksi, $query);

        $total = 0;

        // definisikan
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $produk_name = $row['nama_produk'];
                $harga = $row['harga_produk'];
                $kuantitas = $row['kuantitas'];
                $produk_image = $row['gambar_produk'];
                $subtotal = $harga * $kuantitas;
                $total += $subtotal;

                // tabel
                echo "<tr>
                        <td><img src='../admin/{$produk_image}' alt='{$produk_name}' width='50' style='text-align: center;'></td>
                        <td>{$produk_name}</td>
                        <td>
                            <form action='update_keranjang.php' method='post' style='text-align: center;'>
                                <input type='hidden' name='produk_ID' value='{$row['produk_ID']}'>
                                <button type='submit' name='action' value='decrease' class='btn btn-sm'>-</button>
                                <input type='number' name='kuantitas' value='{$kuantitas}' min='1' class='form-control d-inline w-25'>
                                <button type='submit' name='action' value='increase' class='btn btn-sm'>+</button>
                            </form>
                        </td>
                        <td>Rp " . number_format($harga, 0, ',', '.') . "</td>
                        <td>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
                        <td style='text-align: center;'>
                          <form action='hapus_keranjang.php' method='post'>
                              <input type='hidden' name='produk_ID' value='{$row['produk_ID']}'>
                              <button type='submit' style='border: none; background: none; cursor: pointer;'>
                                  <i class='fas fa-trash' style='font-size: 1em; color: #7e8296;'></i>
                              </button>
                          </form>
                        </td>

                    </tr>";
            }
            echo "<tr>
                    <td colspan='5' class='text-right'><strong>Total</strong></td>
                    <td><strong>Rp " . number_format($total, 0, ',', '.') . "</strong></td>
                </tr>";
        } else {
          echo '<tr><td colspan="6" style="text-align: center; vertical-align: middle;">Pilih produk terlebih dahulu</td></tr>';

        }
    } else {
        echo '<tr><td colspan="6">Username tidak ditemukan.</td></tr>';
    }
    ?>
    </tbody>
</table>


<?php
    // Cek jika keranjang kosong
    $query = "SELECT COUNT(*) AS total FROM keranjang WHERE customer_ID = '$customer_ID'";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    $total_keranjang = $data['total'];

    if ($total_keranjang > 0) {
        // tampilkan form alamat jika ada produk di keranjang
        ?>
<!-- Pengiriman -->
<div class="container">
  <div class="text-center mb-5" style="margin-top: 75px;">
    <h3>Alamat Pengiriman</h3>
  </div>
</div>
    <!-- Form Alamat Baru, disembunyikan secara default -->
 <!-- Form Input -->
<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col text-center">
            <button class="btn btn-light" id="btn-alamat-sendiri" onclick="gunakanAlamatSendiri()">Gunakan Alamat Sendiri</button>
            <button class="btn btn-light" id="btn-kirim-lain" onclick="tampilFormAlamat()">Kirim untuk Orang Lain</button>
        </div>
    </div>

    <!-- Elemen form tetap ada -->
    <div class="row mt-3" id="form-sendiri" style="display:block;">
      <div class="col">
          <form action="pembayaran_pribadi.php" method="post" style="max-width: 600px; margin: 0 auto;">
          <div class="form-group">
              <label for="nama">Nama Penerima</label>
              <input type="text" class="form-control" id="nama" name="nama" required readonly>
          </div>
          <div class="form-group">
              <label for="alamat">Alamat Penerima</label>
              <textarea class="form-control" style="resize:none;" id="alamat" name="alamat" rows="3" required readonly></textarea>
          </div>
          <div class="form-group">
              <label for="telepon">Nomor Telepon Penerima</label>
              <input type="text" class="form-control" id="telepon" name="telepon" required readonly>
          </div>
          <div class="form-group">
              <label for="nama_kota">Nama Kota Penerima</label>
              <input type="text" class="form-control" id="nama_kota" name="nama_kota" required readonly>
          </div>
          <div class="form-group">
              <label for="biaya_pengiriman">Biaya Pengiriman</label>
              <input type="text" class="form-control" id="biaya_pengiriman" name="biaya_pengiriman" required readonly>
          </div>
          <div class="form-group" id="tidak-tersedia" style="display:none;">
            <p>Pengiriman hanya tersedia untuk wilayah JABODETABEK</p>
        </div>
          <div class="row d-flex justify-content-center">
            <div style="margin-bottom: 20px; margin-top: 30px;">
              <button type="submit" id="checkout-btn" class="btn btn-primary">Checkout</button>
            </div>
          </div>
      </form>
          </div>
          </div>
                
          <div class="row mt-3 form-lain" id="form-lain" style="display: none;">
            <div class="col">
                <form action="pembayaran_lain.php" method="post" style="max-width: 600px; margin: 0 auto;">
                    <div class="form-group">
                        <label for="nama">Nama Penerima</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat Penerima</label>
                        <textarea class="form-control" style="resize: none;" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Nomor Telepon Penerima</label>
                        <input type="text" class="form-control" id="telepon" name="telepon" required>
                    </div>

                    <!-- <div class="form-group">
                        <label for="kota">Kota</label>
                        <select class="form-control" id="kota" name="kota" onchange="getBiayaPengiriman()">
                            <option value="">Pilih Kota</option>
                            <?php
                            $query = "SELECT nama_kota FROM pengiriman";
                            $result = mysqli_query($koneksi, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['nama_kota'] . '">' . $row['nama_kota'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                  <div id="biaya_kirim_display">Mohon pilih kota terlebih dahulu</div> -->
                  <!-- <input type="hidden" id="biaya_kirim_value" value=""> -->
                    <!-- <input type="hidden" id="biaya_kirim_value" name="biaya_kirim_value"> -->

<!-- 
                    <div class="row d-flex justify-content-center">
                        <div style="margin-bottom: 20px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">Checkout</button>
                        </div> -->
                        <div class="form-group">
                          <label for="kota">Kota</label>
                          <select class="form-control" id="kota" name="kota" onchange="getBiayaPengiriman()" required>
                              <option value="">Pilih Kota</option>
                              <?php
                              $query = "SELECT nama_kota FROM pengiriman";
                              $result = mysqli_query($koneksi, $query);
                              while ($row = mysqli_fetch_assoc($result)) {
                                  $nama_kota = $row['nama_kota'];
                                  if ($nama_kota == "Lainnya") {
                                      echo '<option value="' . $nama_kota . '" disabled>Lainnya - Silakan Pilih Wilayah JABODETABEK</option>';
                                  } else {
                                      echo '<option value="' . $nama_kota . '">' . $nama_kota . '</option>';
                                  }
                              }
                              ?>
                          </select>
                      </div>

                    <div id="biaya_kirim_display">Mohon pilih kota terlebih dahulu</div>
                    <input type="hidden" id="hidden_biaya_kirim_value" name="biaya_kirim_value" value="">

                    <div class="row d-flex justify-content-center">
                      <div style="margin-bottom: 20px; margin-top: 30px;">
                        <button type="submit" id="checkout-btn-lain" class="btn btn-primary">Checkout</button>
                      </div>
                    </div>

        <?php
        } else {
            // Tampilkan tombol jika keranjang kosong
            echo '<div class="container text-center" style="margin-top:50px;">
                <p class=" mt-5 mb-3"style="font-size: 1em;">Keranjang kosong. Yuk, pilih dulu anggreknya!</p>
                <a href="index.php" class="btn btn-primary">Pilih Produk</a>
            </div>';
        }

        // Tutup koneksi
        mysqli_close($koneksi);
        ?>

  </div>
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
<!-- end footer -->

<script>
function goBack() {
    window.location.href="index.php";
}

function gunakanAlamatSendiri() {
    document.getElementById('btn-alamat-sendiri').classList.remove('btn-light');
    document.getElementById('btn-alamat-sendiri').classList.add('btn-primary');
    
    document.getElementById('btn-kirim-lain').classList.remove('btn-primary');
    document.getElementById('btn-kirim-lain').classList.add('btn-light');

    document.getElementById("form-sendiri").style.display = "block";
    document.getElementById("form-lain").style.display = "none";

    // Fetch data alamat sendiri
    fetch('ambil_alamat_sendiri.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('nama').value = data.nama_customer;
        document.getElementById('telepon').value = data.nomor_telepon_customer;
        document.getElementById('alamat').value = data.alamat_customer;
        document.getElementById('nama_kota').value = data.nama_kota;
        document.getElementById('biaya_pengiriman').value = data.biaya_pengiriman;

        if (data.nama_kota === 'Lainnya') {
            document.getElementById('tidak-tersedia').style.display = 'block';
            document.getElementById('checkout-btn').setAttribute('disabled', true);
        } else {
            document.getElementById('tidak-tersedia').style.display = 'none';
            document.getElementById('checkout-btn').removeAttribute('disabled'); 
        }
    })
    .catch(error => console.error('Error:', error));
}

// Menjalankan gunakanAlamatSendiri saat halaman dimuat
window.addEventListener('DOMContentLoaded', function () {
    gunakanAlamatSendiri();
});

function tampilFormAlamat() {
    // Ganti tombol 'Kirim untuk Orang Lain' menjadi btn-primary
    document.getElementById('btn-kirim-lain').classList.remove('btn-light');
    document.getElementById('btn-kirim-lain').classList.add('btn-primary');
    
    // Kembalikan tombol 'Gunakan Alamat Sendiri' menjadi btn-light
    document.getElementById('btn-alamat-sendiri').classList.remove('btn-primary');
    document.getElementById('btn-alamat-sendiri').classList.add('btn-light');

    // Tampilkan form alamat lain
    document.getElementById("form-sendiri").style.display = "none";
    document.getElementById("form-lain").style.display = "block";
}
// function getBiayaPengiriman() {
//     var kota = document.getElementById("nama_kota");
//     var biayaKirim = kota.options[kota.selectedIndex].value;

//     console.log("Kota yang dipilih:", kota.options[kota.selectedIndex].text); 
//     console.log("Value biaya kirim:", biayaKirim); 

//     if (biayaKirim === "") {
//         console.error("Tidak ada biaya kirim yang dipilih!");
//         alert("Mohon pilih kota terlebih dahulu");
//         document.getElementById("biaya_kirim_display").innerText = ""; 
//         return; 
//     }

//     document.getElementById("biaya_kirim_value").value = parseFloat(biayaKirim.replace(',', '.')).toFixed(2);

//     // Update tampilan di halaman
//     document.getElementById("biaya_kirim_display").innerText = `Rp ${new Intl.NumberFormat('id-ID').format(parseFloat(biayaKirim))}`; 
// }

function getBiayaPengiriman() {
    var kota = document.getElementById('kota').value;
    var checkoutBtn = document.getElementById('checkout-btn-lain');
    
    if (kota !== "") {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "ambil_biaya_pengiriman.php?kota=" + kota, true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                var biayaPengiriman = xhr.responseText;

                // Update tampilan biaya kirim
                document.getElementById("biaya_kirim_display").innerText = "Biaya Pengiriman: Rp " + biayaPengiriman;

                // Update hidden input dengan biaya pengiriman
                document.getElementById("hidden_biaya_kirim_value").value = biayaPengiriman.replace(/\./g, '').replace(',', '.');

                if (biayaPengiriman !== "") {
                    checkoutBtn.removeAttribute('disabled');
                } else {
                    checkoutBtn.setAttribute('disabled', true);
                }
            }
        };
        xhr.send();
    } else {
        document.getElementById("biaya_kirim_display").innerText = 'Mohon pilih kota terlebih dahulu';
        document.getElementById("hidden_biaya_kirim_value").value = ''; 

        checkoutBtn.setAttribute('disabled', true);
    }
}

// window.addEventListener('DOMContentLoaded', function () {
document.getElementById('checkout-btn-lain').setAttribute('disabled', true);



</script>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3G7E3k7cLk7aXzb30O8vI2HgEKn3rD1exuWUtW5J7dd2Z" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-JZR6Spejh4U02d8UazE1zE6qR9x18d5u5Z4x8kR9voEukT0j8s7hdIjCZRIbzz40" crossorigin="anonymous"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/scripts/klorofil-common.js"></script>
</body>
</html>
