<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
} ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Car Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="/Shop_Xe/assets/css/reviews.css">
</head>
<body>
    
    <header class="container">
        
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/images/logo-removebg-preview.png" alt="Logo" style="width: 50px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="index.php">TRANG CHỦ</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">MUA XE</a></li>
                        <li class="nav-item"><a class="nav-link" href="voucher.php">VOUCHER</a></li>
                        <li class="nav-item"><a class="nav-link" href="ve-chung-toi.php">VỀ CHÚNG TÔI</a></li>
                        <li class="nav-item"><a class="nav-link" href="lien-he.php">LIÊN HỆ</a></li>
                    </ul>

                    <div class="d-flex align-items-center">
                        <a href="cart.php" class="nav-link me-3"><i class="bi bi-cart-fill fs-5"></i></a>
                        
                         <div class="search-container">
                             <div class="search-bar">
                                 <input type="text" id="live-search-input" placeholder="Tìm kiếm xe..." autocomplete="off">
                                 <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i></button> 
                             </div>
                             <div id="search-results"></div>
                         </div>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <a href="#" class="nav-link dropdown-toggle ms-2" data-bs-toggle="dropdown">
                                    Chào, <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <li><a class="dropdown-item" href="admin/index.php">Trang Admin</a></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="my_orders.php">Đơn hàng của tôi</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="nav-link me-2">Đăng nhập</a>
                            <a href="register.php" class="btn btn-outline-light">Đăng ký</a>
                        <?php endif; ?>
                        </div>
                </div>
            </div>
        </nav>
    </header>
    <script src="assets/js/main.js"></script>
    <main>