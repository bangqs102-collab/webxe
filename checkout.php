<?php
// BƯỚC 1: KHỞI TẠO VÀ KIỂM TRA
// Luôn bắt đầu session ở dòng đầu tiên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nạp kết nối CSDL
require 'includes/db_connect.php';

// 1. Chuyển hướng nếu chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Chuyển hướng nếu giỏ hàng (trong SESSION) trống
if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit();
}


// BƯỚC 2: LẤY DỮ LIỆU ĐỂ HIỂN THỊ
$cart_items = $_SESSION['cart'];
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// --- BƯỚC 3: HIỂN THỊ GIAO DIỆN ---
include 'includes/header.php';
?>

<div class="container my-5" style="color: white;">
    <h1 class="text-center mb-5">Xác Nhận Đơn Hàng</h1>
    
    <p class="text-center">Vui lòng kiểm tra lại thông tin đơn hàng của bạn trước khi xác nhận.</p>
    
    <table class="table table-dark table-hover mt-4">
        <thead>
            <tr>
                <th colspan="2">Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tạm tính</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $item): ?>
            <tr>
                <td style="width:150px;"><img src="<?= htmlspecialchars($item['image_url']); ?>" class="img-fluid" alt="<?= htmlspecialchars($item['brand']); ?>"></td>
                <td><?= htmlspecialchars($item['brand'] . ' ' . $item['model']); ?></td>
                <td>$<?= number_format($item['price']); ?></td>
                <td><?= htmlspecialchars($item['quantity']); ?></td>
                <td>$<?= number_format($item['price'] * $item['quantity']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- VOUCHER FORM -->
    <div class="row mt-4 justify-content-end">
        <div class="col-md-5">
            <div class="input-group">
                <input type="text" id="voucher_code" class="form-control" placeholder="Nhập mã khuyến mãi">
                <button id="apply_voucher_btn" class="btn btn-secondary">Áp dụng</button>
            </div>
            <div id="voucher_message" class="mt-2"></div>
        </div>
    </div>


    <div class="text-end mt-4">
        <h3>Tổng cộng: $<span id="final_total_display"><?= number_format($total_price); ?></span></h3>
        <form action="php/checkout_handler.php" method="POST">
             <input type="hidden" name="final_total" id="final_total_input" value="<?= $total_price; ?>">
            <button type="submit" name="place_order" class="btn btn-warning btn-lg">Xác nhận Đặt Hàng</button>
        </form>
    </div>
</div>

<!-- JQUERY SCRIPT FOR VOUCHER -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#apply_voucher_btn').on('click', function() {
        var voucherCode = $('#voucher_code').val();
        var messageDiv = $('#voucher_message');
        
        if (voucherCode === '') {
            messageDiv.html('<p class="text-warning">Vui lòng nhập mã voucher.</p>');
            return;
        }

        $.ajax({
            url: 'php/apply_voucher.php',
            type: 'POST',
            dataType: 'json',
            data: { voucher_code: voucherCode },
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<p class="text-success">' + response.message + ' (Giảm: $' + response.discount_formatted + ')</p>');
                    $('#final_total_display').text(response.new_total_formatted);
                    $('#final_total_input').val(response.new_total); // Cập nhật giá trị ẩn
                } else {
                    messageDiv.html('<p class="text-danger">' + response.message + '</p>');
                }
            },
            error: function() {
                messageDiv.html('<p class="text-danger">Không thể kết nối đến máy chủ. Vui lòng thử lại.</p>');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
