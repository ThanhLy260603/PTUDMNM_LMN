<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

// Xử lý thanh toán
if (isset($_POST['thanhtoan'])) {
    // Lấy thông tin giỏ hàng
    $cart = $_SESSION['cart'];
    
    // Kiểm tra giỏ hàng không trống
    if (empty($cart)) {
        $_SESSION['thanhtoan_message'] = "Giỏ hàng của bạn đang trống.";
        header("Location: giohang.php");
        exit();
    }
    
    // Tính tổng tiền
    $total_price = 0;
    foreach ($cart as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
    
    // Lấy thông tin khách hàng
    $customer_id = $_SESSION['user_id'];
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    
    // Tạo đơn hàng mới
    $query = "INSERT INTO donhang (iduser, tongtien, trangthai, ngaymua, diachi) VALUES (?, ?, 'Đang xử lý', NOW(), ?)";
    $stmt = $conn->prepare($query);

    // Kiểm tra lỗi khi chuẩn bị câu lệnh SQL
    if (!$stmt) {
        die("Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error);
    }

    // Bind các tham số
    $stmt->bind_param("iis", $customer_id, $total_price, $customer_address);

    // Thực thi câu lệnh
    if (!$stmt->execute()) {
        die("Lỗi khi thực thi câu lệnh SQL: " . $stmt->error);
    }

    // Lấy ID đơn hàng mới
    $order_id = $stmt->insert_id;
    
    // Thêm sản phẩm vào đơn hàng
    foreach ($cart as $item) {
        $query = "INSERT INTO chitietdonhang (iddonhang, idsp, soluong) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Kiểm tra lỗi khi chuẩn bị câu lệnh SQL
        if (!$stmt) {
            die("Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error);
        }

        // Bind các tham số
        $stmt->bind_param("iii", $order_id, $item['id'], $item['quantity']);

        // Thực thi câu lệnh
        if (!$stmt->execute()) {
            die("Lỗi khi thực thi câu lệnh SQL: " . $stmt->error);
        }
    }
    
    // Xóa giỏ hàng
    unset($_SESSION['cart']);
    
    // Thông báo thành công và chuyển hướng đến trang lịch sử đơn hàng
    $_SESSION['thanhtoan_message'] = "Đơn hàng của bạn đã được đặt thành công.";
    header("Location: lichsudonhang.php");
    exit();
}
// Xử lý mua trực tiếp
if (isset($_GET['direct_buy'])) {
    $product_id = intval($_GET['direct_buy']);
    
    // Truy vấn thông tin sản phẩm
    $query = "SELECT * FROM sanpham WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Reset giỏ hàng
        $_SESSION['cart'] = [];
        
        // Thêm sản phẩm vào giỏ hàng
        $_SESSION['cart'][] = [
            'id' => $product['id'],
            'name' => $product['tensp'],
            'price' => $product['giasp'],
            'quantity' => 1,
            'max_quantity' => $product['soluong']
        ];
    } else {
        $_SESSION['cart_message'] = "Sản phẩm không tồn tại.";
        header("Location: /webbancaphe/customer/index.php");
        exit();
    }
}
// Tính tổng tiền
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/header.php'; ?>

    <div class="container mt-5">
        <!-- Hiển thị thông báo -->
        <?php 
        if (isset($_SESSION['thanhtoan_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . 
                 htmlspecialchars($_SESSION['thanhtoan_message']) . 
                 '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['thanhtoan_message']);
        }
        ?>

        <h2 class="text-center mb-4">Thanh Toán</h2>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <h4>Thông Tin Khách Hàng</h4>
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Tên Khách Hàng:</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">Số Điện Thoại:</label>
                        <input type="text" class="form-control" id="customer_phone" name="customer_phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">Địa Chỉ:</label>
                        <input type="text" class="form-control" id="customer_address" name="customer_address" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4>Tổng Thanh Toán</h4>
                    <p>Tổng Tiền: 
                        <strong><?= number_format($total_price, 0, ',', '.') ?> VNĐ</strong>
                    </p>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" name="thanhtoan" class="btn btn-success">
                    Xác Nhận Thanh Toán
                </button>
            </div>
        </form>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>