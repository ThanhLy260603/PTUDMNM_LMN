<footer class="footer bg-coffee-brown text-light py-5">
    <div class="container">
        <div class="row">
            <!-- Giới thiệu -->
            <div class="col-md-4 mb-4">
                <h5 class="text-tea-green mb-3">
                    <i class="fas fa-coffee me-2"></i>Coffee House
                </h5>
                <p class="text-muted">
                    Chúng tôi cung cấp những hạt cà phê và trà chất lượng cao, được chọn lọc kỹ lưỡng từ những vùng trồng danh tiếng, mang đến trải nghiệm thưởng thức tuyệt vời.
                </p>
                <div class="social-icons">
                    <a href="#" class="text-tea-green me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-tea-green me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-tea-green me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-tea-green"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Liên kết nhanh -->
            <div class="col-md-4 mb-4">
                <h5 class="text-tea-green mb-3">
                    <i class="fas fa-link me-2"></i>Liên Kết Nhanh
                </h5>
                <div class="row">
                    <div class="col-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="#" class="text-muted">
                                    <i class="fas fa-chevron-right me-2 text-tea-green"></i>Trang Chủ
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted">
                                    <i class="fas fa-chevron-right me-2 text-tea-green"></i>Sản Phẩm
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted">
                                    <i class="fas fa-chevron-right me-2 text-tea-green"></i>Về Chúng Tôi
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="#" class="text-muted">
                                    <i class="fas fa-chevron-right me-2 text-tea-green"></i>Liên Hệ
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted">
                                    <i class="fas fa-chevron-right me-2 text-tea-green"></i>Giỏ Hàng
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted">
                                    <i class="fas fa-chevron-right me-2 text-tea-green"></i>Chính Sách
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Liên hệ -->
            <div class="col-md-4 mb-4">
                <h5 class="text-tea-green mb-3">
                    <i class="fas fa-envelope me-2"></i>Liên Hệ
                </h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-tea-green"></i>
                        123 Đường Cà Phê, Quận Thủ Đức, TP.HCM
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2 text-tea-green"></i>
                        0123 456 789
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2 text-tea-green"></i>
                        support@coffeehouse.com
                    </li>
                </ul>
                
                <div class="newsletter mt-3">
                    <h6 class="text-tea-green">Đăng Ký Nhận Tin</h6>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email của bạn">
                        <button class="btn btn-tea-green" type="button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bản quyền -->
        <hr class="border-tea-green">
        <div class="text-center mt-4">
            <p class="mb-0">
                &copy; 2023 Coffee House. Bản quyền thuộc về Công Ty TNHH Coffee House.
            </p>
        </div>
    </div>
</footer>

<style>
    /* Định nghĩa màu sắc */
    :root {
        --coffee-brown: #6F4E37;      /* Màu nâu cà phê */
        --coffee-brown-dark: #4B3621;  /* Màu nâu cà phê đậm */
        --tea-green: #84A98C;          /* Màu xanh lá trà */
        --tea-green-dark: #52796F;     /* Màu xanh lá trà đậm */
    }

    /* Tạo các lớp màu tùy chỉnh */
    .text-tea-green { color: var(--tea-green) !important; }
    .bg-coffee-brown { background-color: var(--coffee-brown-dark) !important; }
    .btn-tea-green { 
        background-color: var(--tea-green) !important; 
        border-color: var(--tea-green) !important;
        color: white !important;
    }
    .border-tea-green {
        border-color: var(--tea-green) !important;
    }

    .footer a {
        transition: all 0.3s ease;
    }

    .footer a:hover {
        color: var(--tea-green) !important;
        transform: translateX(5px);
    }

    .social-icons a {
        font-size: 1.5rem;
        transition: color 0.3s ease;
    }

    .social-icons a:hover {
        color: white !important;
    }
</style>