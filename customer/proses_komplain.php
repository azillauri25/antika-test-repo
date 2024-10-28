<?php
session_start();
include '../admin/konfig.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    die("Anda harus login terlebih dahulu.");
}

// Ambil customer_ID dari session
$username = $_SESSION['username'];
$query = "SELECT customer_ID FROM customer WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Pengguna tidak ditemukan.");
}
$customer = mysqli_fetch_assoc($result);
$customer_ID = $customer['customer_ID'];

// Ambil data dari form
$order_ID = isset($_POST['order_ID']) ? $_POST['order_ID'] : '';
$produk_ID = isset($_POST['produk_ID']) ? $_POST['produk_ID'] : '';
$kontak_yg_dapat_dihubungi = isset($_POST['kontak_yg_dapat_dihubungi']) ? $_POST['kontak_yg_dapat_dihubungi'] : '';
$isi_komplain = isset($_POST['komplain']) ? $_POST['komplain'] : '';
$bukti_komplain = '';

// Proses upload gambar jika ada
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../admin/uploads/komplain/";  // Pastikan folder uploads ada dan memiliki izin tulis
    $file_name = basename($_FILES['gambar']['name']);
    $target_file = $target_dir . time() . "_" . $file_name;  // Tambahkan timestamp untuk menghindari konflik nama
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi tipe file harus diantara 3 ini
    $allowed_types = array('jpg', 'jpeg', 'png');
    if (!in_array($imageFileType, $allowed_types)) {
        die("Format gambar tidak didukung. Hanya JPG, JPEG, dan PNG yang diperbolehkan.");
    }

    // Pindahkan file ke folder tujuan
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
        $bukti_komplain = $target_file;
    } else {
        die("Gagal mengunggah gambar.");
    }
}

// Masukkan data komplain ke dalam tabel komplain
$query = "INSERT INTO komplain (order_ID, customer_ID, isi_komplain, bukti_komplain, kontak_yg_dapat_dihubungi, relasi_karyawan_ID)
          VALUES ('$order_ID', '$customer_ID', '$isi_komplain', '$bukti_komplain', '$kontak_yg_dapat_dihubungi', 'RELASI03')";

if (mysqli_query($koneksi, $query)) {
    // Ambil ID komplain yang baru saja ditambahkan biar muncul di halaman ulasan dan komplain
    $komplain_ID = mysqli_insert_id($koneksi);
    // Redirect ke halaman ulasan
    header("Location: ulasan_komplain.php#komplain");
    exit();
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>
