<?php
// Các hằng số cấu hình database
define('DB_HOST', 'localhost');     // Địa chỉ máy chủ database
define('DB_USERNAME', 'root');       // Tên đăng nhập database
define('DB_PASSWORD', '');           // Mật khẩu database
define('DB_NAME', 'coffeeandtea');   // Tên database đã thay đổi

// Thiết lập kết nối database
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kiểm tra kết nối
if ($conn->connect_error) {
    // Xử lý lỗi kết nối
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// Đặt charset để hỗ trợ tiếng Việt
$conn->set_charset("utf8mb4");

// Hàm đóng kết nối database (nên gọi khi không sử dụng nữa)
function closeConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// Hàm an toàn để chống SQL Injection
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Hàm kiểm tra và hiển thị lỗi
function displayError($message) {
    echo "<div class='alert alert-danger'>$message</div>";
    exit();
}

// Hàm hash mật khẩu an toàn
function hashPassword($password) {
    // Sử dụng password_hash nếu muốn mã hóa mật khẩu
    return password_hash($password, PASSWORD_DEFAULT);
}

// Hàm kiểm tra mật khẩu
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Đăng ký hàm shutdown để đóng kết nối
register_shutdown_function('closeConnection');