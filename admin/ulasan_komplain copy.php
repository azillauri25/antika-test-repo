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
// Ambil data komplain
$query_komplain = "SELECT 
    c.komplain_ID, 
    c.order_ID, 
    c.customer_ID, 
    c.isi_komplain, 
    c.bukti_komplain, 
    c.tanggal_komplain, 
    c.status_komplain, 
    c.kontak, 
    CONCAT(c.customer_ID, ' - ', o.nama_penerima) AS penerima
FROM komplain c
JOIN orders o ON c.order_ID = o.order_ID
ORDER BY c.tanggal_komplain DESC";

$result_komplain = mysqli_query($koneksi, $query_komplain);

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
ORDER BY r.tanggal_ulasan DESC";

$result_ulasan = mysqli_query($koneksi, $query_ulasan);

// Debugging: cek kesalahan SQL
if (!$result_komplain || !$result_ulasan) {
    echo "Error: " . mysqli_error($koneksi);
}
?>

<!doctype html>
<html lang="en" class="fullscreen-bg">
<head>
    <title>Antika Anggrek | Ulasan dan Komplain - Admin</title>
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
<div class="content content-main" style="margin-top: 50px;">
    <h2 class="mb-5 mt-5 text-left">Ulasan dan Komplain</h2>

    <!-- <div class="add-produk-button-container float-left" style="margin-top:-10px;">
        <button type="button" class="btn btn-primary mt-3 btn-ulasan" style="font-size: 10px !important;" id="btnUlasan">Ulasan</button>
        <button type="button" class="btn btn-light mt-3 btn-ulasan" style="font-size: 10px !important;"id="btnKomplain">Komplain</button>
    </div> -->
    <div class="mb-4 float-left">
        <button type="button" class="btn btn-primary" style="font-size: 10px !important;" id="btnUlasan">Ulasan</button>
        <button type="button" class="btn btn-light" style="font-size: 10px !important;"id="btnKomplain">Komplain</button>
    </div>
    <div class="mb-4 float-right">
        <form method="GET" action="ulasan_komplain.php">
            <input type="text" name="search" placeholder="Cari ulasan atau komplain" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</button>
        </form>
    </div>



    <!-- Bagi section -->
    <div id="komplainSection" style="display: none;">
        <!-- Komplain Section -->
        <table class="table table-bordered table-striped admin-laporan mt-4">
            <thead class="thead thead-admin admin-laporan">
                <tr>
                    <th scope="col">Order ID</th>
                    <th scope="col">Customer ID</th>
                    <th scope="col">Isi Komplain</th>
                    <th scope="col">Bukti Komplain</th>
                    <th scope="col">Tanggal Komplain</th>
                    <th scope="col">Status Komplain</th>
                    <th scope="col">Kontak</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_komplain)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['isi_komplain']); ?></td>
                        
                        <td>
                                <?php if ($row['bukti_komplain']): ?>
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-<?php echo $row['komplain_ID']; ?>">
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
                        <td><?php echo date('d-m-Y', strtotime($row['tanggal_komplain'])); ?></td>
                        <td>
                            <form method="post" action="update_status_komplain.php">
                                <input type="hidden" name="komplain_ID" value="<?php echo htmlspecialchars($row['komplain_ID']); ?>">
                                <select name="status_komplain" class="form-control" onchange="this.form.submit()">
                                    <option value="menunggu" <?php if ($row['status_komplain'] == 'menunggu') echo 'selected'; ?>>Menunggu</option>
                                    <option value="komplain selesai" <?php if ($row['status_komplain'] == 'komplain selesai') echo 'selected'; ?>>Komplain Selesai</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($row['kontak']); ?></td>
                        <td>
                            <a href="https://wa.me/<?php echo urlencode($row['kontak']); ?>" class="btn btn-success btn-sm" target="_blank" title="Chat via WhatsApp">
                                <i class="fa fa-message"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="ulasanSection" style="display: block;">
        <!-- Ulasan Section -->
        <table class="table table-bordered table-striped admin-laporan mt-4">
            <thead class="thead thead-admin admin-laporan">
                <tr>
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
    document.getElementById('btnKomplain').addEventListener('click', function () {
        document.getElementById('komplainSection').style.display = 'block';
        document.getElementById('ulasanSection').style.display = 'none';
        this.classList.add('btn-primary');
        document.getElementById('btnUlasan').classList.remove('btn-primary');
        document.getElementById('btnUlasan').classList.add('btn-light');
    });

    document.getElementById('btnUlasan').addEventListener('click', function () {
        document.getElementById('ulasanSection').style.display = 'block';
        document.getElementById('komplainSection').style.display = 'none';
        this.classList.add('btn-primary');
        document.getElementById('btnKomplain').classList.remove('btn-primary');
        document.getElementById('btnKomplain').classList.add('btn-light');
    });
</script>

<!-- end main content -->

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>
