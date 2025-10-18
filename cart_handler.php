<?php
// php/cart_handler.php

// Luôn bắt đầu session ở đầu file
session_start();

// Kiểm tra xem có action và id được gửi lên không
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $product_id = (int)$_GET['id']; // Ép kiểu id về số nguyên để bảo mật

    // Nếu hành động là 'add'
    if ($action === 'add' && $product_id > 0) {
        
        // 1. Khởi tạo giỏ hàng nếu chưa có
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // 2. Thêm sản phẩm vào giỏ hàng
        // Nếu sản phẩm đã có trong giỏ, tăng số lượng lên 1
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } 
        // Nếu sản phẩm chưa có, thêm mới với số lượng là 1
        else {
            $_SESSION['cart'][$product_id] = array(
                'quantity' => 1
                // Bạn có thể thêm các thông tin khác ở đây nếu muốn, 
                // nhưng chỉ cần id và quantity là đủ để hiển thị giỏ hàng
            );
        }
    }
    
    // Các hành động khác như 'remove', 'update' có thể thêm ở đây
    // if ($action === 'remove') { ... }

}

// 3. Sau khi xử lý xong, chuyển hướng người dùng về trang giỏ hàng
header('Location: ../cart.php');
exit(); // Luôn exit() sau khi chuyển hướng

// Xử lý XÓA khỏi giỏ hàng
if (isset($_GET['remove_from_cart'])) {
    $cart_id = $_GET['remove_from_cart'];
    
    // Thêm một lớp bảo mật: chỉ xóa item của đúng user đang đăng nhập
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    
    header("Location: ../cart.php");
    exit();
}
?>