<?php
// php/apply_voucher.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../includes/db_connect.php'; // Chú ý đường dẫn lùi ra 1 cấp

$response = ['success' => false, 'message' => 'Đã có lỗi xảy ra.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['voucher_code'])) {
    $voucher_code = trim($_POST['voucher_code']);

    if (empty($voucher_code)) {
        $response['message'] = 'Vui lòng nhập mã voucher.';
    } else {
        // Tìm voucher hợp lệ: đúng mã, còn hoạt động, chưa hết hạn, còn lượt sử dụng
        $sql = "SELECT * FROM vouchers WHERE code = ? AND is_active = 1 AND (expiry_date IS NULL OR expiry_date >= CURDATE()) AND (usage_limit IS NULL OR times_used < usage_limit)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $voucher_code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $voucher = mysqli_fetch_assoc($result);

        if ($voucher) {
            // Tính toán lại tổng tiền giỏ hàng
            $total_price = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total_price += $item['price'] * $item['quantity'];
            }

            // Tính tiền giảm giá
            $discount_amount = 0;
            if ($voucher['discount_type'] === 'percentage') {
                $discount_amount = $total_price * ($voucher['discount_value'] / 100);
            } else { // 'fixed'
                $discount_amount = $voucher['discount_value'];
            }
            
            // Đảm bảo không giảm giá nhiều hơn tổng tiền
            if ($discount_amount > $total_price) {
                $discount_amount = $total_price;
            }

            $new_total = $total_price - $discount_amount;

            // Lưu thông tin voucher vào session để dùng ở bước thanh toán cuối
            $_SESSION['voucher_info'] = [
                'code' => $voucher['code'],
                'discount_amount' => $discount_amount
            ];

            $response = [
                'success' => true,
                'message' => 'Áp dụng voucher thành công!',
                'new_total_formatted' => number_format($new_total),
                'discount_formatted' => number_format($discount_amount)
            ];

        } else {
            // Nếu không tìm thấy voucher hợp lệ, xóa session cũ nếu có
            unset($_SESSION['voucher_info']);
            $response['message'] = 'Mã voucher không hợp lệ hoặc đã hết lượt sử dụng.';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();