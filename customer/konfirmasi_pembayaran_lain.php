<?php
session_start();
include '../admin/konfig.php';
include 'cek.php';

$nama = $_POST['nama'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$telepon = $_POST['telepon'] ?? '';
$catatan_pesanan = $_POST['catatan_pesanan'] ?? '';
$kota = $_POST['kota'] ?? '';
$biaya_kirim_value = $_POST['biaya_kirim_value'] ?? 0;
$total_akhir = $_POST['total_akhir'] ?? 0;
$nama_promo = $_POST['nama_promo'] ?? ''; // Kode promo yang dimasukkan pengguna
$nominal_diskon = 0;

// Ambil username dari session
$username = $_SESSION['username'] ?? '';
$customer_ID = '';
if ($username) {
    $query = "SELECT customer_ID FROM customer WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        $customer_ID = $customer['customer_ID'];
    } else {
        die("Error: User not found.");
    }
}

// Masukkan pesanan ke tabel orders
$query = "INSERT INTO orders (customer_ID, status_pesanan, harga_total, nama_penerima, nama_kota) 
          VALUES ('$customer_ID', 'menunggu validasi pembayaran', '$total_akhir', '$nama', '$kota')";
if (!mysqli_query($koneksi, $query)) {
    die("Error: menambahkan order: " . mysqli_error($koneksi));
}

// Ambil order_ID
$order_ID_query = "SELECT MAX(order_ID) as last_order_ID FROM orders";
$result = mysqli_query($koneksi, $order_ID_query);
if ($result && mysqli_num_rows($result) > 0) {
    $order = mysqli_fetch_assoc($result);
    $order_ID = $order['last_order_ID'];
} else {
    die("Error dalam mengambil order_ID: " . mysqli_error($koneksi));
}

// Hitung total harga produk untuk pesanan
$total_harga_produk = 0;
$query = "SELECT p.produk_ID, c.kuantitas, p.harga_produk
          FROM keranjang c 
          JOIN produk p ON c.produk_ID = p.produk_ID 
          WHERE c.customer_ID = '$customer_ID'";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $produk_ID = $row['produk_ID'];
        $kuantitas = $row['kuantitas'];
        $harga_produk = $row['harga_produk'];
        $harga_total_produk = $kuantitas * $harga_produk;
        $total_harga_produk += $harga_total_produk;

        $promo_id = '0'; 
        if (!empty($nama_promo)) {
            $promo_query = "SELECT promo_ID FROM promo WHERE nama_promo = '$nama_promo' AND status_promo = 'aktif'";
            $promo_result = mysqli_query($koneksi, $promo_query);
        
            if ($promo_result && mysqli_num_rows($promo_result) > 0) {
                $promo_data = mysqli_fetch_assoc($promo_result);
                $promo_id = $promo_data['promo_ID']; 
            }
        }

        // Simpan detail pesanan ke order_details
        $query = "INSERT INTO order_details (order_ID, produk_ID, kuantitas, harga_produk, harga_total_produk, biaya_pengiriman, nomor_telepon_penerima, alamat_penerima, catatan_pesanan, promo_id) 
                  VALUES ('$order_ID', '$produk_ID', '$kuantitas', '$harga_produk', '$harga_total_produk', '$biaya_kirim_value', '$telepon', '$alamat', '$catatan_pesanan', '$promo_id')";
        if (!mysqli_query($koneksi, $query)) {
            die("Error: menambahkan order_detail: " . mysqli_error($koneksi));
        }
        

        // Update stok produk
        $query = "UPDATE produk
                  SET stok_produk = stok_produk - $kuantitas 
                  WHERE produk_ID = '$produk_ID'";
        if (!mysqli_query($koneksi, $query)) {
            die("Error: memperbarui stok produk: " . mysqli_error($koneksi));
        }
    }

    // Hapus item dari keranjang setelah pesanan berhasil disimpan
    $delete_cart_query = "DELETE FROM keranjang WHERE customer_ID = '$customer_ID'";
    if (!mysqli_query($koneksi, $delete_cart_query)) {
        die("Error: saat menghapus item dari keranjang: " . mysqli_error($koneksi));
    }
} else {
    die("Error: tidak ada produk di keranjang.");
}

// Upload bukti pembayaran
$bukti_bayar = '';
if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['bukti_bayar']['tmp_name'];
    $file_name = $_FILES['bukti_bayar']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    if (in_array($file_ext, $allowed_ext)) {
        $bukti_bayar = 'uploads/payment/' . time() . '.' . $file_ext;
        if (move_uploaded_file($file_tmp, $bukti_bayar)) {
            // Simpan bukti pembayaran
            // $query = "INSERT INTO pembayaran (order_ID, customer_ID, bukti_bayar) 
            //           VALUES ('$order_ID', '$customer_ID', '$bukti_bayar')";
            // if (!mysqli_query($koneksi, $query)) {
            //     die("Error inserting payment proof: " . mysqli_error($koneksi));
            // }
            $query = "INSERT INTO pembayaran (order_ID, customer_ID, bukti_bayar, relasi_karyawan_ID) 
          VALUES ('$order_ID', '$customer_ID', '$bukti_bayar', 'RELASI05')";
if (!mysqli_query($koneksi, $query)) {
    die("Error memasukkan bukti bayar: " . mysqli_error($koneksi) . " | Query: " . $query);
}
        } else {
            die("Error: gagal mengunggah produk.");
        }
    } else {
        die("Error: tipe file invalid.");
    }
} else {
    // Tidak ada file yang diunggah atau error
    echo "Bukti pembayaran akan diunggah kemudian.";
}

header('Location: pesanan.php');
exit();
?>
