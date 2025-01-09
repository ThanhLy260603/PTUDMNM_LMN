<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /webbancaphe/pages/dangnhap.php");
    exit();
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Lấy mật khẩu hiện tại từ CSDL
    $query = "SELECT password FROM taikhoan WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Lỗi truy vấn: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Kiểm tra mật khẩu hiện tại
    $is_password_correct = false;
    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
        // Mật khẩu được lưu dưới dạng hash
        $is_password_correct = password_verify($current_password, $user['password']);
    } else {
        // Mật khẩu được lưu dưới dạng plain text
        $is_password_correct = ($current_password === $user['password']);
    }

    if ($is_password_correct) {
        // Kiểm tra mật khẩu mới và xác nhận mật khẩu
        if ($new_password === $confirm_password) {
            // Hash mật khẩu mới
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu mới
            $update_query = "UPDATE taikhoan SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            if (!$stmt) {
                die("Lỗi truy vấn: " . $conn->error);
            }
            $stmt->bind_param("si", $hashed_password, $user_id);

            if ($stmt->execute()) {
                $success_message = "Đổi mật khẩu thành công!";
            } else {
                $error_message = "Lỗi cập nhật mật khẩu: " . $stmt->error;
            }
        } else {
            $error_message = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
        }
    } else {
        $error_message = "Mật khẩu hiện tại không chính xác.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi Mật Khẩu - Coffee House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --coffee-brown: #6F4E37;
            --tea-green: #84A98C;
        }
        .password-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .password-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--coffee-brown);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: var(--coffee-brown);
            border: none;
            width: 100%;
            padding: 10px;
        }
        .btn-primary:hover {
            background-color: var(--tea-green);
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/header.php'; ?>

    <div class="container">
        <div class="password-form">
            <h2>Đổi Mật Khẩu</h2>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Đổi Mật Khẩu</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>