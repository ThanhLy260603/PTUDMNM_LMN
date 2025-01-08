<?php
session_start();
require_once '../includes/config.php';

// Xử lý đăng nhập khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin từ form
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    // Kiểm tra đầu vào
    $errors = [];
    if (empty($username)) {
        $errors[] = "Vui lòng nhập tên đăng nhập";
    }
    if (empty($password)) {
        $errors[] = "Vui lòng nhập mật khẩu";
    }

    // Nếu không có lỗi thì tiến hành xác thực
    if (empty($errors)) {
        try {
            // Truy vấn kiểm tra user 
            $sql = "SELECT * FROM taikhoan WHERE username = ?";
            $stmt = $conn->prepare($sql);
            
            // Kiểm tra việc chuẩn bị câu lệnh
            if ($stmt === false) {
                throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                // Kiểm tra mật khẩu 
                if ($password === $user['password']) {
                    // Đăng nhập thành công
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Cập nhật thời gian đăng nhập
                    updateLastLogin($conn, $user['id']);

                    // Chuyển hướng theo vai trò
                    switch ($user['role']) {
                        case 'admin':
                            header("Location: ../admin/index.php");
                            exit();
                        case 'customer':
                            header("Location: ../customer/index.php");
                            exit();
                        default:
                            $errors[] = "Vai trò người dùng không hợp lệ";
                            break;
                    }
                } else {
                    // Sai mật khẩu
                    $errors[] = "Tên đăng nhập hoặc mật khẩu không đúng";
                }
            } else {
                // Không tìm thấy user
                $errors[] = "Tên đăng nhập không tồn tại";
            }
        } catch (Exception $e) {
            // Xử lý lỗi
            $errors[] = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
}

// Hàm cập nhật thời gian đăng nhập cuối
function updateLastLogin($conn, $userId) {
    // Kiểm tra kết nối
    if (!$conn) {
        writeLog("Lỗi kết nối database khi cập nhật last_login", 'error');
        return;
    }

    try {
        // Chuẩn bị câu lệnh SQL
        $updateSql = "UPDATE taikhoan SET last_login = NOW() WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        
        // Kiểm tra việc chuẩn bị câu lệnh
        if ($stmt === false) {
            throw new Exception("Lỗi chuẩn bị câu lệnh cập nhật: " . $conn->error);
        }
        
        // Ràng buộc tham số
        $stmt->bind_param("i", $userId);
        
        // Thực thi
        $result = $stmt->execute();
        
        // Kiểm tra kết quả
        if ($result === false) {
            throw new Exception("Lỗi cập nhật thời gian đăng nhập: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Ghi log lỗi
        writeLog("Lỗi cập nhật last_login: " . $e->getMessage(), 'error');
    }
}

// Hàm ghi log
function writeLog($message, $type = 'info') {
    $logFile = '../logs/' . date('Y-m-d') . '.log';
    $logMessage = date('Y-m-d H:i:s') . " | $type | $message\n";
    
    // Tạo thư mục logs nếu chưa tồn tại
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Coffee House</title>
    
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
            background: linear-gradient(135deg, var(--coffee-brown), var(--tea-green));
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            display: flex;
            width: 100%;
            height: 90vh;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .login-banner {
            flex: 1; /* Chiếm toàn bộ bên trái */
            background: url('/webbancaphe/assets/images/logo4.jpg') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .login-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4); /* Lớp phủ tối giúp chữ rõ hơn */
        }

        .login-banner h2,
        .login-banner p {
            position: relative;
            z-index: 2; /* Chữ hiển thị trên lớp phủ */
            color: white;
            text-align: center;
            margin: 0 20px;
        }

        .login-banner h2 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .login-banner p {
            font-size: 18px;
        }

        .login-form {
            flex: 1; /* Chiếm toàn bộ bên phải */
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .btn-coffee {
            background-color: var(--coffee-brown);
            border-color: var(--coffee-brown);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-coffee:hover {
            background-color: var(--tea-green);
            border-color: var(--tea-green);
            transform: translateY(-3px);
        }

        .form-control:focus {
            border-color: var(--tea-green);
            box-shadow: 0 0 0 0.2rem rgba(132, 169, 140, 0.25);
        }

        .social-login .btn {
            margin: 0 10px;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Phần banner bên trái -->
        <div class="login-banner">
            <div class="text-center" style="position: absolute; top: 50%; transform: translateY(-50%); width: 100%;">
                <h2>Chào Mừng Đến Coffee House</h2>
                <p>Khám phá hương vị đích thực của những hạt cà phê và trà cao cấp</p>
            </div>
        </div>

        <!-- Form đăng nhập bên phải -->
        <div class="login-form">
            <h3 class="text-center mb-4">
                <i class="fas fa-coffee text-coffee-brown me-2"></i>Đăng Nhập
            </h3>
            <!-- Hiển thị thông báo lỗi -->
            <?php if(!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    foreach($errors as $error) {
                        echo $error . "<br>";
                    }
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <form action="dangnhap.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user text-coffee-brown me-2"></i>Tên Đăng Nhập
                    </label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock text-coffee-brown me-2"></i>Mật Khẩu
                    </label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Nhớ Đăng Nhập</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-coffee">Đăng Nhập</button>
                </div>

                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none text-muted">Quên Mật Khẩu?</a>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted">Hoặc đăng nhập bằng</p>
                    <div class="social-login">
                        <a href="#" class="btn btn-outline-danger">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="text-muted">
                            Bạn chưa có tài khoản? 
                            <a href="dangky.php" class="text-coffee-brown">Đăng Ký Ngay</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
