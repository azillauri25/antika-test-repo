<?php
session_start();
include '../admin/konfig.php';
include 'cek.php';

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// ambil nama admin
$nama_karyawan = 'Guest'; // Default jika tidak ada session (as guest)
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan'];
    }
}

$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Filter berdasarkan bulan dan tahun
$whereClauses = ["o.status_pesanan = 'pesanan selesai'"];
if ($month) {
    $whereClauses[] = "MONTH(o.waktu_pesanan_dibuat) = " . intval($month); 
}
if ($year) {
    $whereClauses[] = "YEAR(o.waktu_pesanan_diperbarui) = " . intval($year); 
}
$whereSql = implode(' AND ', $whereClauses);

$orderBy = isset($_GET['sort']) && $_GET['sort'] == 'tanggal' ? 'o.waktu_pesanan_dobuat' : 'o.order_ID';

$totalTransaksiQuery = "SELECT 
        DATE(o.waktu_pesanan_dibuat) AS tanggal_transaksi, 
        COUNT(o.order_ID) AS total_transaksi
    FROM orders o
    WHERE $whereSql
    GROUP BY DATE(o.waktu_pesanan_dibuat)
    ORDER BY $orderBy DESC"; 
    $result_total_transaksi = mysqli_query($koneksi, $totalTransaksiQuery);

// Debugging: cek kesalahan SQL
if (!$result_total_transaksi) {
    echo "Error: " . mysqli_error($koneksi);
}


$orderBy2 = isset($_GET['sort']) && $_GET['sort'] == 'produk' ? 'p.nama_produk' : 'o.order_ID';

$totalTransaksiQuery2 = "SELECT 
    p.nama_produk, 
    COUNT(o.order_ID) AS total_transaksi
FROM orders o
JOIN order_details od ON od.order_ID = o.order_ID
JOIN produk p ON od.produk_ID = p.produk_ID
WHERE $whereSql
GROUP BY p.nama_produk
ORDER BY $orderBy2 DESC;
";
    $result_total_transaksi2 = mysqli_query($koneksi, $totalTransaksiQuery2);

// Debugging: cek kesalahan SQL
if (!$result_total_transaksi2) {
    echo "Error: " . mysqli_error($koneksi); // Tampilkan error SQL
    exit; // Keluar dari script jika ada error
}

$query_laporan = "SELECT 
    o.order_ID, 
    o.customer_ID, 
    o.harga_total, 
    o.nama_penerima, 
    o.waktu_pesanan_dibuat, 
    o.waktu_sampai,
    c.nama_customer,
    p.nama_produk,
    od.kuantitas,
    od.harga_produk,
    od.biaya_pengiriman,
    od.harga_total_produk,
    pr.nominal_diskon
FROM orders o
JOIN order_details od ON o.order_ID = od.order_ID
JOIN produk p ON od.produk_ID = p.produk_ID
JOIN customer c ON o.customer_ID = c.customer_ID
LEFT JOIN promo pr ON od.promo_ID = pr.promo_ID
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
    <title>Antika Anggrek | Laporan Penjualan - Direktur Utama</title>
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
<div class="content content-main">
    <h2 class="mb-5 mt-5 text-left">Laporan Penjualan</h2>

    <!-- form fiter -->
    <form method="GET" class="mb-2">
        <div class="form-row">
        <div class="form-group col-md-1.5">
                <label for="month">Bulan</label>
                <select id="month" name="month" class="form-control">
                    <option value="">Semua Bulan</option>
                    <?php 
                    $bulanIndo = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ];
                    
                    for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php echo ($month == $m) ? 'selected' : ''; ?>>
                            <?php echo $bulanIndo[$m]; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group col-md-1.5">
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
            <div class="form-group col-md-1.5">
                <label>&nbsp;</label>
                <button type="submit" style="font-size:10px;" class="btn btn-primary form-control">Ambil Laporan</button>
            </div>
        </div>
    </form>
    <!-- end filter -->

    <!-- tombol unduh -->
        <form class="mb-5 text-left"method="POST" action="unduh_laporan.php">
            <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
            <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
            <button type="submit" name="export" value="csv" class="btn btn-success" style="font-size:10px;">Unduh PDF</button>
        </form>

     <!-- end tombol unduh -->

     <form method="GET" class="mb-2" id="sortForm" onsubmit="showTable()">
        <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
        <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
        
        <a id="transaksiBtn" href="#tanggal" class="btn btn-light mt-3 mb-3" style="float: left !important; font-size: 10px;" onclick="toggleButtons('transaksi')">Urutkan berdasarkan tanggal transaksi</a>
        <a id="produkBtn" href="#produk" class="btn btn-light mt-3 mb-3 m-3" style="float: left !important; font-size: 10px;" onclick="toggleButtons('produk')">Urutkan berdasarkan nama produk</a>
        <a id="defaultBtn" href="laporan.php" class="btn btn-warning mt-3 mb-3" style="float: left !important; font-size: 10px;" onclick="toggleButtons('default')">Tampilkan semua</a>
    </form>

<!-- Tabel ini akan disembunyikan terlebih dahulu -->



    <table class="table table-bordered table-striped admin-laporan" id="default" style="display: table;">
    <thead class="thead thead-admin admin-laporan">
        <tr>
            <th scope="col">Order ID</th>
            <th scope="col">Nama Produk</th>
            <th scope="col">Nama Penerima</th>
            <th scope="col">Kuantitas</th>
            <th scope="col">Harga Produk</th>
            <th scope="col">Harga Total</th>
            <th scope="col">Biaya Pengiriman</th>
            <th scope="col">Promo Digunakan</th>
            <th scope="col">Harga Terbayar</th>
            <th scope="col">Tanggal Order</th>
            <th scope="col">Waktu Sampai</th>
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
                'nama_customer' => $row['nama_customer'],
                'waktu_pesanan_dibuat' => $row['waktu_pesanan_dibuat'],
                'waktu_sampai' => $row['waktu_sampai'],
                'produk' => []
            ];
        }

        // Tambahkan produk ke order_ID yang sesuai
        $orders[$order_ID]['produk'][] = [
            'nama_produk' => $row['nama_produk'],
            'kuantitas' => $row['kuantitas'],
            'harga_produk' => $row['harga_produk'],
            'harga_total_produk' => $row['harga_total_produk'],
            'biaya_pengiriman' => $row['biaya_pengiriman'],
            'nominal_diskon' => $row['nominal_diskon'],
            'harga_total' => $row['harga_total']
        ];
    }

    // Tampilkan data di tabel
    foreach ($orders as $order) {
        $firstproduk = true;
        foreach ($order['produk'] as $produk) {
            echo "<tr>";
            if ($firstproduk) {
                // Jika ini adalah produk pertama untuk order_ID, tampilkan informasi order_ID dan customer
                echo "<td rowspan='" . count($order['produk']) . "'>" . htmlspecialchars($order['order_ID']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['nama_produk']) . "</td>";
                echo "<td rowspan='" . count($order['produk']) . "'>" . htmlspecialchars($order['nama_customer']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['kuantitas']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['harga_produk']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['harga_total_produk']) . "</td>";
                echo "<td rowspan='" . count($order['produk']) . "'>" . htmlspecialchars($produk['biaya_pengiriman']) . "</td>";
                echo "<td rowspan='" . count($order['produk']) . "'>" . htmlspecialchars($produk['nominal_diskon']) . "</td>";
                echo "<td rowspan='" . count($order['produk']) . "'>" . htmlspecialchars($produk['harga_total']) . "</td>";
                echo "<td>" . date('d-m-Y H:i', strtotime($order['waktu_pesanan_dibuat'])) . "</td>";
                echo "<td>" . date('d-m-Y H:i', strtotime($order['waktu_sampai'])) . "</td>";



                $firstproduk = false;
            } else {
                // Jika bukan produk pertama, hanya tampilkan nama produk
                echo "<td>" . htmlspecialchars($produk['nama_produk']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['kuantitas']) . "</td>";
                echo "<td>" . htmlspecialchars($produk['harga_produk']) . "</td>";
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


<table class="table table-bordered table-striped admin-laporan mt-5" id="transaksi" style="display: none; margin-top: 300px;">

    <thead class="thead thead-admin admin-laporan">
        <tr>
            <th style="min-width: 600px;">Tanggal Transaksi</th>
            <th style="min-width: 370px;">Total Transaksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($result_total_transaksi) > 0) {
            while ($row = mysqli_fetch_assoc($result_total_transaksi)) {
                echo "<tr>
                        <td style='min-width: 600px;'>{$row['tanggal_transaksi']}</td>
                        <td style='min-width: 370px;'>{$row['total_transaksi']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>Tidak ada data.</td></tr>";
        }
        ?>
    </tbody>
</table>


<table class="table table-bordered table-striped admin-laporan mt-5" id="produk" style="display: none;">

    <thead class="thead thead-admin admin-laporan">
        <tr>
        <th style="min-width: 600px;">Nama Produk</th>
        <th style="min-width: 370px;">Total Transaksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($result_total_transaksi2) > 0) {
            while ($row = mysqli_fetch_assoc($result_total_transaksi2)) {
                echo "<tr>
                        <td style='min-width: 600px;'>{$row['nama_produk']}</td>
                        <td style='min-width: 370px;'>{$row['total_transaksi']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>Tidak ada data.</td></tr>";
        }
        ?>
    </tbody>
</table>


</div>
    
<!-- Tampilkan total transaksi -->

</div>
<!-- end main content -->
<script>
function toggleButtons(selected) {
    // Mengambil elemen tabel
    var transaksi = document.getElementById('transaksi');
    var defaultTable = document.getElementById('default');
    var produk = document.getElementById('produk');

    // Menyembunyikan semua tabel terlebih dahulu
    transaksi.style.display = 'none';
    defaultTable.style.display = 'none';
    produk.style.display = 'none';

    // Mengambil elemen tombol
    const transaksiBtn = document.getElementById('transaksiBtn');
    const produkBtn = document.getElementById('produkBtn');

    // Menampilkan tabel yang sesuai dan mengubah kelas tombol
    if (selected === 'transaksi') {
        transaksi.style.display = 'block';
        transaksiBtn.classList.remove('btn-light');
        transaksiBtn.classList.add('btn-primary');
        produkBtn.classList.remove('btn-primary');
        produkBtn.classList.add('btn-light');
    } else if (selected === 'produk') {
        produk.style.display = 'block';
        produkBtn.classList.remove('btn-light');
        produkBtn.classList.add('btn-primary');
        transaksiBtn.classList.remove('btn-primary');
        transaksiBtn.classList.add('btn-light');
    } else {
        defaultTable.style.display = 'block';
        transaksiBtn.classList.remove('btn-primary');
        transaksiBtn.classList.add('btn-light');
        produkBtn.classList.remove('btn-primary');
        produkBtn.classList.add('btn-light');
    }
}
</script>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
