<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../pages/dangnhap.php");
    exit();
}
require_once '../../../includes/config.php';

$user_id = $_SESSION['user_id'];


$query = "SELECT * FROM taikhoan WHERE id = ? AND role = 'admin'";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Lỗi truy vấn: " . $conn->error); 
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    die("Không tìm thấy thông tin admin.");
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hoten = htmlspecialchars($_POST['hoten']);
    $username = htmlspecialchars($_POST['username']);
    $diachi = htmlspecialchars($_POST['diachi']);
    $sdt = htmlspecialchars($_POST['sdt']);

    // Cập nhật thông tin admin
    $update_query = "UPDATE taikhoan SET hoten = ?, username = ?, diachi = ?, sdt = ? WHERE id = ? AND role = 'admin'";
    $update_stmt = $conn->prepare($update_query);

    if (!$update_stmt) {
        die("Lỗi truy vấn cập nhật: " . $conn->error); // Hiển thị lỗi nếu truy vấn không hợp lệ
    }

    $update_stmt->bind_param("ssssi", $hoten, $username, $diachi, $sdt, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['profile_message'] = "Cập nhật thông tin thành công!";
        header("Location: admin_profile.php");
        exit();
    } else {
        $_SESSION['profile_message'] = "Có lỗi xảy ra khi cập nhật thông tin.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ Sơ Cá Nhân Admin</title>
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
        <h2 class="text-center mb-4">Hồ Sơ Cá Nhân Admin</h2>

        <!-- Hiển thị thông báo -->
        <?php 
        if (isset($_SESSION['profile_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . 
                 htmlspecialchars($_SESSION['profile_message']) . 
                 '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['profile_message']);
        }
        ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="hoten" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="hoten" name="hoten" value="<?php echo htmlspecialchars($admin['hoten']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" readonly required>
                    </div>
                    <div class="mb-3> 
                    <label for="diachi" class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" id="diachi" name="diachi" value="<?php echo htmlspecialchars($admin['diachi']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="sdt" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="sdt" name="sdt" value="<?php echo htmlspecialchars($admin['sdt']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
</main>
</div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>