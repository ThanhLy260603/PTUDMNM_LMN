<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /webbancaphe/pages/dangnhap.php");
    exit();
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM taikhoan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Lấy số lượng đơn hàng
$order_count_query = "SELECT COUNT(*) as total_orders FROM donhang WHERE iduser = ?";
$stmt = $conn->prepare($order_count_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_count_result = $stmt->get_result();
$order_count = $order_count_result->fetch_assoc()['total_orders'];

// Lấy tổng chi tiêu
$total_spending_query = "SELECT SUM(tongtien) as total_spending FROM donhang WHERE iduser = ?";
$stmt = $conn->prepare($total_spending_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_spending_result = $stmt->get_result();
$total_spending = $total_spending_result->fetch_assoc()['total_spending'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ Sơ Cá Nhân - Coffee House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --coffee-brown: #6F4E37;
            --tea-green: #84A98C;
        }
        .profile-header {
            background: linear-gradient(135deg, var(--coffee-brown), var(--tea-green));
            color: white;
            padding: 50px 0;
            text-align: center;
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
        }
        .profile-stats .card {
            transition: transform 0.3s ease;
        }
        .profile-stats .card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/header.php'; ?>

    <div class="container">
        <!-- Phần Header Hồ Sơ -->
        <div class="profile-header mb-4">
            <img src="<?php echo htmlspecialchars($user['avatar'] ?? '/webbancaphe/assets/images/default-avatar.jpg'); ?>" 
                 alt="Avatar" 
                 class="profile-avatar mb-3">
            <h2><?php echo htmlspecialchars($user['hoten']); ?></h2>
            <p><?php echo htmlspecialchars($user['username']); ?></p>
        </div>

        <!-- Thông Tin Cá Nhân -->
        <div class=" row">
            <div class="col-md-4 profile-stats">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart text-primary fa-2x mb-3"></i>
                        <h5 class="card-title">Số Đơn Hàng</h5>
                        <p class="card-text h3"><?php echo $order_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 profile-stats">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign text-success fa-2x mb-3"></i>
                        <h5 class="card-title">Tổng Chi Tiêu</h5>
                        <p class="card-text h3">
                            <?php echo number_format($total_spending, 0, ',', '.'); ?> VNĐ
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 profile-stats">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-user text-warning fa-2x mb-3"></i>
                        <h5 class="card-title">Thành Viên</h5>
                        <p class="card-text h3">
                            <?php echo date('d/m/Y', strtotime($user['ngaydangky'] ?? 'now')); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông Tin Chi Tiết -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Thông Tin Liên Hệ</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-envelope me-2"></i>Email:</strong>
                        <?php echo htmlspecialchars($user['email'] ?? 'Chưa cập nhật'); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-phone me-2"></i>Số Điện Thoại:</strong>
                        <?php echo htmlspecialchars($user['sdt'] ?? 'Chưa cập nhật'); ?>
                    </div>
                    <div class="col-md-12 mb-3">
                        <strong><i class="fas fa-map-marker-alt me-2"></i>Địa Chỉ:</strong>
                        <?php echo htmlspecialchars($user['diachi'] ?? 'Chưa cập nhật'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nút Hành Động -->
        <div class="text-center">
            <a href="suahoso.php" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Chỉnh Sửa Hồ Sơ
            </a>
            <a href="domatkhau.php" class="btn btn-warning">
                <i class="fas fa-lock me-2"></i>Đổi Mật Khẩu
            </a>
        </div>
    </div>

    <!-- Footer -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>