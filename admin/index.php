<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Chuyển hướng nếu không phải admin
    header("Location: ../pages/dangnhap.php");
    exit();
}

// Lấy thống kê
$stats = [];

// Thống kê số lượng sản phẩm
$product_query = "SELECT COUNT(*) as total_products FROM sanpham";
$product_result = $conn->query($product_query);
$stats['total_products'] = $product_result ? $product_result->fetch_assoc()['total_products'] : 0;

// Thống kê số lượng đơn hàng
$order_query = "SELECT COUNT(*) as total_orders, SUM(tongtien) as total_revenue FROM donhang";
$order_result = $conn->query($order_query);
$order_data = $order_result ? $order_result->fetch_assoc() : ['total_orders' => 0, 'total_revenue' => 0];
$stats['total_orders'] = $order_data['total_orders'];
$stats['total_revenue'] = $order_data['total_revenue'];

// Lấy đơn hàng mới nhất
$recent_orders = null;
try {
    // Đơn hàng mới nhất
    $recent_orders_query = "SELECT d.id, d.tongtien, d.ngaymua, t.hoten 
                            FROM donhang d 
                            JOIN taikhoan t ON d.iduser = t.id 
                            ORDER BY d.ngaymua DESC 
                            LIMIT 5";
    $recent_orders_result = $conn->query($recent_orders_query);

    // Kiểm tra kết quả truy vấn
    if ($recent_orders_result === false) {
        throw new Exception("Lỗi truy vấn: " . $conn->error);
    }
} catch (Exception $e) {
    // Ghi log lỗi hoặc xử lý lỗi
    error_log("Lỗi lấy đơn hàng mới nhất: " . $e->getMessage());
    $recent_orders_result = null;
}

// Thống kê người dùng
$user_query = "SELECT COUNT(*) as total_users FROM taikhoan";
$user_result = $conn->query($user_query);
$stats['total_users'] = $user_result ? $user_result->fetch_assoc()['total_users'] : 0;
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị - Coffee House</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Chart.js for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --coffee-brown: #6F4E37;
            --tea-green: #84A98C;
            --light-bg: #f4f6f9;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Arial', sans-serif;
        }

        .admin-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .stat-icon {
            font-size: 3rem;
            opacity: 0.7;
        }

        .sidebar {
            background-color: var(--coffee-brown);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .sidebar-link {
            color: rgba(255,255,255,0.7);
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .main-content {
    margin-left: 250px;
    transition: margin-left 0.3s ease;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include_once 'includes/admin_navbar.php'; ?>
            <!-- Nội dung chính -->
            <div class="col-md-10 main-content">
                <!-- Thanh tiêu đề -->
                <nav class="navbar bg-white shadow-sm mb-4">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">
                            Xin chào, <?php echo $_SESSION['username']; ?>
                        </span>
                        <div class="d-flex">
                            <a href="#" class="btn btn-outline-coffee me-2">
                                <i class="fas fa-bell"></i>
                            </a>
                            <a href="../pages/dangxuat.php" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Thống kê -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card admin-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-box-open text-primary stat-icon me-3"></i>
                                <div>
                                    <h5 class="card-title">Sản Phẩm</h5>
                                    <p class="card-text"><?php echo $stats['total_products']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card admin-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-shopping-cart text-success stat-icon me-3"></i>
                                <div>
                                    <h5 class="card-title">Đơn Hàng</h5>
                                    <p class="card-text"><?php echo $stats['total_orders']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card admin-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-users text-warning stat-icon me-3"></i>
                                <div>
                                    <h5 class="card-title">Người Dùng</h5>
                                    <p class="card-text"><?php echo $stats['total_users']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card admin-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-dollar-sign text-danger stat-icon me-3"></i>
                                <div>
                                    <h5 class="card-title">Doanh Thu</h5>
                                    <p class="card-text"><?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?> VNĐ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Đơn hàng mới nhất -->
                <h4 class="mb-4">Đơn Hàng Mới Nhất</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người Dùng</th>
                            <th>Tổng Tiền</th>
                            <th>Ngày Tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($recent_orders_result && $recent_orders_result->num_rows > 0): 
                            while ($order = $recent_orders_result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['hoten'] ?? 'Không xác định'); ?></td>
                                <td><?php echo number_format($order['tongtien'] ?? 0, 0, ',', '.'); ?> VNĐ</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['ngaymua'] ?? 'now')); ?></td>
                            </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="4" class="text-center">Không có đơn hàng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>