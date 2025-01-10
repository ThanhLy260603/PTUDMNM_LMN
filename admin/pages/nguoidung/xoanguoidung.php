<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../pages/dangnhap.php");
    exit();
}

// Kiểm tra xem có ID người dùng được truyền không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Không có ID người dùng được chọn.";
    header("Location: quanlynguoidung.php");
    exit();
}

$user_id = intval($_GET['id']);

// Ngăn không cho xóa tài khoản admin đang đăng nhập
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['message'] = "Bạn không thể xóa tài khoản của chính mình.";
    header("Location: quanlynguoidung.php");
    exit();
}

try {
    // Bắt đầu transaction
    $conn->begin_transaction();

    // Xóa các bản ghi liên quan trong các bảng khác (nếu có)
    // Ví dụ: xóa đơn hàng
    $delete_orders_query = "DELETE FROM donhang WHERE iduser = ?";
    $stmt_orders = $conn->prepare($delete_orders_query);
    $stmt_orders->bind_param("i", $user_id);
    $stmt_orders->execute();

    // Xóa chi tiết đơn hàng 
    $delete_order_details_query = "DELETE FROM chitietdonhang 
        WHERE iddonhang IN (SELECT id FROM donhang WHERE iduser = ?)";
    $stmt_order_details = $conn->prepare($delete_order_details_query);
    $stmt_order_details->bind_param("i", $user_id);
    $stmt_order_details->execute();

    // Xóa người dùng
    $delete_user_query = "DELETE FROM taikhoan WHERE id = ?";
    $stmt_user = $conn->prepare($delete_user_query);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();

    // Kiểm tra số dòng bị ảnh hưởng
    if ($stmt_user->affected_rows > 0) {
        // Commit transaction
        $conn->commit();
        
        // Đặt thông báo thành công
        $_SESSION['message'] = "Xóa người dùng thành công.";
    } else {
        // Rollback transaction
        $conn->rollback();
        
        // Đặt thông báo lỗi
        $_SESSION['message'] = "Không tìm thấy người dùng để xóa.";
    }
} catch (Exception $e) {
    // Rollback transaction nếu có lỗi
    $conn->rollback();
    
    // Đặt thông báo lỗi
    $_SESSION['message'] = "Lỗi: " . $e->getMessage();
} finally {
    // Chuyển hướng về trang quản lý người dùng
    header("Location: quanlynguoidung.php");
    exit();
}