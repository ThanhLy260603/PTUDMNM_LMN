<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Lấy ID sản phẩm từ URL
$id = intval($_GET['id']);

// Truy vấn thông tin sản phẩm
$product_query = "SELECT sp.*, dm.tendanhmuc 
                  FROM sanpham sp 
                  JOIN danhmuc dm ON sp.iddanhmuc = dm.id 
                  WHERE sp.id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $id);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows === 0) {
    // Nếu không tìm thấy sản phẩm, chuyển hướng về trang sản phẩm
    header("Location: pages/sanpham.php");
    exit();
}

$product = $product_result->fetch_assoc();

// Hàm hiển thị hình ảnh
function displayImage($image_path) {
    // Danh sách các đuôi file ảnh được hỗ trợ
    $supported_extensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 
        'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP'
    ];

    // Xác định đường dẫn hình ảnh
    $image_paths = [
        $image_path,
        '/webbancaphe/assets/images/sanpham/' . basename($image_path),
        '/webbancaphe/assets/images/default-product.jpg'
    ];

    $valid_image_path = null;
    foreach ($image_paths as $path) {
        // Kiểm tra file tồn tại và có đúng định dạng ảnh
        if (!empty($path)) {
            $full_path = $_SERVER['DOCUMENT_ROOT'] . $path;
            
            // Kiểm tra file tồn tại
            if (file_exists($full_path)) {
                // Lấy thông tin file
                $path_info = pathinfo($full_path);
                
                // Kiểm tra đuôi file
                if (in_array($path_info['extension'], $supported_extensions)) {
                    $valid_image_path = $path;
                    break;
                }
            }
        }
    }

    // Nếu không tìm thấy hình ảnh nào, sử dụng ảnh mặc định
    if ($valid_image_path === null) {
        $valid_image_path = '/webbancaphe/assets/images/default-product.jpg';
    }

    return $valid_image_path;
}

// Xử lý hình ảnh
$images = json_decode($product['hinhanh'], true);
if (!is_array($images)) {
    $images = !empty($product['hinhanh']) ? [$product['hinhanh']] : [];
}
if (empty($images)) {
    $images = ['/webbancaphe/assets/images/default-product.jpg'];
}

// Lấy hình ảnh chính
$image_path = displayImage($images[0]);

// Lấy hình ảnh phụ
$thumb_images = array_slice($images, 1);
$thumb_image_paths = [];
foreach ($thumb_images as $image) {
    $thumb_image_paths[] = displayImage($image);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm - <?= htmlspecialchars($product['tensp']) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .product-image {
            max-width: 100%;
            height: auto;
        }
        
        .thumb-image {
            max-width: 100px;
            height: auto;
            margin: 10px;
        }
        
        .carousel-control-prev-icon, .carousel-control-next-icon {
            background-color: #333;
        }

        .price-highlight {
            font-size: 24px;
            font-weight: bold;
            color: #ff9900;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/header.php'; ?>

    <div class="container py-5">
        <h2 class="text-center mb-4"><?= htmlspecialchars($product['tensp']) ?></h2>
        <div class="row">
            <div class="col-md-6">
                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['tensp']) ?>" class="product-image">
                        </div>
                        <?php foreach ($thumb_image_paths as $thumb_image): ?>
                            <div class="carousel-item">
                                <img src="<?= htmlspecialchars($thumb_image) ?>" alt="Hình ảnh phụ" class="product-image">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <h4>Thông Tin Sản Phẩm</h4>
                <p><strong>Giá:</strong> <span class="price-highlight"><?= number_format($product['giasp'], 0, ',', '.') ?> VNĐ</span></p>
                <p><strong>Trọng Lượng:</strong> <?= htmlspecialchars($product['trongluong']) ?> kg</p>
                <p><strong>Độ Pha Xay:</strong> <?= htmlspecialchars($product['dophaxay'] ?? 'Không áp dụng') ?></p>
                <p><strong>Hương Vị:</strong> <?= htmlspecialchars($product['huongvi'] ?? 'Không xác định') ?></p>
                <p><strong>Danh Mục:</strong> <?= htmlspecialchars($product['tendanhmuc']) ?></p>
                <div class="mt-4">
                        <a href="giohang/giohang.php?id=<?= $product['id'] ?>" class="btn btn-tea-green">
                        <i class="fas fa-cart-plus"></i> Thêm vào Giỏ Hàng
                        </a>
                        <a href="giohang/thanhtoan.php?direct_buy=<?= $product['id'] ?>" class="btn btn-danger">
                        <i class="fas fa-money-bill-wave-alt"></i> Mua Ngay
                    </a>
                </div>
            </div>
            <div class="col-md-12 mt-4">
            <h4>Mô Tả Sản Phẩm</h4>
            <div class="border p-4">
                <?= htmlspecialchars($product['mota']) ?>
            </div>
        </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>