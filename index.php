<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee House - Cà Phê Rang Xay Chất Lượng</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --coffee-brown: #6F4E37;
            --tea-green: #84A98C;
        }

        body {
            font-family: 'Arial', sans-serif;
        }

        .banner {
            background:url('assets/images/banner1.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 150px 0;
            text-align: center;
        }

        .section-title {
            color: var(--coffee-brown);
            border-bottom: 3px solid var(--tea-green);
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .product-card {
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .feature-icon {
            color: var(--coffee-brown);
            font-size: 3rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner -->
    <div class="banner">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4"></h1>
            <p class="lead mb-5"></p>
        </div>
    </div>

    <!-- Sản Phẩm Nổi Bật -->
    <div class="container py-5">
        <h2 class="text-center section-title">Sản Phẩm Nổi Bật</h2>
        <div class="row">
            <?php 
            $featured_products = [
                [
                    'name' => 'Cà Phê Rang Xay Đặc Biệt',
                    'price' => 250000,
                    'image' => 'coffee1.jpg',
                    'description' => 'Hỗn hợp cà phê cao cấp từ các vùng trồng nổi tiếng'
                ],
                [
                    'name' => 'Trà Xanh Thượng Hạng',
                    'price' => 180000,
                    'image' => 'tea1.jpg',
                    'description' => 'Trà xanh nguyên chất từ vùng cao Bảo Lộc'
                ],
                [
                    'name' => 'Cà Phê Espresso Đậm Đà',
                    'price' => 120000,
                    'image' => 'coffee2.jpg',
                    'description' => 'Hương vị đậm đà, phù hợp cho những tín đồ cà phê'
                ]
            ];

            foreach ($featured_products as $product): 
            ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card">
                    <img src="assets/images/<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $product['name'] ?></h5>
                        <p class="card-text"><?= $product['description'] ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-danger mb-0"><?= number_format($product['price']) ?> VNĐ</span>
                            <a href="#" class="btn btn-tea-green">
                                <i class="fas fa-cart-plus me-2"></i>Mua Ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tính Năng Nổi Bật -->
    <div class="container py-5 bg-light">
        <h2 class="text-center section-title">Tại Sao Chọn Chúng Tôi</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h4>Nguồn Gốc Chất Lượng</h4>
                <p>Các hạt cà phê và trà được chọn lọc từ những vùng trồng danh tiếng</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h4>Giao Hàng Nhanh Chóng</h4>
                <p>Đảm bảo sản phẩm luôn tươi ngon khi đến tay khách hàng</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <h4>Chất Lượng Đảm Bảo</h4>
                <p>Cam kết hoàn tiền nếu sản phẩm không đạt chất lượng</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>