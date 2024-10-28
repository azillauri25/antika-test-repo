<?php
session_start();
include 'konfig.php';
include 'cek.php'; // Memastikan user sudah login saat akses ke halaman ini

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$nama_karyawan = 'Guest';
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan']; // Ambil nama_karyawan dari hasil query
    }
}

$search = isset($_GET['search']) ? $_GET['search'] : '';


$searchQuery = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $searchQuery = "WHERE nama_produk LIKE '%$search%' OR produk_ID LIKE '%$search%' ";
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'DESC';
$next_sort = ($sort === 'ASC') ? 'DESC' : 'ASC'; 

// Memasukkan parameter sorting ke query
$query_produk = "SELECT p.produk_ID, p.nama_produk, p.gambar_produk, p.harga_produk, p.stok_produk, p.request_tambah_produk, p.request_hapus_produk, pp.request_ubah_produk
                 FROM produk p
                 LEFT JOIN perubahan_produk pp ON p.produk_ID = pp.produk_ID
                 $searchQuery
                 ORDER BY p.produk_ID $sort";
$result_produk = mysqli_query($koneksi, $query_produk);

?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Produk - Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="icon" href="../gambar/logoAntika.png" type="image/png"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" crossorigin="anonymous">
    
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
    <h2 class="mb-5 mt-5 text-left">Produk</h2>

    <!-- button tambah produk -->

    <div class="add-produk-button-container float-left">
        <a href="tambah_produk.php" class="btn btn-primary" style="font-size:10px;">Tambah Produk</a>
    </div>
    <div class="mb-3 float-right">
        <form method="GET" action="produk.php">
            <input type="text" name="search" placeholder="Cari produk" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</button>
        </form>
    </div>

    <table class="table table-bordered table-striped">
    <thead class="thead thead-admin" style="font-size:10px;">
        <tr>
            <th scope="col">
                <a href="?sort=<?php echo $next_sort; ?>" style="color: white; text-decoration: none;">
                    Produk ID
                    <?php if ($sort === 'ASC') : ?>
                        <i class="fas fa-sort-up"></i> <!-- Ikon naik untuk ascending -->
                    <?php else : ?>
                        <i class="fas fa-sort-down"></i> <!-- Ikon turun untuk descending -->
                    <?php endif; ?>
                </a>
            </th>
            <th scope="col">Gambar Produk</th>
            <th scope="col">Nama Produk</th>
            <th scope="col">Stok Produk</th>
            <th scope="col">Harga Produk</th>
            <th scope="col">Persetujuan Tayang</th>
            <th scope="col">Ubah Produk</th>
            <th scope="col">Hapus Produk</th>
            <th scope="col">Lihat Produk</th>
        </tr>
    </thead>
    <tbody style="font-size:10px;">
    <?php
if ($result_produk && mysqli_num_rows($result_produk) > 0) {
    while ($row = mysqli_fetch_assoc($result_produk) ) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['produk_ID']) . "</td>";
        echo "<td><img src='" . htmlspecialchars($row['gambar_produk']) . "' width='50' alt='Gambar Produk'></td>";
        echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>";
        echo "<td>" . htmlspecialchars($row['stok_produk']) . "</td>";
        echo "<td>Rp " . number_format($row['harga_produk'], 0, ',', '.') . "</td>";

        if ($row['request_tambah_produk'] == 'disetujui') {
            echo "<td><span class='badge badge-success' style='font-size:10px;'>Sedang Tayang</span></td>";
        } else {
            echo "<td><span class='badge badge-danger' style='font-size:10px;'>Tidak Tayang</span></td>";
        }

        // if ($row['request_ubah_produk'] != 'menunggu') {
        //     echo "<td><a href='edit_produk.php?produk_ID=" . htmlspecialchars($row['produk_ID']) . "' class='btn btn-warning' style='font-size:10px;'>Ubah</a></td>";
        // } else {
        //     echo "<td><a href='edit_produk.php?produk_ID=" . htmlspecialchars($row['produk_ID']) . "' class='btn btn-warning' style='font-size:10px; margin-right:10px;'>Ubah</a><i class='fas fa-info-circle' data-toggle='tooltip' data-placement='top' title='Sudah dilakuan perubahan dan menunggu persetujuan' style='cursor:pointer;'></i></td>";
        // }

        if ($row['request_tambah_produk'] == 'menunggu') {
            echo "<td></td>"; // Gak ada button "Ubah" jika request_tambah_produk = 'menunggu'
        } else {
            if ($row['request_ubah_produk'] != 'menunggu') {
                echo "<td><a href='edit_produk.php?produk_ID=" . htmlspecialchars($row['produk_ID']) . "' class='btn btn-warning' style='font-size:10px; margin-left:0px;'>Ubah</a></td>";
            } else {
                echo "<td><a href='edit_produk.php?produk_ID=" . htmlspecialchars($row['produk_ID']) . "' class='btn btn-warning' style='font-size:10px; margin-left: 20px;'>Ubah</a><i class='fas fa-info-circle' style='margin-left: 10px;' data-toggle='tooltip' data-placement='top' title='Sudah dilakukan perubahan dan menunggu persetujuan' style='cursor:pointer;'></i></td>";
            }
            
        }
        
        if ($row['request_hapus_produk'] != 'menunggu') {
            echo "<td><a href='request_hapus_produk.php?produk_ID=" . htmlspecialchars($row['produk_ID']) . "' class='btn btn-danger' style='font-size:10px;'>Hapus</a></td>";
        } else {
            echo "<td>Pengajuan telah dilakukan</td>"; // Menampilkan tulisan jika sudah mengajukan
        }

        echo "<td><a href='detail_produk.php?produk_ID=" . htmlspecialchars($row['produk_ID']) . "' style='font-size:10px;'>Lihat</a></td>";     
           
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>Tidak ada produk yang tersedia.</td></tr>";
}
?>

</tbody>


</table>
</div>
<!-- end -->

<script>
function goBack() {
    window.history.back();
}

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); // Inisialisasi semua tooltip
});
</script>
</body>
</html>
