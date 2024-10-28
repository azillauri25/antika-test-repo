-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2024 at 07:31 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `antika`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_ID` varchar(10) NOT NULL,
  `nama_customer` varchar(30) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(15) NOT NULL,
  `email_customer` varchar(30) NOT NULL,
  `nomor_telepon_customer` varchar(14) NOT NULL,
  `alamat_customer` text NOT NULL,
  `ttl_customer` date NOT NULL,
  `tanggal_bergabung` timestamp NOT NULL DEFAULT current_timestamp(),
  `nama_kota` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_ID`, `nama_customer`, `username`, `password`, `email_customer`, `nomor_telepon_customer`, `alamat_customer`, `ttl_customer`, `tanggal_bergabung`, `nama_kota`) VALUES
('CUSTOMER07', 'andika nugraha', 'andika', 'andika', 'andika02@gmail.com', '0987455362876', 'Taman Anggrek Ragunan Kav 10, Jl Harsono RM, Ragunan, Pasar Minggu, Jakarta Selatan', '0000-00-00', '2024-10-07 16:16:59', NULL),
('CUSTOMER09', 'Sutejo Warso', 'sutejojo', 'sutejojo', 'Sutejojo@gmail.com', '092645536278', 'JL. Melati IV, Ragunan, Pasar Minggu, Jakarta Selatan, DKI Jakarta', '2002-10-31', '2024-10-07 16:37:57', NULL),
('CUSTOMER13', 'zila', 'zil', 'zil', 'zila@gmail.com', '23039823732', 'pasar minggu', '2024-10-10', '2024-10-13 06:09:26', 'Jakarta Selatan');

--
-- Triggers `customer`
--
DELIMITER $$
CREATE TRIGGER `sebelum_insert_customer` BEFORE INSERT ON `customer` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    SELECT IFNULL(RIGHT(customer_ID, 2), '00') INTO last_id
    FROM customer
    ORDER BY customer_ID DESC
    LIMIT 1;
    
    SET last_id = last_id + 1;
    SET new_id = CONCAT('CUSTOMER', LPAD(last_id, 2, '0'));
    
    SET NEW.customer_ID = new_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `karyawan_ID` varchar(10) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(15) NOT NULL,
  `posisi` enum('admin','finance','direktur utama','manager') DEFAULT NULL,
  `nama_karyawan` varchar(30) NOT NULL,
  `nomor_telepon_karyawan` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`karyawan_ID`, `username`, `password`, `posisi`, `nama_karyawan`, `nomor_telepon_karyawan`) VALUES
('KARYAWAN01', 'dirutantika', 'dirutoke', 'direktur utama', 'Adi K', '081234567890'),
('KARYAWAN02', 'managerantika', 'manageroke', 'manager', 'Eva T', '081234567891'),
('KARYAWAN03', 'adminantika', 'adminadmin123', 'admin', 'Sri M', '081234567892'),
('KARYAWAN04', 'financeantika', 'financeuang', 'finance', 'Tami', '081234567893');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `customer_ID` varchar(10) NOT NULL,
  `produk_ID` varchar(10) NOT NULL,
  `kuantitas` int(3) DEFAULT NULL,
  `harga_total_produk` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `komplain`
--

CREATE TABLE `komplain` (
  `komplain_ID` varchar(10) NOT NULL,
  `order_ID` varchar(10) NOT NULL,
  `customer_ID` varchar(10) NOT NULL,
  `isi_komplain` text NOT NULL,
  `bukti_komplain` varchar(255) DEFAULT NULL,
  `tanggal_komplain` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_komplain` enum('menunggu','komplain selesai') DEFAULT 'menunggu',
  `kontak_yg_dapat_dihubungi` varchar(14) DEFAULT NULL,
  `solusi_komplain` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Triggers `komplain`
--
DELIMITER $$
CREATE TRIGGER `before_insert_komplain` BEFORE INSERT ON `komplain` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    -- Ambil ID terakhir yang sudah ada
    SELECT IFNULL(RIGHT(komplain_ID, 2), '00') INTO last_id
    FROM komplain
    ORDER BY komplain_ID DESC
    LIMIT 1;
    
    -- Buat ID baru dengan format UXX
    SET last_id = last_id + 1;
    SET new_id = CONCAT('KOMP', LPAD(last_id, 2, '0'));
    
    -- Assign ID baru ke kolom customer_ID
    SET NEW.komplain_ID = new_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sebelum_insert_komplain` BEFORE INSERT ON `komplain` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    SELECT IFNULL(RIGHT(komplain_ID, 2), '00') INTO last_id
    FROM komplain
    ORDER BY komplain_ID DESC
    LIMIT 1;
    
    SET last_id = last_id + 1;
    SET new_id = CONCAT('KOMPLAIN', LPAD(last_id, 2, '0'));
    
    SET NEW.komplain_ID = new_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_ID` varchar(10) NOT NULL,
  `customer_ID` varchar(10) NOT NULL,
  `status_pesanan` enum('menunggu validasi pembayaran','pesanan diterima','pesanan dikirim','pesanan ditolak','pesanan selesai') DEFAULT 'menunggu validasi pembayaran',
  `harga_total` decimal(11,2) NOT NULL,
  `nama_penerima` varchar(30) NOT NULL,
  `nama_kota` varchar(30) DEFAULT NULL,
  `waktu_pesanan_dibuat` timestamp NOT NULL DEFAULT current_timestamp(),
  `waktu_pesanan_diperbarui` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `waktu_pengiriman` datetime DEFAULT NULL,
  `waktu_sampai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `before_insert_orders` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    -- Ambil ID terakhir yang sudah ada
    SELECT IFNULL(RIGHT(order_ID, 2), '00') INTO last_id
    FROM orders
    ORDER BY order_ID DESC
    LIMIT 1;
    
    -- Buat ID baru dengan format UXX
    SET last_id = last_id + 1;
    SET new_id = CONCAT('ORANT', LPAD(last_id, 2, '0'));
    
    -- Assign ID baru ke kolom orders_ID
    SET NEW.order_ID = new_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sebelum_insert_order` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    SELECT IFNULL(RIGHT(order_ID, 2), '00') INTO last_id
    FROM orders
    ORDER BY order_ID DESC
    LIMIT 1;
    
    SET last_id = last_id + 1;
    SET new_id = CONCAT('ORDER', LPAD(last_id, 2, '0'));
    
    SET NEW.order_ID = new_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_ID` varchar(10) NOT NULL,
  `produk_ID` varchar(10) DEFAULT NULL,
  `kuantitas` int(3) NOT NULL,
  `harga_produk` decimal(11,2) NOT NULL,
  `biaya_pengiriman` decimal(11,2) NOT NULL,
  `harga_total_produk` decimal(11,2) DEFAULT NULL,
  `nomor_telepon_penerima` varchar(14) DEFAULT NULL,
  `alamat_penerima` text DEFAULT NULL,
  `catatan_pesanan` text DEFAULT NULL,
  `promo_id` varchar(10) NOT NULL DEFAULT 'NONE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `pembayaran_ID` varchar(10) NOT NULL,
  `order_ID` varchar(10) DEFAULT NULL,
  `customer_ID` varchar(10) DEFAULT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`pembayaran_ID`, `order_ID`, `customer_ID`, `bukti_bayar`) VALUES
('BAYAR01', 'ORDER05', 'CUSTOMER13', 'uploads/payment/1728981195.png'),
('BAYAR02', 'ORDER10', 'CUSTOMER13', 'uploads/payment/1729037437.png'),
('BAYAR03', 'ORDER11', 'CUSTOMER13', 'uploads/payment/1729042360.png'),
('BAYAR04', 'ORDER12', 'CUSTOMER13', 'uploads/payment/1729042823.png'),
('BAYAR05', 'ORDER13', 'CUSTOMER13', 'uploads/payment/1729043018.png'),
('BAYAR06', 'ORDER14', 'CUSTOMER13', 'uploads/payment/1729043808.png'),
('BAYAR07', 'ORDER15', 'CUSTOMER13', 'uploads/payment/1729072402.png'),
('BAYAR08', 'ORDER16', 'CUSTOMER13', 'uploads/payment/1729141877.png');

--
-- Triggers `pembayaran`
--
DELIMITER $$
CREATE TRIGGER `sebelum_insert_bayar` BEFORE INSERT ON `pembayaran` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    -- Ambil ID terakhir yang sudah ada
    SELECT IFNULL(RIGHT(pembayaran_ID, 2), '00') INTO last_id
    FROM pembayaran
    ORDER BY pembayaran_ID DESC
    LIMIT 1;
    
    -- Buat ID baru dengan format UXX
    SET last_id = last_id + 1;
    SET new_id = CONCAT('BAYAR', LPAD(last_id, 2, '0'));
    
    -- Assign ID baru ke kolom customer_ID
    SET NEW.pembayaran_ID = new_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `nama_kota` varchar(30) NOT NULL,
  `biaya_pengiriman` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`nama_kota`, `biaya_pengiriman`) VALUES
('Bekasi', '175000.00'),
('Bogor Kota', '175000.00'),
('Depok', '150000.00'),
('Jakarta Barat', '150000.00'),
('Jakarta Pusat', '125000.00'),
('Jakarta Selatan', '80000.00'),
('Jakarta Timur', '150000.00'),
('Jakarta Utara', '175000.00'),
('Lainnya', '0.00'),
('Tangerang', '175000.00'),
('Tangerang Selatan', '150000.00');

-- --------------------------------------------------------

--
-- Table structure for table `perubahan_produk`
--

CREATE TABLE `perubahan_produk` (
  `perubahan_ID` varchar(10) NOT NULL,
  `produk_ID` varchar(10) DEFAULT NULL,
  `nama_produk` varchar(50) DEFAULT NULL,
  `deskripsi_produk` text DEFAULT NULL,
  `stok_produk` int(3) DEFAULT NULL,
  `harga_produk` decimal(11,2) DEFAULT NULL,
  `gambar_produk` varchar(255) DEFAULT NULL,
  `request_ubah_produk` enum('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  `tanggal_permintaan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Triggers `perubahan_produk`
--
DELIMITER $$
CREATE TRIGGER `sebelum_insert_per` BEFORE INSERT ON `perubahan_produk` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    -- Ambil ID terakhir yang sudah ada
    SELECT IFNULL(RIGHT(perubahan_ID, 2), '00') INTO last_id
    FROM perubahan_produk
    ORDER BY perubahan_ID DESC
    LIMIT 1;
    
    -- Buat ID baru dengan format UXX
    SET last_id = last_id + 1;
    SET new_id = CONCAT('PERUBAHAN', LPAD(last_id, 2, '0'));
    
    -- Assign ID baru ke kolom customer_ID
    SET NEW.perubahan_ID = new_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sebelum_insert_perubahan` BEFORE INSERT ON `perubahan_produk` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    -- Ambil ID terakhir yang sudah ada
    SELECT IFNULL(RIGHT(perubahan_ID, 2), '00') INTO last_id
    FROM perubahan_produk
    ORDER BY perubahan_ID DESC
    LIMIT 1;
    
    -- Buat ID baru dengan format UXX
    SET last_id = last_id + 1;
    SET new_id = CONCAT('RUBAH', LPAD(last_id, 2, '0'));
    
    -- Assign ID baru ke kolom customer_ID
    SET NEW.perubahan_ID = new_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `produk_ID` varchar(10) NOT NULL,
  `nama_produk` varchar(50) NOT NULL,
  `deskripsi_produk` text NOT NULL,
  `stok_produk` int(3) NOT NULL,
  `harga_produk` decimal(11,2) NOT NULL,
  `gambar_produk` varchar(255) NOT NULL,
  `request_tambah_produk` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `request_hapus_produk` enum('menunggu','disetujui','ditolak') DEFAULT NULL,
  `request_ubah_produk` enum('menunggu','disetujui','ditolak') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`produk_ID`, `nama_produk`, `deskripsi_produk`, `stok_produk`, `harga_produk`, `gambar_produk`, `request_tambah_produk`, `request_hapus_produk`, `request_ubah_produk`) VALUES
('PRODUK11', 'Rangkaian Anggrek Bulan Putih Spesial Ramadhan Isi', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 28, '625000.00', 'uploads/ANTIKA - Feeds IG (62).png', 'disetujui', NULL, NULL),
('PRODUK12', 'Rangkaian Anggrek Bulan Putih LIdah Ungu Isi 10', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 210, '2150000.00', 'uploads/ANTIKA - Feeds IG (26).png', 'disetujui', NULL, NULL),
('PRODUK13', 'Rangkaian Anggrek Bulan Kuning LIdah Ungu Isi 6', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 53, '1275000.00', 'uploads/ANTIKA - Feeds IG (12).png', 'disetujui', NULL, NULL),
('PRODUK14', 'Rangkaian Anggrek Bulan Putih Isi 3', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 1000, '650000.00', 'uploads/ANTIKA - Feeds IG (10).png', 'disetujui', NULL, NULL),
('PRODUK15', 'Rangkaian Anggrek Bulan Peach Isi 3', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 234, '650000.00', 'uploads/ANTIKA - Feeds IG (9).png', 'disetujui', NULL, NULL),
('PRODUK16', 'Rangkaian Anggrek Bulan Dalmantion Isi 3', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 45, '650000.00', 'uploads/ANTIKA - Feeds IG (7).png', 'disetujui', NULL, NULL),
('PRODUK17', 'Rangkaian Anggrek Bulan Dots Isi 10', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 67, '2150000.00', 'uploads/Premium 7.png', 'disetujui', NULL, NULL),
('PRODUK18', 'Rangkaian Anggrek Bulan Stripe Isi 5', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 434, '1025000.00', 'uploads/super 5.png', 'disetujui', NULL, NULL),
('PRODUK19', 'Rangkaian Anggrek Bulan Mini Isi 4 Dua Spike', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 324, '545000.00', 'uploads/mini.png', 'disetujui', NULL, 'disetujui'),
('PRODUK20', 'Rangkaian Anggrek Bulan Orange Isi 1', '<html>\r\n<body>\r\n  <p>Rangkaian Anggrek Bulan Premium</p>\r\n  <p>Setiap pemesanan rangkaian anggrek bulan akan termasuk:</p>\r\n  <ul>\r\n    <li>Anggrek bulan premium dengan panjang kuntum 8-10</li>\r\n    <li>Pot glasur elegan</li>\r\n    <li>Pita Ucapan (warna bisa request)</li>\r\n    <li>Kartu Ucapan (untuk ucapan taruh di notes</li>\r\n  </ul>\r\n</body>\r\n</html>', 235, '250000.00', 'uploads/super 1.png', 'disetujui', NULL, NULL);

--
-- Triggers `produk`
--
DELIMITER $$
CREATE TRIGGER `sebelum_insert_produk` BEFORE INSERT ON `produk` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    SELECT IFNULL(RIGHT(produk_ID, 2), '00') INTO last_id
    FROM produk
    ORDER BY produk_ID DESC
    LIMIT 1;
    
    SET last_id = last_id + 1;
    SET new_id = CONCAT('PRODUK', LPAD(last_id, 2, '0'));
    
    SET NEW.produk_ID = new_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `promo_ID` varchar(10) NOT NULL,
  `nama_promo` varchar(20) NOT NULL,
  `nominal_diskon` decimal(11,2) NOT NULL,
  `deskripsi_promo` text NOT NULL,
  `tanggal_mulai_promo` date NOT NULL,
  `tanggal_berakhir_promo` date NOT NULL,
  `request_tambah_promo` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `status_promo` enum('aktif','nonaktif') DEFAULT 'aktif',
  `request_nonaktif_promo` enum('menunggu','nonaktifkan','ditolak') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Triggers `promo`
--
DELIMITER $$
CREATE TRIGGER `before_insert_promo` BEFORE INSERT ON `promo` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    -- Ambil ID terakhir yang sudah ada
    SELECT IFNULL(RIGHT(promo_id, 2), '00') INTO last_id
    FROM promo
    ORDER BY promo_id DESC
    LIMIT 1;
    
    -- Buat ID baru dengan format UXX
    SET last_id = last_id + 1;
    SET new_id = CONCAT('PROMO', LPAD(last_id, 2, '0'));
    
    -- Assign ID baru ke kolom customer_ID
    SET NEW.promo_id = new_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sebelum_insert_promo` BEFORE INSERT ON `promo` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    SELECT IFNULL(RIGHT(promo_ID, 2), '00') INTO last_id
    FROM promo
    ORDER BY promo_ID DESC
    LIMIT 1;
    
    SET last_id = last_id + 1;
    SET new_id = CONCAT('PROMO', LPAD(last_id, 2, '0'));
    
    SET NEW.promo_ID = new_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `ulasan_ID` varchar(10) NOT NULL,
  `order_ID` varchar(10) NOT NULL,
  `customer_ID` varchar(10) NOT NULL,
  `isi_ulasan` text NOT NULL,
  `penilaian` int(1) DEFAULT NULL,
  `tanggal_ulasan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Triggers `ulasan`
--
DELIMITER $$
CREATE TRIGGER `sebelum_insert_ulasan` BEFORE INSERT ON `ulasan` FOR EACH ROW BEGIN
    DECLARE last_id INT DEFAULT 0;
    DECLARE new_id VARCHAR(10);
    
    SELECT IFNULL(RIGHT(ulasan_ID, 2), '00') INTO last_id
    FROM ulasan
    ORDER BY ulasan_ID DESC
    LIMIT 1;
    
    SET last_id = last_id + 1;
    SET new_id = CONCAT('ULASAN', LPAD(last_id, 2, '0'));
    
    SET NEW.ulasan_ID = new_id;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_ID`),
  ADD KEY `nama_kota` (`nama_kota`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`karyawan_ID`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`customer_ID`,`produk_ID`),
  ADD KEY `cart_ibfk_2` (`produk_ID`);

--
-- Indexes for table `komplain`
--
ALTER TABLE `komplain`
  ADD PRIMARY KEY (`komplain_ID`),
  ADD KEY `complaint_ibfk_1` (`order_ID`),
  ADD KEY `complaint_ibfk_2` (`customer_ID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_ID`),
  ADD KEY `customer_ID` (`customer_ID`),
  ADD KEY `nama_kota` (`nama_kota`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD KEY `order_details_ibfk_1` (`order_ID`),
  ADD KEY `order_details_ibfk_2` (`produk_ID`),
  ADD KEY `promo_id` (`promo_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`pembayaran_ID`),
  ADD KEY `pembayaran_ibfk_1` (`order_ID`),
  ADD KEY `pembayaran_ibfk_2` (`customer_ID`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`nama_kota`);

--
-- Indexes for table `perubahan_produk`
--
ALTER TABLE `perubahan_produk`
  ADD PRIMARY KEY (`perubahan_ID`),
  ADD KEY `perubahan_produk_ibfk_1` (`produk_ID`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`produk_ID`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`promo_ID`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`ulasan_ID`),
  ADD KEY `review_ibfk_1` (`order_ID`),
  ADD KEY `review_ibfk_2` (`customer_ID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`customer_ID`) REFERENCES `customer` (`customer_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`produk_ID`) REFERENCES `produk` (`produk_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `komplain`
--
ALTER TABLE `komplain`
  ADD CONSTRAINT `komplain_ibfk_1` FOREIGN KEY (`order_ID`) REFERENCES `orders` (`order_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `komplain_ibfk_2` FOREIGN KEY (`customer_ID`) REFERENCES `customer` (`customer_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_ID`) REFERENCES `orders` (`order_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`produk_ID`) REFERENCES `produk` (`produk_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `perubahan_produk`
--
ALTER TABLE `perubahan_produk`
  ADD CONSTRAINT `perubahan_produk_ibfk_1` FOREIGN KEY (`produk_ID`) REFERENCES `produk` (`produk_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
