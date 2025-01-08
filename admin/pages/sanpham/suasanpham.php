<?php
session_start();
require_once '../../../includes/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../pages/dangnhap.php");
    exit();
}

// Hàm upload nhiều hình ảnh
function uploadMultipleImages($files) {
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/assets/images/sanpham/';
    
    // Tạo thư mục nếu chưa tồn tại
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $uploaded_images = [];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $max_images = 5; // Giới hạn số lượng ảnh

    foreach ($files['tmp_name'] as $key => $tmp_name) {
        // Bỏ qua nếu không có file hoặc số lượng ảnh vượt quá giới hạn
        if ($files['error'][$key] !== UPLOAD_ERR_OK || count($uploaded_images) >= $max_images) continue;

        // Kiểm tra loại và kích thước file
        if (!in_array($files['type'][$key], $allowed_types) || $files['size'][$key] > $max_size) continue;

        // Tạo tên file duy nhất
        $filename = uniqid() . '_' . basename($files['name'][$key]);
        $target_path = $upload_dir . $filename;

        // Upload file
        if (move_uploaded_file($tmp_name, $target_path)) {
            // Resize ảnh
            resizeImage($target_path);
            $uploaded_images[] = '/webbancaphe/assets/images/sanpham/' . $filename;
        }
    }

    return $uploaded_images;
}

// Hàm resize ảnh
function resizeImage($filepath, $max_width = 800, $max_height = 800) {
    // Lấy thông tin ảnh
    $image_info = getimagesize($filepath);
    
    // Xác định loại ảnh
    $mime = $image_info['mime'];
    
    // Tạo source image
    switch ($mime) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($filepath);
            break;
        case 'image/png':
            $source = imagecreatefrompng($filepath);
            break;
        case 'image/gif':
            $source = imagecreatefromgif($filepath);
            break;
        case 'image/webp':
            $source = imagecreatefromwebp($filepath);
            break;
        default:
            return false;
    }

    // Tính toán kích thước mới
    $width = imagesx($source);
    $height = imagesy($source);
    $ratio = $width / $height;

    if ($width > $max_width || $height > $max_height) {
        if ($width / $max_width > $height / $max_height) {
            $new_width = $max_width;
            $new_height = $max_width / $ratio;
        } else {
            $new_height = $max_height;
            $new_width = $max_height * $ratio;
        }

        // Tạo ảnh mới
        $new_image = imagecreatetruecolor($new_width, $new_height);
        
        // Đối với PNG và GIF, giữ trong suốt
        if ($mime == 'image/png' || $mime == 'image/gif') {
            imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
        }

        // Resize
        imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Lưu ảnh
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($new_image, $filepath, 80);
                break;
            case 'image/png':
                imagepng($new_image, $filepath, 6);
                break;
            case 'image/gif':
                imagegif($new_image, $filepath);
                break;
            case 'image/webp':
                imagewebp($new_image, $filepath, 80);
                break;
        }

        // Giải phóng bộ nhớ
        imagedestroy($new_image);
    }

    imagedestroy($source);
    return true;
}

// Hàm xóa ảnh cũ
function deleteOldImages($old_images) {
    if (!is_array($old_images)) return;

    foreach ($old_images as $image) {
        $full_path = $_SERVER['DOCUMENT_ROOT'] . $image;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }
}

// Lấy ID sản phẩm từ URL
$id = intval($_GET['id']);

// Xử lý sửa sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tensp = trim($_POST['tensp']);
    $giasp = intval($_POST['giasp']);
    $mota = trim($_POST['mota']);
    $soluong = intval($_POST['soluong']);
    $trongluong = floatval($_POST['trongluong']);
    $dophaxay = $_POST['dophaxay'] ?? NULL;
    $huongvi = $_POST['huongvi'] ?? NULL;
    $iddanhmuc = intval($_POST['iddanhmuc']);

    // Lấy danh sách ảnh cũ
    $old_images = json_decode($_POST['existing_images'] ?? '[]', true);

    // Xử lý xóa ảnh
    $remove_images = $_POST['remove_images'] ?? [];
    $old_images = array_diff($old_images, $remove_images);

    // Xóa các file ảnh đã chọn xóa
    deleteOldImages($remove_images);

    // Xử lý upload ảnh mới
    $new_images = [];
    if (!empty($_FILES['hinhanh']) && count(array_filter($_FILES['hinhanh']['name'])) > 0) {
        $new_images = uploadMultipleImages($_FILES['hinhanh']);
    }

    // Kết hợp ảnh cũ và mới
    $hinhanh = array_merge($old_images, $new_images);

    // Giới hạn số lượng ảnh
    $hinhanh = array_slice($hinhanh, 0, 5);

    // Chuyển đổi mảng thành JSON
    $hinhanh_json = json_encode($hinhanh);

    // Cập nhật sản phẩm
    $sql = "UPDATE sanpham SET tensp = ?, giasp = ?, mota = ?, hinhanh = ?, soluong = ?, 
            trongluong = ?, dophaxay = ?, huongvi = ?, iddanhmuc = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissddssii", $tensp, $giasp, $mota, $hinhanh_json, $soluong, 
                      $trongluong, $dophaxay, $huongvi, $iddanhmuc, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cập nhật sản phẩm thành công!";
        header("Location: quanlysanpham.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra, vui lòng thử lại.";
    }
}

// Truy vấn sản phẩm để hiển thị
$product_query = "SELECT * FROM sanpham WHERE id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

// Parse hình ảnh
$existing_images = json_decode($product['hinhanh'], true) ?? [];

// Truy vấn danh mục
$categories_query = "SELECT * FROM danhmuc";
$categories_result = $conn->query($categories_query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .image-preview {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            margin: 5px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/admin/includes/admin_navbar.php'; ?>

        <main class="col-md-10 ms-sm-auto">
            <div class="container">
                <h2 class="mt-4">Sửa Sản Phẩm</h2>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="existing_images" value='<?= htmlspecialchars(json_encode($existing_images)) ?>'>
                    <div class="mb-3">
                        <label for="tensp" class="form-label">Tên Sản Phẩm</label>
                        <input type="text" class="form-control" name="tensp" value="<?= htmlspecialchars($product['tensp']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="giasp" class="form-label">Giá Sản Phẩm (VNĐ)</label>
                        <input type="number" class="form-control" name="giasp" value="<?= htmlspecialchars($product['giasp']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mota" class="form-label">Mô Tả</label>
                        <textarea class="form-control" name="mota" rows="3"><?= htmlspecialchars($product['mota']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="hinhanh" class="form-label">Hình Ảnh (Tối đa 5 ảnh)</label>
                        <input type="file" class="form-control" name="hinhanh[]" accept="image/*" multiple>
                        <small class="form-text text-muted">Bạn có thể chọn nhiều hình ảnh (tối đa 5 ảnh)</small>
                    
                        <!-- Hiển thị các ảnh hiện tại -->
                        <div class="mt-3">
                            <h6>Ảnh hiện tại:</h6>
                            <div class="d-flex">
                                <?php foreach ($existing_images as $image): ?>
                                    <div class="position-relative">
                                        <img src="<?= htmlspecialchars($image) ?>" alt="Hình ảnh sản phẩm" class="image-preview">
                                        <input type="checkbox" name="remove_images[]" value="<?= htmlspecialchars($image) ?>" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="cursor: pointer;">
                                        <span class="visually-hidden">Xóa</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="soluong" class="form-label">Số Lượng</label>
                        <input type="number" class="form-control" name="soluong" value="<?= htmlspecialchars($product['soluong']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="trongluong" class="form-label">Trọng Lượng (kg)</label>
                        <input type="number" step="0.1" class="form-control" name="trongluong" value="<?= htmlspecialchars($product['trongluong']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="dophaxay" class="form-label">Độ Pha Xay</label>
                        <select name="dophaxay" class="form-select">
                            <option value="">Chọn độ pha xay</option>
                            <option value="Mịn" <?= $product['dophaxay'] == 'Mịn' ? 'selected' : '' ?>>Mịn</option>
                            <option value="Trung Bình" <?= $product['dophaxay'] == 'Trung Bình' ? 'selected' : '' ?>>Trung Bình</option>
                            <option value="Thô" <?= $product['dophaxay'] == 'Thô' ? 'selected' : '' ?>>Thô</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="huongvi" class="form-label">Hương Vị</label>
                        <input type="text" class="form-control" name="huongvi" value="<?= htmlspecialchars($product['huongvi']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="iddanhmuc" class="form-label">Danh Mục</label>
                        <select name="iddanhmuc" class="form-select" required>
                            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>" <?= $product['iddanhmuc'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['tendanhmuc']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập Nhật Sản Phẩm</button>
                    <a href="quanlysanpham.php" class="btn btn-secondary">Quay Lại</a>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>