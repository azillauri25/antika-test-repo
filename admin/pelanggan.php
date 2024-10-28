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

$limit = 30; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$start = ($page - 1) * $limit;


$search = isset($_GET['search']) ? $_GET['search'] : ''; // Definisikan di atas
$searchQuery = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $searchQuery = "WHERE c.nama_customer LIKE '%$search%' OR c.username LIKE '%$search%' OR c.nomor_telepon_customer LIKE '%$search%'";
}
$queryCount = "SELECT COUNT(*) AS total FROM customer c $searchQuery";
$resultCount = mysqli_query($koneksi, $queryCount);
$totalData = mysqli_fetch_assoc($resultCount)['total'];
$totalPages = ceil($totalData / $limit);


$sort_transaksi = isset($_GET['sort']) && $_GET['sort'] == 'transaksi' ? 'ORDER BY jumlah_transaksi DESC' : 'ORDER BY c.customer_ID';
$sort_customer = isset($_GET['sort']) && $_GET['sort'] == 'customer' ? 'ORDER BY c.customer_ID DESC' : 'ORDER BY c.customer_ID';
$search = isset($_GET['search']) ? $_GET['search'] : '';


$sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'ASC' ? 'DESC' : 'ASC';

$sort_transaksi = isset($_GET['sort']) && $_GET['sort'] == 'transaksi' ? "ORDER BY jumlah_transaksi $sortOrder" : "ORDER BY c.customer_ID";

$query = "SELECT c.customer_ID, c.nama_customer, c.username, c.email_customer, c.nomor_telepon_customer, c.alamat_customer, COUNT(o.order_ID) AS jumlah_transaksi
    FROM customer c
    LEFT JOIN orders o ON c.customer_ID = o.customer_ID
    $searchQuery
    GROUP BY c.customer_ID
    $sort_transaksi
    LIMIT $start, $limit";

$result_pelanggan = mysqli_query($koneksi, $query);
?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Pelanggan - Admin</title>
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
    <h2 class="mb-5 mt-5 text-left">Pelanggan</h2>
    <div class="mb-4 float-left">
        <?php
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'customer';
        ?>
        <!-- <a id="btnCustomer" href="?sort=customer" class="btn <?php echo $sort == 'customer' ? 'btn-primary' : 'btn-light'; ?>" style="font-size:10px;" onclick="sortByCust()">Urutkan Berdasarkan Customer ID</a>
        <a id="btnTransaksi" href="?sort=transaksi" class="btn <?php echo $sort == 'transaksi' ? 'btn-primary' : 'btn-light'; ?>" style="font-size:10px;" onclick="sortByTransaksi()">Urutkan Berdasarkan Jumlah Transaksi</a> -->
    </div>
    <div class="mb-4 float-right">
        <form method="GET" action="pelanggan.php">
            <input type="text" name="search" placeholder="Cari pelanggan" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
            <a href="pelanggan.php" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</a>
        </form>
    </div>


    <table class="table table-bordered table-striped admin-laporan">
    <thead class="thead thead-admin admin-laporan">
        <tr>
            <th scope="col">Customer ID</th>
            <th scope="col">Nama</th>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Telepon</th>
            <th scope="col">Alamat</th>
            <th scope="col">
                <a href="?sort=transaksi&sortOrder=<?php echo $sortOrder; ?>" style="color:white;">
                    Transaksi
                    <?php if (isset($_GET['sort']) && $_GET['sort'] == 'transaksi'): ?>
                        <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?>"></i>
                    <?php else: ?>
                        <i class="fas fa-sort"></i>
                    <?php endif; ?>
                </a>
            </th>
            <th scope="col">Ubah Password</th>
        </tr>
    </thead>
        <tbody>
            <?php
            if ($result_pelanggan && mysqli_num_rows($result_pelanggan) > 0) {
                while ($row = mysqli_fetch_assoc($result_pelanggan)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['customer_ID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_customer']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email_customer']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nomor_telepon_customer']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['alamat_customer']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['jumlah_transaksi']) . "</td>";
                    echo "<td><a href='edit_pelanggan.php?customer_ID=" . htmlspecialchars($row['customer_ID']) . "' class='btn btn-warning btn-sm' style='font-size:10px;''>Ubah Password</a></td>";
                    // echo "<td><a href='#' class='btn btn-danger btn-sm' style='font-size:10px;' onclick=\"return konfirmasiHapus('" . htmlspecialchars($row['customer_ID']) . "');\">Hapus</a></td>";
                    echo "</tr>";
                }
            } else {
                // echo "<tr><td colspan='8'>Tidak ada pelanggan yang tersedia.</td></tr>";  
                echo "<tr><td colspan='8' style='text-align:center !important;'>Tidak ada pelanggan yang tersedia.</td></tr>";
 
            }
            ?>
        </tbody>
    </table>
    
    <!-- Paginasi -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- end main content -->

<script>
function konfirmasiHapus(customer_ID) {

    var confirmDelete = confirm("Apakah Anda yakin mau menghapus pelanggan ini?");

    // Cek apakah pengguna menekan "OK"
    if (confirmDelete) {
        window.location.href = 'hapus_pelanggan.php?customer_ID=' + customer_ID;
    } else {
        // Jika tidak, batal
        alert("Penghapusan dibatalkan.");
        return false;
    }
}
function setButtonState() {
    const urlParams = new URLSearchParams(window.location.search);
    const sort = urlParams.get('sort');

    if (sort === 'transaksi') {
        sortByTransaksi();
    } else {
        sortByCust();
    }
}
function sortByCust() {
    document.getElementById('btnCustomer').classList.remove('btn-light');
    document.getElementById('btnCustomer').classList.add('btn-primary');
    
    document.getElementById('btnTransaksi').classList.remove('btn-primary');
    document.getElementById('btnTransaksi').classList.add('btn-light');
}

    function sortByTransaksi() {
    // Ganti tombol 'Kirim untuk Orang Lain' menjadi btn-primary
    document.getElementById('btnTransaksi').classList.remove('btn-light');
    document.getElementById('btnTransaksi').classList.add('btn-primary');
    
    // Kembalikan tombol 'Gunakan Alamat Sendiri' menjadi btn-light
    document.getElementById('btnCustomer').classList.remove('btn-primary');
    document.getElementById('btnCustomer').classList.add('btn-light');
    }
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rTqYd1iL2n2h8oL77r2bDgB9c2D8fd6/tcbs8pPuv2daU9mS6P0" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-J8xgT5+40I4sI9hb3R9F7eQ0pU0pI7Z7S1h/J4Xo+nAfCPoM4Fk5V8aEOy5h4A0f" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuP2fNcAY4v2eKZp1r6sFZ4+4h1M4xFf8l0DQ5x1z/U4RzYdo2tsoXovscg" crossorigin="anonymous"></script>
</body>
</html>
