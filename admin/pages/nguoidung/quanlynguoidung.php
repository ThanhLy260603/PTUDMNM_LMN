<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra quyền admin (nếu cần)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dangnhap.php");
    exit();
}

// Xử lý xóa người dùng
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);

    // Xóa người dùng
    $query = "DELETE FROM taikhoan WHERE id = ?";
    $stmt = $conn->prepare($query);

    // Kiểm tra lỗi khi chuẩn bị câu lệnh SQL
    if (!$stmt) {
        die("Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error);
    }

    // Bind tham số
    $stmt->bind_param("i", $user_id);

    // Thực thi câu lệnh
    if (!$stmt->execute()) {
        die("Lỗi khi thực thi câu lệnh SQL: " . $stmt->error);
    }

    // Thông báo thành công
    $_SESSION['message'] = "Xóa người dùng thành công.";
    header("Location: quanlynguoidung.php");
    exit();
}

// Lấy danh sách người dùng
$query = "SELECT * FROM taikhoan ORDER BY id DESC";
$users = $conn->query($query);

// Kiểm tra lỗi khi truy vấn
if (!$users) {
    die("Lỗi khi truy vấn người dùng: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Người Dùng</title>
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

        <h2 class="text-center mb-4">Quản Lý Người Dùng</h2>

        <!-- Nút Thêm Người Dùng -->
        <div class="mb-3">
            <a href="themnguoidung.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm Người Dùng
            </a>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ Tên</th>
                    <th>Tên Đăng Nhập</th>
                    <th>Địa Chỉ</th>
                    <th>Số Điện Thoại</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['hoten']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['diachi']); ?></td>
                    <td><?php echo htmlspecialchars($user['sdt']); ?></td>
                    <td>
                        <a href="suanguoidung.php?id=<?php echo $user['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="quanlynguoidung.php?delete_user=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                            <i class="fas fa-trash"></i> Xóa
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>