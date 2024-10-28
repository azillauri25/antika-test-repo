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
    $searchQuery = "WHERE nama_produk LIKE '%$search%' OR produk_ID LIKE '%$search%'";
}
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'DESC';
$next_sort = ($sort === 'ASC') ? 'DESC' : 'ASC'; 
$query_produk = "SELECT p.produk_ID, p.nama_produk, p.deskripsi_produk, p.stok_produk, p.harga_produk, p.gambar_produk, p.request_tambah_produk, p.request_hapus_produk, pp.request_ubah_produk 
                 FROM produk p
                 LEFT JOIN perubahan_produk pp ON p.produk_ID = pp.produk_ID
                 $searchQuery ORDER BY produk_ID $sort";
$result_produk = mysqli_query($koneksi, $query_produk);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Antika Anggrek | Manager - Review Produk</title>
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
    <h2 class="mb-5 mt-4 text-left">Produk</h2>
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
    <?php if (mysqli_num_rows($result_produk) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result_produk)): ?>
            <tr>
            <td><?php echo htmlspecialchars($row['produk_ID']); ?></td>    
            <td><img src="../admin/<?php echo htmlspecialchars($row['gambar_produk']); ?>" width="50" alt="Gambar Produk"></td>
                <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                <td><?php echo htmlspecialchars($row['stok_produk']); ?></td>
                <td>Rp <?php echo number_format($row['harga_produk'], 0, ',', '.'); ?></td>
                <td>
                    <?php if ($row['request_tambah_produk'] == 'menunggu'): ?>
                        <form method="POST" action="proses_tambah_produk.php">
                            <input type="hidden" name="produk_ID" value="<?php echo $row['produk_ID']; ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success m-1" style="font-size:10px;">Setujui</button>
                            <button type="submit" name="action" value="reject" class="btn btn-rejected m-1" style="font-size:10px;" onclick="confirmReject(<?php echo $row['produk_ID']; ?>)">Tolak</button>
                        </form>
                        <?php elseif ($row['request_tambah_produk'] == 'disetujui' OR $row['request_ubah_produk'] == 'disetujui'): ?> 
                            <span class="badge badge-success" style="font-size:10px;">Sedang tayang</span>
                        <?php else: ?>
                            <span class="badge badge-success" style="font-size:10px;">Tidak Tayang</span>
                        <?php endif; ?>

                </td>
                <td>
                    <?php if ($row['request_ubah_produk'] == 'menunggu'): ?>
                        <form method="POST" action="proses_ubah_produk.php">
                            <input type="hidden" name="produk_ID" value="<?php echo $row['produk_ID']; ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success m-1" style="font-size:10px;">Setujui</button>
                            <button type="submit" name="action" value="reject" class="btn btn-rejected m-1" style="font-size:10px;" onclick="confirmReject(<?php echo $row['produk_ID']; ?>)">Tolak</button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['request_hapus_produk'] == 'menunggu'): ?>
                        <a href="proses_hapus_produk.php?produk_ID=<?php echo htmlspecialchars($row['produk_ID']); ?>&action=approve" class="btn btn-danger m-1" style="font-size:10px;">Setujui</a>
                        <a href="proses_hapus_produk.php?produk_ID=<?php echo htmlspecialchars($row['produk_ID']); ?>&action=reject" class="btn btn-light m-1" style="font-size:10px;">Tolak</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                <a href='detail_produk.php?produk_ID=<?php echo htmlspecialchars($row["produk_ID"]); ?>' style='font-size:10px;'>Lihat</a>



                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="9" class="text-center">Tidak ada produk pending atau permintaan hapus untuk direview.</td>
        </tr>
    <?php endif; ?>
</tbody>
</table>
</div>
<!-- end main content -->

<script>
    function confirmReject(produkId) {
        if (confirm('Apakah Anda yakin ingin menolak produk ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'proses_produk.php';

            const input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = 'produk_ID';
            input1.value = produkId;

            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = 'action';
            input2.value = 'reject';

            form.appendChild(input1);
            form.appendChild(input2);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
