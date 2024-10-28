<?php
session_start();
include '../admin/konfig.php';
require('../customer/libs/fpdf.php'); // Pastikan path ke FPDF benar

// Periksa koneksi database
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

$month = isset($_POST['month']) ? intval($_POST['month']) : '';
$year = isset($_POST['year']) ? intval($_POST['year']) : '';

$whereClauses = ["o.status_pesanan = 'pesanan selesai'"];
if ($month) {
    $whereClauses[] = "MONTH(o.waktu_pesanan_dibuat) = $month";
}
if ($year) {
    $whereClauses[] = "YEAR(o.waktu_pesanan_diperbarui) = $year";
}
$whereSql = implode(' AND ', $whereClauses);

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

if (!$result_laporan) {
    die("Query error: " . mysqli_error($koneksi));
}

// Buat PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->Image('polosan_laporan.png', 0,0, 297, 210 );
$pdf->SetFont('Arial', 'B', 7);
$pdf->Ln(12); // Line break
$date_generated = date('d-m-Y H:i:');
$pdf->Cell(0, 10, 'Tanggal: ' . $date_generated, 0, 1, 'R');
$pdf->Ln(5); // Line break

$pdf->Cell(15, 10, 'Order ID', 1, 0, 'C');
$pdf->Cell(55, 10, 'Nama Produk', 1, 0, 'C');
$pdf->Cell(30, 10, 'Nama Customer', 1, 0, 'C');
$pdf->Cell(10, 10, 'Qty', 1, 0, 'C');
$pdf->Cell(20, 10, 'Harga', 1, 0, 'C');
$pdf->Cell(20, 10, 'Harga Total', 1, 0, 'C');
$pdf->Cell(20, 10, 'Biaya Kirim', 1, 0, 'C');
$pdf->Cell(20, 10, 'Diskon', 1, 0, 'C');
$pdf->Cell(25, 10, 'Harga Terbayar', 1, 0, 'C');
$pdf->Cell(30, 10, 'Tanggal Order', 1, 0, 'C');
$pdf->Cell(30, 10, 'Waktu Sampai', 1, 0, 'C');
$pdf->Ln();

// Insert datanya ke PDF
if (mysqli_num_rows($result_laporan) > 0) {
    while ($row = mysqli_fetch_assoc($result_laporan)) {
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(15, 10, $row['order_ID'], 1);
        $pdf->Cell(55, 10, $row['nama_produk'], 1);
        $pdf->Cell(30, 10, $row['nama_customer'], 1);
        $pdf->Cell(10, 10, $row['kuantitas'], 1);
        $pdf->Cell(20, 10, number_format($row['harga_produk'], 0, ',', '.'), 1);
        $pdf->Cell(20, 10, number_format($row['harga_total_produk'], 0, ',', '.'), 1);
        $pdf->Cell(20, 10, number_format($row['biaya_pengiriman'], 0, ',', '.'), 1);
        $pdf->Cell(20, 10, number_format($row['nominal_diskon'], 0, ',', '.'), 1);
        $pdf->Cell(25, 10, number_format($row['harga_total'], 0, ',', '.'), 1);
        $pdf->Cell(30, 10, $row['waktu_pesanan_dibuat'], 1);
        $pdf->Cell(30, 10, $row['waktu_sampai'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada laporan yang tersedia.', 1, 1, 'C');
}

// Output PDF
$filename = 'laporan_penjualan(' . date('d-m-Y') . ').pdf';

// Output PDF langsung diunduh
$pdf->Output('D', $filename);

?>
