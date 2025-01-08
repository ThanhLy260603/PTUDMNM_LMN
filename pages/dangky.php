<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Coffee House</title>
    
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }

        .register-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .register-banner {
            flex: 1;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
            url('/webbancaphe/assets/images/logo4.jpg') no-repeat center center;
            background-size: cover;
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 40px;
            text-align: center;
        }

        .register-form {
            flex: 1;
            padding: 40px;
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

        .password-strength {
            height: 5px;
            background: #e0e0e0;
            border-radius: 5px;
            margin-top: 5px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Banner bên trái -->
        <div class="register-banner">
            <h2 class="mb-4">Gia Nhập Coffee House</h2>
            <p class="lead">Trở thành thành viên để nhận những ưu đãi đặc biệt</p>
        </div>

        <!-- Form đăng ký bên phải -->
        <div class="register-form">
            <h3 class="text-center mb-4">
                <i class="fas fa-user-plus text-coffee-brown me-2"></i>Đăng Ký Tài Khoản
            </h3>

            <form id="registerForm" action="process_register.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName" class="form-label">
                            <i class="fas fa-user text-coffee-brown me-2"></i>Họ
                        </label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName" class="form-label">
                            <i class="fas fa-user text-coffee-brown me-2"></i>Tên
                        </label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope text-coffee-brown me-2"></i>Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone text-coffee-brown me-2"></i>Số Điện Thoại
                    </label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock text-coffee-brown me-2"></i>Mật Khẩu
                    </label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    <small id="passwordHelp" class="form-text text-muted">
                        Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số
                    </small>
                </div>

                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">
                        <i class="fas fa-lock text-coffee-brown me-2"></i>Xác Nhận Mật Khẩu
                    </label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">
                        Tôi đồng ý với <a href="#" class="text-coffee-brown">Điều khoản và Điều kiện</a>
                    </label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-coffee">Đăng Ký</button>
                </div>

                <div class="text-center mt-3">
                    <p class="text-muted">
                        Bạn đã có tài khoản? 
                        <a href="dangnhap.php" class="text-coffee-brown">Đăng Nhập Ngay</a>
                    </p> 
                </div>
            </form>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordStrengthBar = document.getElementById('passwordStrengthBar');

        passwordInput.addEventListener('input', function() {
            const value = passwordInput.value;
            let strength = 0;

            if (value.length >= 8) strength++;
            if (/[A-Z]/.test(value)) strength++;
            if (/[a-z]/.test(value)) strength++;
            if (/[0-9]/.test(value)) strength++;
            if (/[^A-Za-z0-9]/.test(value)) strength++;

            const strengthPercentage = strength * 20;
            passwordStrengthBar.style.width = strengthPercentage + '%';

            if (strengthPercentage < 40) {
                passwordStrengthBar.style.backgroundColor = 'red';
            } else if (strengthPercentage < 70) {
                passwordStrengthBar.style.backgroundColor = 'orange';
            } else {
                passwordStrengthBar.style.backgroundColor = 'green';
            }
        });
    </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js">
</body>
</html>