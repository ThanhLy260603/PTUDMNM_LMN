<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra quyền admin (nếu cần)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dangnhap.php");
    exit();
}

// Xử lý cập nhật trạng thái đơn hàng
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];

    // Cập nhật trạng thái đơn hàng
    $query = "UPDATE donhang SET trangthai = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    // Kiểm tra lỗi khi chuẩn bị câu lệnh SQL
    if (!$stmt) {
        die("Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error);
    }

    // Bind các tham số
    $stmt->bind_param("si", $new_status, $order_id);

    // Thực thi câu lệnh
    if (!$stmt->execute()) {
        die("Lỗi khi thực thi câu lệnh SQL: " . $stmt->error);
    }

    // Thông báo thành công
    $_SESSION['message'] = "Cập nhật trạng thái đơn hàng thành công.";
    header("Location: quanlydonhang.php");
    exit();
}

// Lấy danh sách đơn hàng
$query = "SELECT d.*, t.hoten 
          FROM donhang d 
          JOIN taikhoan t ON d.iduser = t.id 
          ORDER BY d.ngaymua DESC";
$orders = $conn->query($query);

// Kiểm tra lỗi khi truy vấn
if (!$orders) {
    die("Lỗi khi truy vấn đơn hàng: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đơn Hàng</title>
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

        <h2 class="text-center mb-4">Quản Lý Đơn Hàng</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Đơn Hàng</th>
                    <th>Khách Hàng </th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th>Ngày Mua</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['hoten']); ?></td>
                    <td><?php echo htmlspecialchars($order['tongtien']); ?> VNĐ</td>
                    <td><?php echo htmlspecialchars($order['trangthai']); ?></td>
                    <td><?php echo htmlspecialchars($order['ngaymua']); ?></td>
                    <td>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal<?php echo $order['id']; ?>">
                            Cập Nhật
                        </button>

                        <!-- Modal cập nhật trạng thái -->
                        <div class="modal fade" id="updateStatusModal<?php echo $order['id']; ?>" tabindex="-1" aria-labelledby="updateStatusLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateStatusLabel">Cập Nhật Trạng Thái Đơn Hàng</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="quanlydonhang.php">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <div class="mb-3">
                                                <label for="new_status" class="form-label">Trạng Thái Mới</label>
                                                <select name="new_status" class="form-select" required>
                                                    <option value="Đang xử lý">Đang xử lý</option>
                                                    <option value="Đã xác nhận đơn hàng">Đã xác nhận đơn hàng</option>
                                                    <option value="Đang vận chuyển">Đang vận chuyển</option>
                                                    <option value="Đã giao">Đã giao</option>
                                                    <option value="Đã hủy">Đã hủy</option>
                                                </select>
                                            </div>
                                            <button type="submit" name="update_status" class="btn btn-success">Cập Nhật</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
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