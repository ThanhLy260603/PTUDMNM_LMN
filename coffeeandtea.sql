-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 04, 2025 lúc 07:10 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `coffeeandtea`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `id` int(11) NOT NULL,
  `iddonhang` int(11) NOT NULL COMMENT 'ID đơn hàng',
  `idsp` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `soluong` int(11) NOT NULL COMMENT 'Số lượng sản phẩm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`id`, `iddonhang`, `idsp`, `soluong`) VALUES
(1, 1, 10, 1),
(2, 2, 13, 2),
(3, 2, 13, 2),
(4, 3, 11, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

CREATE TABLE `danhmuc` (
  `id` int(11) NOT NULL,
  `tendanhmuc` varchar(100) NOT NULL COMMENT 'Tên danh mục sản phẩm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`id`, `tendanhmuc`) VALUES
(1, 'Cà phê rang xay'),
(2, 'Cà phê hạt'),
(3, 'Trà xanh'),
(4, 'Trà đen'),
(5, 'Trà ô long');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `id` int(11) NOT NULL,
  `iduser` int(11) NOT NULL COMMENT 'ID người dùng',
  `tongtien` int(11) NOT NULL COMMENT 'Tổng tiền đơn hàng',
  `trangthai` varchar(50) NOT NULL COMMENT 'Trạng thái đơn hàng',
  `ngaymua` datetime NOT NULL COMMENT 'Ngày đặt hàng',
  `diachi` varchar(255) NOT NULL COMMENT 'Địa chỉ giao hàng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`id`, `iduser`, `tongtien`, `trangthai`, `ngaymua`, `diachi`) VALUES
(1, 2, 135000, 'Đang xử lý', '2024-12-31 16:27:00', 'trà vinh '),
(2, 2, 2116000, 'Đang xử lý', '2024-12-31 16:29:23', 'trà vinh '),
(3, 2, 91000, 'Đang xử lý', '2025-01-04 12:42:23', 'tb');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `id` int(11) NOT NULL,
  `tensp` varchar(255) NOT NULL COMMENT 'Tên sản phẩm',
  `giasp` int(11) NOT NULL COMMENT 'Giá sản phẩm',
  `mota` text DEFAULT NULL COMMENT 'Mô tả chi tiết sản phẩm',
  `hinhanh` varchar(255) NOT NULL COMMENT 'Đường dẫn hình ảnh',
  `soluong` int(11) NOT NULL COMMENT 'Số lượng trong kho',
  `trongluong` float NOT NULL COMMENT 'Khối lượng sản phẩm (kg hoặc gram)',
  `dophaxay` varchar(50) DEFAULT NULL COMMENT 'Độ pha xay (nếu là cà phê)',
  `huongvi` varchar(100) DEFAULT NULL COMMENT 'Hương vị đặc trưng (nếu là trà)',
  `iddanhmuc` int(11) NOT NULL COMMENT 'ID danh mục sản phẩm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`id`, `tensp`, `giasp`, `mota`, `hinhanh`, `soluong`, `trongluong`, `dophaxay`, `huongvi`, `iddanhmuc`) VALUES
(1, 'Cà phê LEGEND', 1119000, 'Đặc điểm: LEGEND là cà phê chồn được sản xuất bằng phương pháp “Lên men sinh học”, sản phẩm chỉ có duy nhất ở Trung Nguyên.', '[\"\\/webbancaphe\\/assets\\/images\\/sanpham\\/677395f969d40_1-5-768x768.jpg\",\"\\/webbancaphe\\/assets\\/images\\/sanpham\\/677395f96c21f_2-5-768x768.jpg\",\"\\/webbancaphe\\/assets\\/images\\/sanpham\\/677395f96da67_3-3-768x768.jpg\"]', 10, 0.5, 'Mịn', 'đắng vừa, thơm, béo', 1),
(10, 'Cà phê Sức Sống ( Nâu)', 135000, 'Ví như sức sống mãnh liệt của đại ngàn, trong bạn luôn tiềm ẩn nguồn năng lượng vô biên. Nhấp một cụm cà phê Trung Nguyên – Nâu, bạn sẽ thấy tràn đầy sinh lực, sẵn sàng để hành động hơn bao giờ hết.\r\n\r\nĐược sáng tạo bởi sự kết hợp 4 loại hạt Arabica, Robusta, Excelsa, Catimor tốt nhất, cũng như công nghệ chế biến cà phê hàng đầu thế giới và bí quyết không thể sao chép, cà phê Trung Nguyên – Nâu mang mùi hương thơm nồng quyến rũ, vị đậm đà đặc trưng sẽ đánh thức những ý tưởng sáng tạo tiềm ẩn và SỨC SỐNG ĐẠI NGÀN còn đang ngủ quên trong bạn.\r\n\r\nPhù hợp với mọi cách uống, thích hợp hơn cho những người có “gu” uống cà phê đậm.', '[\"\\/webbancaphe\\/assets\\/images\\/sanpham\\/67739c511a7b9_1-4-768x768.jpg\",\"\\/webbancaphe\\/assets\\/images\\/sanpham\\/67739c511c255_2-4-768x768.jpg\"]', 10, 0.5, 'Trung Bình', 'Mùi hương thơm nồng quyến rũ. Vị đậm đà đặc trưng.', 1),
(11, 'Cà phê S (Chinh phục)', 91000, 'Con đường CHINH PHỤC THÀNH CÔNG vốn không dễ dàng nhưng với tinh thần chiến binh quả cảm của chính bạn và sự hậu thuẫn vững chắc bằng chất lượng tuyệt hảo của cà phê Trung Nguyên S sẽ tiếp sức và đánh thức những ý tưởng sáng tạo đột phá để thực hiện những giấc mơ. \r\n\r\nVới công thức phối trộn đặc biệt dựa trên sự kết hợp 4 loại hạt Arabica, Robusta, Excelsa, Catimor tốt nhất cũng những công nghệ ché biến cà phê hàng đầuThế giới và bí quyết không thể sao chép, cà phê Trung Nguyên S có màu nước nâu sánh, hương thơm đầy, vị đậm chình là một người bạn đồng hành đích thực trên con đường CHINH PHỤC THÀNH CÔNG.\r\n\r\nThích hợp cho những người có “gu” uống cà phê đậm.', '[\"\\/webbancaphe\\/assets\\/images\\/sanpham\\/67739cc79583d_4-2.jpg\",\"\\/webbancaphe\\/assets\\/images\\/sanpham\\/67739cc7aa8ea_3-2-768x768.jpg\"]', 10, 0.5, 'Thô', 'Cà phê Trung Nguyên S có màu nước nâu sánh, hương thơm đầy, vị đậm đà.', 1),
(13, 'Cà phê Sáng Tạo 8 – 500gr', 529000, 'Với công thức 3S, Tập đoàn Trung Nguyên Legend tạo ra cà phê SÁNG TẠO 8 siêu Sạch, tuyệt ngon dành cho người Sành cà phê, chuyên cho hoạt động Sáng tạo của não. Cà phê SÁNG TẠO 8 của Tập đoàn Trung Nguyên Legend được gọi là “Cà phê của nguyên thủ và ngoại giao” vì sự đặc biệt trong hương vị của nó.', '[\"\\/webbancaphe\\/assets\\/images\\/sanpham\\/6773a270e000e_12-768x768.jpg\"]', 10, 0.5, 'Mịn', 'Hương thơm đầm, thơm rất lâu với hậu vị đậm và êm. Sự cân bằng hoàn hảo giữa hương và vị.', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `id` int(11) NOT NULL,
  `hoten` varchar(100) NOT NULL COMMENT 'Họ tên khách hàng',
  `username` varchar(100) NOT NULL COMMENT 'Tên đăng nhập',
  `password` varchar(255) NOT NULL COMMENT 'Mật khẩu',
  `diachi` varchar(255) NOT NULL COMMENT 'Địa chỉ',
  `sdt` varchar(15) NOT NULL COMMENT 'Số điện thoại',
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `avatar` varchar(255) DEFAULT '/webbancaphe/assets/images/default-avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`id`, `hoten`, `username`, `password`, `diachi`, `sdt`, `role`, `avatar`) VALUES
(2, 'Nguyễn Văn Khách', 'customer', '123456', '123 Đường Cà Phê, Quận Thủ Đức, TP.HCM', '0123456789', 'customer', '/webbancaphe/assets/images/avatars/6774c412a68b5_Screenshot 2023-12-05 085947.png'),
(3, 'Trầm Ngọc Mai', 'admin', '123456', 'trà vinh', '0355782306', 'admin', '/webbancaphe/assets/images/default-avatar.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thongke`
--

CREATE TABLE `thongke` (
  `id` int(11) NOT NULL,
  `thang` int(11) NOT NULL COMMENT 'Tháng thống kê',
  `tongtien` int(11) NOT NULL COMMENT 'Tổng doanh thu',
  `soluongsp` int(11) NOT NULL COMMENT 'Số lượng sản phẩm đã bán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thongke`
--

INSERT INTO `thongke` (`id`, `thang`, `tongtien`, `soluongsp`) VALUES
(1, 11, 2500000, 50),
(2, 12, 3200000, 65);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `iddonhang` (`iddonhang`),
  ADD KEY `idsp` (`idsp`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `iddanhmuc` (`iddanhmuc`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `thongke`
--
ALTER TABLE `thongke`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `thongke`
--
ALTER TABLE `thongke`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`iddonhang`) REFERENCES `donhang` (`id`),
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`idsp`) REFERENCES `sanpham` (`id`);

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`iddanhmuc`) REFERENCES `danhmuc` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
