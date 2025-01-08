
<style>
    :root {
        --coffee-brown: #6F4E37;
        --tea-green: #84A98C;
        --light-bg: #f4f6f9;
    }

    .admin-sidebar {
        background-color: var(--coffee-brown);
        color: white;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        width: 250px;
        transition: all 0.3s ease;
        z-index: 1000;
        overflow-y: auto;
    }

    .admin-sidebar-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .admin-sidebar-logo img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .admin-sidebar-menu {
        padding: 20px 0;
    }

    .admin-sidebar-link {
        color: rgba(255,255,255,0.7);
        padding: 12px 20px;
        display: block;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .admin-sidebar-link:hover,
    .admin-sidebar-link.active {
        background-color: rgba(255,255,255,0.1);
        color: white;
    }

    .admin-sidebar-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .sidebar-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 15px;
        text-align: center;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    /* Responsive sidebar */
    @media (max-width: 768px) {
        .admin-sidebar {
            width: 0;
            overflow-x: hidden;
        }
        .admin-sidebar.show {
            width: 250px;
        }
    }

    .main-content {
        margin-left: 250px;
        transition: margin-left 0.3s ease;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }
    }
</style>

<div class="admin-sidebar">
    <div class="admin-sidebar-logo">
        <img src="/webbancaphe/assets/images/logo5.png" alt="Admin Logo">
        <h4 class="mb-0">Coffee House</h4>
    </div>

    <nav class="admin-sidebar-menu">
        <?php 
        $admin_menu_items = [
            [
                'title' => 'Trang Chủ',
                'icon' => 'fas fa-home',
                'link' => '/webbancaphe/admin/index.php',
                'active' => basename($_SERVER['PHP_SELF']) == 'index.php'
            ],
            [
                'title' => 'Hồ Sơ Cá Nhân',
                'icon' => 'fas fa-user',
                'link' => '/webbancaphe/admin/pages/profile/admin_profile.php',
                'active' => strpos($_SERVER['PHP_SELF'], 'admin_profile.php') !== false
            ],
            [
                'title' => 'Quản lý sản phẩm',
                'icon' => 'fas fa-user',
                'link' => '/webbancaphe/admin/pages/sanpham/quanlysanpham.php',
                'active' => strpos($_SERVER['PHP_SELF'], 'quanlysanpham.php') !== false
            ],
            [
                'title' => 'Quản Lý Đơn Hàng',
                'icon' => 'fas fa-shopping-cart',
                'link' => '/webbancaphe/admin/pages/donhang/quanlydonhang.php',
                'active' => strpos($_SERVER['PHP_SELF'], 'quanlydonhang.php') !== false
            ],
            [
                'title' => 'Quản Lý Người Dùng',
                'icon' => 'fas fa-users',
                'link' => '/webbancaphe/admin/pages/nguoidung/quanlynguoidung.php',
                'active' => strpos($_SERVER['PHP_SELF'], 'quanlynguoidung.php') !== false
            ],
            [
                'title' => 'Quản Lý Danh Mục',
                'icon' => 'fas fa-tags',
                'link' => '/webbancaphe/admin/pages/danhmuc/quanlydanhmuc.php',
                'active' => strpos($_SERVER['PHP_SELF'], 'quanlydanhmuc.php') !== false
            ]
        ];
        foreach ($admin_menu_items as $item):
        ?>
            <a href="<?php echo $item['link']; ?>" 
               class="admin-sidebar-link <?php echo $item['active'] ? 'active' : ''; ?>">
                <i class="<?php echo $item['icon']; ?>"></i>
                <?php echo $item['title']; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="/webbancaphe/pages/dangxuat.php" class="admin-sidebar-link text-danger">
            <i class="fas fa-sign-out-alt"></i>Đăng Xuất
        </a>
    </div>
</div>

<button id="sidebarToggle" class="btn btn-coffee d-md-none">
    <i class="fas fa-bars"></i>
</button>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.querySelector('.admin-sidebar').classList.toggle('show');
    });
</script>