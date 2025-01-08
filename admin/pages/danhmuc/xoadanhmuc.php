<?php
session_start();
require_once '../../../includes/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../pages/dangnhap.php");
    exit();
}

// Xử lý xóa danh mục
$id = $_GET['id'];
$sql = "DELETE FROM danhmuc WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Xóa danh mục thành công";
} else {
    $_SESSION['error_message'] = "Lỗi: " . $stmt->error;
}

header("Location: quanlydanhmuc.php");
exit();