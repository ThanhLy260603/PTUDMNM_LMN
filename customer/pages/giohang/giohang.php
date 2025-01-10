<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    // Truy vấn thông tin sản phẩm
    $query = "SELECT * FROM sanpham WHERE id = ? AND soluong > 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Kiểm tra sản phẩm tồn tại và còn hàng
    if ($product) {
        // Chuẩn bị thông tin sản phẩm
        $product_images = json_decode($product['hinhanh'], true);
        $image = !empty($product_images) ? $product_images[0] : '/webbancaphe/assets/images/default-product.jpg';

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $product_found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $product_found = true;
                
                // Kiểm tra số lượng tồn kho
                if ($item['quantity'] < $product['soluong']) {
                    $item['quantity']++;
                    $_SESSION['cart_message'] = "Đã thêm sản phẩm vào giỏ hàng.";
                } else {
                    $_SESSION['cart_message'] = "Số lượng sản phẩm đã đạt giới hạn tồn kho.";
                }
                break;
            }
        }

        // Nếu sản phẩm chưa có trong giỏ
        if (!$product_found) {
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['tensp'],
                'price' => $product['giasp'],
                'image' => $image,
                'quantity' => 1,
                'max_quantity' => $product['soluong']
            ];
            $_SESSION['cart_message'] = "Đã thêm sản phẩm vào giỏ hàng.";
        }

        // Chuyển hướng về trang giỏ hàng
        header("Location: giohang.php");
        exit();
    } else {
        // Sản phẩm không tồn tại hoặc hết hàng
        $_SESSION['cart_message'] = "Sản phẩm không tồn tại hoặc đã hết hàng.";
        header("Location: /webbancaphe/customer/index.php");
        exit();
    }
}

// Xử lý cập nhật số lượng
if (isset($_POST['update_cart'])) {
    $update_error = false;
    
    // Tạo một mảng mới để lưu giỏ hàng đã cập nhật
    $updated_cart = [];
    
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        // Tìm sản phẩm trong giỏ hàng hiện tại
        foreach ($_SESSION['cart'] as $item) {
            if ($item['id'] == $product_id) {
                // Kiểm tra số lượng hợp lệ
                $new_quantity = max(1, intval($quantity));
                $new_quantity = min($new_quantity, $item['max_quantity']);
                
                // Tạo một bản sao của sản phẩm với số lượng mới
                $updated_item = $item;
                $updated_item['quantity'] = $new_quantity;
                
                // Thêm vào giỏ hàng mới
                $updated_cart[] = $updated_item;
                
                // Nếu số lượng bị giới hạn
                if ($new_quantity < intval($quantity)) {
                    $update_error = true;
                }
                
                break;
            }
        }
    }
    
    // Cập nhật lại giỏ hàng
    $_SESSION['cart'] = $updated_cart;
    
    if ($update_error) {
        $_SESSION['cart_message'] = "Một số sản phẩm đã bị giới hạn số lượng do vượt quá tồn kho.";
    } else {
        $_SESSION['cart_message'] = "Giỏ hàng đã được cập nhật.";
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart_message'] = "Đã xóa sản phẩm khỏi giỏ hàng.";
            break;
        }
    }
    
    // Đánh lại index của mảng
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Tính tổng tiền và số lượng sản phẩm
$total_price = 0;
$total_items = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
        $total_items += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/header.php'; ?>

    <div class="container mt-5">
        <!-- Hiển thị thông báo -->
        <?php 
        if (isset($_SESSION['cart_message'])) {
            $message_type = strpos($_SESSION['cart_message'], 'lỗi') !== false ? 'danger' : 'success';
            echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . 
                 htmlspecialchars($_SESSION['cart_message']) . 
                 '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['cart_message']);
        }
        ?>

        <h2 class="text-center mb-4">
            <i class="fas fa-shopping-cart me-2"></i>Giỏ Hàng 
            <?php if($total_items > 0): ?>
                <span class="badge bg-primary"><?= $total_items ?></span>
            <?php endif; ?>
        </h2>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="alert alert-info text-center">
                Giỏ hàng của bạn đang trống. 
                <a href="/webbancaphe/customer/index.php" class="btn btn-primary mt-3">Tiếp tục mua hàng</a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-8">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="card mb-3">
                                <div class="card-body d-flex align-items-center">
                                <img src="<?= htmlspecialchars($item['image']) ?>" 
                                        alt="<?= htmlspecialchars($item['name']) ?>" 
                                        class="cart-item-image">
                                    
                                    <div class="ms-3 flex-grow-1">
                                        <h5><?= htmlspecialchars($item['name']) ?></h5>
                                        <p>
                                            Giá: <?= number_format($item['price'], 0, ',', '.') ?> VNĐ
                                            <br>
                                            Còn lại: <?= $item['max_quantity'] ?> sản phẩm
                                        </p>
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        <input type="number" 
                                               name="quantity[<?= $item['id'] ?>]" 
                                               value="<?= $item['quantity'] ?>" 
                                               min="1" 
                                               max="<?= $item['max_quantity'] ?>" 
                                               class="form-control me-2" 
                                               style="width: 80px;">
                                        
                                        <a href="?remove=<?= $item['id'] ?>" 
                                           class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="text-end">
                            <button type="submit" name="update_cart" class="btn btn-warning">
                                Cập Nhật Giỏ Hàng
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Tổng Thanh Toán</h4>
                            </div>
                            <div class="card-body">
                                <p>Tổng Tiền: 
                                    <strong><?= number_format($total_price, 0, ',', '.') ?> VNĐ</strong>
                                </p>
                                <a href="thanhtoan.php" class="btn btn-success w-100">
                                    Tiến Hành Thanh Toán
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>