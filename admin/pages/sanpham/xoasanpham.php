<?php
session_start();
require_once '../../../includes/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../pages/dangnhap.php");
    exit();
}

// Lấy ID sản phẩm từ URL
$id = intval($_GET['id']);

// Xử lý xóa sản phẩm
if ($id > 0) {
    $sql = "DELETE FROM sanpham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Xóa sản phẩm thành công!";
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi xóa sản phẩm: " . $stmt->error;
    }
} else {
    $_SESSION['error_message'] = "ID sản phẩm không hợp lệ.";
}

// Chuyển hướng về trang quản lý sản phẩm
header("Location: quanlysanpham.php");
exit();
?>