<?php
session_start();
require_once '../../../includes/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../pages/dangnhap.php");
    exit();
}

// Kiểm tra xem có ID danh mục được truyền không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Không có ID danh mục được chọn";
    header("Location: quanlydanhmuc.php");
    exit();
}

// Lấy ID danh mục
$id = intval($_GET['id']);

// Truy vấn thông tin danh mục
$query = "SELECT * FROM danhmuc WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra xem danh mục có tồn tại không
if ($result->num_rows == 0) {
    $_SESSION['error_message'] = "Danh mục không tồn tại";
    header("Location: quanlydanhmuc.php");
    exit();
}

// Lấy thông tin danh mục
$category = $result->fetch_assoc();

// Xử lý cập nhật danh mục
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy và làm sạch dữ liệu
    $tendanhmuc = trim($_POST['tendanhmuc']);

    // Validate
    $errors = [];
    if (empty($tendanhmuc)) {
        $errors[] = "Tên danh mục không được trống";
    }

    // Kiểm tra tên danh mục đã tồn tại chưa (trừ danh mục hiện tại)
    $check_query = "SELECT * FROM danhmuc WHERE tendanhmuc = ? AND id != ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("si", $tendanhmuc, $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $errors[] = "Tên danh mục này đã tồn tại";
    }

    // Nếu không có lỗi
    if (empty($errors)) {
        // Chuẩn bị câu lệnh SQL cập nhật
        $update_query = "UPDATE danhmuc SET tendanhmuc = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        
        if ($update_stmt === false) {
            $errors[] = "Lỗi chuẩn bị câu lệnh: " . $conn->error;
        } else {
            // Ràng buộc tham số
            $update_stmt->bind_param("si", $tendanhmuc, $id);
            
            // Thực thi câu lệnh
            try {
                if ($update_stmt->execute()) {
                    // Cập nhật thành công
                    $_SESSION['success_message'] = "Cập nhật danh mục thành công";
                    header("Location: quanlydanhmuc.php");
                    exit();
                } else {
                    // Lỗi thực thi
                    $errors[] = "Lỗi cập nhật danh mục: " . $update_stmt->error;
                }
            } catch (Exception $e) {
                $errors[] = "Ngoại lệ xảy ra: " . $e->getMessage();
            }
            
            // Đóng statement
            $update_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Danh Mục</title>
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .main-content {
            margin-left: 250px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        // Thử include sidebar
        $sidebar_paths = [
            $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/admin_navbar.php',
            $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/admin/includes/admin_navbar.php'
        ];
        
        $included = false;
        foreach ($sidebar_paths as $path) {
            if (file_exists($path)) {
                include_once $path;
                $included = true;
                break;
            }
        }
        
        if (!$included) {
            echo "Không tìm thấy file sidebar!";
        }
        ?>

        <!-- Nội dung chính -->
        <main class="col-md-10 ms-sm-auto main-content">
            <div class="container">
                <h1 class="mt-4">Sửa Danh Mục</h1>

                <!-- Hiển thị lỗi -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="tendanhmuc" class="form-label">Tên Danh Mục</label>
                        <input type="text" class="form-control" id="tendanhmuc" name="tendanhmuc" 
                               value="<?php echo htmlspecialchars($category['tendanhmuc']); ?>" 
                               required>
                        <small class="form-text text-muted">
                            Nhập tên danh mục mới. Ví dụ: Cà phê rang xay, Trà xanh, v.v.
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mã Danh Mục</label>
                        <input type="text" class="form-control" value="<?php echo $category['id']; ?>" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Cập Nhật Danh Mục
                    </button>
                    <a href="quanlydanhmuc.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay Lại
                    </a>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>