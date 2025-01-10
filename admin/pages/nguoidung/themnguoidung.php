<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra quyền admin (nếu cần)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dangnhap.php");
    exit();
}

// Xử lý thêm người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ biểu mẫu
    $hoten = $_POST['hoten'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $diachi = $_POST['diachi'];
    $sdt = $_POST['sdt'];

    // Thêm người dùng vào cơ sở dữ liệu
    $query = "INSERT INTO taikhoan (hoten, username, password, diachi, sdt) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Kiểm tra lỗi khi chuẩn bị câu lệnh SQL
    if (!$stmt) {
        die("Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error);
    }

    // Bind các tham số
    $stmt->bind_param("sssss", $hoten, $username, $password, $diachi, $sdt);

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        $_SESSION['message'] = "Thêm người dùng thành công.";
        header("Location: quanlynguoidung.php");
        exit();
    } else {
        $_SESSION['message'] = "Lỗi khi thêm người dùng: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Người Dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/admin/includes/admin_navbar.php'; ?>

        <main class="col-md-10 ms-sm-auto">
    <div class="container mt-5">
        <!-- Hiển thị thông báo -->
        <?php 
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . 
                 htmlspecialchars($_SESSION['message']) . 
                 '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['message']);
        }
        ?>

        <h2 class="text-center mb-4">Thêm Người Dùng</h2>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="hoten" class="form-label">Họ Tên</label>
                <input type="text" class="form-control" id="hoten" name="hoten" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Tên Đăng Nhập</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật Khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="diachi" class="form-label">Địa Chỉ</label>
                <input type="text" class="form-control" id="diachi" name="diachi" required>
            </div>
            <div class="mb-3">
                <label for="sdt" class="form-label">Số Điện Thoại</label>
                <input type="text" class="form-control" id="sdt" name="sdt" required>
            </div>
            <button type="submit" class="btn btn-primary">Thêm Người Dùng</button>
        </form>
    </div>
</main>
</div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>