<?php
// Bắt đầu session
session_start();

// Hủy bỏ tất cả các biến session
session_unset();

// Hủy bỏ session
session_destroy();

// Chuyển hướng người dùng đến trang đăng nhập hoặc trang chủ
header("Location: dangnhap.php"); // Thay đổi đường dẫn nếu cần
exit();
?>