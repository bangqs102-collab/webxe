<?php
// product_detail.php - ĐÃ SỬA LẠI ĐỂ DÙNG MYSQLI

// 1. Nhúng file config để kết nối database
require 'includes/db_connect.php'; // File này tạo ra biến $conn

// 2. Lấy ID của xe từ URL
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($car_id === 0) {
    die("Không tìm thấy xe này!");
}

// 3. Chuẩn bị và thực thi câu lệnh SQL để lấy thông tin xe (SỬ DỤNG MYSQLI)
$sql = "SELECT * FROM cars WHERE id = ?";

// Chuẩn bị câu lệnh
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Gán tham số vào câu lệnh
    mysqli_stmt_bind_param($stmt, "i", $car_id);

    // Thực thi
    mysqli_stmt_execute($stmt);

    // Lấy kết quả
    $result = mysqli_stmt_get_result($stmt);

    // Lấy dữ liệu xe dưới dạng một mảng
    $car = mysqli_fetch_assoc($result);

    // Đóng statement
    mysqli_stmt_close($stmt);

    if (!$car) {
        die("Không có dữ liệu cho xe này!");
    }
    
} else {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="car-detail-container">
        <div class="car-gallery">
            <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
        </div>

        <div class="car-info">
            <h1><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>
<div class="price-section"> <?php
    // Kiểm tra xem 'sale_price' có tồn tại và có giá trị hợp lệ không
    if (isset($car['sale_price']) && $car['sale_price'] > 0) {
        // Nếu có giá khuyến mãi, hiển thị cả hai giá
        echo '<h2>$' . number_format($car['sale_price'], 2) . '</h2>';
        echo '<h4 class="original-price">$' . number_format($car['price'], 2) . '</h4>';
    } else {
        // Nếu không, chỉ hiển thị giá gốc
        echo '<h2>$' . number_format($car['price'], 2) . '</h2>';
    }
    ?>
</div>            <p><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>

            <h3>Thông số kỹ thuật</h3>
            <div class="specs">
                <div><span>Năm sản xuất</span><span><?php echo htmlspecialchars($car['year']); ?></span></div>
                <div><span>Số dặm đã đi</span><span><?php echo htmlspecialchars($car['mileage']); ?></span></div>
                </div>

            <a href="cart.php?action=add&id=<?php echo $car['id']; ?>" class="btn-add-to-cart">Thêm vào giỏ hàng</a>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>