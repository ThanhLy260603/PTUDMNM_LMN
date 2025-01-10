<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/includes/config.php';

// Kiểm tra quyền admin (nếu cần)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dangnhap.php");
    exit();
}

// Kiểm tra xem có ID người dùng được truyền không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Không có ID người dùng được chọn.";
    header("Location: quanlynguoidung.php");
    exit();
}

$user_id = intval($_GET['id']);

// Xử lý cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Thu thập dữ liệu từ form
    $hoten = trim($_POST['hoten']);
    $username = trim($_POST['username']);
    $diachi = trim($_POST['diachi']);
    $sdt = trim($_POST['sdt']);
    $role = $_POST['role'] ?? 'customer'; // Mặc định là customer nếu không chọn

    // Validate dữ liệu
    $errors = [];

    if (empty($hoten)) {
        $errors[] = "Họ tên không được để trống.";
    }

    if (empty($username)) {
        $errors[] = "Tên đăng nhập không được để trống.";
    }

    // Kiểm tra xem username đã tồn tại chưa (trừ user hiện tại)
    $check_username_query = "SELECT * FROM taikhoan WHERE username = ? AND id != ?";
    $check_stmt = $conn->prepare($check_username_query);
    $check_stmt->bind_param("si", $username, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $errors[] = "Tên đăng nhập đã tồn tại.";
    }
    // Nếu không có lỗi
    if (empty($errors)) {
        // Chuẩn bị câu lệnh SQL
        if (!empty($password)) {
            // Cập nhật cả mật khẩu
            $query = "UPDATE taikhoan SET hoten = ?, username = ?, password = ?, diachi = ?, sdt = ?, role = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssi", $hoten, $username, $hashed_password, $diachi, $sdt, $role, $user_id);
        } else {
            // Cập nhật không bao gồm mật khẩu
            $query = "UPDATE taikhoan SET hoten = ?, username = ?, diachi = ?, sdt = ?, role = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssi", $hoten, $username, $diachi, $sdt, $role, $user_id);
        }

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            $_SESSION['message'] = "Cập nhật thông tin người dùng thành công.";
            header("Location: quanlynguoidung.php");
            exit();
        } else {
            $errors[] = "Lỗi khi cập nhật: " . $stmt->error;
        }
    }
}

// Lấy thông tin người dùng hiện tại
$query = "SELECT * FROM taikhoan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Không tìm thấy người dùng.";
    header("Location: quanlynguoidung.php");
    exit();
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Người Dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
        <!-- Sidebar -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/webbancaphe/admin/includes/admin_navbar.php'; ?>

        <main class="col-md-10 ms-sm-auto">

    <div class="container mt-5">
        <h2 class="text-center mb-4">Sửa Thông Tin Người Dùng</h2>

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
                <label for="hoten" class="form-label">Họ Tên</label>
                <input type="text" class="form-control" id="hoten" name="hoten" 
                       value="<?php echo htmlspecialchars($user['hoten']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Tên Đăng Nhập</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" readonly required>
            </div>
            <div class="mb-3">
                <label for="diachi" class="form-label">Địa Chỉ</label>
                <input type="text" class="form-control" id="diachi" name="diachi" 
                       value="<?php echo htmlspecialchars($user['diachi']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="sdt" class="form-label">Số Điện Thoại</label>
                <input type="text" class="form-control" id="sdt" name=" sdt" 
                       value="<?php echo htmlspecialchars($user['sdt']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Vai Trò</label>
                <select class="form-select" id="role" name="role">
                    <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Khách Hàng</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Quản Trị Viên</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập Nhật</button>
            <a href="quanlynguoidung.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</main>
</div>
</div>
</body>
</html>