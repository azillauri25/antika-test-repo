<?php
session_start();
include '../admin/konfig.php';
include 'cek.php'; // Memastikan user sudah login saat akses ke halaman ini

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Query untuk mengambil nama admin yang sedang login
$nama_karyawan = 'Guest'; // Default jika tidak ada session (as guest)
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan'];
    }
}

// Query untuk mengambil data pesanan
$order_ID = isset($_GET['order_ID']) ? $_GET['order_ID'] : '';

// Ambil data pesanan
$query = "SELECT o.order_ID, o.status_pesanan, o.harga_total, o.nama_penerima, d.alamat_penerima, d.nomor_telepon_penerima, d.biaya_pengiriman,
d.produk_ID, d.catatan_pesanan, d.kuantitas, d.harga_produk, p.nama_produk, p.gambar_produk, pr.nominal_diskon, pb.bukti_bayar
FROM orders o
JOIN order_details d ON o.order_ID = d.order_ID
JOIN produk p ON d.produk_ID = p.produk_ID
JOIN pembayaran pb ON o.order_ID = pb.order_ID
LEFT JOIN promo pr ON d.promo_ID = pr.promo_ID
WHERE o.order_ID = '$order_ID'
GROUP BY d.produk_ID
";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Proses validasi dan penolakan pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_ID = isset($_POST['order_ID']) ? $_POST['order_ID'] : '';
    $status_pesanan = isset($_POST['status_pesanan']) ? $_POST['status_pesanan'] : '';

    // Update status pesanan di database
    $queryUpdate = "UPDATE orders SET status_pesanan='$status_pesanan' WHERE order_ID='$order_ID'";
    if (mysqli_query($koneksi, $queryUpdate)) {
        echo "<script>window.location.href='pesanan.php?order_ID=" . htmlspecialchars($order_ID) . "';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status pesanan: " . mysqli_error($koneksi) . "');</script>";
    }
}


$orderData = [];
if (mysqli_num_rows($result) > 0) {
    while ($order = mysqli_fetch_assoc($result)) {
        // Simpan data pesanan
        if (!isset($orderData['order_ID'])) {
            $orderData['order_ID'] = $order['order_ID'];
            $orderData['status_pesanan'] = $order['status_pesanan'];
            $orderData['harga_total'] = $order['harga_total'];
            $orderData['nama_penerima'] = $order['nama_penerima'];
            $orderData['alamat_penerima'] = $order['alamat_penerima'];
            $orderData['nomor_telepon_penerima'] = $order['nomor_telepon_penerima'];
            $orderData['nominal_diskon'] = $order['nominal_diskon'];
            $orderData['catatan_pesanan'] = $order['catatan_pesanan'];
            $orderData['produk'] = [];
        }

        // Tambahkan produk ke data pesanan
        $orderData['produk'][] = [
            'produk_ID' => $order['produk_ID'],
            'kuantitas' => $order['kuantitas'],
            'harga_produk' => $order['harga_produk'],
            'nama_produk' => $order['nama_produk'],
            'gambar_produk' => $order['gambar_produk'],
        ];

        // Simpan bukti pembayaran
        $orderData['bukti_bayar'] = $order['bukti_bayar'];
        $orderData['biaya_pengiriman'] = $order['biaya_pengiriman'];
    }
}

?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Detail Pesanan - Finance</title>
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
<div class="content">

    <button class="btn btn-light mb-3" style="float: left;" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> Kembali
    </button>
    <div style="clear: both;"></div>
    <!-- end tombol back -->


    <h2 class="mb-5 mt-5 text-left">Detail Pesanan</h2>
    <div class="row">
        <div class="col-md-2 text-left">
          <p><strong>Nama Penerima</strong></p>
          <p><strong>Alamat Penerima</strong></p>
          <p><strong>Nomor Telepon Penerima</strong></p>
          <p><strong>Catatan Pesanan</strong></p>
        </div>
        <div class="col-md-10 text-left">
          <p>: <?php echo htmlspecialchars($orderData['nama_penerima']); ?></p>
          <p>: <?php echo htmlspecialchars($orderData['alamat_penerima']); ?></p>
          <p>: <?php echo htmlspecialchars($orderData['nomor_telepon_penerima']); ?></p>
          <p>: <?php echo htmlspecialchars($orderData['catatan_pesanan']); ?></p>
        </div>
      </div>

    <table class="table table-bordered table-striped mt-5">
        <thead class="thead thead-admin">
            <tr>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Catatan Pesanan</th>
                <th>Harga Produk</th>
                <th>kuantitas</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orderData['produk'])): ?>
                <?php foreach ($orderData['produk'] as $produk): ?>
                    <tr>
                    <td><img src="../admin/<?php echo htmlspecialchars($produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" style="width:50px; height:auto;"></td>    
                    <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                    <td><?php echo htmlspecialchars($orderData['catatan_pesanan']); ?></td>
                        <td>Rp <?php echo number_format($produk['harga_produk'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($produk['kuantitas']); ?></td>
                        <td>Rp <?php echo number_format($produk['harga_produk'] * $produk['kuantitas'], 0, ',', '.'); ?></td>
                    </tr>

                    <?php endforeach; ?>
                    <tr>
                        <td colspan="5" style="text-align: right;">Biaya pengiriman</td>
                        <td>Rp <?php echo isset($orderData['biaya_pengiriman']) ? number_format($orderData['biaya_pengiriman'], 0, ',', '.') : '0'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: right;">Promo yang digunakan</td>
                        <td>Rp -<?php echo isset($orderData['nominal_diskon']) ? number_format($orderData['nominal_diskon'], 0, ',', '.') : '0'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: right;"><strong>Total yang harus dibayar</strong></td>
                        <td><strong>Rp <?php echo isset($orderData['harga_total']) ? number_format($orderData['harga_total'], 0, ',', '.') : '0'; ?></strong></td>
                    </tr>
            <?php else: ?>
                <tr><td colspan="6">Tidak ada detail produk untuk pesanan ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <h3 class="mt-5">Bukti Pembayaran</h3>
    <?php if (!empty($orderData['bukti_bayar'])): ?>
        <img src="../customer/<?php echo htmlspecialchars($orderData['bukti_bayar']); ?>" alt="Bukti Pembayaran" style="max-width: 250px; height: auto;">
    <?php else: ?>
        <p>Belum ada bukti pembayaran untuk pesanan ini.</p>
    <?php endif; ?>


    <div class="validasi-pesanan" style="margin-top: 50px; margin-bottom: 100px;">
    <h3>Status Validasi</h3>
    <?php if ($orderData['status_pesanan'] == 'menunggu validasi pembayaran'): ?>
        <table class="table table-bordered" style="width: 50%; margin: auto; text-align: center; border: 1px solid #000;">
            <tr style="border: 1px solid #000;">
                <td style="width: 50%; border: 1px solid #000;">
                    <!-- Tombol Validasi -->
                    <form method="POST" action="">
                        <input type="hidden" name="order_ID" value="<?php echo htmlspecialchars($orderData['order_ID']); ?>">
                        <input type="hidden" name="status_pesanan" value="pesanan diterima">
                        <button type="submit" class="btn btn-success btn-block">Validasi</button>
                    </form>
                </td>
                <td style="width: 50%; border: 1px solid #000;">
                    <!-- Tombol Tolak -->
                    <form method="POST" action="">
                        <input type="hidden" name="order_ID" value="<?php echo htmlspecialchars($orderData['order_ID']); ?>">
                        <input type="hidden" name="status_pesanan" value="pesanan ditolak">
                        <button type="submit" class="btn btn-danger btn-block">Tolak</button>
                    </form>
                </td>
            </tr>
        </table>
        <?php elseif (in_array($orderData['status_pesanan'], ['pesanan diterima', 'pesanan dikirim', 'pesanan selesai'])): ?>
        <p>Pembayaran ini sudah <strong>divalidasi.</strong></p>
    <?php elseif ($orderData['status_pesanan'] == 'pesanan ditolak'): ?>
        <p>Pembayaran ini <strong>ditolak.</strong></p>
    <?php endif; ?>
</div>



    </div>
<!-- end main content -->


<script>
function goBack() {
    window.history.back();
}
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
``
