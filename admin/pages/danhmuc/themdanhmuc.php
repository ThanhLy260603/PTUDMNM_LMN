<?php
session_start();
require_once '../../../includes/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../pages/dangnhap.php");
    exit();
}

// Khởi tạo biến lỗi
$errors = [];

// Xử lý thêm danh mục
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy và làm sạch dữ liệu
    $tendanhmuc = trim($_POST['tendanhmuc']);

    // Validate
    if (empty($tendanhmuc)) {
        $errors[] = "Tên danh mục không được trống";
    }

    // Kiểm tra danh mục đã tồn tại chưa
    $check_query = "SELECT * FROM danhmuc WHERE tendanhmuc = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $tendanhmuc);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $errors[] = "Danh mục này đã tồn tại";
    }

    // Nếu không có lỗi
    if (empty($errors)) {
        // Chuẩn bị câu lệnh SQL an toàn
        $sql = "INSERT INTO danhmuc (tendanhmuc) VALUES (?)";
        
        // Kiểm tra việc chuẩn bị câu lệnh
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            // Nếu việc chuẩn bị câu lệnh thất bại
            $errors[] = "Lỗi chuẩn bị câu lệnh: " . $conn->error;
        } else {
            // Ràng buộc tham số
            $stmt->bind_param("s", $tendanhmuc);
            
            // Thực thi câu lệnh
            try {
                if ($stmt->execute()) {
                    // Thành công
                    $_SESSION['success_message'] = "Thêm danh mục thành công";
                    header("Location: quanlydanhmuc.php");
                    exit();
                } else {
                    // Lỗi thực thi
                    $errors[] = "Lỗi thêm danh mục: " . $stmt->error;
                }
            } catch (Exception $e) {
                $errors[] = "Ngoại lệ xảy ra: " . $e->getMessage();
            }
            
            // Đóng statement
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Danh Mục</title>
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
                <h1 class="mt-4">Thêm Danh Mục</h1>

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
                               value="<?php echo isset($tendanhmuc) ? htmlspecialchars($tendanhmuc) : ''; ?>" 
                               required>
                        <small class="form-text text-muted">Ví dụ: Cà phê rang xay, Trà xanh, v.v.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm Danh Mục</button>
                    <a href="quanlydanhmuc.php" class="btn btn-secondary">Quay Lại</a>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>