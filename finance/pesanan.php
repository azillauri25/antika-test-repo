<?php
session_start();
include '../admin/konfig.php';
include 'cek.php';

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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_ID'], $_POST['waktu_pengiriman'], $_POST['waktu_sampai'])) {
    $order_ID = mysqli_real_escape_string($koneksi, $_POST['order_ID']);
    $waktu_pengiriman = mysqli_real_escape_string($koneksi, $_POST['waktu_pengiriman']);
    $waktu_sampai = mysqli_real_escape_string($koneksi, $_POST['waktu_sampai']);

    $updateEstimasiQuery = "UPDATE orders SET waktu_pengiriman = '$waktu_pengiriman', waktu_sampai = '$waktu_sampai' WHERE order_ID = '$order_ID'";

    if (mysqli_query($koneksi, $updateEstimasiQuery)) {
        $updateStatusQuery = "UPDATE orders SET status_pesanan = 'pesanan dikirim' WHERE order_ID = '$order_ID'";
        mysqli_query($koneksi, $updateStatusQuery);
        
        // Redirect setelah pemrosesan berhasil
        header("Location: pesanan.php?status_pesanan=" . urlencode($statusFilter));
        exit(); // Pastikan untuk keluar setelah redirect
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui estimasi pengiriman: " . mysqli_error($koneksi) . "</div>";
    }
}

// Ambil filter status


// Konfigurasi paginasi
$limit = 30; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman yang aktif
$start = ($page - 1) * $limit;

// Ambil total data untuk paginasi

$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = '';
$filters = [];

// Filter berdasarkan pencarian
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $filters[] = "(p.nama_produk LIKE '%$search%' OR o.status_pesanan LIKE '%$search%')";
}

// Filter berdasarkan status pesanan
$statusFilter = isset($_GET['status_pesanan']) ? $_GET['status_pesanan'] : '';
if (!empty($statusFilter)) {
    $filters[] = "o.status_pesanan = '" . mysqli_real_escape_string($koneksi, $statusFilter) . "'";
}

// Jika ada filter, tambahkan klausa WHERE
if (!empty($filters)) {
    $searchQuery = " WHERE " . implode(' AND ', $filters);
}

// Query utama untuk mengambil data pesanan
$query = "SELECT o.order_ID, o.status_pesanan, o.harga_total, o.waktu_pengiriman, o.waktu_sampai, o.nama_penerima, o.nama_kota, d.catatan_pesanan, d.produk_ID, d.kuantitas, d.harga_produk, p.nama_produk AS nama_produk, p.gambar_produk, pr.nominal_diskon
    FROM orders o
    JOIN order_details d ON o.order_ID = d.order_ID
    JOIN produk p ON d.produk_ID = p.produk_ID 
    LEFT JOIN promo pr ON d.promo_ID = pr.promo_ID $searchQuery";

// Query untuk menghitung total data (pagination)
$queryCount = "SELECT COUNT(DISTINCT o.order_ID) AS total FROM orders o
    JOIN order_details d ON o.order_ID = d.order_ID
    JOIN produk p ON d.produk_ID = p.produk_ID $searchQuery";

// Eksekusi query count
$resultCount = mysqli_query($koneksi, $queryCount);
$totalData = mysqli_fetch_assoc($resultCount)['total'];
$totalPages = ceil($totalData / $limit);

// Tambahkan pagination pada query utama
$query .= " ORDER BY o.order_ID DESC, d.produk_ID LIMIT $start, $limit";

$result_pesanan = mysqli_query($koneksi, $query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_ID'], $_POST['waktu_pengiriman'], $_POST['waktu_sampai'])) {
    $order_ID = mysqli_real_escape_string($koneksi, $_POST['order_ID']);
    $waktu_pengiriman = mysqli_real_escape_string($koneksi, $_POST['waktu_pengiriman']);
    $waktu_sampai = mysqli_real_escape_string($koneksi, $_POST['waktu_sampai']);

    if (mysqli_query($koneksi, $updateEstimasiQuery)) {
        $updateStatusQuery = "UPDATE orders SET status_pesanan = 'pesanan dikirim' WHERE order_ID = '$order_ID'";
        mysqli_query($koneksi, $updateStatusQuery);
    
        // echo "<div class='alert alert-success'>Estimasi pengiriman berhasil diperbarui dan status pesanan diubah menjadi 'pesanan dikirim'!</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui estimasi pengiriman: " . mysqli_error($koneksi) . "</div>";
    }
}    

?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Pesanan - Finance</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="icon" href="../gambar/logoAntika.png?v=<?php echo time(); ?>" type="image/png">
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
    <a href="pesanan.php"><i class="fa fa-shopping-cart"></i> Pesanan</a>
</div>
<!-- end sidebar -->

<!-- start navbar -->
<div class="header">
    <div class="username">
    Selamat Datang, Finance <strong><?php echo htmlspecialchars($nama_karyawan); ?></strong>
    </div>
    <a href="../admin/logout.php" class="logout"><i class="fa fa-sign-out-alt"></i></a>
</div>
<!-- end navbar -->

<!-- start main content -->
<div class="content content-main">
    <h2 class="mb-5 mt-5 text-left">Pesanan</h2>
    
    <!-- filter pesanan -->
    <div class="mb-3">
        <form method="GET" action="">
            <div class="form-group col-md-6" style="padding-left:0px !important;">
                <label for="statusFilter">Filter berdasarkan status:</label>
                <select id="statusFilter" name="status_pesanan" class="form-control" style="width:300px;" onchange="this.form.submit()">
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
                <form method="GET" action="produk.php">
                    <input type="text" name="search" placeholder="Cari produk" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
                    <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
                    <button type="submit" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</button>
                </form>
            </div>

        </form>
    </div>


    
    <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_ID'], $_POST['status_pesanan'])) {
            $order_ID = mysqli_real_escape_string($koneksi, $_POST['order_ID']);
            $newStatus = mysqli_real_escape_string($koneksi, $_POST['status_pesanan']);
        
            $updateStatusQuery = "UPDATE orders SET status_pesanan = '$newStatus' WHERE order_ID = '$order_ID'";
        
            if (mysqli_query($koneksi, $updateStatusQuery)) {
                $_SESSION['message'] = "Status pesanan berhasil diperbarui!";
            } else {
                $_SESSION['error'] = "Gagal memperbarui status pesanan: " . mysqli_error($koneksi);
            }
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect setelah form diproses
            exit;
        }
        
        ?>

    <table class="table table-bordered table-striped admin-laporan">
        <thead class="thead thead-admin admin-laporan">
            <tr>
                <th>Order ID</th>
                <th>Nama Produk</th>
                <!-- <th>Kuantitas</th> -->
                <th>Total Harga</th>
                <th>Status Pesanan</th>
                <!-- <th>Catatan</th> -->
                <th>Nama Penerima</th>
                <th>Nama Kota</th>
                <!-- <th>Validasi Pembayaran</th> -->
                <th>Lihat Detail</th>

            </tr>
        </thead>
        <tbody>
        <?php
            $currentOrderID = '';
            if ($result_pesanan && mysqli_num_rows($result_pesanan) > 0) {
                while ($row = mysqli_fetch_assoc($result_pesanan)) {
                    if ($currentOrderID != $row['order_ID']) {
                        $currentOrderID = $row['order_ID'];
                        
                        // Hitung jumlah produk untuk rowspan
                        $produkCountQuery = "SELECT COUNT(*) as produk_count FROM order_details WHERE order_ID = '{$row['order_ID']}'";
                        $produkCountResult = mysqli_query($koneksi, $produkCountQuery);
                        $produkCount = mysqli_fetch_assoc($produkCountResult)['produk_count'];

                        // Ambil harga_total dari tabel orders
                        $hargaTotalQuery = "SELECT harga_total FROM orders WHERE order_ID = '{$row['order_ID']}'";
                        $hargaTotalResult = mysqli_query($koneksi, $hargaTotalQuery);
                        $hargaTotal = mysqli_fetch_assoc($hargaTotalResult)['harga_total'];

                        echo "<tr>";
                        echo "<td rowspan='{$produkCount}'>" . htmlspecialchars($row['order_ID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>"; 
                        // echo "<td>" . htmlspecialchars($row['kuantitas']) . "</td>"; 
                        echo "<td rowspan='{$produkCount}'>Rp " . number_format($hargaTotal, 0, ',', '.') . "</td>";

                        

                        // Dropdown untuk status

                        echo "<td rowspan='{$produkCount}'>" . htmlspecialchars($row['status_pesanan']) . "</td>";
                        // echo "<td rowspan='{$produkCount}'>" . htmlspecialchars($row['catatan']) . "</td>";
                        echo "<td rowspan='{$produkCount}'>" . htmlspecialchars($row['nama_penerima']) . "</td>";
                        echo "<td rowspan='{$produkCount}'>" . htmlspecialchars($row['nama_kota']) . "</td>";

                        // if ($row['status_pesanan'] == 'menunggu validasi pembayaran') {
                        //     echo "<td rowspan='{$produkCount}' style='text-align: center;'>-</td>";


                        // } elseif (in_array($row['status_pesanan'], ['pesanan diterima', 'pesanan dikirim', 'pesanan selesai'])) {
                            
                        //     echo "<td rowspan='{$produkCount}' style='text-align: center;'>Divalidasi</td>";
                        // } elseif ($row['status_pesanan'] == 'pesanan ditolak') {
                            
                        //     echo "<td rowspan='{$produkCount}' style='text-align: center;'>Ditolak</td>";
                        // } else {
                        //     echo "<td rowspan='{$produkCount}'></td>";
                        // }
                        
                        
                        echo "<td rowspan='{$produkCount}'><a href='detail_pesanan.php?order_ID=" . htmlspecialchars($row['order_ID']) . "' class='btn btn-warning admin-laporan'>Lihat</a></td>";
                        echo "</tr>";
                    } else {
                        $totalHarga = $row['kuantitas'] * $row['harga_produk'];
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>";
                        echo "</tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='8'>Tidak ada pesanan yang tersedia.</td></tr>"; 
            }
            ?>
        </tbody>
    </table>
    
    <!-- Paginasi -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&status_pesanan=<?php echo htmlspecialchars($statusFilter); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&status_pesanan=<?php echo htmlspecialchars($statusFilter); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&status_pesanan=<?php echo htmlspecialchars($statusFilter); ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<!-- end main content -->

<script>
     setTimeout(function() {
        var alertElement = document.getElementById('statusAlert');
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 500); 
    function reloadFavicon() {
    var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
    link.type = 'image/png';
    link.rel = 'icon';
    link.href = '../gambar/logoAntika.png?v=' + new Date().getTime();
    document.getElementsByTagName('head')[0].appendChild(link);
}

// Panggil reloadFavicon setiap kali status diperbarui
reloadFavicon();
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
