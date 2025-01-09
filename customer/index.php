<?php
session_start();

// Xác định đúng đường dẫn config
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user_id']);

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

// Lấy trang hiện tại
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12; // Số sản phẩm trên mỗi trang
$offset = ($page - 1) * $limit;

// Lấy danh mục sản phẩm
$categories_query = "SELECT * FROM danhmuc LIMIT 5";
$categories_result = $conn->query($categories_query);

// Lấy danh mục sản phẩm hiện tại
$category_id = isset($_GET['danhmuc']) ? intval($_GET['danhmuc']) : null;
// Lấy sản phẩm nổi bật với phân trang
if ($category_id) {
    $featured_products_query = "
        SELECT sp.*, dm.tendanhmuc AS category_name 
        FROM sanpham sp
        JOIN danhmuc dm ON sp.iddanhmuc = dm.id
        WHERE sp.iddanhmuc = ?
        LIMIT ?, ?
    ";
    $stmt = $conn->prepare($featured_products_query);
    $stmt->bind_param("iii", $category_id, $offset, $limit);
} else {
    $featured_products_query = "
        SELECT sp.*, dm.tendanhmuc AS category_name 
        FROM sanpham sp
        JOIN danhmuc dm ON sp.iddanhmuc = dm.id
        LIMIT ?, ?
    ";
    $stmt = $conn->prepare($featured_products_query);
    $stmt->bind_param("ii", $offset, $limit);
}
$stmt->execute();
$featured_products_result = $stmt->get_result();

// Đếm tổng số sản phẩm
if ($category_id) {
    $total_products_query = "SELECT COUNT(*) as total FROM sanpham WHERE iddanhmuc = ?";
    $stmt = $conn->prepare($total_products_query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_products = $total_result->fetch_assoc()['total'];
} else {
    $total_products_query = "SELECT COUNT(*) as total FROM sanpham";
    $total_result = $conn->query($total_products_query);
    $total_products = $total_result->fetch_assoc()['total'];
}
$total_pages = ceil($total_products / $limit);

// Lấy thông tin người dùng nếu đã đăng nhập
$userInfo = null;
if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $user_query = "SELECT * FROM taikhoan WHERE id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userInfo = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee House - Trang Khách Hàng</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --coffee-brown: #6F4E37;
            --tea-green: #84A98C;
            --coffee-light: #D2B48C;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('../assets/images/banner1.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 150px 0;
            text-align: center;
        }

        .product-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .category-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">
                <?php echo $isLoggedIn ? "Xin chào, " . htmlspecialchars($userInfo['hoten']) : "Khám Phá Thế Giới Cà Phê"; ?>
            </h1>
            <p class="lead mb-5">Những Hạt Cà Phê Tuyệt Vời Từ Khắp Nơi Trên Thế Giới</p>
            
        </div>
    </div>

   <!-- Danh Mục Sản Phẩm -->
<div class="container py-5">
    <h2 class="text-center mb-5">Danh Mục Sản Phẩm</h2>
    <div class="row">
        <?php 
        // Kiểm tra và lặp danh mục
        if ($categories_result && $categories_result->num_rows > 0):
            while($category = $categories_result->fetch_assoc()): 
        ?>
            <div class="col-md-2 mb-4">
                <div class="category-card">
                    <h5><?php echo htmlspecialchars($category['tendanhmuc']); ?></h5>
                    <a href="?danhmuc=<?php echo $category['id']; ?>" 
                       class="btn btn-outline-coffee btn-sm">
                        Xem Chi Tiết
                    </a>
                </div>
            </div>
        <?php 
            endwhile; 
        else:
            echo "<p class='text-center'>Không có danh mục sản phẩm</p>";
        endif; 
        ?>
    </div>
</div>

    <!-- Sản Phẩm Nổi Bật -->
<div class="container py-5 bg-light">
    <h2 class="text-center mb-5">Sản Phẩm Nổi Bật</h2>
    <div class="row">
        <?php 
        // Kiểm tra và lặp sản phẩm
        if ($featured_products_result && $featured_products_result->num_rows > 0):
            while($product = $featured_products_result->fetch_assoc()): 
                // Xử lý hình ảnh
                $images = json_decode($product['hinhanh'], true);
                
                // Nếu không phải JSON hoặc rỗng, chuyển thành mảng
                if (!is_array($images)) {
                    $images = !empty($product['hinhanh']) ? [$product['hinhanh']] : [];
                }

                // Nếu không có ảnh, sử dụng ảnh mặc định
                if (empty($images)) {
                    $images = ['/webbancaphe/assets/images/default-product.jpg'];
                }
                // Lấy ảnh đầu tiên
                $first_image = $images[0];
                $image_path = displayImage($first_image);
        ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card">
                    <img src="<?= htmlspecialchars($image_path) ?>" 
                        class="card-img-top" 
                        alt="<?= htmlspecialchars($product['tensp']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['tensp']); ?></h5>
                        <p class="card-text"><?= number_format($product['giasp'], 0, ',', '.') . ' VNĐ'; ?></p>
                        <a href="pages/chitiet.php?id=<?= $product['id']; ?>" class="btn btn-coffee">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
        <?php 
            endwhile; 
        else:
            echo "<p class='text-center'>Không có sản phẩm nổi bật</p>";
        endif; 
        ?>
    </div>
</div>

<!-- Phân Trang -->
<div class="container py-3">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&danhmuc=<?= $category_id ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>