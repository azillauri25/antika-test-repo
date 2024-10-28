<?php
session_start();
include '../admin/konfig.php';
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
$search = isset($_GET['search']) ? $_GET['search'] : '';


$searchQueryKomplain = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $searchQueryKomplain = "WHERE c.isi_komplain LIKE '%$search%'";
}
$searchQueryUlasan = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $searchQueryUlasan = "WHERE r.isi_ulasan LIKE '%$search%'";
}
// Ambil data komplain
$query_komplain = "SELECT 
    c.komplain_ID, 
    c.order_ID, 
    c.customer_ID, 
    c.isi_komplain, 
    c.bukti_komplain, 
    c.tanggal_komplain, 
    c.status_komplain, 
    c.kontak_yg_dapat_dihubungi, 
    c.solusi_komplain,
    CONCAT(c.customer_ID, ' - ', o.nama_penerima) AS penerima
FROM komplain c
JOIN orders o ON c.order_ID = o.order_ID
-- $searchQueryKomplain
ORDER BY c.tanggal_komplain DESC";

$result_komplain = mysqli_query($koneksi, $query_komplain);
$komplain_ada = mysqli_num_rows($result_komplain) > 0;

// Ambil data ulasan
$query_ulasan = "SELECT 
    r.ulasan_ID, 
    r.order_ID, 
    r.customer_ID, 
    r.isi_ulasan, 
    r.penilaian, 
    r.tanggal_ulasan, 
    CONCAT(r.customer_ID, ' - ', o.nama_penerima) AS penerima
FROM ulasan r
JOIN orders o ON r.order_ID = o.order_ID
-- $searchQueryUlasan
ORDER BY r.tanggal_ulasan DESC";

$result_ulasan = mysqli_query($koneksi, $query_ulasan);
$ulasan_ada = mysqli_num_rows($result_ulasan) > 0;

// Debugging: cek kesalahan SQL
if (!$result_komplain || !$result_ulasan) {
    echo "Error: " . mysqli_error($koneksi);
}
?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Ulasan dan Komplain - Direktur Utama</title>
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
    <!-- <a href="produk.php"><i class="fa fa-box"></i> Produk</a>
    <a href="pesanan.php"><i class="fa fa-shopping-cart"></i> Pesanan</a>
    <a href="pelanggan.php"><i class="fa fa-users"></i> Pelanggan</a>
    <a href="ulasan_komplain.php"><i class="fa fa-star"></i> Ulasan dan Komplain</a> -->
    <a href="laporan.php"><i class="fa fa-file-alt"></i> Laporan</a>
    <a href="ulasan_komplain.php"><i class="fa fa-star"></i> Ulasan dan Komplain</a>
</div>
<!-- end sidebar -->

<!-- start navbar -->
<div class="header">
    <div class="username">
    Selamat Datang, Direktur Utama <strong><?php echo htmlspecialchars($nama_karyawan); ?></strong>
    </div>
    <a href="../admin/logout.php" class="logout"><i class="fa fa-sign-out-alt"></i></a>
</div>
<!-- end navbar -->

<!-- start main content -->
<div class="content content-main" style="margin-top: 50px;">
    <h2 class="mb-5 mt-5 text-left">Ulasan dan Komplain</h2>

    <!-- <div class="add-produk-button-container float-left" style="margin-top:-10px;">
        <button type="button" class="btn btn-primary mt-3 btn-ulasan" style="font-size: 10px !important;" id="btnUlasan">Ulasan</button>
        <button type="button" class="btn btn-light mt-3 btn-ulasan" style="font-size: 10px !important;"id="btnKomplain">Komplain</button>
    </div> -->
    <div class="mb-4 float-left">
        <a id="ulasanBtn" href="#ulasan" style="font-size: 10px !important;" class="btn btn-primary" onclick="toggleButtons('ulasan')">Ulasan</a>
        <a id="komplainBtn" href="#komplain" style="font-size: 10px !important;" class="btn btn-light" onclick="toggleButtons('komplain')">Komplain</a>
    </div>
    <!-- <div class="mb-4 float-right">
        <form method="GET" action="ulasan_komplain.php">
            <input type="text" name="search" placeholder="Cari ulasan atau komplain" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</button>
        </form>
    </div> -->



    <!-- Bagi section -->

    <div id="komplain" style="display: none;">
        <div class="text-left" style="margin-top: 85px;">
            <h4>Daftar Komplain</h4>
        </div>
        <table class="table table-bordered table-striped admin-laporan mt-4">
            <thead class="thead thead-admin admin-laporan">
                <tr>
                    <th scope="col">Komplain ID</th>
                    <th scope="col">Order ID</th>
                    <th scope="col">Customer ID</th>
                    <th scope="col">Isi Komplain</th>
                    <th scope="col">Bukti Komplain</th>
                    <th scope="col">Status Komplain</th>
                    <th scope="col">Kontak Yang Dapat Dihubungi</th>
                    <th scope="col">Solusi Komplain</th>
                    <th scope="col">Tanggal Komplain</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_komplain)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['komplain_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['isi_komplain']); ?></td>
                        <td>
                                <?php if ($row['bukti_komplain']): ?>
                                    <button type="button" class="btn btn-warning" style="font-size:10px;" data-toggle="modal" data-target="#modal-<?php echo $row['komplain_ID']; ?>">
                                        Lihat Bukti
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="modal-<?php echo $row['komplain_ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel-<?php echo $row['komplain_ID']; ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel-<?php echo $row['komplain_ID']; ?>">Bukti Komplain</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="../admin/uploads/komplain/<?php echo htmlspecialchars(basename($row['bukti_komplain'])); ?>" alt="Gambar Bukti" class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    Tidak ada bukti gambar.
                                <?php endif; ?>
                            </td>

                        <td><?php echo htmlspecialchars($row['status_komplain']); ?></td>
                        <td><?php echo htmlspecialchars($row['kontak_yg_dapat_dihubungi']); ?></td>
                        <td><?php echo htmlspecialchars($row['solusi_komplain']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['tanggal_komplain'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="ulasan" style="display: block;">
    <div class="text-left" style="margin-top: 85px;">
            <h4>Daftar Ulasan</h4>
        </div>
        <table class="table table-bordered table-striped admin-laporan mt-4">
            <thead class="thead thead-admin admin-laporan">
                <tr>
                    <th scope="col">Ulasan ID</th>
                    <th scope="col">Order ID</th>
                    <th scope="col">Customer ID</th>
                    <th scope="col">Isi Ulasan</th>
                    <th scope="col">Penilaian</th>
                    <th scope="col">Tanggal Ulasan</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_ulasan)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['ulasan_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['isi_ulasan']); ?></td>
                        <td><?php echo htmlspecialchars($row['penilaian']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['tanggal_ulasan'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

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

<!-- end main content -->

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK3G7E3k7cLk7aXzb30O8vI2HgEKn3rD1exuWUtW5J7dd2Z" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-JZR6Spejh4U02d8UazE1zE6qR9x18d5u5Z4x8kR9voEukT0j8s7hdIjCZRIbzz40" crossorigin="anonymous"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/scripts/klorofil-common.js"></script>
</body>
</html>
