-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Phiên bản:           12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for btapweb
CREATE DATABASE IF NOT EXISTS `btapweb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `btapweb`;

-- Dumping structure for table btapweb.bienthesp
CREATE TABLE IF NOT EXISTS `bienthesp` (
  `bienthe_id` int NOT NULL AUTO_INCREMENT,
  `sanpham_id` int DEFAULT NULL,
  `kichthuoc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mausac` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `soluong` int NOT NULL,
  `hinhanh` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`bienthe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.bienthesp: ~14 rows (approximately)
INSERT INTO `bienthesp` (`bienthe_id`, `sanpham_id`, `kichthuoc`, `mausac`, `soluong`, `hinhanh`) VALUES
	(1, 1, 'M', 'Đen', 15, 'aothunnam.jpg'),
	(2, 1, 'L', 'Đen', 20, 'aothunnam.jpg'),
	(3, 1, 'XL', 'Trắng', 17, 'aothunnam.jpg'),
	(4, 2, 'M', 'Trắng', 10, 'aothunnam.jpg'),
	(5, 2, 'L', 'Trắng', 15, 'aothunnam.jpg'),
	(6, 2, 'XL', 'Trắng', 5, 'aothunnam.jpg'),
	(7, 3, '29', 'Xanh đậm', 10, 'aothunnam.jpg'),
	(8, 3, '30', 'Xanh đậm', 15, 'aothunnam.jpg'),
	(9, 3, '31', 'Xanh nhạt', 15, 'aothunnam.jpg'),
	(10, 4, '29', 'Be', 10, 'aothunnam.jpg'),
	(11, 4, '30', 'Xám', 10, 'aothunnam.jpg'),
	(12, 4, '31', 'Đen', 5, 'aothunnam.jpg'),
	(13, 5, 'Free Size', 'Nâu', 32, 'aothunnam.jpg'),
	(14, 5, 'Free Size', 'Đen', 30, 'aothunnam.jpg');

-- Dumping structure for table btapweb.chitietdonhang
CREATE TABLE IF NOT EXISTS `chitietdonhang` (
  `chitiet_id` int NOT NULL AUTO_INCREMENT,
  `donhang_id` int DEFAULT NULL,
  `sanpham_id` int NOT NULL DEFAULT '0',
  `bienthe_id` int NOT NULL DEFAULT '0',
  `soluong` int DEFAULT NULL,
  PRIMARY KEY (`chitiet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.chitietdonhang: ~12 rows (approximately)
INSERT INTO `chitietdonhang` (`chitiet_id`, `donhang_id`, `sanpham_id`, `bienthe_id`, `soluong`) VALUES
	(1, 4, 1, 1, 1),
	(2, 5, 1, 1, 15),
	(3, 6, 1, 1, 1),
	(4, 6, 5, 14, 8),
	(5, 7, 4, 11, 1),
	(6, 7, 1, 1, 1),
	(7, 7, 5, 13, 1),
	(8, 8, 2, 6, 1),
	(9, 9, 2, 4, 1),
	(10, 10, 2, 4, 2),
	(11, 11, 2, 4, 2),
	(12, 12, 2, 4, 1),
	(13, 13, 1, 2, 2),
	(14, 13, 2, 4, 1);

-- Dumping structure for table btapweb.chitietphieunhap
CREATE TABLE IF NOT EXISTS `chitietphieunhap` (
  `ct_id` int NOT NULL AUTO_INCREMENT,
  `phieunhap_id` int NOT NULL,
  `bienthe_id` int NOT NULL,
  `soluong` int NOT NULL,
  `dongia` bigint NOT NULL,
  `thanhtien` bigint GENERATED ALWAYS AS ((`soluong` * `dongia`)) STORED,
  `ghichu` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`ct_id`),
  KEY `phieunhap_id` (`phieunhap_id`),
  KEY `bienthe_id` (`bienthe_id`),
  CONSTRAINT `chitietphieunhap_ibfk_1` FOREIGN KEY (`phieunhap_id`) REFERENCES `phieunhap` (`phieunhap_id`) ON DELETE CASCADE,
  CONSTRAINT `chitietphieunhap_ibfk_2` FOREIGN KEY (`bienthe_id`) REFERENCES `bienthesp` (`bienthe_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.chitietphieunhap: ~0 rows (approximately)
INSERT INTO `chitietphieunhap` (`ct_id`, `phieunhap_id`, `bienthe_id`, `soluong`, `dongia`, `ghichu`) VALUES
	(1, 1, 13, 2, 13000, ''),
	(2, 2, 3, 2, 3000, '');

-- Dumping structure for table btapweb.danhgia
CREATE TABLE IF NOT EXISTS `danhgia` (
  `danhgia_id` int NOT NULL AUTO_INCREMENT,
  `sanpham_id` int DEFAULT '0',
  `user_id` int DEFAULT '0',
  `danhgia` int DEFAULT NULL,
  `tieude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`danhgia_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.danhgia: ~2 rows (approximately)
INSERT INTO `danhgia` (`danhgia_id`, `sanpham_id`, `user_id`, `danhgia`, `tieude`, `mota`) VALUES
	(1, 1, 1, 5, 'áo thun nam báic', 'tốt'),
	(2, 2, 1, 4, 'ụnc', 'tốt');

-- Dumping structure for table btapweb.danhmuc
CREATE TABLE IF NOT EXISTS `danhmuc` (
  `danhmuc_id` int NOT NULL AUTO_INCREMENT,
  `tendanhmuc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mota` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`danhmuc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.danhmuc: ~4 rows (approximately)
INSERT INTO `danhmuc` (`danhmuc_id`, `tendanhmuc`, `slug`, `mota`) VALUES
	(1, 'Áo Nam', 'ao-nam', 'Các sản phẩm áo dành cho nam giới'),
	(2, 'Quần Nam', 'quan-nam', 'Các sản phẩm quần dành cho nam giới'),
	(3, 'Phụ Kiện', 'phu-kien', 'Phụ kiện thời trang'),
	(5, 'áo đông', '-o-ng', 'ấm');

-- Dumping structure for table btapweb.diachigiaohang
CREATE TABLE IF NOT EXISTS `diachigiaohang` (
  `diachi_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `tennguoinhan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sodienthoai` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diachichitiet` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phuong` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tinh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `macdinh` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`diachi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.diachigiaohang: ~3 rows (approximately)
INSERT INTO `diachigiaohang` (`diachi_id`, `user_id`, `tennguoinhan`, `sodienthoai`, `diachichitiet`, `phuong`, `tinh`, `macdinh`) VALUES
	(5, 2, 'Tâm Thành Đặng', '02389907639', '8u', 'sd', 'Ninh Bình', '1'),
	(6, 3, 'Tâm Thành Đặng', '02389907639', '19', 'sd', 'Ninh Bình', '1'),
	(8, 3, 'Tâm Thành Đặng', '0389907639', 'Giao Nhân', 'Giao Hưnng', 'Ninh Bình', '0');

-- Dumping structure for table btapweb.donhang
CREATE TABLE IF NOT EXISTS `donhang` (
  `donhang_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `diachi_id` int DEFAULT NULL,
  `ghichu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lydo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `trangthai` enum('choxacnhan','daxacnhan','dangxuly','danggiao','dagiao','dahuy') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'choxacnhan',
  `phuongthucthanhtoan` enum('tienmat','chuyenkhoan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'tienmat',
  `trangthaithanhtoan` enum('chuathanhtoan','dathanhtoan','hoantien') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'chuathanhtoan',
  `tongtienhang` decimal(15,2) DEFAULT NULL,
  `phivanchuyen` decimal(15,2) DEFAULT NULL,
  `tongthanhtoan` decimal(15,2) unsigned DEFAULT NULL,
  `ngaycapnhat` timestamp NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP,
  `ngaytao` timestamp NULL DEFAULT (now()),
  PRIMARY KEY (`donhang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.donhang: ~9 rows (approximately)
INSERT INTO `donhang` (`donhang_id`, `user_id`, `diachi_id`, `ghichu`, `lydo`, `trangthai`, `phuongthucthanhtoan`, `trangthaithanhtoan`, `tongtienhang`, `phivanchuyen`, `tongthanhtoan`, `ngaycapnhat`, `ngaytao`) VALUES
	(2, 2, 1, '', NULL, 'danggiao', 'tienmat', 'chuathanhtoan', 619000.00, 30000.00, 649000.00, '2026-01-10 10:00:17', '2026-01-08 05:09:16'),
	(3, 2, 2, '', NULL, 'daxacnhan', 'chuyenkhoan', 'chuathanhtoan', 299000.00, 30000.00, 329000.00, '2026-01-10 10:00:32', '2026-01-08 05:10:14'),
	(4, 2, 3, '', NULL, 'dangxuly', 'chuyenkhoan', 'chuathanhtoan', 149000.00, 30000.00, 179000.00, '2026-01-10 10:00:34', '2026-01-08 10:20:48'),
	(5, 2, 4, '', 'Thay đổi ý định mua hàng', 'dahuy', 'tienmat', 'chuathanhtoan', 2235000.00, 30000.00, 2265000.00, '2026-01-16 08:11:58', '2026-01-08 15:27:46'),
	(6, 3, 6, '', 'Thay đổi ý định mua hàng', 'dahuy', 'tienmat', 'chuathanhtoan', 1741000.00, 30000.00, 1771000.00, '2026-01-15 11:52:35', '2026-01-14 16:39:11'),
	(7, 2, 5, '', NULL, 'choxacnhan', 'tienmat', 'chuathanhtoan', 668000.00, 30000.00, 698000.00, '2026-01-16 08:16:35', '2026-01-16 08:16:35'),
	(8, 3, 6, '', 'Thay đổi ý định mua hàng', 'dahuy', 'tienmat', 'chuathanhtoan', 299000.00, 30000.00, 329000.00, '2026-01-16 10:38:20', '2026-01-16 10:30:27'),
	(9, 3, 6, '', NULL, 'choxacnhan', 'tienmat', 'chuathanhtoan', 299000.00, 30000.00, 329000.00, '2026-01-16 10:38:33', '2026-01-16 10:38:33'),
	(10, 3, 6, '', NULL, 'choxacnhan', 'tienmat', 'chuathanhtoan', 598000.00, 30000.00, 628000.00, '2026-01-16 13:02:39', '2026-01-16 13:02:39'),
	(11, 3, 6, '', NULL, 'choxacnhan', 'tienmat', 'chuathanhtoan', 598000.00, 30000.00, 628000.00, '2026-01-16 21:57:45', '2026-01-16 21:57:45'),
	(12, 3, 8, '', NULL, 'choxacnhan', 'tienmat', 'chuathanhtoan', 299000.00, 30000.00, 329000.00, '2026-01-16 21:58:05', '2026-01-16 21:58:05'),
	(13, 3, 6, '', NULL, 'choxacnhan', 'tienmat', 'chuathanhtoan', 597000.00, 30000.00, 627000.00, '2026-01-17 00:11:11', '2026-01-17 00:11:11');

-- Dumping structure for table btapweb.giohang
CREATE TABLE IF NOT EXISTS `giohang` (
  `giohang_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `sanpham_id` int NOT NULL DEFAULT '0',
  `bienthe_id` int NOT NULL DEFAULT '0',
  `soluong` int DEFAULT NULL,
  PRIMARY KEY (`giohang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.giohang: ~0 rows (approximately)

-- Dumping structure for table btapweb.phieunhap
CREATE TABLE IF NOT EXISTS `phieunhap` (
  `phieunhap_id` int NOT NULL AUTO_INCREMENT,
  `ngaynhap` datetime DEFAULT CURRENT_TIMESTAMP,
  `tongtien` bigint DEFAULT '0',
  `ghichu` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`phieunhap_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.phieunhap: ~0 rows (approximately)
INSERT INTO `phieunhap` (`phieunhap_id`, `ngaynhap`, `tongtien`, `ghichu`) VALUES
	(1, '2026-01-14 10:44:14', 26000, ''),
	(2, '2026-01-17 07:49:07', 6000, '');

-- Dumping structure for table btapweb.sanpham
CREATE TABLE IF NOT EXISTS `sanpham` (
  `sanpham_id` int NOT NULL AUTO_INCREMENT,
  `danhmuc_id` int DEFAULT NULL,
  `tensanpham` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `giaban` decimal(15,2) NOT NULL,
  `giakhuyenmai` decimal(15,2) DEFAULT NULL,
  `soluong` int NOT NULL,
  `hinhanh` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ngaytao` timestamp NULL DEFAULT (now()),
  `ngaycapnhat` timestamp NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sanpham_id`),
  KEY `FK_sp_danhmuc` (`danhmuc_id`),
  CONSTRAINT `FK_sp_danhmuc` FOREIGN KEY (`danhmuc_id`) REFERENCES `danhmuc` (`danhmuc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.sanpham: ~6 rows (approximately)
INSERT INTO `sanpham` (`sanpham_id`, `danhmuc_id`, `tensanpham`, `slug`, `mota`, `giaban`, `giakhuyenmai`, `soluong`, `hinhanh`, `ngaytao`, `ngaycapnhat`) VALUES
	(1, 1, 'Áo Thun Nam Basic', 'ao-thun-nam-basic', 'Áo thun nam cotton 100% form rộng thoải mái', 199000.00, 149000.00, 50, 'aothunnam.jpg', '2025-12-21 05:15:09', '2025-12-22 10:36:57'),
	(2, 1, 'Áo Sơ Mi Trắng Công Sở', 'ao-so-mi-trang-cong-so', 'Áo sơ mi trắng dài tay phong cách công sở lịch sự', 350000.00, 299000.00, 30, 'ao_so_mi_trang_tay_dai.jpg', '2025-12-21 05:15:09', '2025-12-22 10:37:29'),
	(3, 2, 'Quần Jean Nam Slim Fit', 'quan-jean-nam-slim-fit', 'Quần jean nam dáng ôm vừa phải, chất liệu jean cao cấp', 450000.00, 399000.00, 40, 'quan_jean_nam.png', '2025-12-21 05:15:09', '2025-12-22 10:38:30'),
	(4, 2, 'Quần Kaki Dài Nam', 'quan-kaki-dai-nam', 'Quần kaki dài form straight phù hợp đi làm và dạo phố', 320000.00, NULL, 25, 'quan_haki.jpg', '2025-12-21 05:15:09', '2025-12-22 10:39:27'),
	(5, 3, 'Dây Nịt Nam Da Thật', 'day-nit-nam-da-that', 'Dây nịt nam da bò thật 100% khóa inox cao cấp', 250000.00, 199000.00, 60, 'day_that_lung.webp', '2025-12-21 05:15:09', '2025-12-22 10:38:56'),
	(9, 1, 'Áo Sơ Mi Nam Công Sở', 'ao-so-mi-nam-cong-so', 'Áo sơ mi trắng đẹp dây phong cách công sở lịch sự', 350000.00, 299000.00, 30, 'ao_so_mi_trang_tay_dai.jpg', '2025-12-21 05:15:09', '2025-12-21 05:15:09');

-- Dumping structure for table btapweb.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sex` enum('Nam','Nữ') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ngaysinh` date DEFAULT NULL,
  `email` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `create_at` timestamp NULL DEFAULT (now()),
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.users: ~2 rows (approximately)
INSERT INTO `users` (`user_id`, `fullname`, `sex`, `ngaysinh`, `email`, `phone`, `avatar`, `create_at`, `username`, `password`, `role`, `status`) VALUES
	(2, 'Đặng Thành Tâm', 'Nam', '2005-10-13', 'tamdt.a11k48gtb@gmail.com', '02389907639', 'avatar_2_1768106963.webp', '2025-12-24 15:40:26', 'tamdt1310', '$2y$10$3/eu/Ms9Xt/ghZCcBoFRYOYPqxk871ErAnDd1MvPJ.Vc1lsnniTk6', 'admin', NULL),
	(3, 'Tâm Thành Đặng', 'Nam', '2026-01-01', 'tamdt13102005@gmail.com', '02389907639', 'avatar_3_1768500188.webp', '2026-01-13 15:03:31', 'tamdt131005', '$2y$10$ct9veD40eiVG7zw1D.LLgeRV7r8hM1FcG9cc3l4TimyM41ARRwgCS', 'user', NULL);

-- Dumping structure for table btapweb.yeuthich
CREATE TABLE IF NOT EXISTS `yeuthich` (
  `yeuthich_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `sanpham_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table btapweb.yeuthich: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
