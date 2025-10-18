<?php
// BƯỚC 1: BẮT ĐẦU SESSION VÀ XỬ LÝ LOGIC NGAY LẬP TỨC
// Luôn bắt đầu session ở dòng đầu tiên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nạp kết nối CSDL (file này không được chứa mã HTML)
require 'includes/db_connect.php';

// Khởi tạo giỏ hàng nếu nó chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// BƯỚC 2: XỬ LÝ LOGIC THÊM SẢN PHẨM
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    $stmt = mysqli_prepare($conn, "SELECT brand, model, price, image_url, quantity FROM cars WHERE id = ? AND quantity > 0");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($product = mysqli_fetch_assoc($result)) {
            // Kiểm tra xem số lượng trong giỏ hàng có vượt quá số lượng tồn kho không
            $quantityInCart = isset($_SESSION['cart'][$product_id]['quantity']) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
            
            if ($product['quantity'] > $quantityInCart) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity']++;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        "brand" => $product['brand'],
                        "model" => $product['model'],
                        "price" => $product['price'],
                        "image_url" => $product['image_url'],
                        "quantity" => 1
                    ];
                }
            } else {
                // Tùy chọn: thông báo cho người dùng biết sản phẩm đã hết hàng
                $_SESSION['message'] = "Không thể thêm sản phẩm, đã đạt số lượng tối đa trong kho.";
                $_SESSION['message_type'] = "warning";
            }
        }
        mysqli_stmt_close($stmt);
    }
    // Chuyển hướng về trang giỏ hàng (loại bỏ các tham số action)
    header('Location: cart.php');
    exit();
}

// BƯỚC 3: XỬ LÝ LOGIC XÓA SẢN PHẨM
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header('Location: cart.php');
    exit();
}

// BƯỚC 4: BÂY GIỜ MỚI NẠP HEADER ĐỂ HIỂN THỊ GIAO DIỆN
include 'includes/header.php';
?>

<!-- BƯỚC 5: HIỂN THỊ GIAO DIỆN GIỎ HÀNG -->
<div class="container" style="padding-top: 50px; padding-bottom: 50px; color: white;">
    <h1>GIỎ HÀNG CỦA BẠN</h1>

    <?php 
    // Hiển thị thông báo (ví dụ: hết hàng) nếu có
    if (isset($_SESSION['message'])): 
    ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
            <?php echo $_SESSION['message']; ?>
        </div>
    <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    endif; 
    ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Giỏ hàng của bạn đang trống.</p>
        <a href="products.php" class="btn btn-warning">Tiếp tục mua sắm</a>
    <?php else: ?>
        <table class="table text-light">
            <thead>
                <tr>
                    <th colspan="2">Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tạm tính</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $id => $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td style="width: 150px;">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['brand']) ?>" class="img-fluid rounded">
                        </td>
                        <td><?= htmlspecialchars($item['brand'] . ' ' . $item['model']) ?></td>
                        <td>$<?= number_format($item['price']) ?></td>
                        <td>
                            <!-- Thêm chức năng cập nhật số lượng sau -->
                            <?= $item['quantity'] ?>
                        </td>
                        <td>$<?= number_format($subtotal) ?></td>
                        <td>
                            <a href="cart.php?action=remove&id=<?= $id ?>" class="btn btn-sm btn-danger">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end mt-4">
            <h3>Tổng cộng: <span class="text-warning">$<?= number_format($total) ?></span></h3>
            <a href="checkout.php" class="btn btn-warning mt-2">Tiến hành thanh toán</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

