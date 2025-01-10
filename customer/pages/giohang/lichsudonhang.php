<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

// Lấy thông tin đơn hàng của người dùng
$customer_id = $_SESSION['user_id'];
$query = "SELECT * FROM donhang WHERE iduser = ? ORDER BY ngaymua DESC";
$stmt = $conn->prepare($query);

// Kiểm tra lỗi khi chuẩn bị câu lệnh SQL
if (!$stmt) {
    die("Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error);
}

// Bind tham số
$stmt->bind_param("i", $customer_id);

// Thực thi câu lệnh
if (!$stmt->execute()) {
    die("Lỗi khi thực thi câu lệnh SQL: " . $stmt->error);
}

// Lấy kết quả
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Lịch Sử Đơn Hàng</h2>

        <?php if ($orders->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Đơn Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Mua</th>
                        <th>Địa Chỉ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders-> fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                            <td><?= htmlspecialchars($order['trangthai']) ?></td>
                            <td><?= htmlspecialchars($order['ngaymua']) ?></td>
                            <td><?= htmlspecialchars($order['diachi']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                Bạn chưa có đơn hàng nào.
            </div>
        <?php endif; ?>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>