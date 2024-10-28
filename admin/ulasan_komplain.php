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
        $nama_karyawan = $data['nama_karyawan'];
    }
}

// $search = isset($_GET['search']) ? $_GET['search'] : '';

// $searchQueryKomplain = '';
// $searchQueryUlasan = '';
// if (!empty($search)) {
//     $search = mysqli_real_escape_string($koneksi, $search);
//     $searchQueryKomplain = "WHERE c.isi_komplain LIKE '%$search%'";
//     $searchQueryUlasan = "WHERE r.isi_ulasan LIKE '%$search%'";
// }

$limit = 100; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$start = ($page - 1) * $limit;

$queryCountUlasan = "SELECT COUNT(*) AS total FROM ulasan r";
$resultCountUlasan = mysqli_query($koneksi, $queryCountUlasan);
$totalDataUlasan = mysqli_fetch_assoc($resultCountUlasan)['total'];
$totalPagesUlasan = ceil($totalDataUlasan / $limit);

$queryCountKomplain = "SELECT COUNT(*) AS total FROM komplain c";
$resultCountKomplain = mysqli_query($koneksi, $queryCountKomplain);
$totalDataKomplain = mysqli_fetch_assoc($resultCountKomplain)['total'];
$totalPagesKomplain = ceil($totalDataKomplain / $limit);

$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = '';
$filters = [];

// Filter berdasarkan pencarian
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $filters[] = "(c.status_komplain LIKE '%$search%')";
}

// Filter berdasarkan status pesanan
// Filter berdasarkan status komplain
$statusFilter = isset($_GET['status_komplain']) ? $_GET['status_komplain'] : '';
if (!empty($statusFilter)) {
    $filters[] = "c.status_komplain = '" . mysqli_real_escape_string($koneksi, $statusFilter) . "'";
}

// Jika ada filter, tambahkan klausa WHERE
if (!empty($filters)) {
    $searchQuery = " WHERE " . implode(' AND ', $filters);
} else {
    $searchQuery = ""; // Jangan tambahkan WHERE jika tidak ada filter
}

// Ambil data komplain
// $query_komplain = "SELECT 
//     c.komplain_ID, 
//     c.order_ID, 
//     c.customer_ID, 
//     c.isi_komplain, 
//     c.bukti_komplain, 
//     c.tanggal_komplain, 
//     c.status_komplain, 
//     c.kontak_yg_dapat_dihubungi, 
//     c.solusi_komplain,
//     CONCAT(c.customer_ID, ' - ', o.nama_penerima) AS penerima
// FROM komplain c
// JOIN orders o ON c.order_ID = o.order_ID
// -- $searchQuery
// ORDER BY c.tanggal_komplain DESC 
// LIMIT $start, $limit";
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
" . $searchQuery . " 
ORDER BY c.tanggal_komplain DESC 
LIMIT $start, $limit";


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
ORDER BY r.tanggal_ulasan DESC
LIMIT $start, $limit";

$result_ulasan = mysqli_query($koneksi, $query_ulasan);
$ulasan_ada = mysqli_num_rows($result_ulasan) > 0;
?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Ulasan dan Komplain - Admin</title>
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
        <!-- Komplain Section -->
         <h4 style="margin-top:90px; text-align:left;">Daftar Komplain</h4>
         <div class="mb-3 mt-5">
            <form method="GET" action="">
                <div class="form-group col-md-6" style="padding-left:0px !important;">
                    <label for="statusFilter">Filter berdasarkan status:</label>
                    <select id="statusFilter" name="status_komplain" class="form-control" style="width:300px;" onchange="this.form.submit()">
                        <option value="">Semua Komplain</option>
                        <option value="menunggu" <?php echo $statusFilter == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="komplain selesai" <?php echo $statusFilter == 'komplain selesai' ? 'selected' : ''; ?>>Komplain Selesai</option>
                    </select>
                </div>
            </form>
        </div>
        <table class="table table-bordered table-striped admin-laporan mt-4">
            <thead class="thead thead-admin admin-laporan" >
                <tr>
                    <th scope="col" style="vertical-align:middle;">Komplain ID</th>
                    <th scope="col" style="vertical-align:middle;">Order ID</th>
                    <th scope="col" style="vertical-align:middle;">Customer ID</th>
                    <th scope="col" style="vertical-align:middle;">Isi Komplain</th>
                    <th scope="col" style="vertical-align:middle;">Bukti Komplain</th>
                    <th scope="col" style="min-width: 150px; !important; vertical-align:middle;">Status Komplain</th>
                    <th scope="col" style="vertical-align:middle;">Kontak Yang Dapat Dihubungi</th>
                    <th scope="col" style="vertical-align:middle;">Hubungi Customer</th>
                    <th style="min-width: 100px; !important; vertical-align:middle;" scope="col">Solusi Komplain</th>
                    <th scope="col" style="vertical-align:middle;">Tanggal Komplain</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_komplain)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['komplain_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_ID']); ?></td>
                        <td style="min-width: 110px; !important"><?php echo htmlspecialchars($row['isi_komplain']); ?></td>
                        
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
                                                    <img src="uploads/komplain/<?php echo htmlspecialchars(basename($row['bukti_komplain'])); ?>" alt="Gambar Bukti" class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    Tidak ada bukti gambar.
                                <?php endif; ?>
                            </td>

                        <td>
                            <form method="post" action="update_status_komplain.php">
                                <input type="hidden" name="komplain_ID" value="<?php echo htmlspecialchars($row['komplain_ID']); ?>">
                                <select name="status_komplain" class="form-control" onchange="this.form.submit()">
                                    <option value="menunggu" <?php if ($row['status_komplain'] == 'menunggu') echo 'selected'; ?>>Menunggu</option>
                                    <option value="komplain selesai" <?php if ($row['status_komplain'] == 'komplain selesai') echo 'selected'; ?>>Komplain Selesai</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($row['kontak_yg_dapat_dihubungi']); ?></td>
                        <td>
                            <a href="https://wa.me/<?php echo urlencode($row['kontak_yg_dapat_dihubungi']); ?>" class="btn btn-success btn-sm" target="_blank" title="Chat via WhatsApp">
                            <i class="fa fa-phone"></i>
                            </a>
                        </td>
                        <td style="min-width: 110px; !important">
                            <?php if (empty($row['solusi_komplain'])): ?>
                                <form method="post" action="submit_solusi_komplain.php" class="form-inline">
                                    <input type="hidden" name="komplain_ID" value="<?php echo htmlspecialchars($row['komplain_ID']); ?>">
                                    <input type="text" name="solusi_komplain" class="form-control form-control-sm" style="width:90px; font-size: 8px;" placeholder="Masukkan solusi" required>
                                    <br> <!-- Baris baru untuk memindahkan tombol ke bawah -->
                                    <button type="submit" class="btn btn-primary btn-sm mt-2" style="font-size: 10px;">Kirim</button>
                                </form>
                            <?php else: // Jika sudah diisi ?>
                                <p><?php echo htmlspecialchars($row['solusi_komplain']); ?></p>
                            <?php endif; ?>
                        </td>
                    <td><?php echo date('d-m-Y', strtotime($row['tanggal_komplain'])); ?></td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
            <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPagesKomplain; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $totalPagesKomplain ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>


    <div id="ulasan" style="display: block;">
    <h4 style="margin-top:90px; text-align:left;">Daftar Ulasan</h4>
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
        <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPagesUlasan; $i++): ?>
                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPagesUlasan ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
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
