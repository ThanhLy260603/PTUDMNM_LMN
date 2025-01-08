<!-- includes/header.php -->
<style>
    :root {
        --coffee-brown: #6F4E37;
        --tea-green: #84A98C;
        --coffee-light: #D2B48C;
        --dark-background: #2C2C2C;
    }

    .navbar {
        background-color: var(--dark-background) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover {
        transform: scale(1.05);
    }

    .navbar-brand img {
        border-radius: 50%;
        margin-right: 10px;
        border: 2px solid var(--tea-green);
    }

    .nav-link {
        color: rgba(255,255,255,0.7) !important;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link:hover, .nav-link.active {
        color: var(--tea-green) !important;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 50%;
        background-color: var(--tea-green);
        transition: all 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
        left: 0;
    }

    .dropdown-menu {
        background-color: var(--coffee-brown) !important;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .dropdown-item {
        color: rgba(255,255,255,0.8) !important;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: rgba(255,255,255,0.1);
        color: white !important;
    }

    .btn-login {
        border: 2px solid var(--tea-green);
        color: var(--tea-green);
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        background-color: var(--tea-green);
        color: white !important;
    }

    .btn-register {
        background-color: var(--tea-green);
        color: white !important;
        transition: all 0.3s ease;
    }

    .btn-register:hover {
        background-color: var(--coffee-brown);
        transform: translateY(-3px);
    }

    .user-dropdown .dropdown-toggle {
        display: flex;
        align-items: center;
    }

    .user-dropdown .dropdown-toggle i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <!-- Logo -->
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
        <img src="/webbancaphe/assets/images/logo5.png" width = "120" height="120" alt="Coffee Shop Logo" height="50" class="logo-img">

            <span class="fw-bold text-tea-green">Coffee And Tea</span>
        </a>

        <!-- Toggler cho responsive -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu chính -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/webbancaphe/customer/index.php">
                        <i class="fas fa-home me-2 text-tea-green"></i>Trang Chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/webbancaphe/customer/index.php" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-coffee me-2 text-tea-green"></i>Sản Phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/webbancaphe/customer/pages/giohang/giohang.php">
                        <i class="fas fa-shopping-cart me-2 text-tea-green"></i>Giỏ Hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/gioithieu.php">
                        <i class="fas fa-info-circle me-2 text-tea-green"></i>Về Chúng Tôi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/lienhe.php">
                        <i class="fas fa-envelope me-2 text-tea-green"></i>Liên Hệ
                    </a>
                </li>
            </ul>

            <!-- Phần đăng nhập và đăng ký -->
            <?php 
            // Kiểm tra đăng nhập
            $isLoggedIn = isset($_SESSION['user_id']);
            ?>
            <ul class="navbar-nav">
                <?php if(!$isLoggedIn): ?>
                    <li class="nav-item me-2">
                        <a href="pages/dangnhap.php" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="pages/dangky.php" class="btn btn-register">
                            <i class="fas fa-user-plus me-2"></i>Đăng Ký
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle text-tea-green"></i>
                            <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/webbancaphe/customer/pages/hoso/hoso.php">
                                    <i class="fas fa-user me-2"></i>Hồ Sơ Cá Nhân
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/webbancaphe/customer/pages/giohang/lichsudonhang.php">
                                    <i class="fas fa-shopping-basket me-2"></i>Đơn Hàng
                                </a>
                            </li>
                            <li>
                                 <a class="dropdown-item text-danger" href="/webbancaphe/pages/dangxuat.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>