<?php
session_start();
include 'konfig.php';
include 'cek.php';

$produk_ID = isset($_POST['produk_ID']) ? mysqli_real_escape_string($koneksi, $_POST['produk_ID']) : '';
$nama_produk = isset($_POST['nama_produk']) ? mysqli_real_escape_string($koneksi, $_POST['nama_produk']) : '';
$deskripsi_produk = isset($_POST['deskripsi_produk']) ? $_POST['deskripsi_produk'] : ''; // Ambil tanpa escape
$stok_produk = isset($_POST['stok_produk']) ? intval($_POST['stok_produk']) : 0;
$harga_produk = isset($_POST['harga_produk']) ? floatval($_POST['harga_produk']) : 0.0;

// Validasi data
if (empty($produk_ID) || empty($nama_produk) || empty($deskripsi_produk) || $stok_produk < 0 || $harga_produk < 0) {
    die('Data tidak lengkap atau tidak valid.');
}

$checkQuery = "SELECT COUNT(*), gambar_produk FROM produk WHERE produk_ID = ?";
$checkStmt = mysqli_prepare($koneksi, $checkQuery);
mysqli_stmt_bind_param($checkStmt, 's', $produk_ID);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_bind_result($checkStmt, $count, $gambar_lama);
mysqli_stmt_fetch($checkStmt);
mysqli_stmt_close($checkStmt);

if ($count == 0) {
    die('produk_ID tidak valid. Pastikan produk_ID ada di tabel produk.');
}

$gambar_produk = $_FILES['gambar_produk']['name'];
$gambar_tmp = $_FILES['gambar_produk']['tmp_name'];
$gambar_path = 'uploads/' . basename($gambar_produk);

if (!empty($gambar_produk)) {
    if (move_uploaded_file($gambar_tmp, $gambar_path)) {
        // Ganti gambar dengan yang baru
        $gambar_final = $gambar_path;
    } else {
        die('Gagal mengupload gambar.');
    }
} else {
    $gambar_final = $gambar_lama;
}

$checkPerubahanQuery = "SELECT COUNT(*) FROM perubahan_produk WHERE produk_ID = ?";
$checkPerubahanStmt = mysqli_prepare($koneksi, $checkPerubahanQuery);
mysqli_stmt_bind_param($checkPerubahanStmt, 's', $produk_ID);
mysqli_stmt_execute($checkPerubahanStmt);
mysqli_stmt_bind_result($checkPerubahanStmt, $perubahan_count);
mysqli_stmt_fetch($checkPerubahanStmt);
mysqli_stmt_close($checkPerubahanStmt);

if ($perubahan_count > 0) {
    $query = "UPDATE perubahan_produk SET nama_produk = ?, deskripsi_produk = ?, stok_produk = ?, harga_produk = ?, gambar_produk = ?, request_ubah_produk = 'menunggu' WHERE produk_ID = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'sssdss', $nama_produk, $deskripsi_produk, $stok_produk, $harga_produk, $gambar_final, $produk_ID);
} else {
    $query = "INSERT INTO perubahan_produk (produk_ID, nama_produk, deskripsi_produk, stok_produk, harga_produk, gambar_produk, request_ubah_produk) 
              VALUES (?, ?, ?, ?, ?, ?, 'menunggu')";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'ssssss', $produk_ID, $nama_produk, $deskripsi_produk, $stok_produk, $harga_produk, $gambar_final);
}

// Eksekusi query insert/update
if (mysqli_stmt_execute($stmt)) {
    $query_update_produk = "UPDATE produk SET request_ubah_produk = 'menunggu' WHERE produk_ID = ?";
    $update_stmt = mysqli_prepare($koneksi, $query_update_produk);
    mysqli_stmt_bind_param($update_stmt, 's', $produk_ID);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);

    header('Location: produk.php');
} else {
    die('Query Error: ' . mysqli_error($koneksi));
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
