<?php
session_start();
include 'konfig.php';
include 'cek.php';

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// ambil nama admin
$username_admin = 'Guest'; // Default jika tidak ada session (as guest)
if ($username) {
    $query = "SELECT username FROM management WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $username_admin = $data['username'];
    }
}

$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Filter berdasarkan bulan dan tahun
$whereClauses = ["o.status = 'pesanan selesai'"];
if ($month) {
    $whereClauses[] = "MONTH(o.created_at) = " . intval($month);  // Pastikan month adalah integer
}
if ($year) {
    $whereClauses[] = "YEAR(o.created_at) = " . intval($year);  // Pastikan year adalah integer
}
$whereSql = implode(' AND ', $whereClauses);

$query_laporan = "SELECT 
    o.order_ID, 
    o.customer_ID, 
    o.harga_total, 
    o.nama_penerima, 
    o.created_at, 
    c.nama_cust,
    p.nama_produk,
    od.quantity,
    od.price,
    od.biaya_pengiriman,
    od.harga_total_produk
FROM orders o
JOIN order_details od ON o.order_ID = od.order_ID
JOIN produk p ON od.produk_ID = p.produk_ID
JOIN customer c ON o.customer_ID = c.customer_ID
WHERE $whereSql
ORDER BY o.order_ID, p.nama_produk";

$result_laporan = mysqli_query($koneksi, $query_laporan);

// Debugging: cek kesalahan SQL
if (!$result_laporan) {
    echo "Error: " . mysqli_error($koneksi);
}
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
    <a href="laporan.php"><i class="fa fa-file-alt"></i> Laporan</a>
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
    <h2 class="mb-5 mt-5 text-left">Laporan Penjualan</h2>

    <!-- form fiter -->
    <form method="GET" class="mb-2">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="month">Bulan</label>
                <select id="month" name="month" class="form-control">
                    <option value="">Semua Bulan</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php echo ($month == $m) ? 'selected' : ''; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="year">Tahun</label>
                <select id="year" name="year" class="form-control">
                    <option value="">Semua Tahun</option>
                    <?php
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= 2000; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">Filter</button>
            </div>
        </div>
    </form>
    <!-- end filter -->

    <!-- tombol unduh // admin gabisa unduh -->
        <!-- <form class="mb-5 text-left"method="POST" action="unduh_laporan.php">
            <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
            <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
            <button type="submit" name="export" value="csv" class="btn btn-success">Unduh CSV</button>
        </form> -->

     <!-- end tombol unduh -->

    
    <table class="table table-bordered table-striped admin-laporan">
    <thead class="thead thead-admin admin-laporan">
        <tr>
            <th scope="col">Order ID</th>
            <th scope="col">Nama Produk</th>
            <th scope="col">Nama Customer</th>
            <th scope="col">Quantity</th>
            <th scope="col">Harga Jual</th>
            <th scope="col">Harga Total</th>
            <th scope="col">Biaya Pengiriman</th>
            <th scope="col">Harga Terbayar</th>
            <th scope="col">Tanggal Order</th>
        </tr>
    </thead>
    <tbody>
    <?php
// Array untuk menyimpan data
$orders = [];

// Ambil data dari query
if ($result_laporan && mysqli_num_rows($result_laporan) > 0) {
    while ($row = mysqli_fetch_assoc($result_laporan)) {
        $order_ID = $row['order_ID'];

        // Jika order_ID belum ada dalam array, tambahkan
        if (!isset($orders[$order_ID])) {
            $orders[$order_ID] = [
                'order_ID' => $order_ID,
                'nama_cust' => $row['nama_cust'],
                'created_at' => $row['created_at'],
                'product' => []
            ];
        }

        // Tambahkan produk ke order_ID yang sesuai
        $orders[$order_ID]['product'][] = [
            'nama_produk' => $row['nama_produk'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'harga_total_produk' => $row['harga_total_produk'],
            'biaya_pengiriman' => $row['biaya_pengiriman'],
            'harga_total' => $row['harga_total']
        ];
    }

    // Tampilkan data di tabel
    foreach ($orders as $order) {
        $firstproduk = true;
        foreach ($order['product'] as $produk) {
            echo "<tr>";
            if ($firstproduk) {
                // Jika ini adalah produk pertama untuk order_ID, tampilkan informasi order_ID dan customer
                echo "<td rowspan='" . count($order['product']) . "'>" . htmlspecialchars($order['order_ID']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['nama_produk']) . "</td>";
                echo "<td rowspan='" . count($order['product']) . "'>" . htmlspecialchars($order['nama_cust']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['quantity']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['price']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['harga_total_produk']) . "</td>";
                echo "<td rowspan='" . count($order['product']) . "'>" . htmlspecialchars($produk['biaya_pengiriman']) . "</td>";
                echo "<td rowspan='" . count($order['product']) . "'>" . htmlspecialchars($produk['harga_total']) . "</td>";
                echo "<td rowspan='" . count($order['product']) . "'>" . htmlspecialchars($order['created_at']) . "</td>";
                $firstproduk = false;
            } else {
                // Jika bukan produk pertama, hanya tampilkan nama produk
                echo "<td>" . htmlspecialchars($produk['nama_produk']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['quantity']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['price']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['harga_total_produk']) . "</td>";
            }
            echo "</tr>";
        }
    }
} else {
    echo "<tr><td colspan='9'>Tidak ada pesanan yang tersedia.</td></tr>";
}
?>

    </tbody>
</table>


</div>
    
    <!-- Paginasi -->
</div>
<!-- end main content -->


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
