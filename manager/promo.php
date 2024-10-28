<?php
session_start();
include '../admin/konfig.php';
include 'cek.php'; // Memastikan user sudah login saat akses ke halaman ini

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Query untuk mengambil nama pengguna yang sedang login
$nama_karyawan = 'Guest'; // Default jika tidak ada session (as guest)
if ($username) {
    $query = "SELECT nama_karyawan FROM karyawan WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_karyawan = $data['nama_karyawan'];
    }
}
$search = isset($_GET['search']) ? $_GET['search'] : '';


$searchQuery = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $searchQuery = "WHERE nama_promo LIKE '%$search%' OR deskripsi_promo LIKE '%$search%' OR status_promo LIKE '%$search%'";
}

// Query untuk promo yang status_review-nya 'pending'
$query_promo = "SELECT * FROM promo $searchQuery ORDER BY promo_ID DESC";
$result_promo = mysqli_query($koneksi, $query_promo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Antika Anggrek | Manager - Review Promo</title>
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
    <a href="promo.php"><i class="fa fa-gift"></i> Promo</a>

</div>
</div>
<!-- end sidebar -->

<!-- start navbar -->
<div class="header">
    <div class="username">Selamat Datang, Manager <strong><?php echo htmlspecialchars($nama_karyawan); ?></strong></div>
    <a href="../admin/logout.php" class="logout"><i class="fa fa-sign-out-alt"></i></a>
</div>
<!-- end navbar -->

<!-- start main content -->
<div class="content content-main">
    <h2 class="mb-5 mt-4 text-left">Promo</h2>
    <div class="mb-3 float-right">
        <form method="GET" action="promo.php">
            <input type="text" name="search" placeholder="Cari produk" class="form-control" style="width: 200px; display: inline-block; font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Cari</button>
            <button type="submit" class="btn btn-primary" style="font-size: 10px;">Tampilkan Semua</button>
        </form>
    </div>

    <!-- Tabs for switching between review sections -->
<!-- 
    <div class="tab-content" id="reviewTabsContent">
        <div class="tab-pane fade show active" id="pending-promo" role="tabpanel" aria-labelledby="pending-promo-tab"> -->
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
                        <th>Nonaktifkan Promo</th>

                    </tr>
                </thead>
                <tbody style="font-size:10px;">
            <?php if (mysqli_num_rows($result_promo) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_promo)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_promo']); ?></td>
                        <td><?php echo htmlspecialchars($row['nominal_diskon']); ?></td>
                        <td><?php echo htmlspecialchars($row['deskripsi_promo']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_mulai_promo']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_berakhir_promo']); ?></td>
                        <td>
                            <?php if ($row['request_tambah_promo'] == 'menunggu'): ?>
                                <a href="proses_tambah_promo.php?promo_ID=<?php echo htmlspecialchars($row['promo_ID']); ?>&action=accept" class="btn btn-success" style="font-size:10px; display: inline-block;">Setujui</a>
                                <a href="proses_tambah_promo.php?promo_ID=<?php echo htmlspecialchars($row['promo_ID']); ?>&action=reject" class="btn btn-light" style="font-size:10px; display: inline-block;">Tolak</a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['status_promo']); ?></td>
                        <td>
    <?php if ($row['status_promo'] == 'nonaktif' && $row['request_tambah_promo'] == 'disetujui'): ?>
        <span class="text-muted">Promo dinonaktifkan</span>
    <?php else: ?>
        <?php if ($row['request_nonaktif_promo'] == 'menunggu'): ?>
            <a href="proses_nonaktif_promo.php?promo_ID=<?php echo htmlspecialchars($row['promo_ID']); ?>&action=accept" class="btn btn-danger" style="font-size:10px;">Nonaktifkan</a>
            <a href="proses_nonaktif_promo.php?promo_ID=<?php echo htmlspecialchars($row['promo_ID']); ?>&action=reject" class="btn btn-light" style="font-size:10px;">Tolak</a>
        <?php else: ?>
            -
        <?php endif; ?>
    <?php endif; ?>
</td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada promo untuk direview.</td>
                </tr>
            <?php endif; ?>
        </tbody>
            </table>
        </div>
    <!-- </div>
</div> -->
<!-- end main content -->

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($koneksi);
?>
