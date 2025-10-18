<?php
// BƯỚC 1: KHỞI TẠO VÀ BẢO MẬT
// Luôn bắt đầu session ở dòng đầu tiên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nạp kết nối CSDL
require_once '../includes/db_connect.php';

// --- CÁC KIỂM TRA BẢO MẬT QUAN TRỌNG ---

// 1. Người dùng phải đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// 2. Phải là phương thức POST để tránh truy cập trực tiếp
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

// 3. Giỏ hàng không được trống
if (empty($_SESSION['cart'])) {
    header('Location: ../products.php');
    exit();
}

// BƯỚC 2: TÍNH TOÁN GIÁ TRỊ ĐƠN HÀNG
$user_id = $_SESSION['user_id'];
$cart_items = $_SESSION['cart'];
$total_price = 0;

foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Xử lý mã giảm giá (voucher)
$voucher_code = null;
$discount_amount = 0;
// SỬA: Dùng đúng tên session 'applied_voucher'
if (isset($_SESSION['applied_voucher']) && !empty($_SESSION['applied_voucher'])) {
    $voucher_code = $_SESSION['applied_voucher']['code'];
    $discount_amount = $_SESSION['applied_voucher']['discount_amount'];
}

// Tính giá cuối cùng sau khi đã trừ đi khuyến mãi
$final_price = $total_price - $discount_amount;
if ($final_price < 0) {
    $final_price = 0; // Đảm bảo giá không bị âm
}


// BƯỚC 3: XỬ LÝ LƯU ĐƠN HÀNG VÀO DATABASE BẰNG TRANSACTION
$conn->begin_transaction();

try {
    // 1. Thêm đơn hàng vào bảng `orders`
    $initial_status = 'Chờ xác nhận';
    $sql_order = "INSERT INTO orders (user_id, total_price, status, voucher_code, discount_amount) VALUES (?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("idssd", $user_id, $final_price, $initial_status, $voucher_code, $discount_amount);
    $stmt_order->execute();
    $order_id = $conn->insert_id;
    $stmt_order->close();

    // 2. Thêm các sản phẩm và cập nhật số lượng
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);

    $sql_update_qty = "UPDATE cars SET quantity = quantity - ? WHERE id = ? AND quantity >= ?";
    $stmt_update_qty = $conn->prepare($sql_update_qty);

    foreach ($cart_items as $product_id => $item) {
        // Thêm sản phẩm vào chi tiết đơn hàng
        $stmt_items->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt_items->execute();

        // Trừ số lượng tồn kho
        $stmt_update_qty->bind_param("iii", $item['quantity'], $product_id, $item['quantity']);
        $stmt_update_qty->execute();
        
        // Kiểm tra xem sản phẩm có thực sự được cập nhật không
        if ($stmt_update_qty->affected_rows == 0) {
            throw new Exception("Sản phẩm " . $item['brand'] . " đã hết hàng hoặc không đủ số lượng.");
        }
    }
    $stmt_items->close();
    $stmt_update_qty->close();

    // 3. Cập nhật số lần sử dụng voucher và tự động vô hiệu hóa (nếu cần)
    if ($voucher_code) {
        // BƯỚC 3.1: Tăng số lần đã sử dụng lên 1
        $sql_update_voucher = "UPDATE vouchers SET times_used = times_used + 1 WHERE code = ?";
        $stmt_update_voucher = $conn->prepare($sql_update_voucher);
        $stmt_update_voucher->bind_param("s", $voucher_code);
        $stmt_update_voucher->execute();
        $stmt_update_voucher->close();

        // BƯỚC 3.2: Lấy thông tin mới nhất của voucher để kiểm tra
        $sql_check_voucher = "SELECT times_used, usage_limit FROM vouchers WHERE code = ?";
        $stmt_check = $conn->prepare($sql_check_voucher);
        $stmt_check->bind_param("s", $voucher_code);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $voucher_data = $result->fetch_assoc();
        $stmt_check->close();

        // BƯỚC 3.3: Nếu đã hết lượt sử dụng, đặt is_active = 0
        if ($voucher_data && $voucher_data['usage_limit'] !== NULL && $voucher_data['times_used'] >= $voucher_data['usage_limit']) {
            $sql_deactivate = "UPDATE vouchers SET is_active = 0 WHERE code = ?";
            $stmt_deactivate = $conn->prepare($sql_deactivate);
            $stmt_deactivate->bind_param("s", $voucher_code);
            $stmt_deactivate->execute();
            $stmt_deactivate->close();
        }
    }

    // Nếu mọi thứ thành công, xác nhận transaction
    $conn->commit();

    // 4. Xóa giỏ hàng và chuyển hướng
    unset($_SESSION['cart']);
    unset($_SESSION['applied_voucher']); 
    
    header('Location: ../order_success.php?order_id=' . $order_id);
    exit();

} catch (Exception $e) {
    // Nếu có bất kỳ lỗi nào, hủy bỏ tất cả các thay đổi
    $conn->rollback();
    
    // Lưu lỗi vào session để hiển thị trên trang giỏ hàng
    $_SESSION['checkout_error'] = $e->getMessage();
    header('Location: ../cart.php');
    exit();
}
?>