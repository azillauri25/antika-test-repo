<?php
session_start();
include 'konfig.php';
include 'cek.php';

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// ambil nama admin
$nama_karyawan = 'Guest';
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan']; // Ambil nama_karyawan dari hasil query
    }
}

// Konfigurasi paginasi
    $limit = 30; // Jumlah data per halaman
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman yang aktif
    $start = ($page - 1) * $limit;

    // Ambil total data untuk paginasi
    $queryCount = "SELECT COUNT(*) AS total FROM customer";
    $resultCount = mysqli_query($koneksi, $queryCount);
    $totalData = mysqli_fetch_assoc($resultCount)['total'];
    $totalPages = ceil($totalData / $limit);
    $search = isset($_GET['search']) ? $_GET['search'] : '';


$searchQuery = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $searchQuery = "WHERE nama_promo LIKE '%$search%' OR deskripsi_promo LIKE '%$search%'";
}

    // Ambil data pelanggan
    $query = "SELECT customer_ID, nama_customer, username, email_customer, nomor_telepon_customer, alamat_customer, ttl_customer
            FROM customer
            LIMIT $start, $limit";
    $result_pelanggan = mysqli_query($koneksi, $query);

    ?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Promo - Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="icon" href="../gambar/logoAntika.png" type="image/png"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleadmin.css">
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- start sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="../gambar/logoAntika.png" alt="Logo Antika Anggrek">
        <h3>Antika Anggrek</h3>
    </div>
    <a style="padding-top:50px" href="index.php"><i class="fa fa-home"></i> Home</a>
    <a href="produk.php"><i class="fa fa-box"></i> Produk</a>
    <a href="pesanan.php"><i class="fa fa-shopping-cart"></i> Pesanan</a>
    <a href="pelanggan.php"><i class="fa fa-users"></i> Pelanggan</a>
    <a href="promo.php"><i class="fa fa-gift"></i> Promo</a>
    <a href="ulasan_komplain.php"><i class="fa fa-star"></i> Ulasan dan Komplain</a>
</div>
<!-- end sidebar -->

<!-- start navbar -->
<div class="header">
    <div class="username">
    Selamat Datang, Admin <strong><?php echo htmlspecialchars($nama_karyawan); ?></strong>
    </div>
    <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i></a>
</div>
<!-- end navbar -->

<!-- start main content -->
<div class="content content-main">
<h2 class="mb-5 mt-5 text-left">Promo</h2>
    <div class="mb-4 float-left">
        <button class="btn btn-primary" style="font-size:10px;" id="buatPromoBtn">Buat Promo</button>
    </div>
    <div class="mb-4 float-right">
        <form method="GET" action="promo.php">
            <input type="text" name="search" placeholder="Cari promo" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</button>
        </form>
    </div>


<!-- Form Tambah Promo (Hidden by default) -->
<div id="formPromo" style="display: none; margin-top: 60px;">
    <form action="proses_tambah_promo.php" method="post">
        <div class="form-group"></br>
            <label for="namaPromo">Nama Promo</label>
            <input type="text" class="form-control" id="namaPromo" name="nama_promo" required>
        </div>
        <div class="form-group">
            <label for="nominal_diskon">Nominal Diskon</label>
            <input type="number" class="form-control" id="nominal_diskon" name="nominal_diskon" required>
        </div>
        <div class="form-group">
            <label for="deskripsi_promo">Deskripsi Promo</label>
            <textarea class="form-control" style="resize:none;"id="deskripsi_promo" name="deskripsi_promo" rows="5"required></textarea>
        </div>
        <div class="form-group">
            <label for="tanggalMulai">Tanggal Mulai</label>
            <input type="date" class="form-control" id="tanggalMulai" name="tanggal_mulai_promo" required>
        </div>
        <div class="form-group">
            <label for="tanggalBerakhir">Tanggal Berakhir</label>
            <input type="date" class="form-control" id="tanggalBerakhir" name="tanggal_berakhir_promo" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3 m-2 mb-5" id="simpanBtn">Simpan</button>
        <button type="button" class="btn btn-light mt-3 m-2 mb-5" onclick="goBack()">Batal</button>
    </form>
</div>

<table class="table table-bordered table-striped">
    <thead class="thead thead-admin">
        <tr style="font-size:10px;">
            <th>Nama Promo</th>
            <th>Nominal Diskon</th>
            <th>Deskripsi Promo</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Berakhir</th>
            <th>Persetujuan</th>
            <th>Status Promo</th>
            <th>Nonaktifkan Promo</th> <!-- Tambah kolom ini -->
        </tr>
    </thead>
    <tbody style="font-size:10px;">
    <?php
        $query_promo = "SELECT * FROM promo $searchQuery";
        $result_promo = mysqli_query($koneksi, $query_promo);

        // Cek apakah ada hasil promo
        if (mysqli_num_rows($result_promo) > 0) {
            // Looping jika ada hasil
            while ($row = mysqli_fetch_assoc($result_promo)) {
                $tanggal_berakhir_promo = strtotime($row['tanggal_berakhir_promo']);
                $request_tambah_promo = $row['request_tambah_promo'];
                $request_nonaktif_promo = $row['request_nonaktif_promo'];

                // Cek apakah promo sudah tidak aktif
                if ($tanggal_berakhir_promo < time() && ($request_tambah_promo == 'menunggu' || $request_tambah_promo == 'disetujui')) {
                    // Update status_promo menjadi nonaktif
                    $update_query = "UPDATE promo SET status_promo='nonaktif' WHERE promo_ID='" . $row['promo_ID'] . "'";
                    mysqli_query($koneksi, $update_query);
                    $row['status_promo'] = 'nonaktif'; // Update status di array
                }

                // Tampilkan data promo
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nama_promo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nominal_diskon']) . "</td>";
                echo "<td>" . htmlspecialchars($row['deskripsi_promo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tanggal_mulai_promo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tanggal_berakhir_promo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['request_tambah_promo']) . "</td>";
                
                if ($row['status_promo'] == 'aktif') {
                    echo "<td><span class='badge badge-success' style='font-size:10px;'>Aktif</span></td>";
                    if ($row['request_nonaktif_promo'] != 'menunggu') {
                        echo "<td><a href='request_nonaktif_promo.php?promo_ID=" . htmlspecialchars($row['promo_ID']) . "' class='btn btn-danger' style='font-size:10px;'>Nonaktifkan</a></td>";
                    } else {
                        echo "<td>Pengajuan telah dilakukan</td>"; // Jika sudah mengajukan
                    }
                } else {
                    echo "<td>" . htmlspecialchars($row['status_promo']) . "</td>";
                    echo "<td>-</td>";
                }

                echo "</tr>";
            }
        } else {
            // Jika tidak ada hasil
            echo "<tr><td colspan='8' class='text-center'>Tidak ada promo yang tersedia</td></tr>";
        }
    ?>
    </tbody>

</table>


  
    </div>
<!-- end main content -->


<script>

    document.getElementById("simpanBtn").addEventListener("click", function() {
        document.getElementById("formPromo").style.display = "none";
    });
    document.getElementById("buatPromoBtn").addEventListener("click", function() {
        var formPromo = document.getElementById("formPromo");
        formPromo.style.display = formPromo.style.display === "none" ? "block" : "none";
    });
    function goBack() {
    document.getElementById("formPromo").style.display = "none";
}
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
