<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /webbancaphe/pages/dangnhap.php");
    exit();
}

// Hàm upload hình ảnh
function uploadAvatar($file) {
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/assets/images/avatars/';
    
    // Tạo thư mục nếu chưa tồn tại
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Kiểm tra file upload
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Các loại file được phép
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Kiểm tra loại và kích thước file
    if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
        return null;
    }

    // Tạo tên file duy nhất
    $filename = uniqid() . '_' . basename($file['name']);
    $target_path = $upload_dir . $filename;

    // Di chuyển file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return '/webbancaphe/assets/images/avatars/' . $filename;
    }

    return null;
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM taikhoan WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Lỗi truy vấn: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cập nhật thông tin người dùng
    $hoten = $_POST['hoten'] ?? '';
    $sdt = $_POST['sdt'] ?? '';
    $diachi = $_POST['diachi'] ?? '';

    // Xử lý upload avatar
    $avatar = uploadAvatar($_FILES['avatar'] ?? null);
    if ($avatar) {
        // Nếu có avatar mới, cập nhật cả avatar
        $update_query = "UPDATE taikhoan SET hoten = ?, sdt = ?, diachi = ?, avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        if (!$stmt) {
            die("Lỗi truy vấn: " . $conn->error);
        }
        $stmt->bind_param("ssssi", $hoten, $sdt, $diachi, $avatar, $user_id);
    } else {
        // Nếu không có avatar mới, chỉ cập nhật thông tin khác
        $update_query = "UPDATE taikhoan SET hoten = ?, sdt = ?, diachi = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        if (!$stmt) {
            die("Lỗi truy vấn: " . $conn->error);
        }
        $stmt->bind_param("sssi", $hoten, $sdt, $diachi, $user_id);
    }

    // Thực thi truy vấn
    if ($stmt->execute()) {
        // Cập nhật thành công, chuyển hướng về trang hồ sơ
        header("Location: /webbancaphe/customer/pages/hoso.php");
        exit();
    } else {
        // Xử lý lỗi
        die("Lỗi cập nhật thông tin: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh Sửa Hồ Sơ - Coffee House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/header.php'; ?>

    <div class="container">
        <h2 class="mt-4">Chỉnh Sửa Hồ Sơ</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="hoten" class="form-label">Họ Tên</label>
                <input type="text" class="form-control" id="hoten" name="hoten" value="<?php echo htmlspecialchars($user['hoten']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="sdt" class="form-label">Số Điện Thoại</label>
                <input type="text" class="form-control" id="sdt" name="sdt" value="<?php echo htmlspecialchars($user['sdt']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="diachi" class="form-label">Địa Chỉ</label>
                <input type="text" class="form-control" id="diachi" name="diachi" value="<?php echo htmlspecialchars($user['diachi']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar</label>
                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                <img src="<?php echo htmlspecialchars($user['avatar'] ?? '/webbancaphe/assets/images/default-avatar.jpg'); ?>" alt="Avatar" class="mt-2" style="width: 150px; height: 150px; border-radius: 50%;">
            </div>
            <button type="submit" class="btn btn-primary">Cập Nhật</ ```php
            </button>
        </form>
    </div>

    <!-- Footer -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>