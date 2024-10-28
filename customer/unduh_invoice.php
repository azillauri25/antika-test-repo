<?php
session_start();
include '../admin/konfig.php';
require('libs/fpdf.php'); // Pastikan path ke FPDF benar

// Periksa koneksi database
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Dapatkan order_ID dari query string
$order_ID = isset($_GET['order_ID']) ? $_GET['order_ID'] : '';

if (empty($order_ID)) {
    die("Order ID tidak valid.");
}

// Ambil data pesanan dari database
$query = "SELECT o.*, c.nama_customer, o.nama_penerima, od.nomor_telepon_penerima, od.alamat_penerima, od.catatan_pesanan, p.nama_produk, pr.nominal_diskon
          FROM orders o 
          JOIN order_details od ON o.order_ID = od.order_ID
          JOIN customer c ON o.customer_ID = c.customer_ID 
          JOIN produk p ON od.produk_ID = p.produk_ID
          LEFT JOIN promo pr ON od.promo_ID = pr.promo_ID
          WHERE o.order_ID='$order_ID'";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) > 0) {
    $order = mysqli_fetch_assoc($result);

    // Pastikan nama_customer ada dalam hasil
    if (!isset($order['nama_customer'])) {
        die("Nama customer tidak ditemukan.");
    }

    // Buat instance FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->Image('polosan_invoice.png', 0, 0, 210, 297);
    $pdf->SetMargins(9, 0, 11);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Ln(38); 
    $pdf->SetTextColor(128, 0, 128);
    $pdf->Cell(0, 10, $order['order_ID'], 0, 1, 'R');

    $pdf->SetFont('Arial', '', 10);

    $pdf->Ln(7);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 5, date("d-m-Y"), 0, 1);
    $pdf->Cell(31, 5, 'Nama Customer    : ', 0, 0); 
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, $order['nama_customer'], 0, 1, 'L'); 
    $pdf->SetFont('Arial', '', 10); 
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Nama Penerima    : ' . $order['nama_penerima'], 0, 1);
    $pdf->Cell(0, 5, 'Nomor Penerima   : ' . $order['nomor_telepon_penerima'], 0, 1);
    $pdf->Cell(0, 5, 'Alamat Penerima  : ' . $order['alamat_penerima'], 0, 1);

    $pdf->Ln(18);
    $pdf->SetMargins(10, 0, 11);
    // $pdf->Cell(0, 10, 'Rincian Pesanan:', 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(80, 10, 'Nama Produk', 1);
    $pdf->Cell(20, 10, 'Jumlah', 1);
    $pdf->Cell(30, 10, 'Harga', 1);
    $pdf->Cell(50, 10, 'Harga Total Produk', 1); // Kolom catatan
    $pdf->Ln();


    $totalBiayaPengiriman = 0;// Inisialisasi total biaya pengiriman

    // Ambil detail produk dari database
    $detail_query = "SELECT d.produk_ID, d.harga_produk, d.kuantitas, d.harga_total_produk, d.catatan_pesanan, d.biaya_pengiriman, p.nama_produk, pr.nominal_diskon
                    FROM order_details d
                    JOIN produk p ON d.produk_ID = p.produk_ID
                    LEFT JOIN promo pr ON d.promo_ID = pr.promo_ID
                    WHERE d.order_ID='$order_ID'";
    $detail_result = mysqli_query($koneksi, $detail_query);

    if (!$detail_result) {
        die("Query error: " . mysqli_error($koneksi));
    }

    $totalBiayaPengiriman = 0; // Pastikan variabel ini didefinisikan sebelum loop

    // Menampilkan detail produk dalam tabel
    while ($detail = mysqli_fetch_assoc($detail_result)) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(80, 10, $detail['nama_produk'], 1);
        $pdf->Cell(20, 10, $detail['kuantitas'], 1);
        $pdf->Cell(30, 10, "Rp " . number_format($detail['harga_total_produk'], 0, ',', '.'), 1);
    
        // Hitung total harga berdasarkan kuantitas
        $total_harga = $detail['harga_produk'] * $detail['kuantitas'];
        $pdf->Cell(50, 10, "Rp " . number_format($total_harga, 0, ',', '.'), 1); 
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        // Tambahkan biaya pengiriman ke total
        $totalBiayaPengiriman += $detail['biaya_pengiriman'];
    }
    
    // Baris kosong sebagai pemisah antara tabel produk dan total harga
    $pdf->Ln(0);
    
    // Tampilkan total biaya pengiriman
    $pdf->Cell(130, 10, 'Total Biaya Pengiriman', 1);
    $pdf->Cell(50, 10, "Rp " . number_format($totalBiayaPengiriman, 0, ',', '.'), 1);
    $pdf->Ln();
    $pdf->Cell(130, 10, 'Diskon yang Digunakan', 1);
    $pdf->Cell(50, 10, "Rp -" . number_format($order['nominal_diskon'], 0, ',', '.'), 1); 
    $pdf->Ln();


    // Tampilkan total harga keseluruhan
    $pdf->Cell(130, 10, 'Total Harga Keseluruhan', 1);
    $pdf->Cell(50, 10, "Rp " . number_format($order['harga_total'], 0, ',', '.'), 1); 
    $pdf->Ln();
    
    // Tampilkan catatan, jika ada
    $pdf->Cell(0, 10, 'Catatan: ' . $order['catatan_pesanan'], 0, 1);
    
    // Output PDF
    $pdf->Output('D', 'invoice_' . $order_ID . '.pdf');
    exit();
    
} else {
    die("Order tidak ditemukan.");
}
?>
