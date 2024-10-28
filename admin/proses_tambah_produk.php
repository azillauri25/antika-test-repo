<?php
session_start();
include 'konfig.php';
include 'cek.php'; // Memastikan user sudah login saat akses ke halaman ini

// Periksa apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_produk = $_POST['nama_produk'];
    $deskripsi_produk = $_POST['deskripsi_produk'];
    $stok_produk = $_POST['stok_produk'];
    $harga_produk = $_POST['harga_produk'];

    // Validasi input
    if (empty($nama_produk) || empty($deskripsi_produk) || empty($stok_produk) || empty($harga_produk)) {
        die('Semua field harus diisi.');
    }

    // Proses upload gambar
    $target_dir = "uploads/"; // Direktori tempat menyimpan gambar
    $gambar_produk = basename($_FILES["gambar_produk"]["name"]);
    $target_file = $target_dir . $gambar_produk;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Periksa apakah file adalah gambar sebenarnya
    $check = getimagesize($_FILES["gambar_produk"]["tmp_name"]);
    if ($check === false) {
        die("File yang diupload bukan gambar.");
    }

    // Periksa ukuran file (contoh: maksimum 5MB)
    if ($_FILES["gambar_produk"]["size"] > 5000000) {
        die("Ukuran gambar terlalu besar.");
    }

    // Izinkan format gambar tertentu saja
    $allowed_types = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowed_types)) {
        die("Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan.");
    }

    // Coba upload file
    if (move_uploaded_file($_FILES["gambar_produk"]["tmp_name"], $target_file)) {
        echo "File ". htmlspecialchars($gambar_produk) . " telah diupload.";
    } else {
        die("Terjadi kesalahan saat mengupload gambar.");
    }

    // Simpan informasi produk ke database dengan status 'menunggu'
    $gambar_path = $target_dir . $gambar_produk; // Path lengkap gambar
    $query = "INSERT INTO produk (nama_produk, deskripsi_produk, stok_produk, harga_produk, gambar_produk, request_tambah_produk, relasi_karyawan_ID) VALUES (?, ?, ?, ?, ?, 'menunggu', 'RELASI01')";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssdds', $nama_produk, $deskripsi_produk, $stok_produk, $harga_produk, $gambar_path);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            header("Location: produk.php");
            exit();
        } else {
            die("Gagal menambahkan produk: " . mysqli_error($koneksi));
        }

        mysqli_stmt_close($stmt);
    } else {
        die("Gagal mempersiapkan query: " . mysqli_error($koneksi));
    }

    mysqli_close($koneksi);
} else {
    die('Permintaan tidak valid.');
}
?>
