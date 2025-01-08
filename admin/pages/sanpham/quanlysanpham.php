<?php
session_start();
require_once '../../../includes/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../pages/dangnhap.php");
    exit();
}

// Hàm hiển thị hình ảnh (đã được cập nhật)
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

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Truy vấn danh mục cho bộ lọc
$categories_query = "SELECT * FROM danhmuc";
$categories_result = $conn->query($categories_query);

// Kiểm tra lỗi truy vấn danh mục
if ($categories_result === false) {
    die("Lỗi truy vấn danh mục: " . $conn->error);
}

// Xử lý phân trang
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Truy vấn sản phẩm với điều kiện lọc
$query = "SELECT sp.*, dm.tendanhmuc 
          FROM sanpham sp 
          JOIN danhmuc dm ON sp.iddanhmuc = dm.id
          WHERE 1=1 ";

// Điều kiện tìm kiếm
if (!empty($search)) {
    $search_escaped = $conn->real_escape_string($search);
    $query .= " AND (sp.tensp LIKE '%{$search_escaped}%' OR sp.mota LIKE '%{$search_escaped}%')";
}

// Điều kiện lọc danh mục
if ($category_filter > 0) {
    $query .= " AND sp.iddanhmuc = $category_filter";
}

// Sắp xếp
$allowed_sort_columns = ['id', 'tensp', 'giasp', 'soluong'];
$sort = in_array($sort, $allowed_sort_columns) ? $sort : 'id';
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
$query .= " ORDER BY $sort $order";

// Thêm phân trang
$query .= " LIMIT $start, $limit";

// Thực thi truy vấn sản phẩm
$result = $conn->query($query);

// Kiểm tra lỗi truy vấn
if ($result === false) {
    die("Lỗi truy vấn sản phẩm: " . $conn->error);
}

// Đếm tổng số sản phẩm
$count_query = "SELECT COUNT(*) as total 
                FROM sanpham sp 
                JOIN danhmuc dm ON sp.iddanhmuc = dm.id
                WHERE 1=1 ";

// Thêm điều kiện tìm kiếm và lọc như trên
if (!empty($search)) {
    $count_query .= " AND (sp.tensp LIKE '%{$search_escaped}%' OR sp.mota LIKE '%{$search_escaped}%')";
}

if ($category_filter > 0) {
    $count_query .= " AND sp.iddanhmuc = $category_filter";
}

$count_result = $conn->query($count_query);

// Kiểm tra lỗi đếm số lượng
if ($count_result === false) {
    die("Lỗi đếm số lượng sản phẩm: " . $conn->error);
}

$total_data = $count_result->fetch_assoc();
$total_products = $total_data['total'];
$total_pages = ceil($total_products / $limit);

// Kiểm tra thông báo
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Sản Phẩm</title>
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-image { max-width: 100px; max-height: 100px; object-fit: cover; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/admin/includes/admin_navbar.php'; ?>

        <main class="col-md-10 ms-sm-auto">
            <div class="container-fluid">
                <!-- Thông báo -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3>Quản Lý Sản Phẩm</h3>
                                <a href="themsanpham.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Thêm Sản Phẩm
                                </a>
                            </div>
                            <div class="card-body">
                                <!-- Tìm kiếm và Lọc -->
                                <form method="GET" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" name="search" class="form-control" 
                                                   placeholder="Tìm kiếm sản phẩm"
                                                   value="<?= htmlspecialchars($search) ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <select name="category" class="form-select">
                                                <option value="0">Tất Cả Danh Mục</option>
                                                <?php 
                                                // Reset con trỏ danh mục
                                                $categories_result->data_seek(0);
                                                while($cat = $categories_result->fetch_assoc()): ?>
                                                    <option value="<?= $cat['id'] ?>" 
                                                        <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cat['tendanhmuc']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="sort" class="form-select">
                                                <option value="id" <?= $sort == 'id' ? 'selected' : '' ?>>Mặc Định</option>
                                                <option value="tensp" <?= $sort == 'tensp' ? 'selected' : '' ?>>Tên Sản Phẩm</option>
                                                <option value="giasp" <?= $sort == 'giasp' ? 'selected' : '' ?>>Giá</option>
                                                <option value="soluong" <?= $sort == 'soluong' ? 'selected' : '' ?>>Số Lượng</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Tìm Kiếm
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Bảng Sản Phẩm -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Hình Ảnh</th>
                                                <th>Tên Sản Phẩm</th>
                                                <th>Giá</th>
                                                <th>Số Lượng</th>
                                                <th>Trọng Lượng</th>
                                                <th>Độ Pha Xay</th>
                                                <th>Hương Vị</th>
                                                <th>Danh Mục</th>
                                                <th>Hành Động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php while ($product = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($product['id']) ?></td>
                                                <td>
                                                    <?php 
                                                    // Kiểm tra nếu là JSON
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

                                                    // Hiển thị hình ảnh
                                                    $image_path = displayImage($first_image);
                                                    ?>
                                                    <img 
                                                        src="<?= htmlspecialchars($image_path) ?>" 
                                                        alt="<?= htmlspecialchars($product['tensp']) ?>"
                                                        class="img-thumbnail" 
                                                        style="width: 150px; height: 150px; object-fit: cover;"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target ="#imageModal<?= $product['id'] ?>"
                                                        >
                                                    
                                                    <!-- Modal phóng to hình ảnh -->
                                                    <div class="modal fade" id="imageModal<?= $product['id'] ?>" tabindex="-1">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"><?= htmlspecialchars($product['tensp']) ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <div id="carouselImages<?= $product['id'] ?>" class="carousel slide" data-bs-ride="carousel">
                                                                        <div class="carousel-inner">
                                                                            <?php foreach ($images as $index => $image): 
                                                                                // Hiển thị hình ảnh
                                                                                $image_path = displayImage($image);
                                                                            ?>
                                                                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                                                    <img 
                                                                                        src="<?= htmlspecialchars($image_path) ?>" 
                                                                                        class="img-fluid" 
                                                                                        alt="<?= htmlspecialchars($product['tensp']) ?>"
                                                                                    >
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                        
                                                                        <!-- Nút điều khiển carousel -->
                                                                        <?php if (count($images) > 1): ?>
                                                                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages<?= $product['id'] ?>" data-bs-slide="prev">
                                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                                <span class="visually-hidden">Previous</span>
                                                                            </button>
                                                                            <button class="carousel-control-next" type="button" data-bs-target="#carouselImages<?= $product['id'] ?>" data-bs-slide="next">
                                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                                <span class="visually-hidden">Next</span>
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($product['tensp']) ?></strong>
                                                    <p class="text-muted small"><?= htmlspecialchars(substr($product['mota'], 0, 50)) ?>...</p>
                                                </td>
                                                <td class="text-success">
                                                    <strong><?= number_format($product['giasp'], 0, ',', '.') ?> VNĐ</strong>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $product['soluong'] > 10 ? 'bg-success' : 'bg-warning' ?>">
                                                        <?= htmlspecialchars($product['soluong']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($product['trongluong']) ?> kg</td>
                                                <td>
                                                    <?php if (!empty($product['dophaxay'])): ?>
                                                        <span class="badge bg-info"><?= htmlspecialchars($product['dophaxay']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Không áp dụng</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($product['huongvi'])): ?>
                                                        <?= htmlspecialchars($product['huongvi']) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Không xác định</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($product['tendanhmuc']) ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="suasanpham.php?id=<?= $product['id'] ?>" class="btn btn-warning btn-sm" title="Sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="xoasanpham.php?id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Phân Trang -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&sort=<?= $sort ?>&order=<?= $order ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>